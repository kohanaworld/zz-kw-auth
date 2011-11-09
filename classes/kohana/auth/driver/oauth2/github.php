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
			'service_id'    => $user->id,
			'service_name'  => $user->login,
			'realname'      => $user->name,
			'service_type'  => 'oauth2.github',
			'email'         => $user->email,
			// Github uses Gravatar for profile images
			'avatar'        => $user->gravatar_id,
		);
	}

	protected function _url_verify_credentials()
	{
		return 'https://github.com/api/v2/json/user/show';
	}



}