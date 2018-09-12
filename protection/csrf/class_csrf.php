<?php
/**
* Cross Site Request Forgery (CSRF) Class
* @Author Matt Kent (Matt_Kent9)
* @License MIT
*
* session_start(); must be called before this is utilised.
*/
class Token
{
	// Empty constructor to avoid "Constructor cannot be static" error.
	public function __construct() {}
	
	// Used for is_recent() method.
	private static $max_elapsed = 60 * 60 * 24; // 1 day
	
	/**
	* Generates token for use but doesn't store it.
	*/
	private static function token()
	{
		return bin2hex(openssl_random_pseudo_bytes(64));
	}
	
	/**
	* Generate and store CSRF token in user session.
	* Requires session to have been started already.
	*/
	private static function createToken()
	{
		$token = self::token();
		$_SESSION['token'] = $token;
		$_SESSION['token_time'] = time();
		return $token;
	}
	
	/**
	* Destroys a token by removing it from the session.
	*/
	private static function destroyToken()
	{
		$_SESSION['token'] = null;
		$_SESSION['token_time'] = null;
		return true;
	}
	
	/**
	* Return HTML tag for use in a form.
	*/
	public static function display()
	{
		return "<input type=\"hidden\" name=\"token\" value=\"" . self::createToken() . "\" />";
	}
	
	/**
	* Returns true if user-submitted POST token is
	* identical to the previously stored SESSION token.
	* Returns false otherwise.
	*/
	public static function isValid()
	{
		if (isset($_POST['token']))
		{
			$user_token = $_POST['token'];
			$stored_token = $_SESSION['token'];
			return hash_equals($_SESSION['token'], $_POST['token']);
		}
		else
		{
			return false;
		}
	}
	
	/**
	* You can simply check the token validity and 
	* handle the failure yourself, or you can use 
	* this "stop-everything-on-failure" method.
	*/ 
	public static function exitOnFailure()
	{
		if (!self::isValid())
		{
			exit('Invalid Security Token.');
		}
	}
	
	/**
	* This doesn't have to be used but it
	* checks to see if the token is recent.
	*/
	public static function isRecent()
	{
		if (isset($_SESSION['token_time']))
		{
			$stored_time = $_SESSION['token_time'];
			return ($stored_time + self::$max_elapsed) >= time();
		}
		else
		{
			self::destroyToken();
			return false;
		}
	}
}
