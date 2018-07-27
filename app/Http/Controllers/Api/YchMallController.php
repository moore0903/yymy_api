<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Helpers\YchMallSign;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use App\Http\Helpers\PayFactory;
use App\Models\YchPayOrder;
use App\Models\YchOrderNotifyRecord;
use Illuminate\Support\Facades\DB;

class YchMallController extends ApiController
{
    use YchMallSign;

    const SMS_EXPIRE_TIME = 120;
    const ALIPAY_TYPE = 1;
    const WECHATPAY_TYPE = 2;

    // 登录（弃用）
    public function login(Request $request){
        $user = $request->user();
        if ($user->ych_id) {
            return $this->error("该账号已绑定过会员卡", "-90001");
        }

        $options = $this->setData([
            'WechatID' => '',
            'PhoneNum' => (string)$request->phone,
            'Password' => (string)$request->password
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/WeChatLogin', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "登录失败",
                "-90002");
        }

        //检测是否该会员已绑定其他账号
        $ret = User::where(["ych_id"=>$res["LeaguerID"]])->count();
        if ($ret) {
            return $this->error("该会员卡已绑定其他账号，无法再次绑定", "-90001");
        }

        $user->ych_id = $res["LeaguerID"];
        $user->save();

        return $this->success(['info'=>User::userInfoReturn($user)]);
    }

