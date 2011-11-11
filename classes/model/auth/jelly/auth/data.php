<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_Jelly_Auth_Data extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('auth_data')
			->fields(array(
				'id'           => Jelly::field('primary'),
				'service_id'   => Jelly::field('string'),
				'service_name' => Jelly::field('string'),
				'service_type' => Jelly::field('string'),
				'email'        => Jelly::field('email'),
				'avatar'       => Jelly::field('string'),
				'is_active'    => Jelly::field('boolean', array(
					'default'     => TRUE,
				)),
				'user'         => Jelly::field('BelongsTo'),
			));
	}

}