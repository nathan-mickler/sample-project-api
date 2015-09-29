<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use SampleProject\Domain\Database\Employee;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;

class AssignShift implements DomainInterface
{
    public function __invoke(array $input)
    {
    	$employeeId = $input['employeeId'];
        $shiftId = $input['shiftId'];

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

        if($Shift->employee_id == $employeeId) {
            return SuccessPayload::create(['message' => 'Employee already assigned to this shift']);
        }
        else {
        	// make sure this employee doesn't have any overlapping shifts that would conflict...
        	$filters = [];
        	$filters[] = ['key' => 'employee_id', 'value' => $employeeId];

        	if($Shifts = $Shift->filter($Shift->start_time, $Shift->end_time, $filters)) {
        		if(count($Shifts)) {
                    return ErrorPayload::create('Assiging the employee to this shift would result in overlapping shifts for this employee');
        		}
        	}

      		try{
      			$Shift->update(['employee_id' => $employeeId]);

                return SuccessPayload::create(['message' => 'Shift updated successfully']);
      		}
      		catch(\Exception $e) {
                return ErrorPayload::create('There was an error updating this shift');
      		}
        }
    }
}
