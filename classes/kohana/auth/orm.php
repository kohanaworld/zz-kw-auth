<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_ORM {

	abstract protected function _load_user($data);
	abstract protected function _save_user(array $data);
	abstract protected function _load_token($token);
	abstract protected function _delete_token($token);
	abstract protected function _create_token($user, $driver, $lifetime);

	/**
	 * @param  mixed  $data  user data (Array) or user ID (int)
	 * @return void
	 */
	public function get_user($data)
	{
		return $this->_load_user($data);

		/*if ( is_array($data) )
		{
			// try to load user from DB
			return $this->_load_user($data);
		}
		else
		{
			$user = $this->_load_user($data);
			return $user;
		} */
	}

	public function get_token($token)
	{
		$token = $this->_load_token($token);
		if ($token->is_valid())
		{
			return $token;
		}
		else
		{
			$this->_delete_token($token);
			return FALSE;
		}
	}

	public function generate_token($user, $driver, $lifetime = 1209600)
	{
		return $this->_create_token($user, $driver, $lifetime);
	}
}

