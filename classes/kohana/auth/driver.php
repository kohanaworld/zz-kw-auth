<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver {

	protected $_provider = FALSE;
	/**
	 * @var Auth
	 */
	protected $_auth;

	abstract public function login();
	abstract public function logout();
	abstract public function get_user();

	public function __construct(Auth $auth)
	{
		$this->_auth = $auth;
	}

	public function provider($provider)
	{
		if (func_num_args() == 0)
		{
			return $this->_provider;
		}
		$this->_provider = $provider;
		return $this;
	}

	public function init() {}

	public function complete_login() {}

}