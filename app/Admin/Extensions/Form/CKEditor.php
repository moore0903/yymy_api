<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2018/5/16
 * Time: 16:47
 */

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;

class CKEditor extends Field
{
    public static $js = [
        '/vendor/laravel-admin//ckeditor/ckeditor.js',
        '/vendor/laravel-admin//ckeditor/adapters/jquery.js',
    ];

    protected $view = 'admin.ckeditor';

    public function render()
    {
        $token = csrf_token();
        $this->script = "$('textarea.{$this->getElementClassString()}').ckeditor({
            filebrowserImageUploadUrl:'/admin/editorUpload?_token={$token}'
        });";

        return parent::render();
    }
}