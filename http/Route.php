<?php

    require_once('./http/BaseRoute.php');

    class Route extends BaseRoute{

        /**
         * @var string[]
         */
        private array $middleware = [];

        public function __construct(string $routePath){
            parent::__construct($routePath);
            $this->parseRoute();
        }

        /**
         * Initializes the route object and creates all the relavant data attributes
         */
        public function parseRoute() : void
        {

            $this->pattern = preg_replace('/\//', '\\/', $this->path);
	
            preg_match_all("/({[^}.]+})/", $this->pattern, $matches);
            $routeParas = [];
            $routeInputs = [];
            
            foreach($matches[0] as $match){
            array_push($routeParas, '/' . $match . '/');
            }
            
            foreach($routeParas as $routePara){
            
                $inputPara = preg_replace(
                    [ '/{/', '/\//', '/}/' ],
                    [ '(?<', '', '>[^\/]+)' ],
                    $routePara
                );
                array_push($routeInputs,$inputPara);
            
            }
            
            $this->pattern = "/^" . preg_replace($routeParas,$routeInputs,$this->pattern) . "$/";
        }

        /**
         * Checks if the current request URI matches this endpoint
         * @param request - The current request object
         */
        public function matchRoute(Request $request): bool
        {
            preg_match_all($this->pattern, $request->path, $matches);
            
            if(count($matches[0]) < 1){
                return false;
            }
            
            foreach($matches as $key => $value){
            
                if(is_string($key) && !empty($value))
                    $request->routeParameters[$key] = $value[0];
            }

            return true;
        }

        /**
         * Executes all the middleware related specified for the endpoint
         */
        public function executeMiddleware($handler) : int | array | null
        {
            $result = $handler($this->middleware);

            return $result;
        }

        /**
         * Adds a middleware for this route
         */
        public function middleWare(string $middleWareName,?string $className = null) : Route
        {
            $className = ($className == null)? $middleWareName : $className;

            Router::MIDDLEWARE($middleWareName, $className); // Register the middleware

            if(! in_array($className, $this->middleware)){
                array_push($this->middleware, $className); // Add the middleware string to the array
            }

            return $this;
        }
    }
