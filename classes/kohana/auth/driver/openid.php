<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OpenID extends Auth_Driver {

	/**
	 * @var OpenID
	 */
	protected $_openid;
	protected $_identity_key = 'auth_openid_id';
	protected $_identity;

	protected function _get_identity($id = NULL)
	{
		// this can be changed in child classes
		if (empty($id))
		{
			throw new Kohana_Exception('OpenID identifier required');
		}
		return $id;
	}

	protected function _get_user_data($user)
	{
		return array(
			'service_id'    => $this->_identity,
			'service_name'  => $this->_identity,
			'realname'      => arr::get($user, 'namePerson/friendly', NULL),
			'service_type'  => $this->name,
			'email'         => arr::get($user, 'contact/email', NULL),
		);
	}

	public $name = 'openid';

	public function init()
	{
		$this->_openid = OpenID::factory();
	}

	public function login()
	{
		// some providers like Google dont need accountID
		$id = func_num_args() > 0 ? func_get_arg(0) : NULL;
		$this->_identity = $this->_get_identity($id);
		if ($user = $this->get_user())
		{
			Cookie::set($this->_identity_key, $this->_identity);// ?
			$this->complete_login();
		}

		return $user;
	}

	public function logout()
	{
		Cookie::delete($this->_identity_key);
	}

	public function get_user()
	{
		if ( ! $this->_identity )
		{
			return FALSE;
		}
		$user_data = $this->_get_user_data($this->_openid->attributes());
		return $this->_auth->orm()->get_user($user_data);
	}
}