<?php

abstract class Middleware extends Controller{

    /**
     * Handles incoming requests
     * @param Request $request The current request made to the server
     */
    abstract public function handleIncoming(Request $request);

    /**
     * Handles outgoing requests
     * @param mixed $response The outgoing response for the current request
     */
    abstract public function handleOutgoing($response);
}