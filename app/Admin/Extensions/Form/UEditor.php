<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/5/18
 * Time: 16:58
 */

namespace App\Admin\Extensions\Form;
use Encore\Admin\Form\Field;
class UEditor extends Field
{
    public static $js = [
        '/packages/ueditor/neditor.config.js',
        '/packages/ueditor/neditor.all.min.js',
        '/packages/ueditor/i18n/zh-cn/zh-cn.js',
    ];
    protected $view = 'admin.ueditor';
    public function render()
    {
        $this->script = <<<EOT
        UE.delEditor('$this->id');
        var ue_$this->id = UE.getEditor('$this->id');
        ue_$this->id.ready(function () {
            ue_$this->id.execCommand('serverparam', '_token', '{{ csrf_token() }}');
        });
EOT;
        return parent::render();
    }
}