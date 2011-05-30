<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth2_Facebook extends Auth_Driver_OAuth2 {

	protected $_provider = 'facebook';

	/**
	 * @param   string  $user object (response from provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = json_decode($user);

		return array(
			'service_id'    => $user->name,
			'realname'      => $user->name,
			'service_name'  => 'oauth2.facebook',
			'email'         => $user->email,
		);
	}

	protected function _url_verify_credentials()
	{
		return 'https://graph.facebook.com/me';
	}

}
