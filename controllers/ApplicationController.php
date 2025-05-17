<?php

require('./traits/dto/ApplicationDto.php');
require_once("./traits/models/ApplicationModel.php");

use Utils\UserSession;
use Utils\AppLogger;
use Utils\AppResponse;

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
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateApplication(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'ApplicationSchema.updateApplication');
        if ($errors !== null) {
            return AppResponse::error($errors, 403);
        }

        $body = UpdateApplicationRequest::fromArray($body);

        try {
            $this->updateExistingApplication($body);

            return AppResponse::success([
                "message" => "Application successfully updated",
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAllApplications(Request $request) {
        $body = $request->getRequestBody();
        AppLogger::debug($body);
        
        $errors = $this->validate($body, 'ApplicationSchema.searchApplications');
        if ($errors !== null) {
            return AppResponse::error($errors, 403);
        }

        $body = SearchApplicationsRequest::fromArray($body);
        try {
            $data = $this->getApplications(UserSession::$userId, $body);

            return AppResponse::success([
                "applications" => $data["records"],
                "total" => $data["totalItemCount"],
                "endLimit" => $data["endLimit"]
            ]);
        } catch(Exception $e) {
            AppLogger::error($e->getMessage());
            throw $e;
        }
    }
}
