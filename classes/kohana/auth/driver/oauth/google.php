<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth_Google extends Auth_Driver_OAuth {

	protected $_provider = 'google';

	protected function _url_verify_credentials()
	{
		return 'http://www-opensocial.googleusercontent.com/api/people/@me/@self';
	}

	/**
	 * @param   string  $user object (response from OAuth provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = json_decode($user);
		$user = $user->entry;
		$account = explode('/', $user->profileUrl);
		$login = end($account);
		return array(
			'service_id'    => $login,
			'realname'      => $user->displayName,
			'service_name'  => 'google',
			'email'         => $login.'@gmail.com', // ?
		);

	}

}