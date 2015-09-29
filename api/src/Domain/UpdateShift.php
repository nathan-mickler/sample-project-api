<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use Carbon\Carbon;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;

class UpdateShift implements DomainInterface
{
    public function __invoke(array $input)
    {
    	// Validate the startTime and endTime variables
        if (empty($input['startTime']) || empty($input['endTime'])) {
            return InvalidPayload::create('Missing required inputs: startTime and endTime are required');
        }
        else {
            try{
                $startTime = Carbon::createFromFormat('Y-m-d H:i:s', urldecode($input['startTime']));
                $endTime = Carbon::createFromFormat('Y-m-d H:i:s', urldecode($input['endTime']));
            }
            catch(\Exception $e) {
                return InvalidPayload::create('Invalid date format: startTime and endTime must be in "Y-m-d H:i:s" format');
            }
        	
        }

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

        // Load shift
        $shiftId = $input['shiftId'];
		$Shift = new Shift();

		if(!$Shift->load($shiftId)) {
            return ErrorPayload::create('Shift not found');
        }

		$update_array = [
			'start_time' => $startTime->format("Y-m-d H:i:s"),
			'end_time' => $endTime->format("Y-m-d H:i:s")
		];

		try {
			$Shift->update($update_array);

            return SuccessPayload::create(['message' => 'Shift updated successfully']);
		}
		catch(\Exception $e) {
            return ErrorPayload::create('There was an error updating this shift');
		}
    }
}
