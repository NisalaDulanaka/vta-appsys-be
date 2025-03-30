<?php

require_once("./utils/UserSession.php");
require('./traits/dto/ApplicationDto.php');
require_once("./traits/models/ApplicationModel.php");

class ApplicationController extends Controller
{
    use ApplicationModel;

    public function addApplication(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'ApplicationSchema.addApplication');
        if ($errors !== null) {
            return ["error" => $errors];
        }

        $body = AddApplicationRequest::fromArray($body);

        try {
            $this->addNewApplication(UserSession::$userId, $body);

            return [
                "message" => "Application successfully submitted",
            ];
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
