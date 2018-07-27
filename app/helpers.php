<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2017/12/14
 * Time: 18:31
 */

function br2nl($text) {
    return preg_replace('/<br\\s*?\/??>/i', '', $text);
}