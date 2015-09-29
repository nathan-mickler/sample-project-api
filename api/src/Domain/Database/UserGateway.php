<?php

namespace SampleProject\Domain\Database;

use Illuminate\Database\Eloquent\Model;

class UserGateway extends Model
{
		protected $guarded = [];
		protected $table = 'users';
}
