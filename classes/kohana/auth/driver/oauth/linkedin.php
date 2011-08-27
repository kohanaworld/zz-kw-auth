<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth_LinkedIn extends Auth_Driver_OAuth {

	protected $_provider = 'linkedin';

	protected function _url_verify_credentials()
	{
		return 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name)';
	}

	/**
	 * @param   string  $user object (response from LinkedIn OAuth)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = (array)simplexml_load_string($user);

		$login = trim(Arr::get($user, 'first-name') . ' ' . Arr::get($user, 'last-name'));

		return array(
			'service_id'    => $login,
			'realname'      => $login,
			'service_name'  => 'oauth.linkedin',
			'email'         => NULL,
		);

	}

}
