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

 /**
 * Get the common files from user settings
 * @param  none
 * @return array with TNG location information
 */
function TNGz_userapi_GetTNGpaths()
{
    $TNG = array();

    $TNG['directory']  = trim(trim( pnModGetVar('TNGz', '_loc'     ) ), "/") ;    // given directory name/path for TNG
    $TNG['SitePath']   = dirname(dirname(dirname(realpath(__FILE__))));           // as of TNGz 1.01 this is the Zikula base directory
    $TNG['WebRoot']    = rtrim(pnGetBaseURL(), "/");
    $TNG['TNGpath']    = $TNG['SitePath'] . ($TNG['directory']  != "" ? "/".$TNG['directory'] : "") . "/"; // main TNG directory

    $TNG['configpath'] = $TNG['TNGpath']; // Start in the main TNG directory
    $subrootfile = $TNG['configpath'] . "subroot.php";
    if (file_exists($subrootfile) ){  // Versions before TNG 7.0 did not have this file
        include($subrootfile);        // Sets $tngconfig['subroot'] to be "" or the full path to all the config files
        if ( $tngconfig['subroot'] ) {
            $TNG['configpath'] = $tngconfig['subroot']; // If it is set, use the subroot path for all config files
        }
    }
    $TNG['configfile']  = $TNG['configpath'] . "config.php" ;

    return $TNG;
}


function TNGz_userapi_ShowPage($args){

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_OVERVIEW)) {
	return LogUtil::registerError(_MODULENOAUTH);
    }

    //////////////////////////////////////////////////////
    // Get information on the location of TNG
    //////////////////////////////////////////////////////
    global $TNG;
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // This seems like a kluge, but it is needed to get the included TNG functions to work properly
    $global_variables = pnModGetVar('TNGz', '_globals');
    if ($global_variables) {
        eval("global " . $global_variables .";");
    }

    //////////////////////////////////////////////////////
    // Get Zikula language
    //////////////////////////////////////////////////////
    $zikulalang = SessionUtil::getVar('lang');

    switch ($zikulalang) {
	case "eng":
		$newlanguage = "English";
		break;
	case "deu":
		$newlanguage = "German";
		break;
	case "fra":
		$newlanguage = "French";
		break;
	case "pol":
		$newlanguage = "Polish";
		break;
	case "ita":
		$newlanguage = "Italian";
		break;
	case "nld":
		$newlanguage = "Dutch";
		break;
	case "esp":
		$newlanguage = "Spanish";
		break;
	default:
		$newlanguage = "English";
    }

    //////////////////////////////////////////////////////
    // Get the TNG configuration information
    //////////////////////////////////////////////////////
    $cms[tngpath] = $TNG['TNGpath'];
    $have_info = false;
    if (file_exists($TNG['configfile']) ){
	include $TNG['configfile'];
        $TNGhomepage = $homepage;
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = true;
    }
    if (!$have_info) {
	return LogUtil::registerError("Error accessing TNG config file.");
    }

    // Now that TNG config file is loaded, update the cms parameters (which at one time was in customconfig.php)
    // At one time, this was in the customconfig.php, but it can be done here.
    $cms[auto]  = true;
    $cms[TNGz]  = 1; //
    $cms[support]    = "zikula";
    $cms[module]     = "TNGz";    
    $cms[url]        = "index.php?module=TNGz&func=main&show";    
    $cms[tngpath]    = $TNG['directory']. "/";
    $cms[adminurl]   = "index.php?module=TNGz&func=admin";
    $cms[noend]      = true; // Tell TNG to not include end.php file
    $cms[cloaklogin] = "Yes";
    $cms[credits]    = "<!-- TNGz --><br />";
    
    // Fix up file paths to look in the right place
    $homepage = ($dot = strrchr($homepage, '.')) ? substr($homepage, 0, -strlen($dot)): $homepage;// strip .php or .html
    $rootpath        = $TNG['SitePath'] . "/";                     // Overwrite setting from TNG configuration
