<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth_Twitter extends Auth_Driver_OAuth {

	protected $_provider = 'twitter';

	protected function _url_verify_credentials()
	{
		return 'http://api.twitter.com/1/account/verify_credentials.json';
	}


	/**
	 * @param   string  $user object (response from Twitter OAuth)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = json_decode($user);

		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->screen_name,
			'realname'      => $user->name,
			'service_type'  => 'oauth.twitter',
			'email'         => NULL, // ?
			'avatar'        => $user->profile_image_url,
		);

	}

}
