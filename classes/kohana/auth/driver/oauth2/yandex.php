<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Auth_Driver_OAuth2_Yandex extends Auth_Driver_OAuth2 {

	protected $_provider = 'yandex';

	/**
	 * @param   string  $user object (response from provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = simplexml_load_string($user);
		return array(
			'service_id'    => $user->name,
			'realname'      => $user->name,
			'service_name'  => 'oauth2.yandex',
			'email'         => $user->email,
		);
	}

	protected function _credential_params(OAuth2_Client $client, OAuth2_Token_Access $token)
	{
		return array(
			'oauth_token' => $token->token,
		);
	}

	protected function _url_verify_credentials()
	{
		return 'https://api-yaru.yandex.ru/me/';
	}

}