//  $custommetta     = dirname(realpath(__FILE__)) . "/meta.php";  // Overwrite setting from TNG configuration

    $gendexfile      = $cms[tngpath] . $gendexfile ;
    $mediapath       = $cms[tngpath] . $mediapath ;
    $headstonepath   = $cms[tngpath] . $headstonepath ;
    $historypath     = $cms[tngpath] . $historypath ;
    $backuppath      = $cms[tngpath] . $backuppath ;
    $documentpath    = $cms[tngpath] . $documentpath ;
    $photopath       = $cms[tngpath] . $photopath ;
    $logname         = $cms[tngpath] . $logname ;

    
    // Now fix Zikula's $register_globals=off code for TNG
    // NOTE: Is this still needed?
    $register_globals = (bool) ini_get('register_globals');
    if( $register_globals ) {
        $the_globals = $_SERVER + $_ENV + $_GET +$_POST;
        if( $the_globals && is_array( $the_globals ) ) {
            foreach( $the_globals as $key=>$value ) {
                if($key == 'cms' || $key == 'lang' || $key == 'mylanguage') die("sorry!");               
                ${$key} = $value;
            }
        }
        unset($the_globals);
        if( $_FILES && is_array( $_FILES ) ) {
            foreach( $_FILES as $key=>$value ) {
                ${$key} = $value[tmp_name];
            }
        }
    }

    //////////////////////////////////////////////////////
    // Check Arguments
    //////////////////////////////////////////////////////
    $TNGshowpage   = (isset($args['showpage'])) ? $args['showpage'] : $TNGhomepage;
    if ( !strpos( $TNGshowpage, ".php") ) {
	$TNGshowpage .= ".php";
    }
    $TNGrenderpage = (isset($args['render']))   ? $args['render']   : true;  // Default value
    $TNGrenderpage = (FormUtil::getPassedValue('tngprint', false, 'GET'))? false : $TNGrenderpage; //Don't wrap print pages

    //////////////////////////////////////////////////////
    // Get User Login information
    //////////////////////////////////////////////////////
    if (pnUserLoggedIn()) {
        $TNGusername = pnUserGetVar('uname');
    } else {
        if (pnModGetVar('TNGz', '_guest') == 1){
            $TNGusername = ($TNGguestname=="") ? "Guest" : pnModGetVar('TNGz', '_gname');
	} else {
            pnRedirect(pnModURL('Users','user','loginscreen')) ;
	}
    }

    //////////////////////////////////////////////////////
    // Create User if needed
    //////////////////////////////////////////////////////
    $ok = pnModAPIFunc('TNGz','user','ModifyCreateUser',array() );
    if (!$ok ) {
	return LogUtil::registerError("Error Creating User information. ");
    }

    //////////////////////////////////////////////////////
    // Get User Information from TNG database
    //////////////////////////////////////////////////////
    $TNG_conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $query = "SELECT * FROM $users_table WHERE username = '$TNGusername' ";
    if (!$result = &$TNG_conn->Execute($query) ) {
            return(false);
    }
    $found = $result->RecordCount();
    if( $found == 1 ) {
        $row = $result->fields;
    	$check = ( $row[allow_living] == -1 );
    }

    if( $found == 1 && !$check ) {
        // Update time of last login/use
	$newdate = date ("Y-m-d H:i:s", time() + ( 3600 * $time_offset ) );
	$query = "UPDATE $users_table SET lastlogin=\"$newdate\" WHERE userID=\"$row[userID]\"";
        if (!$result = &$TNG_conn->Execute($query) ) {
		return LogUtil::registerError("$admtext[cannotexecutequery]: $query");
        }

        // setup session information for the user
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
	session_register('session_language');
   	$session_language = $_SESSION[session_language] = $newlanguage;
	session_register('lastpage');
	$logged_in = $_SESSION[logged_in] = 1;
	$allow_edit_db = $_SESSION[allow_edit_db] = $row[allow_edit];
	$allow_add_db = $_SESSION[allow_add_db] = $row[allow_add];
	$tentative_edit_db = $_SESSION[tentative_edit_db] = $row[tentative_edit];
	$allow_delete_db = $_SESSION[allow_delete_db] = $row[allow_delete];
	if( $allow_edit_db || $allow_add_db || $allow_delete_db )
		$allow_admin_db = $_SESSION[allow_admin_db] = 1;
	else
		$allow_admin_db = $_SESSION[allow_admin_db] = 0;
	if( !$livedefault ) //depends on permissions
		$allow_living_db = $_SESSION[allow_living_db] = $row[allow_living];
	elseif( $livedefault == 2 ) //always do living
		$allow_living_db = $_SESSION[allow_living_db] = 1;
	else //never do living
		$allow_living_db = $_SESSION[allow_living_db] = 0;
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
    }

    $TNG_conn->Close();

    //////////////////////////////////////////////////////
    // Capture the TNG output
    //////////////////////////////////////////////////////
    ob_start();

    include $TNG['directory']."/".$TNGshowpage;

    $TNGoutput = ob_get_contents();
    ob_end_clean();


    //////////////////////////////////////////////////////
    // Now Clean up the TNG output
    //////////////////////////////////////////////////////

    // Email Filters
    $TNGemail  = pnModGetVar('TNGz', '_email');
    if ($TNGemail == "A" ) {
        $TNGoutput = pnModAPIFunc('TNGz','user','CleanEmail', array('source' => $TNGoutput, 'mode' => 'both', 'text2link' => true ));
    } elseif ($TNGemail == "E" ) {
        $TNGoutput = pnModAPIFunc('TNGz','user','CleanEmail', array('source' => $TNGoutput, 'mode' => 'both', 'text2link' => false ));
    }

    // Get Title information to add to Zikula title
    if (preg_match("/<meta name=\"Keywords\" content=\"(.+)\"/", $TNGoutput, $tng_title) ){
        $GLOBALS['info']['title'] = $tng_title[1];
    }

    // Clean up TNG HTML to remove HTML validation errors
    // First set up the changes
    // Remove TNG <title> tag.  Each page should only have one and Zikula provides.
    $patterns[0]     = "/<title>(.*)<\/title>/i";
    $replacements[0] = "\n<!-- $0 -->\n";
    // Remove the <meta> tags.  TNG's not in the right place and do not add much new informaiton
    $patterns[1]     = "/<meta (.*)>/i";
    $replacements[1] = "<!-- $0 -->\n";
    // Remove the TNG's DOCTYPE (Each page should only have one, and Zikula provides)
    $patterns[2]     = "@<!DOCTYPE[^\"]+\"([^\"]+)\"[^\"]+\"([^\"]+)/([^/]+)\.dtd\">@i";
    $replacements[2] = "<!-- $0 -->\n";
    // Remove TNG javascripts and load files into head (also using Zikula versions).
    $patterns[3]     = "/<script(.*)prototype.js(.*)<\/script>/i";
    $replacements[3] = "\n<!-- $0 -->\n";
    $patterns[4]     = "/<script(.*)scriptaculous.js(.*)<\/script>/i";
    $replacements[4] = "\n<!-- $0 -->\n";
    $patterns[5]     = "/<script(.*)net.js(.*)<\/script>/i";
    $replacements[5] = "\n<!-- $0 -->\n";
    $patterns[6]     = "/<script(.*)litbox.js(.*)<\/script>/i";
    $replacements[6] = "\n<!-- $0 -->\n";

    PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/net.js');
    PageUtil::AddVar('javascript', 'javascript/ajax/prototype.js');
    PageUtil::AddVar('javascript', 'javascript/ajax/scriptaculous.js');
    PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/litbox.js');
    // Question: What happens if Zikula and TNG use different versions of these libraries
    //           Could this cause odd behavior in TNG?

    // Now go do the clean up
    ksort($patterns);      // The sorts are recommended to make sure the pattern/replacement are aligned
    ksort($replacements);
    $TNGoutput = preg_replace($patterns, $replacements, $TNGoutput);

    //////////////////////////////////////////////////////
    // Now get ready to display
    //////////////////////////////////////////////////////

    if (!$TNGrenderpage) {
        echo $TNGoutput;   // do not wrap with Zikula
        return true;       // signal output has already been displayed
    }

    // Add TNG output to normal Zikula display
    $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

    $pnRender =& new pnRender('TNGz');

    $pnRender->assign('TNGoutput'    , $TNGoutput);
    $pnRender->assign('TNGtitle'     , $tng_title[1] );
    $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );

    return $pnRender->fetch('TNGz_user_main.htm');

}

 /**
 * Get the global variables declared by TNG
 * @param  str args['dir'] The directory containing the TNG php programs
 * @return false on error or the comma separated list of global variables used by TNG
 */
 function TNGz_userapi_GetTNGglobals($args){

    $dir = $args['dir'];
    if (!is_dir($dir)) {  // Make sure it is a real directory
        return false;  // Error
    }
    if (!$dh  = opendir($dir) ){  //Open the directory
        return false;  // Error
    }
    while (false !== ($filename = readdir($dh))) {  // Get all the files in the directory
         $files[] = $filename;
    }
    closedir($dh);

    //////////////////////////////////////////////////////
    // Now scan through all the .php files looking for global variable declarations
    //////////////////////////////////////////////////////
    $allglobals = array();                                                           // Holder for the global variables found
    foreach ($files as $filename) {                                                  // Look through each file
        if ( strpos( $filename, ".php") ) {                                          // only need to check .php files
	    if ($thefile = file_get_contents("$dir/$filename") ){;                   // read in the file, keep going if no error
	        $globalmatches = array();                                            // holder for all the global statements found
	        preg_match_all('/[\s|^]global[\s]+(.*)\;/',$thefile,$globalmatches); // Look for php global variable statements
                $match = $globalmatches[0];                                          // all the matches in an array
	        foreach ($match as $line) {                                          // Look through each of the statements found
	            $line = preg_replace('/[\s]*global[\s]+/', "", $line);           // take out "global" at the beginning (and spaces)
	            $line = preg_replace('/[\s]*\;/', "", $line);                    // take out ; at the end (and spaces)
                    $theglobals = preg_split('/[\s]*,[\s]*/', $line);                // now get each of the global variables listed
                    foreach ($theglobals as $aglobal){                               // Look at each global variable
		    if ( !$allglobals[$aglobal] ) {                                 //   if not already found, add to the list
	                    $allglobals[$aglobal] = $aglobal;
		        }
	            }
		}
	    }
        }
    }
    //////////////////////////////////////////////////////
    // Now get ready to return the global variables found
    //////////////////////////////////////////////////////
    sort($allglobals);
    return implode(", ", $allglobals );
}


 /**
 * Generate a random password
 * @param int args['length'] length of password
 * @param int args['numitems'] number of items to get
 * @return generated password
 */
 function TNGz_userapi_ranpass($args){
    extract($args);

    // Optional arguments.
    if (!isset($length)) {
        $length = 8;
    }

    $pass = NULL;
    for($i=0; $i<$length; $i++) {
        $char = chr(rand(48,122));
        while (!ereg("[a-zA-Z0-9]", $char)) {
            if($char == $lchar) continue;
            $char = chr(rand(48,90));
       }
       $pass .= $char;
       $lchar = $char;
    }
    return $pass;
}


 /**
 * Generate a random password
 * @param int args['length'] length of password
 * @param int args['numitems'] number of items to get
 * @return false = error condition, 1 = modified user info, 2 = created user, true = nothing done
 */
