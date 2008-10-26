<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * @package TNGz
 * @url http://code.zikula.org/tngz
 * @license http://www.gnu.org/copyleft/gpl.html
 *
 * @author Wendel Voigt
 * @version $Id$
 */

global $cms;
$cms[auto]    = true;
$cms[support] = "zikula";
$cms[tngpath] = "";
include("config.php");
include($cms[tngpath] . "getlang.php");
include($cms[tngpath] . "genlib.php");

$reqVar = '_' . $_SERVER['REQUEST_METHOD'];
$form_vars = $$reqVar;
$parm      = $form_vars['parm'] ;

list($paramcheck,$f_username,$f_pwd, $goto_url) = explode('|', $parm);

if ( ($f_username=="") || ($parmcheck != md5(implode('|', array($f_username,$f_pwd, $goto_url) ) ) )){
	$login_url = getURL( "login", 1 );
	header( "Location: " . "$login_url" . "message=loginfailed" );
}

$link = @mysql_pconnect($database_host, $database_username, $database_password);
$select_result = mysql_select_db($database_name, $link);
$query = "SELECT * FROM $users_table WHERE username = '$f_username' AND password='$f_pwd'";
$result = mysql_query($query) or die ("Cannot execute query: $query");
$found = mysql_num_rows( $result );
if( $found == 1 ) {
	$row = mysql_fetch_assoc( $result );
	$check = strcmp( $f_pwd, $row[password] ) || ( $row[allow_living] == -1 );
}
mysql_free_result($result);

if( $found == 1 && !$check ) {
	session_register('logged_in');
	session_register('allow_admin_db');
	session_register('allow_edit_db');
	session_register('allow_add_db');
        session_register('tentative_edit_db');
	session_register('allow_delete_db');
	session_register('allow_living_db');
	session_register('allow_ged_db');
	session_register('allow_lds_db');
	session_register('assignedtree');
	session_register('assignedbranch');
	session_register('currentuser');
	session_register('currentuserdesc');
	session_register('session_rp');

	$logged_in = $_SESSION[logged_in] = 1;
	$allow_edit_db = $_SESSION[allow_edit_db] = $row[allow_edit];
	$allow_add_db = $_SESSION[allow_add_db] = $row[allow_add];
	$tentative_edit_db = $_SESSION[tentative_edit_db] = $row[tentative_edit];
	$allow_delete_db = $_SESSION[allow_delete_db] = $row[allow_delete];
	if( $allow_edit_db || $allow_add_db || $allow_delete_db )
		$allow_admin_db = $_SESSION[allow_admin_db] = 1;
	else
		$allow_admin_db = $_SESSION[allow_admin_db] = 0;
	$allow_living_db = $_SESSION[allow_living_db] = $row[allow_living];
	$allow_ged_db = $_SESSION[allow_ged_db] = $row[allow_ged];
	if( !$ldsdefault ) //always do lds
		$allow_lds_db = $_SESSION[allow_lds_db] = 1;
	elseif( $ldsdefault == 2 )  //depends on permissions
		$allow_lds_db = $_SESSION[allow_lds_db] = $row[allow_lds];
	else  //never do lds
		$allow_lds_db = $_SESSION[allow_lds_db] = 0;
	$assignedtree = $_SESSION[assignedtree] = $row[gedcom];
	$assignedbranch = $_SESSION[assignedbranch] = $row[branch];
	$currentuser = $_SESSION[currentuser] = $row[username];
	$currentuserdesc = $_SESSION[currentuserdesc] = $row[description];
	$session_rp = $_SESSION[session_rp] = $rootpath;

	header( "Location: " . $goto_url );
	exit;
}
else {
	$login_url = getURL( "login", 1 );
	header( "Location: " . "$login_url" . "message=loginfailed" );
}