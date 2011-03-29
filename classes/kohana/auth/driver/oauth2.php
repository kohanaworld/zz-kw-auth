<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth2 extends Auth_Driver {
	/**
	 * @var OAuth_v2
	 */
	protected $_oauth;
	/**
	 * @var OAuth_v2_Provider
	 */
	protected $_provider;
	/**
	 * @var OAuth_v2_Token_Access
	 */
	protected $_token;
	protected $_token_key = 'auth_oauth2_token';

	abstract protected function _get_user_data($user);


	public $name = 'oauth2';

	public function init()
	{
		$this->_oauth = OAuth::v2();
		$this->_provider = $this->_oauth->provider($this->_provider);
		if ($token = Cookie::get($this->_token_key))
		{
			$this->_token = unserialize($token);
		}
	}

	/**
	 * @param  OAuth_v2_Token_Access $token
	 * @param  Boolean               $remember
	 * @return void
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

	public function logout()
	{
		Cookie::delete($this->_token_key);
	}

	/**
	 * @param   OAuth_v2_Token_Access  $token
	 * @return  Model_User
	 */
	public function get_user()
	{
		if ( ! $this->_token )
		{
			return FALSE;
		}
		// get user info from OAuth service
		$user = $this->_provider->resource_account()->get_userinfo($this->_token, NULL);
		$user_data = $this->_get_user_data($user);
		return $this->_auth->orm()->get_user($user_data);
	}
}