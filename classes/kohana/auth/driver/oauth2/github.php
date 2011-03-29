<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Auth_Driver_OAuth2_Github extends Auth_Driver_OAuth2 {

	protected $_provider = 'github';

	/**
	 * @param   stdClass  $user object
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = $user->user;
		return array(
			'service_id'    => $user->login,
			'realname'      => $user->name,
			'service_name'  => 'github',
			'email'         => $user->email,
		);

	}

}