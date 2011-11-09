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
		$avatar = NULL;

		foreach($user->link as $link)
		{
			if ($link->rel = 'userpic')
			{
				$avatar = $link->href;
			}
		}
		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->name,
			'realname'      => $user->name,
			'service_type'  => 'oauth2.yandex',
			'email'         => $user->email,
			'avatar'        => $avatar,
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
