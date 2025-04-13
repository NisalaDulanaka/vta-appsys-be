<?php

require('./traits/dto/ApplicationDto.php');
require_once("./traits/models/ApplicationModel.php");

use App\Utils\UserSession;
use App\Utils\AppLogger;
use App\Utils\AppResponse;

class ApplicationController extends Controller
{
    use ApplicationModel;

    public function addApplication(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'ApplicationSchema.addApplication');
        if ($errors !== null) {
            return AppResponse::error($errors, 403);
        }

        $body = AddApplicationRequest::fromArray($body);

        try {
            $this->addNewApplication(UserSession::$userId, $body);

            return AppResponse::success([
                "message" => "Application successfully submitted",
            ], 201);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAllApplications() {
        try {
            return $this->getApplications(UserSession::$userId);
        } catch(Exception $e) {
            AppLogger::error($e->getMessage());
            throw $e;
        }
    }
}
