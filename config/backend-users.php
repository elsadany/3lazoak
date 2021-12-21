<?php

return [
    /*
     * the main layout for the backend to put content on it
     */
    'backend_layout'=>'backend.layout.master',
    
    /*
     * the content area name which include in master layout file like :-
     *  @yield('content')
     * you should put name 'content' below
     */
    
    'layout_content_area'=>'content',
    
    /*
     * middel ware required to access some pages
     */
    
    'middlewares'=>[
        // put middelwares here
        'web',
        'auth:admin'
        // 'backend-users-auth',
    ],
    
    /*
     * url prefix for backend for admin area
     * like :- www.domain.com/admin or wwww.domain.com/backend
     * you should path the prefix like admin or backend below
     */
    
    'url_prefix'=>'backend',
    
    /*
     * templete path is the path where you can get the views files for users data 
     */
    'templates_path'=>'backend',
    
    /*
     * redirect url after user login 
     */
    'login_redirect'=>'./backend',
    
    /*
     * reset password email from 
    */
    'mail_from'=>'admin@backend.com',
    
    /*
     * bootstrap version 
     * use b+version like b3,b4,b5
     */
    'bootstrap_v'=>'b3',
    'logo'=>'logo.png'
];

