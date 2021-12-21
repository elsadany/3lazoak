<?php

namespace App\Http\Middleware;
use Elsayednofal\BackendLanguages\Models\Languages;
use Closure;

class FrontLanguageMiddleWare {

    public function handle($request, Closure $next) {
      if(session('lang_id')!=''){
         $language= Languages::where('id',session('lang_id'))->first();
         if(!is_object($language))
             $language= Languages::first();
      }else{
          $language= Languages::first();
      }  
      \Session::put('lang_id',$language->id);
      \Session::put('language',$language);
      $request->lang_id=$language->id;
     
              app()->setLocale($language->symbole);

        return $next($request);
    }
    

}
