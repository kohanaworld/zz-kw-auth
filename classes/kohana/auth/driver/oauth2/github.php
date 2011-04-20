<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Auth_Driver_OAuth2_Github extends Auth_Driver_OAuth2 {

	protected $_provider = 'github';

	/**
	 * @param   string  $user object (response from provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = json_decode($user);
		$user = $user->user;
		return array(
			'service_id'    => $user->login,
			'realname'      => $user->name,
			'service_name'  => 'github',
			'email'         => $user->email,
		);
	}

	protected function _url_verify_credentials()
	{
		return 'https://github.com/api/v2/json/user/show';
	}



}