<?php

namespace SampleProject\Payload;

use Spark\Payload;
use SampleProject\Payload\AbstractPayload;

class SuccessPayload
{
		public static function create(Array $output) {
			return AbstractPayload::create(Payload::OK, $output);
		}
}
