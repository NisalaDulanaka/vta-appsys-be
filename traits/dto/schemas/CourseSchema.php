<?php

$addCenterSchema = [
    'centerName' => 'required|alpha_spaces',
    'address' => 'required',
    'telNo' => ['regex:/^(\d{10}|+94\d{9})$/'],
    'email' => 'email',
];

$addCourseSchema = [
    'courseName' => 'required|alpha_spaces',
    'nvqLevel' => 'required|numeric|min:1|max:7',
    'courseType' => 'required|in:fullTime,partTime',
    'centers' => 'required|array',
    'centers.*.centerId' => 'required|alpha_num',
    'centers.*.centerName' => 'required|alpha_spaces'
];

return [
    "addCenter" => $addCenterSchema,
    "addCourse" => $addCourseSchema,
];
