<?
/**
 * This is the PHP API which must be called by your application
 * POST or GET Querystrings must be provided:
 * 	synoToken: string of synoToken
 *  action: (optional) string for requested action. 
 * 		"getUserData" by default if not provided
 *
 * Response is JSON style
 * {
 *	rtn: string of return code (200 for success, 401 is no SynoToken has been provided, 402 if SynoToken does not match with existing session
 *  result: {
 *     username: string with username
 *     usergroups: array of string with user's groups
 * }
 */
error_reporting(E_ALL); 
ini_set('display_startup_errors',1);  
ini_set('display_errors',1);

// Load of SynoAuth class
require_once("synoAuth.php");

$synoToken = (array_key_exists('synoToken',$_POST)) ? $_POST['synoToken'] : (array_key_exists('synoToken',$_GET)) ? $_GET['synoToken'] : "";
$action = (array_key_exists('action',$_POST)) ? $_POST['action'] : (array_key_exists('action',$_GET)) ? $_GET['action'] : "";

// Creation of synoAuth object
$synoauth = new synoAuth();

// If no synoToken provided
if ($synoToken == "")
	die(json_encode(array('rtn' => '401', 'error' => "No synoToken Provided")));

// Get username corresponding with session
$username = $synoauth->login($synoToken);

// If no username can be found
if ($username === False)
	die(json_encode(array('rtn' => '402', 'error' => "Not logged in")));

switch($action)
{
	// Get user's data (username & groups)
	case "getUserData":
	default:
		die(json_encode(array(
							'rtn' => '200', 
							'result' => array(
									'username' => $username,
									'usergroups'=> $synoauth->getGroups()
											)
								)
						)
			);
		break;
}
?>
