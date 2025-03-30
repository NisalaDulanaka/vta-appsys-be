<?php

//This is the file where you should set up all your routes

//Setup Routes
Router::POST('/login', [AuthController::class, 'login']);

Router::POST('/register', [AuthController::class, 'register']);

Router::POST('/confirm-user', [AuthController::class, 'confirmUser']);

Router::POST('/application/add', [ApplicationController::class, 'addApplication'])/*->middleWare('AuthMiddleware')*/;

Router::GET('/application', [ApplicationController::class, 'getAllApplications'])->middleWare('AuthMiddleware');
