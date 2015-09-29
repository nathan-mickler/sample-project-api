<?php

namespace SampleProject\Payload;

use Spark\Payload;
use SampleProject\Payload\AbstractPayload;

class InvalidPayload
{
		public static function create($output) {
			return AbstractPayload::create(Payload::INVALID, ['error' => $output]);
		}
}
