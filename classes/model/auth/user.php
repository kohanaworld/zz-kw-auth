<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_User extends Model_Auth_Jelly_User {

	public function get_avatar($size = NULL)
	{
		if ( ! $this->loaded() )
		{
			return FALSE;
		}

		$avatar = $this->auth_data->avatar;
		if (empty($avatar) AND ! empty($this->email) )
		{
			$avatar = md5($this->email);
		}

		if (strpos($avatar, '://') == FALSE)
		{
			// its a Gravatar ID
			$avatar = 'http://gravatar.com/avatar/' . $avatar;
			$params = array();
			if (empty($avatar))
			{
				// use default Gravatar
				$params['f'] = 'y';
			}

			if ($size)
			{
				$params['s'] = intval($size);
			}

			if ( ! empty($params) )
			{
				$avatar .= http_build_query($params);
			}
		}

		return $avatar;
	}
}