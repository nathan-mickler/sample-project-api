<?php

namespace SampleProject\Payload;

use Spark\Payload;

class AbstractPayload
{
		public static function create($status, $output) {
			return (new Payload)
						->withStatus($status)
						->withOutput([
							'data' => json_encode($output)
						]);
		}
}
