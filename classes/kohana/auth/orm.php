<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_ORM {

	abstract protected function _load_user($data);
	abstract protected function _save_user(array $data);

	/**
	 * @param  mixed  $data  user data (Array) or user ID (int)
	 * @return void
	 */
	public function get_user($data)
	{
		if ( ! is_array($data) )
		{
			// try to load user from DB
			return $this->_load_user($data);
		}
		else
		{
			$user = $this->_load_user($data);

			return $user;
		}
	}
}

