<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth {

	protected static $_instance;

	public static $default_orm = 'ORM';

	/**
	 * @static
	 * @param   string  Instance name (driver)
	 * @return  Auth
	 */
	public static function instance()
	{
		if ( empty(Auth::$_instance))
		{
			$config = Kohana::config('auth');
			Auth::$_instance = new Auth($config);
		}

		return Auth::$_instance;
	}

	/**
	 * @var  Config
	 */
	protected $_config;
	/**
	 * @var  Session
	 */
	protected $_session;

	protected $_user_key      = 'auth_user';
	protected $_driver_key    = 'auth_driver';
	protected $_autologin_key = 'auth_auto_login';
	protected $_forced_key    = 'auth_forced';

	/**
	 * @var  Auth_Driver[]  Auth driver collection
	 */
	protected $_drivers = array();
	/**
	 * @var Auth_ORM
	 */
	protected $_orm;

	protected function __construct($config = NULL)
	{
		$this->_config = $config;
		$session = Arr::get($config, 'session');
		$this->_session = Session::instance($session);
	}

	/**
	 * @param bool  reload user data from DB
	 * @return  FALSE|Model_User
	 */
	public function get_user($refresh = FALSE)
	{
		$authdata = $this->get_authdata($refresh);
		if ( ! $authdata)
		{
			return FALSE;
		}

		return $authdata->user;		
	}

	/**
	 * @param bool  reload user data from DB
	 * @return  FALSE|Model_Auth_Data
	 */
	public function get_authdata($refresh = FALSE)
	{
		$driver = $this->_session->get($this->_driver_key);
		if ( ! $driver AND $this->_session->get($this->_forced_key) !== TRUE )
		{
			return FALSE;
		}

		if ($user = $this->_session->get($this->_user_key))
		{
			if ($refresh)
			{
				$user = $this->orm()->get_user($user->id());
				$this->_session->set($this->_user_key, $user);
			}
			return $user;
		}

		return $this->driver($driver)->get_user();
	}

	/**
	 * This method can use different param types and count depends on driver.
	 *
	 *      // try to log in via OAuth v2 as Github user (access token required)
	 *      Auth::instance()->login('oauth2.github', $token, TRUE);
	 *
	 * @return  Boolean
	 */
	public function login()
	{
		if (func_num_args() < 2)
		{
			throw new Auth_Exception('Minimum two params required to log in');
		}

		// automatically logout
		$this->logout();

		$params = func_get_args();
		$driver_name = array_shift($params);
		$driver = $this->driver($driver_name);
		if ($user = call_user_func_array(array($driver, 'login'), $params))
		{
			$this->_complete_login($user, $driver_name);
			// check for autologin option
			$remember = (bool)arr::get($params, 1, FALSE);
			if ($remember)
			{
				$token = $this->orm()->generate_token($user, $this->_config['lifetime']);
				Cookie::set($this->_autologin_key, $token);
			}
			return TRUE;
		}

		return FALSE;
	}

	protected function _complete_login($user, $driver = NULL)
	{
		$this->_session->set($this->_driver_key, $driver);
		$this->_session->set($this->_user_key, $user);
	}

	/**
	 *
	 *      Auth::instance()->force_login($user_id);
	 *
	 * @return void
	 */
	public function force_login($user, $mark_as_forced = TRUE)
	{
		$user = $this->orm()->get_user($user);
		if ( ! $user )
		{
			return FALSE;
		}

		$this->_complete_login($user, NULL);
		//$this->_session->set($this->_driver_key, NULL);
		//$this->_session->set($this->_user_key, $user);

		if ($mark_as_forced)
		{
			$this->_session->set($this->_forced_key, TRUE);
		}

		return TRUE;
	}

	public function auto_login()
	{
		if ( ! $token = Cookie::get($this->_autologin_key))
		{
			return FALSE;
		}

		$token = $this->orm()->get_token($token);
		if ($token AND $token->is_valid())
		{
			// its a valid token
			$this->_session->set($this->_driver_key, $token->driver);
			$this->_session->set($this->_user_key, $token->user);
			$token->generate($this->_config['lifetime']);
			Cookie::set($this->_autologin_key, $token->token);
			return $token->user;
		}

		return FALSE;
	}

	public function logout()
	{
		if ( ! $driver = $this->_session->get($this->_driver_key))
		{
			return TRUE;
		}

		$this->driver($driver)->logout();
		$this->_session
			->delete($this->_user_key)
			->delete($this->_driver_key)
			->delete($this->_forced_key);
		Cookie::delete($this->_autologin_key);
	}

	/**
	 * @param  String  Driver type
	 * @return Auth_Driver
	 */
	public function driver($name = NULL)
	{
		if ($name === NULL AND ! $name = $this->_session->get($this->_driver_key))
		{
			throw new Auth_Exception('Auth driver name required');
		}
		// OAuth.Google will be a OAuth_Google driver
		$name = str_replace('.', '_', $name);
		if ( ! isset($this->_drivers[$name]))
		{
			$class = 'Auth_Driver_'.$name;
			$driver = new $class($this);
			$driver->init();
			$this->_drivers[$name] = $driver;
		}

		return $this->_drivers[$name];
	}

	/**
	 * @return Auth_ORM
	 */
	public function orm()
	{
		if ( ! $this->_orm)
		{
			$type = Arr::get($this->_config, 'orm', Auth::$default_orm);
			$class = 'Auth_ORM_'.$type;
			$this->_orm = new $class;
		}

		return $this->_orm;
	}
}