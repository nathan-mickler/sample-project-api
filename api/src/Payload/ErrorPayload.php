<?php

namespace SampleProject\Payload;

use Spark\Payload;
use SampleProject\Payload\AbstractPayload;

class ErrorPayload
{
		public static function create($output) {
			return AbstractPayload::create(Payload::ERROR, ['error' => $output]);
		}
}
