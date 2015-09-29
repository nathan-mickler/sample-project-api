<?php

namespace SampleProject\Domain\Database;

use SampleProject\Domain\Database\ShiftGateway;
use SampleProject\Domain\Database\User;
use Carbon\Carbon;

class Shift
{
		private $data;

		public function load($shift_id = false) {
			if($shift_id) {
				if($ShiftGateway = ShiftGateway::find($shift_id)) {
					$this->data = $ShiftGateway;
					return $this;
				}
			}

			return $this;
		}

		public function __get($key) {
			if($this->data) {
				return $this->data->{$key};
			}

			return null;
		}

		public static function create(Array $input) {
			$Shift = new Shift();
			if($ShiftGateway = ShiftGateway::create($input)) {
				$Shift->data = $ShiftGateway;
				return $Shift;
			}

			return null;
		}

		public function update(Array $input) {
			if($this->data) {
				$input = array_merge($this->data->toArray(), $input);
				$this->data->update($input);
				return $this;
			}

			return null;
		}

		public function delete() {
			if($this->data) {
				$this->data->delete();
				return true;
			}
			
			return false;
		}

		public function assign(User $User) {
			switch($User->role) {
				case 'manager';
					$this->update(['manager_id' => $User->id]);
					break;

				case 'employee';
					$this->update(['employee_id' => $User->id]);
					break;
			}
		}

		// find all shifts within or overlapping start/end times
		public function filter($startTime, $endTime, $otherFilters = []) {
			$shifts = [];
				$Shifts = ShiftGateway::where('end_time', '>=', $startTime)
						->where('start_time', '<=', $endTime)
						->with('manager')
						->with('employee');

				// filter by additionally passed filters
				if(count($otherFilters)) {
					foreach($otherFilters as $filter) {
						$key = $filter['key'];
						$operator = empty($filter['operator']) ? '=' : $filter['operator'];
						$value = $filter['value'];
						$Shifts = $Shifts->where($key, $operator, $value);
					}
				}

				if($Shifts = $Shifts->orderBy('start_time', 'asc')
						->orderBy('end_time', 'asc')
						->get()) {

					foreach($Shifts as $Shift) {
						$shifts[] = [
								'id' => $Shift->id,
								'break' => $Shift->break,
								'startTime' => $Shift->start_time,
								'endTime' => $Shift->end_time,
								'managerId' => @$Shift->manager->id,
								'manager' => @$Shift->manager->name,
								'managerPhone' => @$Shift->manager->phone,
								'managerEmail' => @$Shift->manager->email,
								'employeeId' => @$Shift->employee->id,
								'employee' => @$Shift->employee->name,
								'employeePhone' => @$Shift->employee->phone,
								'employeeEmail' => @$Shift->employee->email
							];
					}
				}

			return $shifts;
		}

		// generate a list of all co-workers with shifts intersecting this shift
		public function listCoworkers() {
			if($this->data) {
				$data = $this->data;
				$Shifts = ShiftGateway::where('employee_id', '<>', $this->employee_id)
											->whereNotNull('employee_id');

				$Shifts = $Shifts->where(function($query) use($data) {
					// search for shift's start_time inside of another shift
					$query->where(function($query) use($data) {
						$query->where('start_time', '<=', $data->start_time)
								->where('end_time', '>=', $data->start_time);
					// search for shift's end_time inside of another shift
					})->orWhere(function($query) use($data) {
						$query->where('start_time', '<=', $data->end_time)
								->where('end_time', '>=', $data->end_time);
					// search for shifts that are fully contained within this shift's time range
					})->orWhere(function($query) use($data) {
						$query->where('start_time', '>', $data->start_time)
								->where('end_time', '<', $data->end_time);
					});
				});

				$coworkers = [];
				if($Shifts = $Shifts->with('employee')->get()) {
					foreach($Shifts as $Shift) {
						$coworkers[$Shift->employee->name] = $Shift->employee->name;
					}
				}

				$coworkers = array_values($coworkers);
				sort($coworkers);

				return [
					'id' => $this->id,
					'break' => $this->break,
					'startTime' => $this->start_time,
					'endTime' => $this->end_time,
					'coworkers' => $coworkers
				];
			}
		}
}
