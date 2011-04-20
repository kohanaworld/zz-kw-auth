<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OpenID_Wordpress extends Auth_Driver_OpenID {

	public $name = 'openid.wordpress';

	/*protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);
		$username = $result['service_id'];

		// Wordpress returns username as http://username.wordpress.com
		if ($pos = strpos($username, '://'))
		{
			$username = substr($username, $pos + 3);
			$username = trim($username, '/');
			$username = current(explode('.', $username));
			$result['service_id'] = $username;
		}

		return $result;
	} */

}