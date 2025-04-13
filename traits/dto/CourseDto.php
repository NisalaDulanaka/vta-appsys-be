<?php

class AddCenterRequest extends Dto {
    public function __construct(
        public string $centerName,
        public string $address,
        public string $telNo,
        public string $email,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            centerName: $data['centerName'] ?? '',
            address: $data['address'] ?? '',
            telNo: $data['telNo'] ?? '',
            email: $data['email'] ?? '',
        );
    }
}

enum CourseType {
    case fullTime;
    case partTime;
}

class AddCourseRequest extends Dto {
    public function __construct(
        public string $courseName,
        public string $nvqLevel,
        public CourseType $courseType,
        public array $centers,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            courseName: $data['courseName'] ?? '',
            nvqLevel: $data['nvqLevel'] ?? '',
            courseType: $data['courseType'] ?? '',
            centers: array_map(fn($center) => [
                'centerId' => $center['centerId'] ?? '',
                'centerName' => $center['centerName'] ?? ''
            ], $data['centers'] ?? [])
        );
    }
}

class GetCourseRequest extends Dto {
    public function __construct(
        public string $term,
        public int $startLimit,
        public int $itemCount,
        public array $filters
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            term: $data['term'] ?? '',
            startLimit: (int)($data['startLimit'] ?? 0),
            itemCount: (int)($data['itemCount'] ?? 10),
            filters: $data['filters'] ?? []
        );
    }
}
