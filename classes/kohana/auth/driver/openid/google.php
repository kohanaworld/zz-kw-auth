<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OpenID_Google extends Auth_Driver_OpenID {

	public $name = 'openid.google';

	protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);

		// Google returns contact/email field only
		$result['service_name']   = current(explode('@', $result['email']));
		if (empty($result['realname']))
		{
			$result['realname'] = trim(arr::get($result, 'namePerson/first').' '.arr::get($result, 'namePerson/last'));
		}
		return $result;
	}


	public function init()
	{
		parent::init();

		// Google ignores optional parameters
		$required = $this->_openid->required() + $this->_openid->optional() + array(
			'namePerson/first',
			'namePerson/last'
		);

		$this->_openid->required($required);
	}

}