function TNGz_userapi_ModifyCreateUser()
{
    $return_code = true;  // default if nothing is done
    $guest     = pnModGetVar('TNGz', '_guest');
    $guestname = pnModGetVar('TNGz', '_gname');
    $loggedin  = pnUserLoggedIn();

    if ( $loggedin || $guest == 1 ) {
        if ( $loggedin ) {
            $uid    = pnSessionGetVar('uid');  // find out which user #
            $u      = pnUserGetVars($uid, true);     // $u[] has all the logged in user vars
            // Start Fix... Starting with Zikula, user values have changed.  So fix up so it looks like old
            // List is from \system\Profile\pnuserapi.php function Profile_userapi_aliasing
            $vars = array();
            $vars['_UREALNAME']      = 'name';
            $vars['_UREALEMAIL']     = 'email';
            $vars['_UFAKEMAIL']      = 'femail';
            $vars['_YOURHOMEPAGE']   = 'url';
            $vars['_TIMEZONEOFFSET'] = 'timezone_offset';
            $vars['_YOURAVATAR']     = 'user_avatar';
            $vars['_YICQ']           = 'user_icq';
            $vars['_YAIM']           = 'user_aim';
            $vars['_YYIM']           = 'user_yim';
            $vars['_YMSNM']          = 'user_msnm';
            $vars['_YLOCATION']      = 'user_from';
            $vars['_YOCCUPATION']    = 'user_occ';
            $vars['_YINTERESTS']     = 'user_intrest';
            $vars['_SIGNATURE']      = 'user_sig';
            $vars['_EXTRAINFO']      = 'bio';
            // map new names to old names  Note: these should only exist starting with Zikula
            foreach ( $vars as $key => $value) {
                if (isset($u[$key])) {
                    $u[$value] = $u[$key];
                }
            }
            $pass_hash_number = (isset($u['hash_method'])) ? $u['hash_method'] : 1 ; // old PN used md5
            $use_password = ($pass_hash_number == 1) ? true : false ;                // 1 = MD5, same as TNG
            // End of fix...

            $userid = $u['uname'];
        } else {
            $guestname = ($guestname == "") ? "Guest" : $guestname;
            $userid = $guestname ;  // If not logged in, then must be a guest
        }

        $TNG_create = pnModGetVar('TNGz', '_users');
        $TNG_sync   = pnModGetVar('TNGz', '_sync');

        $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

        // Check to be sure we can get to the TNG information
        $have_info = 0;
        if (file_exists($TNG['configfile']) ){
            include($TNG['configfile']);
            $TNG_conn = &ADONewConnection('mysql');
            $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
            $have_info = 1;
        }
        if (!$have_info) {
            return(false);
        }

        $query     =  "SELECT userID, email, realname, website, password FROM $users_table WHERE username = '$userid' ";
        if (!$result = &$TNG_conn->Execute($query) ) {
            return(false);
        }
        $found = $result->RecordCount();
        if ( $found == 0 ) {
            // username not found, prepare to create it in TNG
            if ( !$loggedin && $guest == 1) {
                // Base Guest account
                $TNG_user     = $guestname;
                $TNG_email    = "";
                $TNG_name     = $guestname;
                $TNG_website  = "";
                $TNG_mail     = "";
                $TNG_living   = pnModGetVar('TNGz', '_living');
                $TNG_gedcom   = pnModGetVar('TNGz', '_gedcom');
                $TNG_lds      = pnModGetVar('TNGz', '_lds');
                $TNG_db       = "";
                $TNG_pwd      = pnModAPIFunc('TNGz','user','ranpass',array());
		$TNG_pwd_safe = md5( $TNG_pwd );

            } elseif ( $TNG_create == 1) {
                // A registered Zikula user
                $TNG_user     = $u['uname'] ;
                $TNG_email    = $u['email'];
                $TNG_name     = $u['name'];
                $TNG_website  = $u['url'];
                $TNG_mail     = pnModGetVar('TNGz', '_mail');
                $TNG_living   = pnModGetVar('TNGz', '_living');
                $TNG_gedcom   = pnModGetVar('TNGz', '_gedcom');
                $TNG_lds      = pnModGetVar('TNGz', '_lds');
                $TNG_db       = "";
                $TNG_pwd      = "";
//              $TNG_pwd      = pnModAPIFunc('TNGz','user','ranpass',array());
//		        $TNG_pwd_safe = base64_encode( $TNG_pwd );
                if ($use_password) {
		    $TNG_pwd_safe = $u['pass']; // can use same md5 password
                } else {
		    // otherwise generate a random one just for TNG
                    $TNG_pwd_safe  = md5(pnModAPIFunc('TNGz','user','ranpass',array()) );
                }

                if ($TNG_name != "") {
                    $TNG_desc = $TNG_name;
                } else {
                    $TNG_desc = $TNG_user;
                }
            }
            if ( ($loggedin && $TNG_create == 1) || (!$loggedin && $guest == 1)) {
                $adding  = "INSERT INTO $users_table ";
                $adding .= "(";
                $adding .= "description, username, realname  , email      , website      , gedcom , allow_living, allow_ged  , allow_lds, lastlogin ";
                $adding .= ( $use_password ) ? ", password " : "";
                $adding .=  ") VALUES ";
                $adding .= "(";
                $adding .= "'$TNG_desc','$TNG_user','$TNG_name','$TNG_email','$TNG_website','$TNG_db','$TNG_living' ,'$TNG_gedcom','$TNG_lds' , $TNG_conn->DBTimeStamp(NOW())";
                $adding .=  ( $use_password ) ? ",'$TNG_pwd_safe'" : "";
                $adding .= ") ";


                if (!$added = &$TNG_conn->Execute($adding) ) {
                        return(false);
                }
                $added->Close();
                /*  No longer need since using md5 in TNG as well as PostNuke
		        if ( $TNG_mail == 1 && $loggedin ){ // dont send for Guest
			        $TNG_mail_msg  = "Your password for the TNG site at";
			        $TNG_mail_msg .=  " " . $tngdomain . " ";
			        $TNG_mail_msg .= "is : ";
			        $TNG_mail_msg .= $TNG_pwd ;
			        $TNG_mail_subject   = "Password for the TNG site, user ";
			        $TNG_mail_subject  .= $TNG_user ;
			        pnMail($TNG_email, $TNG_mail_subject, $TNG_mail_msg, "");
		        }
                */
                $return_code = 2;
	        }
        } elseif ($TNG_sync == 1 && $loggedin ) {
            // The user was found, so check for updates
            $TNG_changed = false;
            list($TNG_uid, $TNG_email, $TNG_name, $TNG_website, $TNG_password ) = $result->fields;
            $adding =  "UPDATE $users_table SET ";
            if ( $TNG_email != $u['email'] ) {
                $TNG_email = $u['email'];
                $adding .= " email='$TNG_email',";
                $TNG_changed = true;
            }

            if ( $TNG_name != $u['name'] ) {
                $TNG_name = $u['name'];
                $adding .= " realname='$TNG_name',";
                $TNG_changed = true;
            }
            if ( $TNG_website != $u['url'] ) {
                $TNG_website = $u['url'];
                $adding .= " website='$TNG_website',";
                $TNG_changed = true;
            }
            if ( $TNG_password != $u['pass'] && $use_password) {
                $TNG_password   = $u['pass'];
                $adding .= " password='$TNG_password',";
                $TNG_changed = true;
            }
            if ($TNG_changed == true) {
                $adding = rtrim($adding, " ,"); // take off last comma and spaces
   		        $adding .=  " WHERE userID='$TNG_uid'";
                if (!$added = &$TNG_conn->Execute($adding) ) {
                    return(false);
                } else {
                    $return_code = 1;
                }
                $added->Close();
            }
        }
        $TNG_conn->Close();
    }
    return ($return_code);
}

