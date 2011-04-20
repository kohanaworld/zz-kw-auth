<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth extends Auth_Driver {

	/**
	 * @var OAuth_v1
	 */
	//protected $_oauth;
	/**
	 * @var OAuth_v1_Provider
	 */
	protected $_provider;
	/**
	 * @var OAuth_Consumer
	 */
	protected $_consumer;
	/**
	 * @var Auth_v1_Token_Access
	 */
	protected $_token;
	protected $_token_key = 'auth_oauth_token';

	abstract protected function _get_user_data($user);
	abstract protected function _url_verify_credentials();

	protected function _verify_credentials(OAuth_Token $token, OAuth_Consumer $consumer)
	{
		$request = OAuth_Request::factory('credentials', 'GET', $this->_url_verify_credentials(), array(
			'oauth_consumer_key' => $consumer->key,
			'oauth_token' => $token->token,
		));

		$response = $request->sign($this->_provider->signature, $consumer, $token)->execute();
		return $this->_get_user_data($response);
	}

	public $name = 'oauth';

	public function init()
	{
		$this->_consumer = OAuth_Consumer::factory(Kohana::config('oauth.'.$this->_provider));
		$this->_provider = OAuth_Provider::factory($this->_provider);
		if ($token = Cookie::get($this->_token_key))
		{
			$this->_token = unserialize($token);
		}
	}

	public function login()
	{
		$this->_token = func_get_arg(0);
		if ($user = $this->get_user())
		{
			Cookie::set($this->_token_key, serialize($this->_token));
			// successfully logged in
			$this->complete_login();
		}
		return $user;
	}

	public function logout()
	{
		Cookie::delete($this->_token_key);
	}

	public function get_user()
	{
		if ( ! $this->_token )
		{
			return FALSE;
		}
		// get user info from OAuth service
		$user = $this->_verify_credentials($this->_token, $this->_consumer);
		return $this->_auth->orm()->get_user($user);
	}


}