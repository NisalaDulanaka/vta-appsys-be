<?php

use Rakit\Validation\Validator;

$loginSchema = [
    'email' => 'required|email',
    'password' => 'required|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
];

$registerSchema = function (Validator $validator) {
    return [
        'nic' => ['required', 'regex:/^(\d{9}[vV]|\d{12})$/'],
        'email' => 'required|email',
        'name' => 'required|alpha_spaces',
        'password' => 'required|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        'userRole' => [
            'required',
            $validator('in', ["vta-student-role"]), // @TODO: move user roles to a separate config file
        ],
        'dob' => 'required|regex:/^\d{2}\/\d{2}\/\d{4}/',
    ];
};

$confirmUserSchema = [
    'userName' => ['required', 'regex:/^(\d{9}[vV]|\d{12})$/'],
    'code' => 'required|numeric',
];

return [
    "login" => $loginSchema,
    "register" => $registerSchema,
    "confirm" => $confirmUserSchema,
];