//-----------------------------------------------------------------------
// Function to find email addresses and hide from email harvestors
// $source is the text/html to scan
// $mode = 'mailto'   to work only on mailto email addresses
// $mode = 'text'     to work only on text email addresses
// $mode = 'both'     to work on both of the above
// $text2link = true  to make plain text emails into mailto links
// $text2link = false to just encode text emails without making into mailto links
//-----------------------------------------------------------------------
function TNGz_userapi_CleanEmail($args) {
    // Arguments
    $source     = (isset($args['source']))    ? $args['source']    : "";
    $mode       = (isset($args['mode']))      ? $args['mode']      : 'both';
    $text2link  = (isset($args['text2link'])) ? $args['text2link'] : true;

    $symbols_new      = array(" [ a t ] "," [ d o t ] ");
    $symbols_email    = array("@"     ,   "."   );
    $random_max       = 5;
    $random_char      = rand(0,$random_max );
    $regex_html_email = '!<a\s([^>]*)href=["\']mailto:([^"\']+)["\']([^>]*)>(.*?)</a[^>]*>!is';
    $regex_text_email = '![a-zA-Z0-9\-_]+@[a-zA-Z0-9\-_]+.[a-z]{2,3}!is';
    $hex_mailto       = "&#109;&#097;&#105;&#108;&#116;&#111;&#058;";
    switch($mode) {
        case false:    return $source; break;                                 // fast return
        case 'mailto': $regexes = array('html' => $regex_html_email); break;  // Just work on mailto
        case 'text'  : $regexes = array('text' => $regex_text_email); break;  // Just work on text emails
        case 'both':                                                          // Work on both mailto and text emails
        default:       $regexes = array('html' => $regex_html_email,
                                        'text' => $regex_text_email); break;
    }
    foreach($regexes as $regex_type => $regex) {
        preg_match_all($regex, $source, $matches);
        if(empty($matches[0])) {
            continue; // no matches
        }
        $modifications = $matches[0];
        foreach($modifications as $key => $match) {
           if($regex_type === 'html') {
	       $address = $matches[2][$key];
               $display = $matches[4][$key];
           } elseif($regex_type === 'text') {
               $address = $matches[0][$key];
               $display = str_replace( $symbols_email, $symbols_new , $matches[0][$key]); //substitute characters
	   }
           // Encode the email address
           $hex_address = '';
           $length = strlen($address);
           for($x = 0; $x < $length; $x++) {
	       $char = substr($address,$x,1);
	       if ( ( ($x % $random_max ) == $random_char ) && !(strpos("@.", $char) !== false) ){
	           $hex_address .= $char;  // every once in a while, keep it the same to make it harder to decode
	       } else {
		   $hex_address .= '&#' . ord($char) . ';';
	       }
           }

	   // Encode the display information
           $hex_display = '';
           $length = strlen($display);
           for($x = 0; $x < $length; $x++) {
	       $char = substr($display,$x,1);
	       if ( ( ($x % $random_max ) == $random_char ) && !(strpos("@.", $char) !== false) ){
		   $hex_display .= $char;  // every once in a while, keep it the same to make it harder to decode
	       } else {
                   $hex_display .= '&#' . ord($char) . ';';
	       }
           }

          // now put it back together
	  if ($regex_type === 'text' && $text2link!=true ){
	      $modifications[$key] = $hex_display;
	  } else {
              $modifications[$key] = '<a href="'.$hex_mailto.$hex_address.'" title="'.$hex_display.'">'.$hex_display.'</a>';
	  }
	  $source = str_replace($matches[0], $modifications, $source);
        }
    }
    return $source;
}


 /**
 * Get the url used to open TNG in an iFRAME
 * @param  none
 * @return false on error or the URL to be used by the iFRAME
 */
 function TNGz_userapi_GetTNGurl($args){

     //////////////////////////////////////////////////////
    // Get the TNG configuraiton information
    //////////////////////////////////////////////////////
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    $have_info = false;
    if (file_exists($TNG['configfile']) ){
	include $TNG['configfile'];
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = true;
    }
    if (!$have_info) {
        return false;
    }

    //////////////////////////////////////////////////////
    // Get the TNG user informaiton for the person logged in Zikula
    // NOTE: Get password from TNG just in case they are not in sync
    //////////////////////////////////////////////////////
    $username = pnUserGetVar('uname');

    $link = @mysql_pconnect($database_host, $database_username, $database_password);
    $select_result = mysql_select_db($database_name, $link);
    $query = "SELECT * FROM $users_table WHERE username = '$username' ";
    $result = mysql_query($query) or die ("Cannot execute query: $query");
    $found = mysql_num_rows( $result );
    if( $found == 1 ) {
	$row = mysql_fetch_assoc( $result );
	$userpass = $row[password];
    } else {
        $userpass = "";
    }
    mysql_free_result($result);
    $TNG_conn->Close();

    //////////////////////////////////////////////////////
    // Now encode everything to pass on,
    //////////////////////////////////////////////////////
    $parmcheck = implode('|', array($username,$userpass, 'admin/index.php') );  // Used just to get the check value
    $check=md5($paramcheck) ;  // Add another check to make sure someone doesn't just edit a few characters.
    $parm = implode('|', array($check,$username,$userpass,'admin/index.php') );
    $url = $TNG['directory'] . "/index_TNGz.php?parm=$parm";

    return $url;
 }



 /**
 * Generate reference link back to TNGz items
 * @param int args['RefType'] 0 or 1 depending upon URL style
 * @param int args['func'] what function to call from TNG
 * @param int args['personID']
 * @param int args['tree']
 * @param int args['ordernum']
 * @param int args['photoID']
 * @param int args['familyID']
 * @param int args['docID']
 * @param int args['url']
 * @param int args['target']
 * @param int args['description'] image description
 * @return reference link
 */
