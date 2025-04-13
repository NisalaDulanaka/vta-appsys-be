<?php

use App\Utils\AppResponse;
use App\Utils\UserSession;

class AdminMiddleWare extends Middleware
{
    public function handleIncoming(Request $request)
    {
        // check for auth header
        if (UserSession::$userRole !== 'vta-admin-role') {
            return AppResponse::error(["message" => "Access denied"], 403);
        }

        return NEXT_ROUTE;
    }

    public function handleOutgoing($response)
    {
        // Put your outgoing logic here
    }
}