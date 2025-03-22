<?php

    require_once('./http/Request.php');

    abstract class BaseRoute{

        public $path;
        public $pattern;
        private $data;

        public function __construct(String $routePath){
            $this->path = $routePath;
            $this->data = [];
        }

        abstract public function parseRoute() : void ;

        abstract public function matchRoute(Request $request): bool;
    }