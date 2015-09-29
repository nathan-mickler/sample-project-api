<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Manager;
use SampleProject\Domain\Database\Employee;
use Carbon\Carbon;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;
use SampleProject\Auth;

class ListShifts implements DomainInterface
{
    public function __invoke(array $input)
    {
        $userId = $input['userId'];
        $startTime = null;
        $endTime = null;
        $withContactDetails = false;

        // make sure that employees are not accessing information for another employee
        if(Auth::isEmployee() && Auth::getId() != $userId) {
            return InvalidPayload::create('You can only access your own shifts');
        }

        if (!empty($input['startTime'])) {
            $startTime = urldecode($input['startTime']);
        }

        if (!empty($input['endTime'])) {
            $endTime = urldecode($input['endTime']);
        }

		$Employee = new Employee();
		$Manager = new Manager();

		// Attempt to load employee
		if($Employee->load($userId)) {
            $User = $Employee;
			$withContactDetails = isset($_GET['manager']) ? true : false;
		}
		// Attempt to load manager
		elseif($Manager->load($userId)) {
			$User = $Manager;
			$withContactDetails = isset($_GET['employee']) ? true : false;
		}
		// User was not an employee or manager, respond with error message
		else {
            return ErrorPayload::create('User not found');
		}

        $shifts = $User->shifts($startTime, $endTime, $withContactDetails);
        if(count($shifts)) {
            return SuccessPayload::create($shifts);
        }
        else {
            return ErrorPayload::create('No shifts found');
        }
    }
}