function TNGz_userapi_MakeRef($args) {

    extract($args);
    // Valid call combinations
    // $RefType, $func="getperson", $personID, $tree $target $description
    // $RefType, $func="showphoto", $personID, $tree, $ordernum $target $description
    // $RefType, $func="photo", $photoID $target $description
    // $RefType, $func="familygroup", $familyID, $tree $target $description
    // $RefType, $func="showhistory", $docID $target $description
    // $RefType, $func="url", $url $target $description
    // $RefType, $func="main" $target $description

    // Optional arguments.
    if (!isset($RefType)) {
        $RefType = 0;
    }

    $RefType = 0; // FIX

    if (!isset($target)) {
        $target = "";
    }
    if ($target !="" ){
        $target = "target = \"$target\"";
    }
    if (!isset($func)) {
        $func = "main";
    }
    if (!isset($description)) {
        $description = "";
    }
    if (!isset($url)) {
        $url = false;
        $amp = "&amp;";
    } else {
        $url = true;
        $amp = "&";
    }

    switch ($RefType) {
    case "1":
            $Ref = "index.php?module=TNGz".$amp."func=";
            break;
    case "0":
    default :
            $Ref = "index.php?module=TNGz".$amp."func=main".$amp."show=";
    }


    switch ($func) {
    case "getperson":
            $Ref .= "getperson".$amp."personID=$personID".$amp."tree=$tree";
            break;
    case "showmedia":
            $Ref .= "showmedia".$amp."mediaID=$mediaID".$amp."medialinkID=$medialinkID";
            break;
    case "showphoto":
            $Ref .= "showmedia".$amp."personID=$personID".$amp."tree=$tree".$amp."mediatypeID=photo".$amp."ordernum=$ordernum";
            break;
    case "photo":
            $Ref .= "showmedia".$amp."mediaID=$photoID";
            break;
    case "familygroup":
            $Ref .= "familygroup".$amp."familyID=$familyID".$amp."tree=$tree";
            break;
    case "showhistory":
            $Ref .= "showhistory".$amp."docID=$docID";
            break;
    case "main":
    default:
	    $Ref = "index.php?module=TNGz";
    }

    return "<a href=\"" . $Ref . "\" $target >$description</a>";

}
 /**
 * Generate reference link back to TNGz items
 * @param int $args['RefType'],     0 or 1 depending upon URL style
 * @param str $args['target'],      to add any special target parameters to the link
 * @param str $args['description'], the text for the link
 * @param str $args['func'],        the TNG file to run
 * @param str $args['...],          any other parameters are passed directly to the TNG function
 * @return reference link
 */
