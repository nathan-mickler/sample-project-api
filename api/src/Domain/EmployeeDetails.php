<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Employee;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;

class EmployeeDetails implements DomainInterface
{
    public function __invoke(array $input)
    {
        $employeeId = $input['employeeId'];

        $Employee = new Employee();
        if($Employee->load($employeeId)) {
            $response = [
                'id' => $Employee->id,
                'name' => $Employee->name,
                'phone' => $Employee->phone,
                'email' => $Employee->email
            ];
            return SuccessPayload::create($response);
        }
        else {
            return ErrorPayload::create('Employee not found');
        }
    }
}
