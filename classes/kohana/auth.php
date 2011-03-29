<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Auth {

	protected static $_instance;

	public static $default_orm = 'jelly';

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

	protected $_user_key = 'auth_user';
	protected $_cache = FALSE;
	protected $_driver_key = 'auth_driver';

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
		$session = arr::get($config, 'session');
		$this->_session = Session::instance($session);
	}

	public function get_user()
	{
		$driver = $this->_session->get($this->_driver_key);
		if ( ! $driver )
		{
			return FALSE;
		}

		if ($this->_cache === TRUE)
		{
			if ( $user = $this->_session->get($this->_user_key))
			{
				return $user;
			}
		}

		return $this->driver($driver)->get_user();
	}

	/**
	 * @return  Boolean
	 */
	public function login()
	{
		if (func_num_args() < 2)
		{
			throw new Auth_Exception('Minimum two params required to log in');
		}

		$params = func_get_args();
		$driver_name = array_shift($params);
		$driver = $this->driver($driver_name);
		if (call_user_func_array(array($driver, 'login'), $params))
		{
			$this->_session->set($this->_driver_key, $driver_name);
			//$this->_session->set($this->_user_key, $result);
			return TRUE;
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
		$this->_session->delete($this->_user_key)->delete($this->_driver_key);
	}

	/**
	 * @param  String  Driver type
	 * @return Auth_Driver
	 */
	public function driver($name)
	{
		if ( ! isset($this->_drivers[$name]))
		{
			// OAuth.Google will be a OAuth_Google driver
			$name = str_replace('.', '_', $name);
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