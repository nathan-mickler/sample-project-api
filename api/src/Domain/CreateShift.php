<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use SampleProject\Domain\Database\Manager;
use SampleProject\Domain\Database\Employee;
use Carbon\Carbon;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;
use SampleProject\Auth;

class CreateShift implements DomainInterface
{
    public function __invoke(array $input)
    {
		$employeeId = $input['employeeId'];
		$managerId = null;
		$break = empty($input['break'] ? 0 : $input['break']);
		$startTime = null;
		$endTime = null;

    		// Validate the startTime and endTime variables
        if (empty($input['startTime']) || empty($input['endTime'])) {
            return InvalidPayload::create('Missing required inputs (startTime and endTime are required');
        }
        else {
        	$startTime = Carbon::createFromFormat('Y-m-d H:i:s', urldecode($input['startTime']));
        	$endTime = Carbon::createFromFormat('Y-m-d H:i:s', urldecode($input['endTime']));
        }

        // validation for start and end times
		$error_message = null;
        if($startTime > $endTime) {
        	$error_message = "startTime must be before endTime";
        }
        elseif($startTime->diffInHours($endTime) > 24) {
        	$error_message = "startTime and endTime can not differ by more than 24 hours";
        }

        if($error_message) {
            return ErrorPayload::create($error_message);
        }

        // validation for employeeId
        $employeeId = null;
        if(!empty($input['employeeId'])) {
        	$employeeId = $input['employeeId'];

        	// Make sure this is a valid employee
	        $Employee = new Employee();
	        if(!$Employee->load($employeeId)) {
                return ErrorPayload::create('Employee not found');
	        }
	        else {
				// make sure this employee doesn't have any overlapping shifts that would conflict...
		      	$filters = [];
        		$filters[] = ['key' => 'employee_id', 'value' => $employeeId];

		      	$Shift = new Shift();
		      	if($Shifts = $Shift->filter($startTime, $endTime, $filters)) {
		      		foreach($Shifts as $checkShift) {
		      			if(count($Shifts)) {
                            return ErrorPayload::create('Creating this shift for this employee would result in overlapping shifts for this employee');
						}
		      		}
		      	}
	        }
        }

        $managerId = null;
        if(!empty($input['managerId'])) {
        	$managerId = $input['managerId'];
        }
        else {
            // NOTE: This would pull the actual managerId in the live application (set self to default if no managerId provided)
            // here we are simply going to assume that it is manager 4 since there isn't a login in this sample project
        	$managerId = Auth::getId();;
        }

      	// Make sure this is a valid employee
        $Manager = new Manager();
        if(!$Manager->load($managerId)) {
            return ErrorPayload::create('Manager not found');
        }
        
		try{
			Shift::create([
				'manager_id' => $managerId,
				'employee_id' => $employeeId,
				'break' => $break,
				'start_time' => $startTime,
				'end_time' => $endTime,
			]);

            return SuccessPayload::create(['message' => 'Shift created successfully']);
		}
		catch(\Exception $e) {
            return ErrorPayload::create('There was an error creating this shift');
		}
    }
}
