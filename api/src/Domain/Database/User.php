<?php

namespace SampleProject\Domain\Database;

use SampleProject\Domain\Database\UserGateway;

abstract class User
{
		protected $data;

		public abstract function shifts($start_time = null, $end_time = null);
		public abstract function create(Array $input);

		public function load($user_id = false) {
			if($user_id) {
				if($UserGateway = UserGateway::find($user_id)) {
					$this->data = $UserGateway;
					return true;
				}
			}

			$this->data = null;
			return false;
		}

		public function __get($key) {
			if($this->data) {
				return $this->data->{$key};
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
}
