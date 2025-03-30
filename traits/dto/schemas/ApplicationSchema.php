<?php

$addApplicationSchema = [
    'name' => 'required|alpha_spaces',
    'nic' => ['required', 'regex:/^(\d{9}[vV]|\d{12})$/'],
    'telNo' => 'required|numeric',
    'address' => 'required',
    'applicationType' => 'required|alpha_spaces',
    'courses.*.courseId'   => 'required',
    'courses.*.courseName' => 'required|alpha_spaces',
    'courses.*.centerId'   => 'required',
    'courses.*.centerName' => 'required|alpha_spaces',
];

return [
    "addApplication" => $addApplicationSchema,
];
