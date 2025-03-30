<?php

class LoginRequestDto extends Dto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            email: $data['email'] ?? '',
            password: $data['password'] ?? ''
        );
    }
}

class RegisterRequestDto extends Dto
{
    public function __construct(
        public string $nic,
        public string $email,
        public string $name,
        public string $password,
        public string $userRole,
        public ?string $dob,
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            nic: $data['nic'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            name: $data['name'] ?? '',
            userRole: $data['userRole'] ?? '',
            dob: $data['dob'] ?? ''
        );
    }
}

class ConfirmUserRequestDto extends Dto
{
    public function __construct(
        public string $userName,
        public string $code,
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            userName: $data['userName'] ?? '',
            code: $data['code'] ?? ''
        );
    }
}

class UpdateUserDataDto
{
    public function __construct(
        public string $userName,
        public string $code,
    ) {}
}
