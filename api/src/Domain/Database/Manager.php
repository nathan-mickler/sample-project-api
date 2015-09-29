<?php

namespace SampleProject\Domain\Database;

use SampleProject\Domain\Database\UserGateway;

class Manager extends User
{
		public function load($employee_id) {
			if(parent::load($employee_id)) {
				if($this->role == 'manager') {
					return $this;
				}
			}

			return null;
		}

		public function create(Array $input) {
			$Manager = new Manager();
			$input = array_merge($input, ['role' => 'manager']);

			if($UserGateway = UserGateway::create($input)) {
				$Manager->data = $UserGateway;
				return $Manager;
			}

			return null;
		}

		public function update(Array $input) {
			$input = array_merge($input, ['role' => 'manager']);
			return parent::update($input);
		}

		public function shifts($startTime = null, $endTime = null, $withEmployee = false) {
			$shifts = [];
			if($managerId = $this->id) {
				$Shifts = ShiftGateway::where('manager_id', $managerId);
				if($startTime) {
					$Shifts = $Shifts->where('start_time', '>=', $startTime);
				}
				if($endTime) {
					$Shifts = $Shifts->where('end_time', '<=', $endTime);
				}

				if($withEmployee) {
					$Shifts = $Shifts->with('employee');
				}
				
				if($Shifts = $Shifts->get()) {
					foreach($Shifts as $Shift) {
						$newShift = [
								'id' => $Shift->id,
								'break' => $Shift->break,
								'startTime' => $Shift->start_time,
								'endTime' => $Shift->end_time
						];

						if($withEmployee) {
							$newShift['employee'] = $Shift->employee->name;
							$newShift['employeePhone'] = $Shift->employee->phone;
							$newShift['employeeEmail'] = $Shift->employee->email;
						}

						$shifts[] = $newShift;
					}
				}
			}

			return $shifts;
		}
}
