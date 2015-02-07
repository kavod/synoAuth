<?php

error_reporting(E_ALL | E_STRICT);  
ini_set('display_startup_errors',1);  
ini_set('display_errors',1);

class SynoAuth {
	protected $synotoken;
	protected $username;
	protected $usergroups;
	protected $logged;
	protected $REMOTE_ADDR;
	protected $cookie_id;

	// Constructor
	public function SynoAuth()
	{
		$this->logged = False; // By default: not logged
		$this->REMOTE_ADDR = escapeshellcmd($_SERVER['REMOTE_ADDR']); // Get IP Address
		$this->cookie_id = escapeshellcmd($_COOKIE['id']); // Get Cookie ID
	}

	// Get login information
	// If token is provided, use it. If not, use the previously provided one (with setToken method)
	public function login($token="")
	{
		if ($token == "")
		{
			$token = $this->getToken();
		} else
		{
			$token = $this->setToken($token);
		}
		if ($token == "")
		{
			return False;
		}
		if ($this->_authentificate() == "")
			return False;
		$this->_getGroups();
		return $this->getUsername();
	}

	// Call authenticate.cgi with provided synoToken, IP address and Cookie ID in order to get the username
	protected function _authentificate()
	{
		$cmd = 'export QUERY_STRING="SynoToken=' . escapeshellcmd($this->synotoken).'" ';
		$cmd .= 'REMOTE_ADDR=' . escapeshellarg($this->REMOTE_ADDR) . ' ';
		$cmd .= 'HTTP_COOKIE="id=' . escapeshellcmd($this->cookie_id) . '" ';
		$cmd .= '&& /usr/syno/synoman/webman/authenticate.cgi';
		exec($cmd,$result);
		if (count($result) < 1)
		{
			return False;
		}
		$this->username = $result[0];
		return $this->getUsername();
	}

	// Get groups
	protected function _getGroups()
	{
		// Only if userdata has already been determined
		if (escapeshellarg($this->username) == "")
			return False;
		exec('id -Gn ' . escapeshellarg($this->username) . ' 2>/dev/null',$result);
		$this->usergroups = explode(" ",$result[0]);
		return $this->getGroups();
	}

	public function getGroups()
	{
		return $this->usergroups;
	}

	public function setToken($token)
	{
		$this->synotoken = escapeshellcmd($token);
		return $this->getToken();
	}

	public function getToken()
	{
		return escapeshellcmd($this->synotoken);
	}

	public function getUsername()
	{
		return escapeshellcmd($this->username);
	}
}
?>
