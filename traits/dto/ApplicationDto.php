<?php

class ApplicationCourse extends Dto
{
    public function __construct(
        public string $courseId,
        public string $courseName,
        public string $centerId,
        public string $centerName,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            courseId: $data['courseId'] ?? '',
            courseName: $data['courseName'] ?? '',
            centerId: $data['centerId'] ?? '',
            centerName: $data['centerName'] ?? '',
        );
    }
}

enum ApplicationStatus: string {
    case ADDED = "added";
    case INVITED = "invited";
    case SELECTED = "selected";
}

class AddApplicationRequest extends Dto
{
    public string $name;
    public string $nic;
    public string $telNo;
    public string $address;
    public string $applicationType;
    /**
     * @var ApplicationCourse[]
     */
    public array $courses;

    public function __construct(
        string $name,
        string $nic,
        string $telNo,
        string $address,
        string $applicationType,
        array $courses,
    ) {
        $this->name = $name;
        $this->nic = $nic;
        $this->telNo = $telNo;
        $this->address = $address;
        $this->applicationType = $applicationType;
        $this->courses = $courses;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            nic: $data['nic'] ?? '',
            telNo: $data['telNo'] ?? '',
            address: $data['address'] ?? '',
            applicationType: $data['applicationType'] ?? '',
            courses: array_map(fn ($course) => ApplicationCourse::fromArray($course), $data['courses'] ?? [])
        );
    }
}

class ApplicationUpdates extends Dto
{
    public string $name;
    public string $telNo;
    public string $address;
    public string $applicationType;
    /**
     * @var ApplicationCourse[]
     */
    public array $courses;

    public function __construct(
        string $name,
        string $telNo,
        string $address,
        string $applicationType,
        array $courses,
    ) {
        $this->name = $name;
        $this->telNo = $telNo;
        $this->address = $address;
        $this->applicationType = $applicationType;
        $this->courses = $courses;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            telNo: $data['telNo'] ?? '',
            address: $data['address'] ?? '',
            applicationType: $data['applicationType'] ?? '',
            courses: array_map(fn ($course) => ApplicationCourse::fromArray($course), $data['courses'] ?? [])
        );
    }
}

class UpdateApplicationRequest extends Dto {
    public function __construct(
        public string $applicationId,
        public string $userId,
        public string $nic,
        public ApplicationUpdates $updates,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            applicationId: $data['applicationId'] ?? '',
            userId: $data['userId'] ?? '',
            nic: $data['nic'] ?? '',
            updates: ApplicationUpdates::fromArray($data['updates'] ?? [])
        );
    }
}

class SearchApplicationsRequest extends Dto
{
    public function __construct(
        public string $term,
        public string $searchField, // must be 'name', 'nic', or 'address'
        public array $filters = [],
        public int $startLimit = 0,
        public int $itemCount = 10,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            term: $data['term'] ?? '',
            searchField: $data['searchField'] ?? 'name',
            filters: array_key_exists('filters', $data) ? [
                'applicationType' => $data['filters']['applicationType'] ?? null,
                'status' => $data['filters']['status'] ?? null,
                'courses.courseId' => $data['filters']['courseId'] ?? null,
                'courses.centerId' => $data['filters']['centerId'] ?? null,
            ]: [],
            startLimit: $data['startLimit'] ?? 0,
            itemCount: $data['itemCount'] ?? 10,
        );
    }
}
