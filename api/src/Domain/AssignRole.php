<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use SampleProject\Domain\Database\Employee;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;
use SampleProject\Auth;

class AssignRole implements DomainInterface
{
    public function __invoke(array $input)
    {
    	$role = $input['role'];

        $Auth = new Auth();
        switch($role) {
            case 'employee':
                $Auth->flagAsEmployee();
                return SuccessPayload::create(['message' => 'Logged in as employee']);
                break;

            case 'manager':
                $Auth->flagAsManager();
                return SuccessPayload::create(['message' => 'Logged in as manager']);
                break;

            default:
                return ErrorPayload::create('Invalid role: must be employee or manager');
                break;
        }
    }
}