function TNGz_userapi_MakeRefproposed($args) {

    // QUESTION: Why does file based short URLs not work with pnModURL????

    $RefType = (isset($args['RefType'])) ? $args['RefType'] : 0 ;
    unset($args['RefType']);
    $RefType = 0; // FIX - just do type 0 for now...

    $target = (isset($args['target'])) ? $args['target'] : "" ;
    unset($args['target']);
    if ($target !="" ){
        $target = "target = \"$target\"";
    }

    $description = (isset($args['description'])) ? $args['description'] : "" ;
    unset($args['description']);

    $prog = (isset($args['func'])) ? $args['func'] : "main" ;
    unset($args['func']);
    if ($prog == "main") {
        $args = array();
    }

    switch ($RefType) {
    case "1":
            $func=$prog;
            break;
    case "0":
    default :
            $func="main";
	    if ($prog != "main") {
	        $args = array_merge(array("show"=>$prog),$args); // add to the front (so comes out first
	    }
    }

    return "<a href=\"" . pnModURL("TNGz", null, $func, $args) . "\" $target >$description</a>";

}
 /**
 * Generate scaled photo link
 * @param string args['photo_file'] image file name of image
 * @param string args['web_ref'] image web reference
 * @param int args['max_height'] maximum height in pixels
 * @param int args['max_width'] maximum width in pixels
 * @param int args['text'] image alternate text
 * @param int args['description'] image description
 * @return scaled image reference
 */
