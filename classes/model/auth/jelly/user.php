<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Auth_Jelly_User extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('users')
			->fields(array(
				'id'         => Jelly::field('primary'),
				'username'   => Jelly::field('string'),
				'email'      => Jelly::field('email'),
				'auth_data'  => Jelly::field('BelongsTo', array(
					//'foreign'   => 'auth_data',
					'column'     => 'auth_id',
				)),
			))
			->foreign_key('user');
	}


}