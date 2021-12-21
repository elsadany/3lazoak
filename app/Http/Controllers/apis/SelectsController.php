<?php

namespace App\Http\Controllers\apis;

use Elsayednofal\BackendLanguages\Models\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class SelectsController extends Controller {

   
    function getCities(Request $request){
        $cities= \App\Models\City::all();
        return response()->json(['status' => 200, 'data' => $cities->toArray()]);
    }
    
    function getLanguages() {
        $languages = Languages::all();
        return response()->json(['status' => 200, 'data' => $languages->toArray()]);
    }

    function getActiveLang(){
       
        $lang_id=session()->has('language')?session('language')->id:1;
        return response()->json(['status'=>true,'data'=>$lang_id]);
    }

}
