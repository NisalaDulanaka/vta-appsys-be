<?php

use Utils\AppResponse;
use Utils\AppLogger;

require('./traits/dto/CourseDto.php');
require_once("./traits/models/CourseModel.php");

class CourseController extends Controller
{
    use CourseModel;

    public function addCenter(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'CourseSchema.addCenter');
        if ($errors !== null) {
            return AppResponse::error($errors, 403);
        }

        $body = AddCenterRequest::fromArray($body);

        try {
            $this->addNewCenter($body);

            return AppResponse::success([
                "message" => "Center successfully added",
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function addCourse(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'CourseSchema.addCourse');
        if ($errors !== null) {
            return AppResponse::error($errors, 403);
        }

        $body = AddCourseRequest::fromArray($body);

        try {
            $this->addNewCourse($body);

            return AppResponse::success([
                "message" => "Course successfully added",
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateCourse(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'CourseSchema.updateCourse');
        if ($errors !== null) {
            return AppResponse::error($errors, 403);
        }

        $body = UpdateCourseRequest::fromArray($body);

        try {
            $this->updateExistingCourse($body);

            return AppResponse::success([
                "message" => "Course successfully updated",
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteCourse(Request $request)
    {
        $body = $request->getRequestBody();
        if (!isset($body["courseId"]) || empty($body["courseId"])) {
            return AppResponse::error([
                "message" => "courseId is required",
            ], 403);
        }
        $courseId = $body["courseId"];
        try {
            $this->deleteExistingCourse($courseId);

            return AppResponse::success([
                "message" => "Course deleted successfully",
            ], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function searchCourses(Request $request)
    {
        $body = $request->getRequestBody();
        $body = GetCourseRequest::fromArray($body);

        try {
            $data = $this->getCourses($body);

            return AppResponse::success([
                "courses" => $data["records"],
                "total" => $data["totalItemCount"],
                "endLimit" => $data["endLimit"]
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getCourse(Request $request)
    {
        $courseId = $request->query("courseId", null);
        if (empty($courseId)) {
            return AppResponse::error([
                "message" => "courseId is required"
            ], 403);
        }

        try {
            $data = $this->getASingleCourse($courseId);
            if (empty($data)) {
                return AppResponse::error([
                    "message" => "Course not found"
                ], 404);
            }

            return AppResponse::success([
                "course" => $data,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function searchCenters(Request $request)
    {
        $body = $request->getRequestBody();
        $body = GetCenterRequest::fromArray($body);

        try {
            $data = $this->getCenters($body);

            return AppResponse::success([
                "centers" => $data["records"],
                "total" => $data["totalItemCount"],
                "endLimit" => $data["endLimit"]
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
