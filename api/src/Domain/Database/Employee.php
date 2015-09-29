<?php

namespace SampleProject\Domain\Database;

use SampleProject\Domain\Database\UserGateway;
use SampleProject\Domain\Database\ShiftGateway;

class Employee extends User
{
		public function load($employee_id) {
			if(parent::load($employee_id)) {
				if($this->role == 'employee') {
					return $this;
				}
			}

			return null;
		}

		public function create(Array $input) {
			$Employee = new Employee();
			$input = array_merge($input, ['role' => 'employee']);

			if($UserGateway = UserGateway::create($input)) {
				$Employee->data = $UserGateway;
				return $Employee;
			}

			return null;
		}

		public function update(Array $input) {
			$input = array_merge($input, ['role' => 'employee']);
			return parent::update($input);
		}

		public function shifts($startTime = null, $endTime = null, $withManager = false) {
			if($employeeId = $this->id) {
				$Shifts = ShiftGateway::where('employee_id', $employeeId);
				if($startTime) {
					$Shifts = $Shifts->where('start_time', '>=', $startTime);
				}
				if($endTime) {
					$Shifts = $Shifts->where('end_time', '<=', $endTime);
				}
				
				if($withManager) {
					$Shifts = $Shifts->with('manager');
				}

				if($Shifts = $Shifts->get()) {
					foreach($Shifts as $Shift) {
						$newShift = [
								'id' => $Shift->id,
								'break' => $Shift->break,
								'startTime' => $Shift->start_time,
								'endTime' => $Shift->end_time
						];

						if($withManager) {
							$newShift['manager'] = $Shift->manager->name;
							$newShift['managerPhone'] = $Shift->manager->phone;
							$newShift['managerEmail'] = $Shift->manager->email;
						}

						$shifts[] = $newShift;
					}
				}
			}

			return $shifts;
		}
	}
