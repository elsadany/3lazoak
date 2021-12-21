<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class Deliveryauth
{
      public function handle(Request $request, Closure $next)
    {
          if($request->user()->type!=3)
              return response ()->json (['status'=>500,'errors'=>['you are not authorised to this data']]);
        
          if($request->user()->active!=1)
              return response ()->json (['status'=>500,'errors'=>['you are not accepted yet']]);
        
        return $next($request);
    }

}