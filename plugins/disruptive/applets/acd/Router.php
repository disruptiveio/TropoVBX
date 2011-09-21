<?php

/**
* ACD Router. Routes a call, depending on the route type.
*/
class ACDRouter
{
	/**
	 * The actual routing class.
	 */
	private $_router;

	function __construct($routeType, $users)
	{
		$routeClass = "Router_".
			preg_replace('/(?:^|_)(.?)/e',"strtoupper('$1')",$routeType);
		
		if (!class_exists($routeClass))
			throw new Exception("Invalid ACDRouter type: {$routeClass}");
		
		$this->_router = new $routeClass($users);
	}

	public function next()
	{
		return $this->_router->next();
	}

	public function getNextUrl()
	{
		return $this->_router->getNextUrl();
	}
}

/**
* Base router class.
*/
abstract class RouterBase
{
	protected $_usersToDial;
	
	function __construct($users)
	{
		$this->_usersToDial = $users;
	}

	public function getNextUrl()
	{
		// Get user IDs
		$user_ids = array();
		foreach ($this->_usersToDial as $user)
			$user_ids[] = $user->id;
		
		// Generate URL
		return preg_replace('/\?(.*)/', '', $_SERVER['REQUEST_URI'])
			.'?user_ids='.implode(',', $user_ids);
	}

	abstract function next();
}

/**
* Routes a call round robin.
*/
class Router_RoundRobin extends RouterBase
{
	// TODO: fetch by round robin
	public function next()
	{
		return array_shift($this->_usersToDial);
	}
}

/**
* Routes a call longest idle.
*/
class Router_LongestIdle extends RouterBase
{
	// TODO: fetch by longest idle
	public function next()
	{
		return array_shift($this->_usersToDial);
	}
}