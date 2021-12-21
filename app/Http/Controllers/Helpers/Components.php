<?php

namespace App\Http\Controllers\Helpers;

class Components {

 public static function upload($image = './assets/images/avatar_image.png', $css_classes = '',$name) {
        if($image == '' || !file_exists($image))
            $image = './assets/images/avatar_image.png';
        $html = file_get_contents(__DIR__ . '/uploader1.html');
        $html = str_replace('profile_picture', $image, $html);
        $html = str_replace('{css_classes}', $css_classes, $html);
        $html = str_replace('{name}', $name, $html);

        return $html;
    }
}
