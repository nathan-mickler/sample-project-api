<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;

class ListShiftsByDate implements DomainInterface
{
    public function __invoke(array $input)
    {
        $startTime = urldecode($input['startTime']);
        $endTime = urldecode($input['endTime']);
        
        $Shift = new Shift();
        $shifts = $Shift->filter($startTime, $endTime);

        if(count($shifts)) {
            return SuccessPayload::create($shifts);
        }
        else {
            return ErrorPayload::create('No shifts found');
        }
    }
}
