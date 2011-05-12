<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_Jelly_Token extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('user_tokens')
			->fields(array(
				'id'          => Jelly::field('primary'),
				'token'       => Jelly::field('string'),
				'driver'      => Jelly::field('string'),
				'user_agent'  => Jelly::field('string'),
				'expires'     => Jelly::field('timestamp'),
				'user'        => Jelly::field('belongsTo', array(
					'foreign'      => 'auth_data',
				)),
			));
	}

	public function is_valid()
	{
		return $this->saved() AND $this->expires > time() AND $this->user_agent == sha1(Request::$user_agent);
	}

	/*public function refresh($lifetime)
	{
		$this->expires = time() + $lifetime;
		$this->token =$this->_generate_token();
		$this->save();
	} */

	protected function _generate_token()
	{
		do
		{
			$token = sha1(uniqid(Text::random('alnum', 32), TRUE));
		}
		while(Jelly::query('token')->where('token', '=', $token)->limit(1)->execute()->loaded());
		//while(ORM::factory('user_token', array('token' => $token))->loaded());

		return $token;
	}

	public function generate($lifetime)
	{
		$this->expires = time() + $lifetime;
		$this->token = $this->_generate_token();
		if ( ! $this->user_agent )
		{
			// this is a new token, so we dont need to save it (yet)
			$this->user_agent = sha1(Request::$user_agent);
		}
		else {
			// save new token value & timestamp
			$this->save();
		}
	}

}