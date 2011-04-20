<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OpenID_Yahoo extends Auth_Driver_OpenID {

	public $name = 'openid.yahoo';

	protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);

		if ( ! empty($result['email']))
		{
			// Yahoo returns contact/email field - get username from its
			$result['service_id']   = current(explode('@', $result['email']));
		}

		return $result;
	}
}