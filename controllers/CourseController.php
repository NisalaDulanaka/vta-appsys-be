<?php

use App\Utils\AppResponse;

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
            ], 201);
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
            ], 201);
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

    public function getCenters(Request $request)
    {
        return $request->getRequestBody();
    }
}
