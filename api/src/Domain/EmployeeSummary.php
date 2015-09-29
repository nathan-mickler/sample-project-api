<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Employee;
use SampleProject\Domain\Database\Shift;
use Carbon\Carbon;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;
use SampleProject\Auth;

class EmployeeSummary implements DomainInterface
{
    public function __invoke(array $input)
    {
        $employeeId = $input['employeeId'];
        $startTime = Carbon::now()->startOfYear();
        $endTime = Carbon::now()->endOfYear();

        // Make sure this employee isn't looking at another employee's information
        if(Auth::isEmployee() && Auth::getId() != $employeeId) {
            return InvalidPayload::create('You can not list the summary for another employee');
        }

        if(!empty($input['startTime'])) {
        	$startTime = $input['startTime'];
        }

        if(!empty($input['endTime'])) {
        	$endTime = $input['endTime'];
        }

        $Employee = new Employee();
        if(!$Employee->load($employeeId)) {
          return ErrorPayload::create('Employee not found');
        }

        // limit filtered results to this employee and within the start/end times
      	$filters = [];
      	$filters[] = ['key' => 'employee_id', 'value' => $employeeId];
      	$filters[] = ['key' => 'start_time', 'operator' => '>=', 'value' => $startTime];
      	$filters[] = ['key' => 'end_time', 'operator' => '<=', 'value' => $endTime];

      	$summary = [];
      	$Shift = new Shift();
      	if($Shifts = $Shift->filter($startTime, $endTime, $filters)) {
      		foreach($Shifts as $Shift) {
      			$startTime = Carbon::createFromFormat('Y-m-d H:i:s', $Shift['startTime']);
      			$endTime = Carbon::createFromFormat('Y-m-d H:i:s', $Shift['endTime']);

      			$startOfWeek = max($startTime->copy()->startOfWeek(), $startTime->copy()->startOfYear());
      			$endOfWeek = min($endTime->copy()->endOfWeek(), $endTime->copy()->endOfYear());
      			$weekDates = $startOfWeek->format('Y-m-d') . ' to ' . $endOfWeek->format('Y-m-d');
      			$diff = $endTime->diff($startTime);

      			$timeWorked = $diff->d * 24 + $diff->h + $diff->i / 60;
      			$timeWorked = $timeWorked - $Shift['break'];
      			$summary[$weekDates] = @$summary[$weekDates] + $timeWorked;
      		}

      		foreach($summary as $week => $hours) {
      			$summary[$week] = "$hours hour" . (($hours == 1) ? '' : 's');
      		}
      	}

        if(count($summary)) {
          return SuccessPayload::create($summary);
        }
        else {
          return ErrorPayload::create('No shifts found');
        }
    }
}
