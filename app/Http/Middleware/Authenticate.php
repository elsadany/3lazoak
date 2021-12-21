<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        //dd($request->path());
        if (! $request->expectsJson()) {
            if(strstr($request->path(),'backend/')){
                return route('backend.login');
            }else if(strstr($request->path(),"shop/")){
                return route('shops.login');
            }else{

                return route('login');
            }
        }
    }
}
