<?php

$config = require('./bootstrap/config/config.php');
require_once('./bootstrap/config/environment.php');
require_once('./bootstrap/config/errorHandler.php');
require_once('./utils/Logger.php');
require_once('./controllers/Controller.php');
require_once('./http/middleware/Middleware.php');
require('./http/Router.php');
require_once('./database/DB.php');

//Controller imports start
include_all('./controllers');
//Controller imports end


//Add global middleware
$globalMiddleware = [];

/**
 * Initializes the main components and starts the program.
 * Sessions and Database connections are made from here
 */
function bootstrap()
{
    ob_start();
    //Initialize the Router
    Router::init();

    //Setup database connections
    DB::connect();

    //Initialize the session
    session_start();

    //Listens to incoming requests
    Router::listen();
}
