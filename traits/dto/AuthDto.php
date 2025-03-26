<?php

class LoginRequestDto {
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}

class RegisterRequestDto {
    public function __construct(
        public string $nic,
        public string $email,
        public string $name,
        public string $password,
        public string $userRole,
        public ?string $dob,
    ) {}
}

class ConfirmUserRequestDto {
    public function __construct(
        public string $userName,
        public string $code,
    ) {}
}

class UpdateUserDataDto {
    public function __construct(
        public string $userName,
        public string $code,
    ) {}
}
