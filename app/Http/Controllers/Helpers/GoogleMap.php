<?php

namespace App\Http\Controllers\Helpers;

class GoogleMap {

    public static function addPoint() {
        return view('Helpers.google-map.add-point');
        $html = file_get_contents(__DIR__ . "/googlemap.html");
        return $html;
    }

//    public static function showPoint($lat, $lng) {
//        if ($lat == '') {
//            $lat = 28.07198030177986;
//        }
//        if ($lng == '') {
//            $lng = 30.9814453125;
//        }
//        $data=['lat'=>$lat,'lng'=>$lng];
//        return view('Helpers.google-map.show-point',$data);
//        $html = file_get_contents(__DIR__ . "/googlemapshow.html");
//        $html = str_replace("{lat}", $lat, $html);
//        $html = str_replace('{lng}', $lng, $html);
//        return $html;
//    }

    public static function editPoint($lat=null, $lng=null, $zoom = 6) {
        if($lat==null && $lng==null){
            return static::addPoint();
        }
            
        if ($lat == '') {
            $lat = 28.07198030177986;
        }
        if ($lng == '') {
            $lng = 30.9814453125;
        }
        if($zoom==''){
            $zoom=6;
        }
        $data=['lat'=>$lat,'lng'=>$lng,'zoom'=>$zoom];
        return view('Helpers.google-map.edit-point',$data);
        $html = file_get_contents(__DIR__ . "/googlemapedit.html");
        $html = str_replace("{lat}", $lat, $html);
        $html = str_replace('{lng}', $lng, $html);
        //$html = str_replace('{img}', $img, $html);
        //$html = str_replace('{desc}', $desc, $html);
        $html = str_replace('{zoom}', $zoom, $html);
        return $html;
    }

    public static function showPoint($lat, $lng, $zoom) {
        $data=['lat'=>$lat,'lng'=>$lng,'zoom'=>$zoom];
        return view('Helpers.google-map.show-point',$data);
        $html = file_get_contents(__DIR__ . "/showgooglemap.html");
        $html = str_replace("{lat}", $lat, $html);
        $html = str_replace('{lng}', $lng, $html);
        //$html = str_replace('{img}', $img, $html);
        //$html = str_replace('{desc}', $desc, $html);
        $html = str_replace('{zoom}', $zoom, $html);
        return $html;
    }

//    public static function showGoogleMapMultiple($result, $urlImage) {
//        $html = file_get_contents(__DIR__ . "/showgooglemapmultiple.html");
//        $html = str_replace('{result}', $result, $html);
//        $html = str_replace('{url_image}', $urlImage, $html);
//        return $html;
//    }

}
