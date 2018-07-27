<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/5/16
 * Time: 17:07
 */

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function editorUpload(Request $request)
    {
        $file = $request->file('upload');
        $date = date('Ymd');
        $disk = Storage::disk('qiniu');

        $info = $disk->put($date, $file);
        $path = env('QINIU_DOMAIN') . '/' . $info;
        return json_encode([
            'uploaded' => 1,
            'fileName' => $info,
            'url' => $path
        ]);
    }
}