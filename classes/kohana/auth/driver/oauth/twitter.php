<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth_Driver_OAuth_Twitter extends Auth_Driver_OAuth {

	protected $_provider = 'twitter';

	/**
	 * @param   stdClass  $user object
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		return array(
			'service_id'    => $user->screen_name,
			'realname'      => $user->name,
			'service_name'  => 'twitter',
			'email'         => NULL, // ?
		);

	}

}