    // 发送绑定短信
    public function send_join_veri_code(Request $request){
        $options = $this->setData([
            'PhoneNumber' => $request->phone,
        ]);

        $tempIdName = 'LeaguerTempID|'.$request->phone;
        $expireFlag = $tempIdName."flag";
        if (Redis::get($expireFlag)) {
            return $this->error("短信发送太过频繁", "-90015");
        }

        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/SendJoinVeriCode', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "发送失败",
                "-90003");
        }

        //保存临时id
        Redis::set($tempIdName, $res["LeaguerTempID"]);
        Redis::expire($tempIdName, 1800);
        Redis::set($expireFlag, 1);
        Redis::expire($expireFlag, self::SMS_EXPIRE_TIME);

        return $this->success([]);
    }

    // 会员入会
    public function leaguer_apply(Request $request){
        $user = $request->user();
        if ($user->ych_id) {
            return $this->error("该账号已绑定过会员卡", "-90001");
        }

        $phone = $request->phone;
        $tempId = Redis::get('LeaguerTempID|'.$phone);
        if (!($newTempId = $this->check_join_veri_code($request->code, $tempId) ) ) {
            return $this->error("验证码错误", "-90004");
        }

        $options = $this->setData([
            'WeChatID' => '',
            'Password' => (string)$request->password,
            'Sex' => (int)$request->sex,   //0-女 1-男 2-未知
            'RealName' => 'foo',
            'Age' => (int)$request->age,
            'LeaguerTempID' => $newTempId,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/LeaguerApply', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "注册失败",
                "-90005");
        }

        $user->ych_id = $res["LeaguerID"];
        $user->save();

        return $this->success(['info'=>User::userInfoReturn($user)]);
    }

    // 获取会员信息
    public function get_leaguer_info(Request $request){
        $user = $request->user();
        $ychId = $user->ych_id;

        return $this->get_leaguer_info_inside($ychId);
    }

    public function get_leaguer_info_inside($ychId = ""){
        $data = ["gold_coin"=>0.00, "token_coin"=>0.00, "pagtit_count"=>0,
            "is_take_card"=>0, "card_qrcode"=>"", "age"=>0, "sex"=>-1, "cards"=>[]];
        //用户信息
        $ychId = isset($_POST["ych_id"]) ? $_POST["ych_id"]: $ychId;
        $options = $this->setData([
            'LeaguerID' => isset($_POST["ych_id"]) ? $_POST["ych_id"]: $ychId,
            'Phone' => "",
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetLeaguerInfo', $options);
        if ($res && $res["ResponseStatus"]["ErrorCode"] === "0") {
            $data["sex"] = $res["Sex"];
            $brithdayTime = strtotime($res["Birthday"] ?? null);
            $data["age"] = $brithdayTime ? floor((time() - $brithdayTime)/(365 * 24 * 3600)) + 1 : 0;

            $isTakeCard = (int)$res["IsTake"];
            $data["is_take_card"] = $isTakeCard;

            if ($isTakeCard) {
                $cards = $this->get_iccard_info($ychId);
                $data["cards"] = $cards;
            } else {
                $data["card_qrcode"] = "http://qr.liantu.com/api.php?text=LG_".$ychId;
            }
        }

        if ($ychId) {
            //币信息
            $data = $this->get_leaguer_values($ychId) + $data;

            //套票数量
            $pagtits = $this->get_lgpagtit_details($ychId);
            $data["pagtit_count"] = count($pagtits);
        }

        return $this->success(["info" => $data]);
    }

    // 获取优惠券列表
    public function get_leaguer_coupon_list(Request $request){
        $user = $request->user();

        $options = $this->setData([
            'LeaguerID' => $user->ych_id,
            'CouponStatus' => (int)$request->type,    //1-未使用 2-已使用 3-已过期
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetLeaguerCoupon',$options);

        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "获取失败",
                "-90006");
        }

        return $this->success(["list" => $res["List"]]);
    }

    // 获取用户套票列表
    public function get_leaguer_pagtit_list(Request $request){
        $user = $request->user();

        $data = $this->get_lgpagtit_details($user->ych_id);

        return $this->success(["list" => $data]);
    }

    // 获取商品列表
    public function get_goods_list(Request $request){
        $data = $this->get_goods_data((int)$request->type);

        if ($data === false) {
            return $this->error("获取失败", "-90006");
        }

        return $this->success(["list" => $data]);
    }

    // 获取套票详情
    public function get_pagtit_detail(Request $request){
        $list = [
            "淘气堡月票"=>[
                "BussinessID"=>"bd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"淘气堡月票",
                "GoodsID"=>"3d4b3b83-1368-47ce-96d0-a8f00157133b",
                "GoodsPrice"=>288,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：淘气堡",
                "UsageTime"=>"购买后立即生效，有效期30天 ，30天内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>"本套票各项目每天限制使用1次"
            ],
            "淘气堡季票"=>[
                "BussinessID"=>"a2fbd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"淘气堡季票",
                "GoodsID"=>"45de929b-f10f-4159-93c8-a8f0015a72e4",
                "GoodsPrice"=>688,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：淘气堡",
                "UsageTime"=>"购买后立即生效，有效期90天 ，90天内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>"本套票各项目每天限制使用1次"
            ],
            "淘气堡开业年票"=>[
                "BussinessID"=>"a2fbd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"淘气堡开业年票",
                "GoodsID"=>"7ca95689-8208-4bfa-87a4-a8f1010f1f8a",
                "GoodsPrice"=>888,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：淘气堡",
                "UsageTime"=>"购买后立即生效，有效期1年 ，1年内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>"本套票各项目每天限制使用1次"
            ],
            "淘气堡次票"=>[
                "BussinessID"=>"a2fbd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"淘气堡次票",
                "GoodsID"=>"00a7c7b9-94a2-46d2-b7a4-a8fa012dc8c6",
                "GoodsPrice"=>300,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：淘气堡(12次)",
                "UsageTime"=>"购买后立即生效，有效期2年 ，2年内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>""
            ],
            "淘气堡三次票"=>[
                "BussinessID"=>"a2fbd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"淘气堡三次票",
                "GoodsID"=>"f7f9da79-1242-4aaf-8702-a90a00f23250",
                "GoodsPrice"=>100,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：沙池(2次), 淘气堡(3次)",
                "UsageTime"=>"购买后立即生效，有效期2年 ，2年内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>"本套票共计使用次数为5次"
            ],
            "168元暑假月卡"=>[
                "BussinessID"=>"a2fbd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"168元暑假月卡",
                "GoodsID"=>"89467f75-eb4f-4fd1-aa66-a90a00f456f1",
                "GoodsPrice"=>168,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：淘气堡",
                "UsageTime"=>"购买后立即生效，有效期1月 ，1月内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>"本套票各项目每天限制使用1次"
            ],
            "淘气堡7次票"=>[
                "BussinessID"=>"a2fbd221-89a3-4065-b0e7-ba46c04e4686",
                "GoodsName"=>"淘气堡7次票",
                "GoodsID"=>"e7aec161-9a27-41bd-892b-a90a01072d78",
                "GoodsPrice"=>200,
                "GoodsType"=>102,
                "Detail"=>"本套票包含以下项目：沙池(5次), 淘气堡(7次)",
                "UsageTime"=>"购买后立即生效，有效期2年 ，2年内必须用完",
                "ReturnInstructions"=>"售出后不可退货",
                "Conditions"=>"本套票共计使用次数为12次"
            ]
        ];
        $goodsName = $request->goods_name;
        return isset($list[$goodsName]) ? $this->success(["info"=>$list[$goodsName]]) :
            $this->error("套票信息不存在", -90018);
    }

    // 创建订单
    public function create_order(Request $request){
        $user = $request->user();
        $type = $request->goods_type;
        $goodsId = $request->goods_id;
        $payType = $request->pay_type;

        $pay = PayFactory::getInstance($payType);
        if (!$pay) {
            return $this->error("支付方式错误", "-90016");
        }

        $goodsInfo = $this->get_goods_data($type, $goodsId);
        if (!$goodsInfo || count($goodsId) < 1)
            return $this->error("商品信息错误", "-90007");
        $goodsInfo = $goodsInfo[0];

        $orderNumber = date("YmdHis").rand(100000, 999999);
        $options = $this->setData([
            "TPOrderNo"=>$orderNumber,
            "GoodsType"=>$type,   //101 代币 102套票
            "LeaguerID"=>$user->ych_id,
            "GuestName"=>"",
            "GuestMobile"=>"",
            "OrderMoney"=>$goodsInfo["GoodsPrice"],
            "CouponNumber" => "",
            "SendAddress"=>"",
            "Summary"=>"",
            "OrderItem"=>[[
                "GoodsID"=>$goodsId,
                "GoodsName"=>$goodsInfo["GoodsName"],
                "GoodsPrice"=>$goodsInfo["GoodsPrice"],
                "Amount"=>1.00000,
                "Summary"=>""
            ]],
        ], [$this, "getOrderSign"]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/CreateOrder', $options, "post", "json");
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "创建订单失败",
                "-90008");
        }

        YchPayOrder::create([
            "order_number" => $orderNumber,
            "user_id" => $user->id,
            "ych_id" => $user->ych_id,
            "total_amount" => $goodsInfo["GoodsPrice"] * 100,
            "paid_amount" => "0",
            "pay_type" => $payType,
            "goods_type" => $type,
            "goods_id" => $goodsId,
        ]);

        //调用支付
        $result = $pay->purchase($orderNumber, $goodsInfo["GoodsPrice"], $goodsInfo["GoodsName"]);

        return is_array($result) ? $this->success(["info" => $result + ["order_number"=>$res["OrderNo"]] ])
            : $this->error("请求错误", -90017);
    }

    // 支付宝回调
    public function alipay_notify(Request $request){
        $pay = PayFactory::getInstance(self::ALIPAY_TYPE);

        // 记录回调记录
        $record = YchOrderNotifyRecord::record($request->out_trade_no, $request->all(), self::ALIPAY_TYPE);
        if (!$pay->notify() ) return "fail";

        $ret = $this->order_pay_completed($request->out_trade_no, $request->trade_no,
            $request->total_amount * 100, self::ALIPAY_TYPE);

        if ($record && $ret) {
            $record->is_ok = 1;
            $record->save();
        }
        return $ret ? "success" : "fail";
    }

    // 微信回调
    public function wechatpay_notify(Request $request, Response $response)
    {
        $pay = PayFactory::getInstance(self::WECHATPAY_TYPE);

        $xml = simplexml_load_string(file_get_contents("php://input") );
        // 记录回调记录
        $record = YchOrderNotifyRecord::record($xml->out_trade_no, $xml, self::WECHATPAY_TYPE);
        if (!$pay->notify()) return "fail";

        $ret = $this->order_pay_completed((string)$xml->out_trade_no, (string)$xml->transaction_id,
            (string)$xml->total_fee, self::WECHATPAY_TYPE);

        if ($record && $ret) {
            $record->is_ok = 1;
            $record->save();
        }

        return $ret ? $response->header("Content-Type", "application/xml")->setContent("
            <xml>
            <return_code><![CDATA[SUCCESS]]></return_code>
            <return_msg><![CDATA[OK]]></return_msg>
            </xml>")
            : "fail";
    }


    // 获取订单列表
    public function get_order_page_list(Request $request){
        $page = max($request->page, 1);
        $pageSize = isset($request->pagesize)?$request->pagesize:10;

        $user = $request->user();
        $options = $this->setData([
            'LeaguerID' => $user->ych_id,
            "PageSize"=>$pageSize,
            "PageIndex"=>$page,
            "PayState"=>"5000",
            "LogisticsState"=>"",
            "OrderNum"=>$request->order_number ? : "",
            "LgNammeOrCode"=>"",
            "StartTime"=>"",
            "EndTime"=>"",
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetOrderPageList', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "获取失败",
                "-90010");
        }
        $data = $res["List"];

        foreach ($data as $k=>$v) {
            $detail = $this->get_order_details($v["ID"]);
            $data[$k] = $v + ($detail ? : [
                    "GoodsAmount" => 1,
                    "GoodsNames" => "",
                    "GoodsTypes" => 101,
                    "PayTypeNames" => "通用支付"
                ]);
        }

       // 去除非充值记录
        $data = array_filter($data, function ($v) {
            return in_array($v["GoodsTypes"], [101, 102]);
        });
        $data = array_values($data);

        return $this->success(["list" => $data]);
    }

    // 获取会员游玩记录
    public function get_leaguer_play_log(Request $request){
        $page = max($request->page, 1);
        $pageSize = isset($request->pagesize)?$request->pagesize:10;

        $user = $request->user();
        $options = $this->setData([
            'LeaguerID' => $user->ych_id,
            "StartLogTime"=>"",
            "EndLogTime"=>"",
            "PageSize"=>$pageSize,
            "PageIndex"=>$page,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetPlayLogListByPage', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "获取失败",
                "-90011");
        }

        return $this->success(["list" => $res["List"]]);
    }

    // 获取商户信息
    public function get_business_info(){
        $client = new Client();
        $options = $this->setData([
            'BisID' => env('YCH_MALL_BUSSINESS_ID')
        ]);
        $res = $client->request('POST',env('YCH_MALL_VPN').env('YCH_MALL_VPN').'/OnLine/v1.2/GetBusiness',[
            'form_params' => $options
        ]);
        info($res->getBody());
    }

    //根据用户手机号查找guid
    public function get_leaguer_by_phone(Request $request){
        $options = $this->setData([
            'Phone' =>  isset($_POST["phone"]) ? $_POST["phone"] : "",
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetLeaguerByPhone', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error("获取失败", "-90017");
        }

        return $this->success(["info" => $res]);
    }

    // 获取扫描机器信息
    public function get_scheme(Request $request){
        $code = $request->code;

        preg_match("/qrcode=(.*?)_/", $code, $match);
        if (!$match) {
            return $this->error("二维码信息错误", "-90012");
        }

        $options = [
            'code' => $match[1]
        ];
        $res = $this->request(env('YCH_MALL_NODE_VPN').'/get_scheme', $options);
        if (!$res || !$res["status"] || !count($res["data"])) {
            return $this->error("扫描失败", "-90019");
        }

        return $this->success(["info"=>$res["data"][0]]);
    }

    // 扫描机器二维码
    public function scan_code(Request $request){
        $user = $request->user();
        $code = $request->code;

        preg_match("/qrcode=(.*?)$/", $code, $match);
        if (!$match) {
            return $this->error("二维码信息错误", "-90012");
        }

        $options = $this->setData([
            'LeaguerID' => $user->ych_id,
            'Encode' => $match[1]
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/ScanCode', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "扫描失败",
                "-90013");
        }

        return $this->success(["info"=>$res]);
    }

    // 获取被扫描机器状态    轮询?
    public function get_remote_trans(Request $request){
        $options = $this->setData([
            'StateID' => $request->trans_id
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetRemoteTrans', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0" || !in_array($res["State"], [17, 5, 9]) ) {
            $errorMsg = "扫描失败";
            if (isset($res["ResponseStatus"])) {
                $errorMsg = isset($res["State"]) && $res["State"] != 17 ?
                    $res["StateMsg"] : $res["ResponseStatus"]["Message"];
            }
            return $this->error($errorMsg, "-90014");
        }
        preg_match('/(?:\[)(.*)(?:\])/i', $res["StateMsg"], $money);
        preg_match('/:(.*)$/', $res["StateMsg"], $balance);

        return $this->success(["state"=>$res["State"], "message"=>$res["StateMsg"], 'money' => $money?$money[1]:null, 'balance' => $balance?$balance[1]:null, 'datetime' => date('Y-m-d H:i:s',time())] );
    }

    // 赠送代币
    public function send_coin(Request $request){
        $user = $request->user();

        DB::beginTransaction();
        $userInfo = DB::select("select * from users where id=:id for update",
            ['id'=>$user->id]);
        if ($userInfo[0]->has_send_coin) $this->message("领取成功");

        $ret = DB::update("update users set has_send_coin=1 where id=:id ",
            ['id'=>$user->id]);
        if ($ret) {
            $options = $this->setData([
                'LeaguerID' => $user->ych_id,
                'CoinAmount' => 5,          //赠送代币数量
            ]);
            $res = $this->request(env('YCH_MALL_VPN') . '/OnLine/v1.2/RechargeCoin', $options);
            if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
                DB::rollBack();
                return $this->error(isset($res["ResponseStatus"]) ? $res["ResponseStatus"]["Message"] : "领取失败",
                    "-90013");
            }

            DB::commit();
            return $this->message("领取成功");
        } else {
            DB::rollBack();
            return $this->error("该手机号已领取过");
        }

    }

    // 获取会员代币信息
    private function get_leaguer_values($id){
        $options = $this->setData([
            'LeaguerID' => $id,
            "ValueCode"=>0,
            "PayId"=>""
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetLeaguerValues', $options);
        $data = ["gold_coin" => 0.00, "token_coin" => 0.00];

        if ($res && $res["ResponseStatus"]["ErrorCode"] === "0") {
            $rows = $res["rows"];
            foreach ($rows as $v) {
                switch ($v["ValueCode"]) {
                    case 401:
                        $data["gold_coin"] = $v["Value"];
                        break;
                    case 402:
                        $data["token_coin"] = $v["Value"];
                        break;
                    default:;
                }
            }
        }
        return $data;
    }

    // 获取会员卡信息
    private function get_iccard_info($id){
        $options = $this->setData([
            'LeaguerID' => $id,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetIcCardInfo', $options);
        $data = [];
        if ($res && $res["ResponseStatus"]["ErrorCode"] === "0") {
            $data = $res["list"];
        }

        return $data;
    }

    // 验证绑定短信
    private function check_join_veri_code($code, $tempId){
        $options = $this->setData([
            'LeaguerTempID' => $tempId,
            'VerificationCode' => $code,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/CheckJoinVeriCode', $options);
        if ($res && $res["ResponseStatus"]["ErrorCode"] === "0") {
            return $res["LeaguerTempID"];
        }

        return false;
    }

    // 套票信息
    private function get_lgpagtit_details($id){
        $options = $this->setData([
            'LeaguerID' => $id,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetLgPagTitDetails', $options);
        $data = [];
        if ($res && $res["ResponseStatus"]["ErrorCode"] === "0") {
            $data = $res["LgPagTitDetails"];
        }

        return $data;
    }

    // 获取优惠券可用数(废弃)
    private function get_lgcoupon_remain($id){
        $options = $this->setData([
            'LeaguerID' => $id,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetLgCouponRemian', $options);
        $count = 0;
        if ($res && $res["ResponseStatus"]["ErrorCode"] === "0") {
            $count = $res["Remain"];
        }
        return $count;
    }

    // 获取商品信息
    private function get_goods_data($type, $goodsId = ""){
        $options = $this->setData([
            "LeaguerID"=>"",
            "GoodsID"=> $goodsId,
            "GoodsType"=>(int)$type,   //2 预存款 101 代币 102套票
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetGoodsList', $options);

        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return false;
        }

        return $res["list"];
    }

    //完成订单
    private function order_pay_completed($orderNumber, $TPorderNumber, $money, $payType=""){
        //改变订单状态
        DB::beginTransaction();
        $order = DB::select("select * from ych_pay_order where order_number=:n and status=0 for update",
            ['n'=>$orderNumber]);
        if (!$order) return false;

        $ret = DB::update("update ych_pay_order set status=1,paid_amount=:m,pay_time=:t where order_number=:n",
            ['n'=>$orderNumber, "t"=>date("Y-m-d H:i:s"), 'm'=>$money]);
        if ($ret) {
            DB::commit();

            //请求接口充值
            $options = $this->setData([
                'OnLineOrder' => $TPorderNumber,
                "AccountNumber"=>"",
                "TPOrderNo"=>$orderNumber,
                "PayName"=>(int)(3-$payType),  //油菜花支付类型：其他-0 微信-1 支付宝2
            ]);
            $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/OrderPayAndCompleted', $options);
            info("position:完成订单响应,message:".var_export($res,true));
            if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
              info("position:请求购买接口失败,data:".var_export($options,true).",message:".var_export($res,true));
            }

            return true;
        } else {
            DB::rollBack();
            return false;
        }
    }

    // 获取订单详情
    private function get_order_details($orderId){
        $options = $this->setData([
            'ID' => $orderId,
        ]);
        $res = $this->request(env('YCH_MALL_VPN').'/OnLine/v1.2/GetOrderDetails', $options);
        if (!$res || $res["ResponseStatus"]["ErrorCode"] !== "0") {
            return null;
        }

        return $res;
    }
}
