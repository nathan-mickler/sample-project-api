<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use SampleProject\Domain\Database\Employee;
use Carbon\Carbon;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;
use SampleProject\Auth;

class ListCoworkers implements DomainInterface
{
    public function __invoke(array $input)
    {
        $employeeId = $input['employeeId'];
        $shiftId = $input['shiftId'];

        // Make sure this employee isn't looking at another employee's information
        if(Auth::isEmployee() && Auth::getId() != $employeeId) {
            return InvalidPayload::create('You can not list coworkers for another employee');
        }

        // Make sure this is a valid employee
        $Employee = new Employee();
        if(!$Employee->load($employeeId)) {
            return ErrorPayload::create('Employee not found');
        }

        // Make sure this is a valid shift
        $Shift = new Shift();
        if(!$Shift->load($shiftId)) {
            return ErrorPayload::create('Shift not found');
        }

        // Make sure that this employee is assigned to this shift
        if($Shift->employee_id != $employeeId) {
            return ErrorPayload::create('You do not work this shift');
        }

        $coworkers = $Shift->listCoworkers();
        if(count($coworkers)) {
            return SuccessPayload::create($coworkers); 
        }
        else {
            return ErrorPayload::create('No coworkers found for this shift');  
        }
    }
}
