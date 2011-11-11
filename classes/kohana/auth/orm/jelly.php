<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_ORM_Jelly extends Auth_ORM {

	protected function _load_user($data)
	{
		if ( ! is_array($data) )
		{
			$user = Jelly::factory('auth_data', $data);
		}
		else
		{
			$user = Jelly::query('auth_data')
				->where('service_id', '=', $data['service_id'])
				->where('service_type', '=', $data['service_type'])
				->limit(1)
				->execute();
			/*if ( ! $user->loaded() )
			{
				return FALSE;
			} */
			if ($user->loaded())
			{
				return $user;
			}
			// user not found
			/*return Jelly::factory('auth_data')
				->set('service_id', $data['service_id'])
				->set('service_name', $data['service_name']);*/
			return $this->_save_user($data);
		}

		return $user->loaded() ? $user : FALSE;
	}

	protected function _save_user(array $data)
	{
		$user = Jelly::factory('auth_data')
			->set('service_id', $data['service_id'])
			->set('service_type', $data['service_type'])
			->set('service_name', $data['service_name'])
			->set('email', $data['email'])
			->set('avatar', Arr::get($data, 'avatar'));
		$user->save();
		return $user;
	}

	protected function _load_token($token)
	{
		return Jelly::query('token')->where('token', '=', $token)->limit(1)->execute();
	}

	protected function _delete_token($token)
	{
		if ( ! is_object($token))
		{
			$token = $this->_get_token($token);
		}

		if ($token->loaded())
		{
			$token->delete();
		}
	}

	protected function _create_token($user, $driver,$lifetime)
	{
		$token = Jelly::factory('token');
		$token->generate($lifetime);
		$token->user = $user;
		$token->driver = $driver;
		$token->save();
		return $token;
	}

}