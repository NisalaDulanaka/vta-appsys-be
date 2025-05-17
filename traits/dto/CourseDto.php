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

enum CourseType: string {
    case fullTime = 'fullTime';
    case partTime = 'partTime';
}

class AddCourseRequest extends Dto {
    public function __construct(
        public string $courseName,
        public string $nvqLevel,
        public string $description,
        public CourseType $courseType,
        public array $centers,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            courseName: $data['courseName'] ?? '',
            nvqLevel: $data['nvqLevel'] ?? '',
            courseType: CourseType::from($data['courseType'] ?? ''),
            description: $data['description'] ?? '',
            centers: array_map(fn($center) => [
                'centerId' => $center['centerId'] ?? '',
                'centerName' => $center['centerName'] ?? ''
            ], $data['centers'] ?? [])
        );
    }
}

class CourseUpdates extends Dto {
    public function __construct(
        public string $courseName,
        public string $nvqLevel,
        public string $description,
        public CourseType $courseType,
        public array $centers,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            courseName: $data['courseName'] ?? '',
            nvqLevel: $data['nvqLevel'] ?? '',
            courseType: CourseType::from($data['courseType'] ?? ''),
            description: $data['description'] ?? '',
            centers: array_map(fn($center) => [
                'centerId' => $center['centerId'] ?? '',
                'centerName' => $center['centerName'] ?? ''
            ], $data['centers'] ?? [])
        );
    }
}

class UpdateCourseRequest extends Dto {
    public function __construct(
        public string $courseId,
        public CourseUpdates $updates,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            courseId: $data['courseId'] ?? '',
            updates: CourseUpdates::fromArray($data['updates'] ?? [])
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

class GetCenterRequest extends Dto {
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
