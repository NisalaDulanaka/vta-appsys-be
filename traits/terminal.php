<?php

    trait Terminal{

        public static function log($message)
        {
            $message = date("H:i:s") . " - $message - ".PHP_EOL;
            print($message);
            flush();
            ob_flush();
        }
    }