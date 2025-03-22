<?php

//This is the file where you should set up all your routes

//Setup Routes
Router::POST('/login',[AuthController::class,'login']);

Router::POST('/register',[AuthController::class,'register']);

Router::POST('/confirm-user',[AuthController::class,'confirmUser']);

Router::POST('/protected-route',function () {
    return [
        "message" => "you are authenticated"
    ];
})->middleWare('AuthMiddleware');
