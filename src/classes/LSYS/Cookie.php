<?php
/**
 * lsys Cookie
 *
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @copyright  (c) 2008-2012 kohana Team
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @license    http://kohanaframework.org/license
 */
namespace LSYS;
class Cookie {

	/**
	 * @var  string  Magic salt to add to the cookie
	 */
	public static $salt = NULL;

	/**
	 * @var  integer  Number of seconds before the cookie expires
	 */
	public static $expiration = 0;

	/**
	 * @var  string  Restrict the path that the cookie is available to
	 */
	public static $path = '/';

	/**
	 * @var  string  Restrict the domain that the cookie is available to
	 */
	public static $domain = NULL;

	/**
	 * @var  boolean  Only transmit cookies over secure connections
	 */
	public static $secure = FALSE;

	/**
	 * @var  boolean  Only transmit cookies over HTTP, disabling Javascript access
	 */
	public static $httponly = FALSE;

	/**
	 * Gets the value of a signed cookie. Cookies without signatures will not
	 * be returned. If the cookie signature is present, but invalid, the cookie
	 * will be deleted.
	 *
	 *     // Get the "theme" cookie, or use "blue" if the cookie does not exist
	 *     $theme = Cookie::get('theme', 'blue');
	 *
	 * @param   string  $key        cookie name
	 * @param   mixed   $default    default value to return
	 * @return  string
	 */
	public static function get(string $key, $default = NULL)
	{
		if ( ! isset($_COOKIE[$key]))
		{
			// The cookie does not exist
			return $default;
		}

		// Get the cookie value
		$cookie = $_COOKIE[$key];

		if (Cookie::$salt==null) return $cookie;
		
		// Find the position of the split between salt and contents
		$split = strlen(Cookie::salt($key, NULL));

		if (isset($cookie[$split]) AND $cookie[$split] === '~')
		{
			// Separate the salt and the value
			list ($hash, $value) = explode('~', $cookie, 2);

			if (Cookie::salt($key, $value) === $hash)
			{
				// Cookie signature is valid
				return $value;
			}

			// The cookie signature is invalid, delete it
			Cookie::delete($key);
		}
		if ($key==session_name()) return $cookie;//session name not salt 
		return $default;
	}

	/**
	 * Deletes a cookie by making the value NULL and expiring it.
	 *
	 *     Cookie::delete('theme');
	 *
	 * @param   string  $name   cookie name
	 * @return  boolean
	 */
	public static function delete($name,$path=null,$domain=null):bool
	{
		// Remove the cookie
		unset($_COOKIE[$name]);
		if ($path === NULL)
		{
			$path =Cookie::$path;
		}
		if ($domain=== NULL)
		{
			$domain=Cookie::$domain;
		}
		// Nullify the cookie and make it expire
		return (bool)@setcookie($name, NULL, -86400,$path, $domain);
	}
	
	/**
	 * Generates a salt string for a cookie based on the name and value.
	 *
	 *     $salt = Cookie::salt('theme', 'red');
	 *
	 * @param   string  $name   name of cookie
	 * @param   string  $value  value of cookie
	 * @return  string
	 */
	protected static function salt(string $name,?string $value):string
	{
		// Require a valid salt
		return substr(sha1($name.$value.Cookie::$salt),0,12);
	}
	/**
	 * Sets a signed cookie. Note that all cookie values must be strings and no
	 * automatic serialization will be performed!
	 *
	 *     // Set the "theme" cookie
	 *     Cookie::set('theme', 'red');
	 *
	 * @param   string  $name       name of cookie
	 * @param   string  $value      value of cookie
	 * @param   integer $expiration lifetime in seconds
	 * @return  boolean
	 * @uses    Cookie::salt
	 */
	public static function set(string $name, ?string $value, ?int $expiration = NULL,?string $path=NULL,?string $domain=NULL,?bool $secure=NULL,?bool $httponly=NULL):bool
	{
		if ($expiration === NULL)
		{
			// Use the default expiration
			$expiration = Cookie::$expiration;
		}
		if ($expiration !== 0)
		{
			// The expiration is expected to be a UNIX timestamp
			$expiration += time();
		}
		if ($path === NULL)
		{
			$path =Cookie::$path;
		}
		if ($domain=== NULL)
		{
			$domain=Cookie::$domain;
		}
		if ($secure === NULL)
		{
			$secure =Cookie::$secure;
		}
		if ($httponly === NULL)
		{
			$httponly =Cookie::$httponly;
		}
		// Add the salt to the cookie value
		if (Cookie::$salt!=null) $value = Cookie::salt($name, $value).'~'.$value;
	
		return (bool)@setcookie($name, $value, $expiration, $path,$domain, $secure, $httponly);
	}

}