function TNGz_userapi_PhotoRef($args) {

    extract($args);

    // Optional arguments.
    if (!isset($text)) {
        $text = "";
    }
    if (!isset($description)) {
        $description = "";
    }
    if (!isset($max_height) || !is_numeric($max_height)) {
        $max_height = 100;
    }
    if (!isset($max_width) || !is_numeric($max_width)) {
        $max_width = 100;
    }

    list($width, $height, $type, $attr) = getimagesize($photo_file);
    $height_scale = $height / $max_height;
    $width_scale  = $width  / $max_width;
    if (($height_scale > 1) || ($width_scale > 1)) {
        if ($height_scale > $width_scale) {
            $scale = $height_scale;
        } else {
            $scale = $width_scale;
        }
    } else {
        $scale = 1;
    }
    $new_width = floor($width / $scale);
    $new_height= floor($height / $scale);

    return "<img width='$new_width' height='$new_height' src='$web_ref' alt='$text' $description />";
}


function TNGz_userapi_getRecords($args) {
    extract($args);

    // check out $kind
    if (!isset($kind)) {
        $kind = "people";
    }
    if ( ($kind != "people") && ($kind != "family")) {
        return(false);
    }  

    $limit = true;      // first assume number of records returned are limited unless found otherwise.
    // Check out $start
    if (!isset($start)) {
        $limit = false;    // No starting value given
    } elseif (!is_numeric($start)){
        $limit = false;    // not a number
    } elseif ( $start < 0 ) {
        $limit = false;    // Invalid starting value given
    } else {
        $start = intval($start);
    }
    
    // Check out $count
    if (!isset($count)) {
        $limit = false;    // No starting value given
    } elseif (!is_numeric($count)){
        $limit = false;    // not a number
    } elseif ( $count < 0 ) {
        $limit = false;    // Invalid starting value given
    } else {
        $count = intval($count);
    }
    // $limit is still true only if $start and $count are valid
    
    // Now go get the informaiton
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // Check to be sure we can get to the TNG information
    $have_info = 0;
    if (file_exists($TNG['configfile']) ){
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    }
    if (!$have_info) {
        return(false);
    }
    if ($kind == "people") {
        $query  =  "SELECT gedcom, personID, changedate FROM $people_table";
    } elseif ( $kind == "family") {
        $query   =  "SELECT gedcom, familyID, changedate FROM $families_table";
    }
    if ($limit) {
        $query  .= " LIMIT ". $start . ", " . $count;
	
    }

    if (!$result = &$TNG_conn->Execute($query) ) {
        return(false);
    }
    $thelist = array();
    if($result->RecordCount()>0) {
        for (; !$result->EOF; $result->MoveNext()) {
            $items = array();
            list( $items['tree'],$items['id'],$items['changedate'] ) = $result->fields;
		    $items['changedate'] = substr($items['changedate'],0,10);  // mod to handle date format change in TNGv6
		    if ($items['changedate'] == "0000-00-00" || $items['changedate'] == "" ) {
     		      $items['changedate'] = date("Y-m-d");
		    }
            $thelist[] = $items;
        }
    }
    return($thelist);
}

