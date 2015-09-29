<?php

namespace SampleProject\Domain\Database;

use Illuminate\Database\Eloquent\Model;

class ShiftGateway extends Model
{
		protected $guarded = [];
		protected $table = 'shifts';

		public function employee()
    {
        return $this->belongsTo('SampleProject\Domain\Database\UserGateway', 'employee_id', 'id');
    }

		public function manager()
    {
        return $this->belongsTo('SampleProject\Domain\Database\UserGateway', 'manager_id', 'id');
    }
}
