<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OpenID_MyOpenID extends Auth_Driver_OpenID {

	public $name = 'openid.myopenid';

	protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);
		// Myopenid's OpenID identity is an unique URL, so we need to change it to original ID
		$result['service_name'] = $this->_openid->public_id();
		return $result;
	}

}