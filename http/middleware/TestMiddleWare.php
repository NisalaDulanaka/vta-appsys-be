<?php

class TestMiddleWare extends Middleware{

    public function handleIncoming(Request $request){
        // Put your incoming logic here
        echo "This will be printed for all incoming requests to endpoints using this middleware!<br><br>";

        return NEXT_ROUTE; // This will indicate the request to move to the next middleware or endpoint
    }

    public function handleOutgoing($response){
        // Put your outgoing logic here
    }
}