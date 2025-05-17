<?php

//This is the file where you should set up all your routes

//Setup Routes
Router::POST('/login', [AuthController::class, 'login']);

Router::POST('/register', [AuthController::class, 'register']);

Router::POST('/confirm-user', [AuthController::class, 'confirmUser']);

Router::POST('/resend-code', [AuthController::class, 'resendCode']);

Router::GET('/get-user', [AuthController::class, 'getUser'])->middleWare('AuthMiddleware');

Router::POST('/application/add', [ApplicationController::class, 'addApplication'])->middleWare('AuthMiddleware');

Router::POST('/application/update', [ApplicationController::class, 'updateApplication'])->middleWare('AuthMiddleware');

Router::POST('/application/search', [ApplicationController::class, 'getAllApplications'])->middleWare('AuthMiddleware');


Router::POST('/course/add', [CourseController::class, 'addCourse'])->middleWare('AuthMiddleware')->middleWare('AdminMiddleWare');

Router::POST('/course/update', [CourseController::class, 'updateCourse'])->middleWare('AuthMiddleware')->middleWare('AdminMiddleWare');

Router::POST('/course/delete', [CourseController::class, 'deleteCourse'])->middleWare('AuthMiddleware')->middleWare('AdminMiddleWare');

Router::POST('/course/search', [CourseController::class, 'searchCourses']);

Router::GET('/course', [CourseController::class, 'getCourse'])->middleWare('AuthMiddleware');


Router::POST('/center/add', [CourseController::class, 'addCenter'])->middleWare('AuthMiddleware')->middleWare('AdminMiddleWare');

Router::POST('/center/search', [CourseController::class, 'searchCenters'])->middleWare('AuthMiddleware');

// TODO: Update application, Update course, Update center
// TODO: Add admin middleware
// TODO: Add interview round, update interview round, update interview round status.
