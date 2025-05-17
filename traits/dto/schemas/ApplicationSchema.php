<?php

use Rakit\Validation\Validator;

$addApplicationSchema = [
    'name' => 'required|alpha_spaces',
    'nic' => ['required', 'regex:/^(\d{9}[vV]|\d{12})$/'],
    'telNo' => 'required',
    'address' => 'required',
    'applicationType' => 'required|alpha_spaces',
    'courses.*.courseId'   => 'required',
    'courses.*.courseName' => 'required|alpha_spaces',
    'courses.*.centerId'   => 'required',
    'courses.*.centerName' => 'required|alpha_spaces',
];

$updateApplicationSchema = [
    'applicationId' => 'required',
    'userId' => 'required',
    'nic' => ['required', 'regex:/^(\d{9}[vV]|\d{12})$/'],
    'updates.name' => 'required|alpha_spaces',
    'updates.telNo' => 'required',
    'updates.address' => 'required',
    'updates.applicationType' => 'required|alpha_spaces',
    'updates.courses.*.courseId'   => 'required',
    'updates.courses.*.courseName' => 'required|alpha_spaces',
    'updates.courses.*.centerId'   => 'required',
    'updates.courses.*.centerName' => 'required|alpha_spaces',
];

$searchApplicationsSchema =  function (Validator $validator) {
    return [
        'term' => 'alpha_spaces',
        'startLimit' => 'numeric',
        'itemCount' => 'numeric',
        'filters.applicationType' => 'alpha_spaces',
        'filters.status' => [
            $validator('in', [
                "added",
                "invited",
                "selected",
            ]),
        ],
    ];
};

return [
    "addApplication" => $addApplicationSchema,
    "updateApplication" => $updateApplicationSchema,
    "searchApplications" => $searchApplicationsSchema,
];
