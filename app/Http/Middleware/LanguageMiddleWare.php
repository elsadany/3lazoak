<?php

namespace App\Http\Middleware;
use Elsayednofal\BackendLanguages\Models\Languages;
use Closure;

class LanguageMiddleWare {

    public function handle($request, Closure $next) {
      if($request->has('lang_id')&&$request->lang_id!=''){
         $language= Languages::where('id',$request->lang_id)->first();
         if(!is_object($language))
             $language= Languages::first();
      }else{
          $language= Languages::first();
      }  
      \Session::put('lang_id',$language->id);
      $request->lang_id=$language->id;
              app()->setLocale($language->symbole);

        return $next($request);
    }

}
