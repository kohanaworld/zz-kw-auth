<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_ORM_Jelly extends Auth_ORM {

	protected function _load_user($data)
	{
		if ( ! is_array($data) )
		{
			$user = Jelly::factory('user', $data);
		}
		else
		{
			$user = Jelly::query('auth_data')
				->where('service_id', '=', $data['service_id'])
				->where('service_name', '=', $data['service_name'])
				->limit(1)
				->execute();

			if ($user->loaded())
			{
				return $user->user;
			}
			// user not found
			return Jelly::factory('auth_data')
				->set('service_id', $data['service_id'])
				->set('service_name', $data['service_name']);
		}

		return $user->loaded() ? $user : FALSE;
	}

	protected function _save_user(array $data)
	{
		$user = Jelly::factory('user')
			->set('username', $data['service_id'])
			->set('email', $data['email']);
		$user->save();
		$auth_data = Jelly::factory('auth_data')->set($data);
		$auth_data->user = $user;
		$auth_data->save();
		return $user;
	}

}