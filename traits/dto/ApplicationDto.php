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

enum ApplicationStatus {
    case added;
    case invited;
    case selected;
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
