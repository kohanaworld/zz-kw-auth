<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth_Google extends Auth_Driver_OAuth {

	protected $_provider = 'google';

	/**
	 * @param   stdClass  $user object
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = $user->entry;
		$account = explode('/', $user->profileUrl);
		$login = end($account);
		return array(
			'service_id'    => $login,
			'realname'      => $user->displayName,
			'service_name'  => 'github',
			'email'         => $login.'@gmail.com', // ?
		);

	}

}