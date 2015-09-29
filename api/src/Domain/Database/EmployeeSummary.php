<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Employee;
use Carbon\Carbon;

class EmployeeSummary implements DomainInterface
{
    public function __invoke(array $input)
    {
        $employeeId = $input['employeeId'];
        $startTime = Carbon::now()->startOfYear();
        $endTime = Carbon::now()->endOfYear();

        if(!empty($input['startTime'])) {
        	$startTime = $input['startTime'];
        }

        if(!empty($input['endTime'])) {
        	$endTime = $input['endTime'];
        }

        $Employee = new Employee();
        if(!$Employee->load($employeeId)) {
        	return (new Payload)
            ->withStatus(Payload::ERROR)
            ->withOutput([
                'data' => json_encode(['error' => 'Employee not found'])
            ]);
        }

        // limit filtered results to this employee and within the start/end times
      	$filters = [];
      	$filters[] = ['key' => 'employee_id', 'value' => $employeeId];
      	$filters[] = ['key' => 'start_time', 'operator' => '>=', 'value' => $startTime];
      	$filters[] = ['key' => 'end_time', 'operator' => '>=', 'value' => $endTime];

      	$summary = [];
      	$Shift = new Shift();
      	if($Shifts = $Shift->filter($startTime, $endTime, $filters)) {
      		foreach($Shifts as $Shift) {
      			$startTime = Carbon::createFromFormat('Y-m-d H:i:s', $Shift['start_time']);
      			$endTime = Carbon::createFromFormat('Y-m-d H:i:s', $Shift['end_time']);
      			$weekNumber = $startTime->weekOfYear;
      			$diff = $endTime->diff($startTime);

      			$timeWorked = $diff->day * 12 + $diff->hour + $diff->minute / 60 + $diff->second / 3600;
      			$timeWorked = $timeWorked - $Shift['break'];
      			$summary["Week #$weekNumber"] += $timeWorked;
      		}
      	}

      	return (new Payload)
            ->withStatus(Payload::ERROR)
            ->withOutput([
                'data' => json_encode($summary)
            ]);
    }
}
