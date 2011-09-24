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
		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->displayName,
			'realname'      => $user->displayName,
			'service_type'  => 'oauth.google',
			'email'         => isset($user->email) ? $user->email : NULL, // may be empty
		);

	}

}