//
function TNGz_userapi_getRecordsCount($args) {
    extract($args);
    
    $MaxPerMap = 1000; // maximum number of sitemap records that can be returned. 
    
    $facts = array();
    
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // Check to be sure we can get to the TNG information
    $have_info = 0;
    if (file_exists($TNG['configfile']) ){
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    }
    if (!$have_info) {
        return(false);
    }

    $query  =  "SELECT count(id) as pcount FROM $people_table";
    if (!$result = &$TNG_conn->Execute($query) ) {
        return(false);
    }
    if($result->RecordCount()>0) {
        list( $facts['people'] ) = $result->fields;
    }
    
    $query  =  "SELECT count(id) as pcount FROM $families_table";
    if (!$result = &$TNG_conn->Execute($query) ) {
        return(false);
    }
    if($result->RecordCount()>0) {
        list( $facts['family'] ) = $result->fields;
    }
    
    $facts['total'] = $facts['people'] + $facts['family'];
    $facts['sitemapindex'] = false;
    
    if ($facts['total']>$MaxPerMap){
        $sitemaps = array();
        for($i=0; $i < $facts['people']; $i+= $MaxPerMap) {
            $sitemaps[] = array( 'map' => 'people', 'start' => $i, 'count' => $MaxPerMap);
        }
        for($i=0; $i < $facts['family']; $i+= $MaxPerMap) {
            $sitemaps[] = array( 'map' => 'family', 'start' => $i, 'count' => $MaxPerMap);
        }
        $facts['sitemapindex'] = $sitemaps;       
    }   

    return($facts);
}

/* **************************  POSTNUKE TO TNG Field Mappings **************************************************************
 * POSTNUKE              pnTYPE          TNG             TNG TYPE    DESCRIPTION
 *--------------------------------------------------------------------------------------------------------------------------
 * $u['uname']           varchar(25)   username        varchar(20)   The unique one word username
 * $u['email']           varchar(60)   email           varchar(50)   Email address
 * $u['name']            varchar(60)   realname        varchar(50)   Person's real name
 * $u['url']             varchar(254)  website         varchar(128)  URL of person's website
 *                                     descripiton     varchar(50)
 * $u['pass']                          password        varchar(20)   TNG password in md5
 *                                     phone           varchar(30)   Phone number
 *                                     address         varchar(100)  Address
 *                                     city            varchar(64)   City
 *                                     state           varchar(64)   State
 *                                     zip             varchar(10)   Zip code
 *                                     country         varchar(64)   Country
 *                                     notes           text          Notes on user
 * TNGz _db                           gedcom          varchar(20)   File for user to access (blank is all files)
 *                                     branch          varchar(20)   Particular branch
 *                                     allow_edit      tinyint(4)    1=TNG user can edit data, default=0
 *                                     allow_add       tinyint(4)    1=TNG user can add data , default=0
 *                                     tentative_edit  tinyint(4)    1=TNG user can submit data to change, default=0
 *                                     allow_delete    tinyint(4)    1=TNG user can delete data, default=0
 * TNGz _sync                         allow_lds       tinyint(4)    1=TNG user can view LDS data, default=0
 * TNGz _living                       allow_living    tinyint(4)    1=TNG user can view data on living people, default=0
 * TNGz _gedcom                       allow_ged       tinyint(4)    1=TNG user can download GEDCOMs, default=0
 *                                     lastlogin       date          Last TNG login (not PostNuke),default 0000-00-00
 *                                     userID          int(11)       TNG unique number for each user
 * $u['uid']             int(11)                                     PostNuke unique number for each user
 * $u['femail']          varchar(60)                                 fake email address
 * $u['timezone_offset'] float(3,1)                                  time zone offset so display user time not server time
 * $u['user_from']       varchar(100)                                PostNuke user from
 * $u['user_occ']        varchar(100)                                PostNuke user Occupation
 * $u['user_intrest']    varchar(150)                                PostNuke user interest
 * $u['bio']             tinytext                                    PostNuke user biography
 *
 * NOTES:
 * This does not include all PostNuke user fields, just those of interest to TNG
 * PostNuke passwords are MD5 based.  It would be great if TNG moved to MD5
 *
 */
 */