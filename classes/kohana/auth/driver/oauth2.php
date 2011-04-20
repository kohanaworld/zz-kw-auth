<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth2 extends Auth_Driver {
	/**
	 * @var OAuth2
	 */
	protected $_oauth;
	/*
	 * @var OAuth_Client
	 */
	protected $_consumer;
	/**
	 * @var OAuth2_Provider
	 */
	protected $_provider;
	/**
	 * @var OAuth2_Token_Access
	 */
	protected $_token;
	protected $_token_key = 'auth_oauth2_token';

	abstract protected function _get_user_data($user);
	abstract protected function _url_verify_credentials();

	protected function _verify_credentials(OAuth2_Token_Access $token, OAuth2_Client $client)
	{
		$request = OAuth2_Request::factory('credentials', 'GET', $this->_url_verify_credentials(), array(
			'oauth_consumer_key' => $client->id,
			'oauth_token' => $token->token,
		));

		$response = $request->execute();
		return $this->_get_user_data($response);
	}

	public $name = 'oauth2';

	public function init()
	{
		$this->_oauth = new OAuth2;
		$this->_consumer = new OAuth2_Client(Kohana::config('oauth.'.$this->_provider));
		$this->_provider = $this->_oauth->provider($this->_provider);
		if ($token = Cookie::get($this->_token_key))
		{
			$this->_token = unserialize($token);
		}
	}

	/**
	 * @param  OAuth2_Token_Access $token
	 * @param  Boolean             $remember
	 * @return FALSE|object
	 */
	public function login()
	{
		$this->_token = func_get_arg(0);
		if ($user = $this->get_user($this->_token))
		{
			Cookie::set($this->_token_key, serialize($this->_token));
			// successfully logged in
			$this->complete_login();
		}
		return $user;
	}

	public function force_login($id)
	{

	}

	public function logout()
	{
		Cookie::delete($this->_token_key);
	}

	/**
	 * @param   OAuth2_Token_Access  $token
	 * @return  Model_Auth_Data
	 */
	public function get_user()
	{
		if ( ! $this->_token )
		{
			return FALSE;
		}
		// get user info from OAuth provider
		$user = $this->_verify_credentials($this->_token, $this->_consumer);
		return $this->_auth->orm()->get_user($user);
	}

}