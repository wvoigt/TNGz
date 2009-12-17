<?php
/**
 * Zikula Application Framework
 *
 * @copyright  (c) Zikula Development Team
 * @link       http://www.zikula.org
 * @version    $Id$
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Wendel Voigt
 * @category   Zikula_Extension
 * @package    Content
 * @subpackage TNGz
 */

 /**
 * Get the common files from user settings
 * @param  none
 * @return array with TNG location information
 */
function TNGz_userapi_GetTNGpaths()
{
    static $TNG;
    if (!isset($TNG)) { // just do this once
        $TNG = array();

        $TNG['directory']  = trim(trim( pnModGetVar('TNGz', '_loc'     ) ), "/") ;    // given directory name/path for TNG
        $TNG['SitePath']   = dirname(dirname(dirname(realpath(__FILE__))));           // as of TNGz 1.01 this is the Zikula base directory
        $TNG['WebRoot']    = rtrim(pnGetBaseURL(), "/");
        $TNG['TNGpath']    = $TNG['SitePath'] . ($TNG['directory']  != "" ? "/".$TNG['directory'] : "") . "/"; // main TNG directory

        $TNG['configpath'] = $TNG['TNGpath']; // Start in the main TNG directory
        $subrootfile = $TNG['configpath'] . "subroot.php";
        if (file_exists($subrootfile) ) { // Versions before TNG 7.0 did not have this file
            include($subrootfile);        // Sets $tngconfig['subroot'] to be "" or the full path to all the config files
            if ( $tngconfig['subroot'] ) {
                $TNG['configpath'] = $tngconfig['subroot']; // If it is set, use the subroot path for all config files
            }
        }
        $TNG['configfile']  = $TNG['configpath'] . "config.php" ;
    }
    return $TNG;
}

/*
* DBconnect
* @param  none
* @return mixed ADO connection to TNG data, or false if connection failed.
*/
function TNGz_userapi_DBconnect()
{
    static $TNG_conn;
    if (!isset($TNG_conn)) {
        $TNG = pnModAPIFunc('TNGz','user','TNGconfig');
        if ($TNG) {
            $TNG_conn = ADONewConnection('mysql');
            $TNG_conn->NConnect($TNG['database_host'], $TNG['database_username'], $TNG['database_password'], $TNG['database_name']);
            $TNG_conn->SetFetchMode(ADODB_FETCH_ASSOC);
        } else {
            $TNG_conn = false;
        }
    }
    return $TNG_conn;
}

/*
* TNGconfig
* @param  none
* @return array of TNG configuration information, false on failure
*/
function TNGz_userapi_TNGconfig()
{
    static $TNGconfig;
    if (!isset($TNGconfig)) { // just do this once
        $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

        // Check to be sure we can get to the TNG information
        if (!file_exists($TNG['configfile']) ) {
            $TNGconfig = false;
        } else {          
            // config.php
            include($TNG['configfile']);
            $TNGconfig['database_host']         = $database_host;
            $TNGconfig['database_name']         = $database_name;
            $TNGconfig['database_username']     = $database_username;
            $TNGconfig['database_password']     = $database_password;
            $TNGconfig['people_table']          = $people_table;
            $TNGconfig['families_table']        = $families_table;
            $TNGconfig['children_table']        = $children_table;
            $TNGconfig['albums_table']          = $albums_table;
            $TNGconfig['album2entities_table']  = $album2entities_table;
            $TNGconfig['albumlinks_table']      = $albumlinks_table;
            $TNGconfig['media_table']           = $media_table;
            $TNGconfig['medialinks_table']      = $medialinks_table;
            $TNGconfig['mediatypes_table']      = $mediatypes_table;
            $TNGconfig['address_table']         = $address_table;
            $TNGconfig['languages_table']       = $languages_table;
            $TNGconfig['cemeteries_table']      = $cemeteries_table;
            $TNGconfig['states_table']          = $states_table;
            $TNGconfig['countries_table']       = $countries_table;
            $TNGconfig['places_table']          = $places_table;
            $TNGconfig['sources_table']         = $sources_table;
            $TNGconfig['repositories_table']    = $repositories_table;
            $TNGconfig['citations_table']       = $citations_table;
            $TNGconfig['events_table']          = $events_table;
            $TNGconfig['eventtypes_table']      = $eventtypes_table;
            $TNGconfig['reports_table']         = $reports_table;
            $TNGconfig['trees_table']           = $trees_table;
            $TNGconfig['notelinks_table']       = $notelinks_table;
            $TNGconfig['xnotes_table']          = $xnotes_table;
            $TNGconfig['saveimport_table']      = $saveimport_table;
            $TNGconfig['users_table']           = $users_table;
            $TNGconfig['temp_events_table']     = $temp_events_table;
            $TNGconfig['tlevents_table']        = $tlevents_table;
            $TNGconfig['branches_table']        = $branches_table;
            $TNGconfig['branchlinks_table']     = $branchlinks_table;
            $TNGconfig['assoc_table']           = $assoc_table;
            $TNGconfig['mostwanted_table']      = $mostwanted_table;
            $TNGconfig['rootpath']              = $rootpath;
            $TNGconfig['homepage']              = $homepage;
            $TNGconfig['tngdomain']             = $tngdomain;
            $TNGconfig['sitename']              = $sitename;
            $TNGconfig['site_desc']             = $site_desc;
            $TNGconfig['target']                = $target;
            $TNGconfig['language']              = $language;
            $TNGconfig['charset']               = $charset;
            $TNGconfig['maxsearchresults']      = $maxsearchresults;
            $TNGconfig['lineendingdisplay']     = $lineendingdisplay;
            $TNGconfig['lineending']            = $lineending;
            $TNGconfig['mediapath']             = $mediapath;
            $TNGconfig['photopath']             = $photopath;
            $TNGconfig['documentpath']          = $documentpath;
            $TNGconfig['emailaddr']             = $emailaddr;
            $TNGconfig['dbowner']               = $dbowner;
            $TNGconfig['time_offset']           = $time_offset;
            $TNGconfig['requirelogin']          = $requirelogin;
            $TNGconfig['treerestrict']          = $treerestrict;
            $TNGconfig['livedefault']           = $livedefault;
            $TNGconfig['ldsdefault']            = $ldsdefault;
            $TNGconfig['chooselang']            = $chooselang;
            $TNGconfig['photosext']             = $photosext;
            $TNGconfig['showextended']          = $showextended;
            $TNGconfig['thumbprefix']           = $thumbprefix;
            $TNGconfig['thumbsuffix']           = $thumbsuffix;
            $TNGconfig['thumbmaxh']             = $thumbmaxh;
            $TNGconfig['thumbmaxw']             = $thumbmaxw;
            $TNGconfig['newmedialinks']         = $newmedialinks;
            $TNGconfig['customheader']          = $customheader;
            $TNGconfig['customfooter']          = $customfooter;
            $TNGconfig['custommeta']            = $custommeta;
            $TNGconfig['gendexfile']            = $gendexfile;
            $TNGconfig['headstonepath']         = $headstonepath;
            $TNGconfig['historypath']           = $historypath;
            $TNGconfig['backuppath']            = $backuppath;
            $TNGconfig['maxgedcom']             = $maxgedcom;
            $TNGconfig['change_cutoff']         = $change_cutoff;
            $TNGconfig['change_limit']          = $change_limit;
            $TNGconfig['time_offset']           = $time_offset;
            $TNGconfig['defaulttree']           = $defaulttree;
            $TNGconfig['nonames']               = $nonames;
            $TNGconfig['notestogether']         = $notestogether;
            $TNGconfig['lnprefixes']            = $lnprefixes;
            $TNGconfig['lnpfxnum']              = $lnpfxnum;
            $TNGconfig['specpfx']               = $specpfx;
            $TNGconfig['nameorder']             = $nameorder;

            $TNGconfig = array_merge($tngconfig, $TNGconfig);

            // version.php
            include(dirname($TNG['configfile']) . "/version.php");
            $TNGconfig['tng_title']             = $tng_title;
            $TNGconfig['tng_version']           = $tng_version;
            $TNGconfig['tng_copyright']         = $tng_copyright;
            $TNGconfig['tng_date']              = $tng_date;

        }
    }
    return $TNGconfig;
}

/*
* getperson
* @param  id     the personID in TNG database
* @param  tree   the gedcom/tree that the person belongs to.  Default is primary person if set
* @return array  information on the person, false on error
*/
function TNGz_userapi_getperson($args)
{
    // Check arguments.  Try primary person if no arguments given
    $args['id']   = (isset($args['id']  )    ) ? $args['id']   : pnModGetVar('TNGz', '_personID',   '' ) ;
    $args['id']   = (strlen($args['id'] ) > 1) ? $args['id']   : false ;
    $args['tree'] = (isset($args['tree'])    ) ? $args['tree'] : pnModGetVar('TNGz', '_persontree', '0') ;
    $args['tree'] = ($args['tree'] != "0"    ) ? $args['tree'] : false ;
       
    if (!$args['id'] || !$args['tree']) {
        return false;
    }
    
    if (!$TNGconn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return false; // can't get to the data
    }
    
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
       return false; // can't get to the data
    }
    
    $query = "SELECT * FROM ". $TNG['people_table'] . " WHERE personID = '".$args['id']. "' AND gedcom = '".$args['tree'] ."'";

    if (!$result = $TNGconn->Execute($query) ) {
        return false; 
    }

    if($result->RecordCount()>0) {
        $row = $result->fields;
    } else {
       return false;
    }

    $locnameorder = $row['nameorder'] ? $row['nameorder'] : ($TNGconfig['nameorder']  ? $TNGconfig['nameorder']  : 1);
    $lastname = trim( $row['lnprefix']." ".$row['lastname'] );
    $title = $row['title'];

    // Full name
    $firstname = trim( $title." ".$row['firstname'] );
    if( $locnameorder == 1 ) {
        $namestr = trim("$firstname $lastname");
    } else {
        $namestr = trim("$lastname $firstname");
    }
    if( $row[suffix] ) {
        $namestr .= ", $row[suffix]";
    }
    $row['fullname'] = $namestr;
    
    $row['id']   = $args['id'];   // Add for easier self reference, should be the same as personID
    $row['tree'] = $args['tree']; // Add for easier self reference, should be the same as gedcom

    return $row;
}

function TNGz_userapi_ShowPage($args)
{

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_OVERVIEW)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    // Check Render arguments
    $TNGrenderpage = (isset($args['render']))   ? $args['render']   : true;  // Default value
    $TNGrenderpage = (FormUtil::getPassedValue('tngprint', false, 'GET'))? false : $TNGrenderpage; //Don't wrap print pages

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

    // Get information for the module
    $TNGz_modinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));
    
    // Set module display name
    $TNGz_modname = "TNGz";
    // set the module name to the display name if this is present
    if (isset($TNGz_modinfo['displayname']) && !empty($TNGz_modinfo['displayname'])) {
        $TNGz_modname = rawurlencode($TNGz_modinfo['displayname']);
    } 
    
    //////////////////////////////////////////////////////
    // Language Settings
    //////////////////////////////////////////////////////
    $newlanguage = pnModAPIFunc('TNGz','user','GetTNGlanguage');

    // Now fix lang parameter in URL
    // As of Zikula 1.2.0, the lang parameter is used to pass the non-site default language
    // But for TNG, lang had been used as by folks attacking sites, and now assumes you are bad if you use
    // So the solution is to save the value at this point, remove it from $_GET and then restore it after TNG is done.
    unset($Save_GET_lang);   // Being safe.  Should only be set if using.
    $Pass_lang = "";         // Used to pass on lang parameter in the TNG URL
    if (isset($_GET['lang'])) {
        $Pass_lang = "&lang=" . $_GET['lang']; // pass on the language parameter
        $Save_GET_lang = $_GET['lang'];       // save for later
        unset($_GET['lang']);                 // Clear so TNG does not choke
    }

    //////////////////////////////////////////////////////
    // Get the TNG configuration information
    //////////////////////////////////////////////////////
    // NOTE: This normally is done for TNG in the begin.php file.  We are doing it here
    //       Can't just use TNGz_userapi_TNGconfig()
    //       TNG files need config file loaded so all the variables are available to it
    $cms[tngpath] = $TNG['TNGpath'];
    if (file_exists($TNG['configfile']) ) {
        include $TNG['configfile'];  
        $TNGhomepage = $homepage;
    } else {
        return LogUtil::registerError("Error accessing TNG config file.");
    }

    // Now that TNG config file is loaded, update the cms parameters (which at one time was in customconfig.php)
    $cms[auto]       = true;
    $cms[TNGz]       = 1;
    $cms[support]    = "zikula";
    $cms[module]     = "TNGz";
    
                        // TODO: Need to figure out the best way to do this
    //$cms[url]        = "index.php?module=$TNGz_modname&func=main&show";
    //$cms[url]        = "index.php?module=$TNGz_modname&amp;func=main&amp;show";
    $cms[url]          = "index.php?module=$TNGz_modname".$Pass_lang."&show";
    //$cms[url]        = rtrim(pnModURL('TNGz','user','main', array('show'=>'')),"=");
                         // these are not as good
    //$cms[url]        = _TNGZ_PREFIX;
    //$cms[url]        = pnModURL('TNGz','user','main')."&amp;show"; //Some part of TNG does not work with short URLs enabled

    
    $cms[tngpath]    = $TNG['directory']. "/";
    //$cms[adminurl]   = "index.php?module=TNGz&func=admin";
    // using pnModURL
    $cms[adminurl]      = DataUtil::formatForDisplay(pnModURL('TNGz','admin','TNGadmin'));
    $cms[noend]      = true; // Tell TNG to not include end.php file
    $cms[cloaklogin] = "Yes";
    $cms[credits]    = "<!-- TNGz --><br />";

    // Fix up file paths to look in the right place
    $homepage = ($dot = strrchr($homepage, '.')) ? substr($homepage, 0, -strlen($dot)): $homepage;// strip .php or .html
    $rootpath        = $TNG['SitePath'] . "/";                     // Overwrite setting from TNG configuration

    if ($TNGrenderpage) {
        $custommeta = dirname(realpath(__FILE__)) . "/meta.php";  // Overwrite setting from TNG configuration
    } else {
        $custommeta  = $cms[tngpath] . $custommeta ;
    }

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
            foreach ( $the_globals as $key=>$value ) {
                if ( in_array($key, array('cms','lang', 'language', 'mylanguage', 'session_language', 'rootpath')) ) {
                    // die("sorry!");  // OK, so let's just remove instead of dying
                    unset($_GET[$key]);
                } else {
                    ${$key} = $value;
                }
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

    //////////////////////////////////////////////////////
    // Get User Login information
    //////////////////////////////////////////////////////
    if (pnUserLoggedIn()) {
        $TNGusername = pnUserGetVar('uname');
    } else {
        if (pnModGetVar('TNGz', '_guest') == 1) {
            $TNGusername = pnModGetVar('TNGz', '_gname');
            $TNGusername = ($TNGusername!="") ? $TNGusername: "Guest";
        } else {
            pnRedirect(pnModURL('Users','user','loginscreen')) ;
        }
    }

    //////////////////////////////////////////////////////
    // Create User if needed
    //////////////////////////////////////////////////////
    $ok = pnModAPIFunc('TNGz','user','ModifyCreateUser');
    if (!$ok ) {
        return LogUtil::registerError("Error Creating User information. ");
    }

    //////////////////////////////////////////////////////
    // Get User Information from TNG database
    //////////////////////////////////////////////////////
    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
           return LogUtil::registerError("Error accessing TNG database.");
    }
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
        if (!$result = $TNG_conn->Execute($query) ) {
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
        if ($newlanguage) {
            session_register('session_language');
            $session_language = $_SESSION[session_language] = $newlanguage;
        }
        session_register('lastpage');
        $logged_in           = $_SESSION[logged_in]         = 1;
        $allow_edit_db       = $_SESSION[allow_edit_db]     = $row[allow_edit];
        $allow_add_db        = $_SESSION[allow_add_db]      = $row[allow_add];
        $tentative_edit_db   = $_SESSION[tentative_edit_db] = $row[tentative_edit];
        $allow_delete_db     = $_SESSION[allow_delete_db]   = $row[allow_delete];
        if( $allow_edit_db || $allow_add_db || $allow_delete_db )
            $allow_admin_db  = $_SESSION[allow_admin_db]    = 1;
        else
            $allow_admin_db  = $_SESSION[allow_admin_db]    = 0;
        if ( !$livedefault ) //depends on permissions
            $allow_living_db = $_SESSION[allow_living_db]   = $row[allow_living];
        elseif ( $livedefault == 2 ) //always do living
            $allow_living_db = $_SESSION[allow_living_db]   = 1;
        else //never do living
            $allow_living_db = $_SESSION[allow_living_db]   = 0;
        $allow_ged_db        = $_SESSION[allow_ged_db]      = $row[allow_ged];
        if( !$ldsdefault ) //always do lds
            $allow_lds_db    = $_SESSION[allow_lds_db]      = 1;
        elseif( $ldsdefault == 2 )  //depends on permissions
            $allow_lds_db    = $_SESSION[allow_lds_db]      = $row[allow_lds];
        else  //never do lds
            $allow_lds_db    = $_SESSION[allow_lds_db]      = 0;
        $assignedtree        = $_SESSION[assignedtree]      = $row[gedcom];
        $assignedbranch      = $_SESSION[assignedbranch]    = $row[branch];
        $currentuser         = $_SESSION[currentuser]       = $row[username];
        $currentuserdesc     = $_SESSION[currentuserdesc]   = $row[description];
        $session_rp          = $_SESSION[session_rp]        = $rootpath;
    }

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

    // Short URL Filter
    //$shorturls     = pnConfigGetVar('shorturls');
    //$shorturlstype = pnConfigGetVar('shorturlstype');
    //if ($shorturls && $shorturlstype == 0 ) { // This has problems, so disable for now
        //$TNGoutput = preg_replace_callback( "/(\s+href\s*=\s*[\"\'])(.*)([\"\'])/iU", "TNGz_userapi_ShortURLencode", $TNGoutput);
    //}

    $HTMLvalidation = true; // Make this optional in the future when TNG fixes

    if($TNGrenderpage) {
        // Clean up TNG HTML, remove HTML validation errors, etc.

        // Remove TNG <title> tag.  Each page should only have one and Zikula provides.
        $patterns[]     = "/<title>(.*)<\/title>/iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";

        // Now Get Title information to add to Zikula title
        if (preg_match("/<meta name=\"Keywords\" content=\"(.+)\"/", $TNGoutput, $tng_title) ) {
            $GLOBALS['info']['title'] = $tng_title[1];
        }

        // Remove the <meta> tags.  TNG's not in the right place and do not add much new informaiton
        $patterns[]     = "/<meta (.*)>/iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
    
        // Remove the TNG's DOCTYPE (Each page should only have one, and Zikula provides)
        $patterns[]     = "|<!DOCTYPE[^\"]+\"([^\"]+)\"[^\"]+\"([^\"]+)/([^/]+)\.dtd\">|i";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
    
        // Remove TNG javascripts and load files into head (also using Zikula versions).
        $patterns[]     = "/<script(.*)prototype.js(.*)<\/script>/iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
        PageUtil::AddVar('javascript', 'javascript/ajax/prototype.js');

        $patterns[]     = "/<script(.*)scriptaculous.js(.*)<\/script>/iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
        PageUtil::AddVar('javascript', 'javascript/ajax/scriptaculous.js');

        $patterns[]     = "/<script(.*)net.js(.*)<\/script>/iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
        PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/net.js');

        $patterns[]     = "/<script(.*)litbox.js(.*)<\/script>/iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
        PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/litbox.js');

        // Question: What happens if Zikula and TNG use different versions of these libraries
        //           Could this cause odd behavior in TNG?

        // Take care of embedded <style>, move up into Zikula head
        $patterns[]     = "|\<style (.*)\<\/style\>|isU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
        preg_match_all("|\<style (.*)\<\/style\>|isU", $TNGoutput, $matches, PREG_SET_ORDER);
        foreach($matches as $match){
            PageUtil::AddVar('rawtext', $match[0]);
        }
        
        // Take care of embedded <link>, move up into Zikula head
        $patterns[]     = "|\<link (.*)\/\>|iU";
        //$replacements[] = "<!-- $0 -->\n";
        $replacements[] = "";
        preg_match_all("|\<link (.*)\/\>|iU", $TNGoutput, $matches, PREG_SET_ORDER);
        foreach($matches as $match){
            PageUtil::AddVar('rawtext', $match[0]);
        }

        if ($HTMLvalidation) {
            // Fix up href and onclick for HTML validation
            $TNGoutput = preg_replace_callback(
               '/(href\=\"(.*)\")|(onclick\=\"(.*)\")|(onchange\=\"(.*)\")/iU',
                create_function(
                    '$matches',
                    'return str_replace("&","&amp;", str_replace("&amp;", "&", $matches[0]) );'
                ),
                $TNGoutput);

            // Add CDATA for scripts, but only do if not already there.  Also don't do if just giving src file name
            $TNGoutput = preg_replace_callback(
                     "|(\<script[ ]+.*\>)(.*)(\<\/script\>)|isU",
                     create_function(
                        '$matches',
                        'if (strpos($matches[2],"<![CDATA[")===false && strpos($matches[1],"src=")===false){
                            return $matches[1]."\n/*<![CDATA[*/\n".$matches[2]."\n/*]]>*/\n".$matches[3];
                         } else {
                            return $matches[0];
                         }'
                    ),
                    $TNGoutput);
        }

        // Now finish the clean up
        ksort($patterns);      // The sorts are recommended to make sure the pattern/replacement are aligned
        ksort($replacements);
        $TNGoutput = preg_replace($patterns, $replacements, $TNGoutput);
    }
    
    if ($TNGshowpage=="tngrss.php" && $HTMLvalidation) {
        // Fix up links
        $TNGoutput = preg_replace_callback(
                "|(\<link\>)(.*)(\<\/link\>)|isU",
                create_function(
                    '$matches',
                    'return str_replace("&","&amp;", str_replace("&amp;", "&", $matches[0]) );'
                ),
                $TNGoutput);
    }

  
    // Restore the original $_GET['lang'] if needed
    if (isset($Save_GET_lang)) {
        $_GET['lang'] = $Save_GET_lang;
    }


    //////////////////////////////////////////////////////
    // Now get ready to display
    //////////////////////////////////////////////////////

    if (!$TNGrenderpage) {
        echo $TNGoutput;   // do not wrap with Zikula
        return true;       // signal output has already been displayed
    }

    // Add TNG output to normal Zikula display
    $pnRender = pnRender::getInstance('TNGz', false);
    $pnRender->assign('TNGoutput'    , $TNGoutput);
    $pnRender->assign('TNGtitle'     , $tng_title[1] );
    $pnRender->assign('TNGzVersion'  , $TNGz_modinfo['version'] );

    return $pnRender->fetch('TNGz_user_main.htm');

}

 /**
 * Get the language to be used for TNG
 * @return false on error or the name of the TNG language
 */
function TNGz_userapi_GetTNGlanguage($args)
{
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');
    //////////////////////////////////////////////////////
    // Language Settings
    //////////////////////////////////////////////////////
    $languages3 = array(
    // Zikula => TNG
        'deu' => 'German',
        'fra' => 'French',
        'pol' => 'Polish',
        'ita' => 'Italian',
        'nld' => 'Dutch',
        'esp' => 'Spanish',
        'afr' => 'Afrikaans',
        'hrv' => 'Croatian',
        'ces' => 'Czech',
        'dan' => 'Danish',
        'fin' => 'Finnish',
        'ell' => 'Greek',
        'isl' => 'Icelandic',
        'nob' => 'Norwegian',
        'nno' => 'Norwegian',
        'rom' => 'Romanian',
        'rus' => 'Russian',
        'srp' => 'Serbian',
        'swe' => 'Swedish',
        'por' => 'Portuguese',
         // Non-ISO entries are written as x_[language name]
        'x_brazilian_portuguese' => 'PortugueseBR',
        'eng' => 'English'
    );

    $languages2 = array(
        'de'    => 'German-UTF8',
        'fr'    => 'French-UTF8',
        'pl'    => 'Polish-UTF8',
        'it'    => 'Italian-UTF8',
        'dk'    => 'Dutch-UTF8',
        'es'    => 'Spanish-UTF8',
        'af'    => 'Afrikaans-UTF8',
        'hr'    => 'Croatian-UTF8',
        'cz'    => 'Czech-UTF8',
        'da'    => 'Danish-UTF8',
        'fi'    => 'Finnish-UTF8',
        'el'    => 'Greek-UTF8',
        'is'    => 'Icelandic-UTF8',
        'nb'    => 'Norwegian-UTF8',
        'nn'    => 'Norwegian-UTF8',
        'ro'    => 'Romanian-UTF8',
        'ru'    => 'Russian-UTF8',
        'sr'    => 'Serbian-UTF8',
        'sv'    => 'Swedish-UTF8',
        'pt'    => 'PortugueseBR-UTF8',
        'en'    => 'English-UTF8'
    );
    

    // Check legacy 3 character language codes  
    $zikulalang3 = ZLanguage::getLanguageCodeLegacy(); // get old 3 character language code used in Zikula
    if ( isset($languages3[$zikulalang3]) ) { // is it defined?
        // If the Zikula language has been installed in TNG, then use it
        // NOTE: May want to add a Zikula Administration setting to turn this on/off
        // QUESTION: Is there a TNG setting that must be enabled for this to work?
        if (file_exists($TNG['directory']. "/" . $languages3[$zikulalang3] . "/text.php") ) {
            $newlanguage3 = $languages3[$zikulalang3];
        } else {
            $newlanguage3 = false;
        }
    }

    // Check new 2 character language codes
    $zikulalang2 = ZLanguage::getLanguageCode();       // get new 2 character language code used in Zikula
    if ( isset($languages2[$zikulalang2]) ) { // is it defined?
        // If the Zikula language has been installed in TNG, then use it
        // NOTE: May want to add a Zikula Administration setting to turn this on/off
        // QUESTION: Is there a TNG setting that must be enabled for this to work?
        if (file_exists($TNG['directory']. "/" . $languages2[$zikulalang2] . "/text.php") ) {
            $newlanguage2 = $languages2[$zikulalang2];
        } else {
            $newlanguage2 = false;
        }
    }
    
    // echo "<!--" . "languages: $newlanguage2 $newlanguage3 " . echo "-->\n";
    
    if ($newlanguage2) {  // prefer this if set
       return $newlanguage2;
    }
    if ($newlanguage3) {  // fall back to legacy if set
       return $newlanguage3;
    }
    return false;  // otherwise, default to use language setting from TNG
}


 /**
 * Get the text strings defined by TNG
 * @param  str args['textpart'] The type of text to retrieve from TNG
 * @return false on error or an array with the TNG text elements
 */
function TNGz_userapi_GetTNGtext($args)
{
    static $TNGz_text         = array(); // holds the text values
    static $TNGz_text_fetched = array(); // keeps track of the texttypes visited
    static $languagepath;
    
   // Get parameter
    $valid     = array( 'common', 'sources', 'language', 'gedcom', 'getperson', 'relate', 'familygroup','pedigree',
                        'search', 'reports', 'showlog', 'headstones', 'showphoto', 'surnames','places', 'whatsnew',
                        'timeline', 'trees', 'login', 'stats', 'notes', 'help', 'install');  // first in list is default
    $texttype  = (in_array($args['textpart'], $valid)) ? $args['textpart'] : $valid[0];

    if (isset($TNGz_text_fetched[$texttype])){
        return $TNGz_text; // already have it, so just give it 
    }

    global $text;
    $text    = array();
    
    if (!isset($languagepath)){  // This should only have to run once
        $TNG        = pnModAPIFunc('TNGz','user','GetTNGpaths');
        $mylanguage = pnModAPIFunc('TNGz','user','GetTNGlanguage');
        $languagepath = $TNG['directory']. "/" . $mylanguage . "/";

        // get language files that do not depend upon texttype
        global $dates, $admtext;
        $dates   = array();
        $admtext = array();
        include_once($languagepath . "alltext.php");
        include_once($languagepath . "admintext.php");
        include_once($languagepath . "cust_text.php");
        $TNGz_text = array_merge($dates, $admtext, $text);
    }

    $rootpath = ""; // kludge for text.php 
    global $textpart;
    $textpart  = ($texttype == 'common') ? '' : $texttype;
    include($languagepath . "text.php");

    $TNGz_text = array_merge($text, $TNGz_text); // now add to the list we already have

    $TNGz_text_fetched[$texttype] = true;        // flag that we have this texttype
    $TNGz_text_fetched['common']  = true;        // flag common too since always get these with others

    return $TNGz_text;
}

 /**
 * Initialize cache if needed
 * @return str location of the cache if it exists, false otherwise
 */
function TNGz_userapi_CacheInit($args)
{
    $cache = DataUtil::formatForOS(pnConfigGetVar('temp')) . "/" . DataUtil::formatForOS(pnModGetName());
    if (is_dir($cache)) {
        return $cache;  
    } else {
        Loader::loadClass('FileUtil');
        if (FileUtil::mkdirs($cache)) {
          if (FileUtil::writeFile($cache . "/index.html", " ") ) {
              return $cache;
          }
        }
    }
    return false;
}

 /**
 * Does the cache exist
 * @return bool true if it does, false if not
 */
function TNGz_userapi_CacheExists($args)
{
    $cache = DataUtil::formatForOS(pnConfigGetVar('temp')) . "/" . DataUtil::formatForOS(pnModGetName());
    if (is_dir($cache)) {
        return true;  
    }
    return false;
}


 /**
 * Delete cache
 * @return str true if deleted (or does not exist), false otherwise
 */
function TNGz_userapi_CacheDelete($args)
{
    $cache = pnModAPIFunc('TNGz','user','CacheInit');
    if (!$cache) {
        return true; // It didn't exist = deleted = successfull
    }
    if (!is_dir($cache)) {
        return true; // It didn't exist = deleted = successfull
    } else {
        Loader::loadClass('FileUtil');
        return FileUtil::deldir($cache);
    }
}


 /**
 * Get information from the TNGz cache if it exists and is still valid
 * @param  str args['item'] The cache entry name (filename)
 * @return str cached item if exists and still valid, otherwise false
 */
function TNGz_userapi_Cache($args)
{
    static $TNGz_cache, $TNG_updated, $TNGz_useDBtime, $TNGz_sec2expire;

    // Get the Cache settings
    if (!isset($TNGz_useDBtime)) { // do this only once
        $TNGz_useDBtime  = pnModGetVar('TNGz', '_cachedb',  0 );
        $TNGz_sec2expire = pnModGetVar('TNGz', '_cachesec', 0 );
    }
    if ( $TNGz_useDBtime == 0 && $TNGz_sec2expire == 0 ) {
        return false;  // disabled cache
    }

    // Make sure Cache exists
    if (!isset($TNGz_cache)) { //do this only once
        $TNGz_cache = pnModAPIFunc('TNGz','user','CacheInit');
    }
    if (!$TNGz_cache) {
        return false;  // can't use cache for some reason
    }

    $time_zero = '0000-00-00 00:00:00';

    // If needed, get latest TNG database update time
    if (!isset($TNG_updated) &&  $TNGz_useDBtime !=0) { // do this only once

        $TNG_updated = $time_zero; // Initialize, also a flag if stays at $time_zero
    
        if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
           return false; // can't get to the data
        }

        // Set the tables we want to check
        $TNG_tables['people']   = $TNG['people_table'];
        $TNG_tables['family']   = $TNG['families_table'];
        $TNG_tables['children'] = $TNG['children_table'];
        $TNG_tables['places']   = $TNG['places_table'];
        $TNG_tables['events']   = $TNG['events_table'];
        /* Others that could be checked include:
          albums_table, album2entities_table, albumlinks_table, media_table, medialinks_table,
          mediatypes_table, address_table, languages_table, cemeteries_table, states_table,
          countries_table, sources_table, repositories_table, citations_table
        */

        // Now actually go find the last update time stamp on various TNG tables
        if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
           return false; // can't get to the data
        }
        foreach($TNG_tables as $table){
            // now get the update time for the table
            $query = "SHOW TABLE STATUS LIKE '$table'";
            
            if (!$result = $TNG_conn->Execute($query) ) {
                return false;
            }
            for (; !$result->EOF; $result->MoveNext()) {
                $table_updated  = $result->fields['Update_time'];
                $TNG_updated =  ($table_updated > $TNG_updated) ? $table_updated : $TNG_updated;
            }
            $result->Close();
        }
    }

    // Can't cache if settings and values don't make sense
    if ( $TNG_updated == $time_zero && $TNGz_useDBtime !=0 && $TNGz_sec2expire == 0) {
        // Can't tell database update time, and elapsed time is off, so don't let it be cached
        return false;
    }

    // set the cache expire time as a timestamp.  Note one of these two must be !=0 or would not be here
    $time2expire  = ($TNGz_sec2expire == 0 ) ? 0 : time() - $TNGz_sec2expire; // off or elapsed time?
    $TNGdbTime    = ($TNGz_useDBtime  == 0 ) ? 0 : strtotime($TNG_updated);   // off or DB updated time?
    $cache_expire = max( $TNGdbTime, $time2expire ); // pick the latest one 

    // OK so far, so return the file contents if it exists and cache not expired
    $item = DataUtil::formatForOS($args['item']);
    $file = $TNGz_cache . "/" . $item;
    if (file_exists($file)) {
        $filetime = filemtime($file);
        if ($filetime > $cache_expire) {
            Loader::loadClass('FileUtil');
            return FileUtil::readFile($file);  // OK to return cached data
        } else {
            // TNG data is newer or cache has expired, so clear out the cache and start over
            pnModAPIFunc('TNGz','user','CacheDelete');
            return false;
        }
    }
    return false;
}

 /**
 * Update the TNGz cache with the item
 * @param  str args['item'] The cache entry name (filename)
 * @param  str args['data'] The information to store in the cache
 * @return writeFile code
 */
function TNGz_userapi_CacheUpdate($args)
{
    if (!isset($args['data'])){
        $args['data'] = "";
    }

    static $TNGz_cache;
    if (!isset($TNGz_cache)) { //do this only once
        $TNGz_cache = pnModAPIFunc('TNGz','user','CacheInit');
    }
    if (!$TNGz_cache) {
        return false;
    }

    $item       = DataUtil::formatForOS($args['item']);
    $path_parts = pathinfo($item);
    $ext        = $path_parts['extension'];
    if ( $ext == 'htm' || $ext == 'html' ) {
        $timestamp = date('Y-m-d H:i:s', time() );
        $header    = "\n<!-- Cache Start $timestamp $item  -->\n";
        $footer    = "\n<!-- Cache End   $timestamp $item  -->\n";
    } else {
        $header = "";
        $footer = "";
    }
    Loader::loadClass('FileUtil');
    return FileUtil::writeFile($TNGz_cache . "/" . $item , $header . $args['data'] . $footer);
}

 /**
 * Get the global variables declared by TNG
 * @param  str args['dir'] The directory containing the TNG php programs
 * @return false on error or the comma separated list of global variables used by TNG
 */
function TNGz_userapi_GetTNGglobals($args)
{

    $dir = $args['dir'];
    if (!is_dir($dir))  { // Make sure it is a real directory
        return false;  // Error
    }
    if (!$dh  = opendir($dir) ) { //Open the directory
        return false;  // Error
    }
    while (false !== ($filename = readdir($dh))) { // Get all the files in the directory
        $files[] = $filename;
    }
    closedir($dh);

    //////////////////////////////////////////////////////
    // Now scan through all the .php files looking for global variable declarations
    //////////////////////////////////////////////////////
    $allglobals = array();                                                           // Holder for the global variables found
    foreach ($files as $filename) {                                                  // Look through each file
        if ( strpos( $filename, ".php") ) {                                          // only need to check .php files
        if ($thefile = file_get_contents("$dir/$filename") ) { // read in the file, keep going if no error
                $globalmatches = array();                                            // holder for all the global statements found
                preg_match_all('/[\s|^]global[\s]+(.*)\;/',$thefile,$globalmatches); // Look for php global variable statements
                $match = $globalmatches[0];                                          // all the matches in an array
                foreach ($match as $line) {                                          // Look through each of the statements found
                    $line = preg_replace('/[\s]*global[\s]+/', "", $line);           // take out "global" at the beginning (and spaces)
                    $line = preg_replace('/[\s]*\;/', "", $line);                    // take out ; at the end (and spaces)
                    $theglobals = preg_split('/[\s]*,[\s]*/', $line);                // now get each of the global variables listed
                    foreach ($theglobals as $aglobal) {// Look at each global variable
                    if ( !$allglobals[$aglobal] )  {//   if not already found, add to the list
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
function TNGz_userapi_ranpass($args)
{
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

        // Check to be sure we can get to the TNG information
        if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
            return LogUtil::registerError("Error accessing TNG config file.");
        }
        if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
            return LogUtil::registerError("Error accessing TNG database.");
        }

        $query     =  "SELECT userID, email, realname, website, password FROM ". $TNG['users_table'] ." WHERE username = '$userid' ";
        if (!$result = $TNG_conn->Execute($query) ) {
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
                if ($use_password) {
                    $TNG_pwd_safe = $u['pass']; // can use same md5 password
                } else { // otherwise generate a random one just for TNG
                    $TNG_pwd_safe  = md5(pnModAPIFunc('TNGz','user','ranpass',array()) );
                }

                if ($TNG_name != "") {
                    $TNG_desc = $TNG_name;
                } else {
                    $TNG_desc = $TNG_user;
                }
            }
            if ( ($loggedin && $TNG_create == 1) || (!$loggedin && $guest == 1)) {
                $adding  = "INSERT INTO " . $TNG['users_table'] . " ";
                $adding .= "(";
                $adding .= "description, username, realname  , email      , website      , gedcom , allow_living, allow_ged  , allow_lds, lastlogin ";
                $adding .= ( $use_password ) ? ", password " : "";
                $adding .=  ") VALUES ";
                $adding .= "(";
                $adding .= "'$TNG_desc','$TNG_user','$TNG_name','$TNG_email','$TNG_website','$TNG_db','$TNG_living' ,'$TNG_gedcom','$TNG_lds' , $TNG_conn->DBTimeStamp(NOW())";
                $adding .=  ( $use_password ) ? ",'$TNG_pwd_safe'" : "";
                $adding .= ") ";

                if (!$added = $TNG_conn->Execute($adding) ) {
                        return(false);
                }
                $added->Close();
                $return_code = 2;
            }
        } elseif ($TNG_sync == 1 && $loggedin ) {
            // The user was found, so check for updates
            $TNG_changed = false;
            $theUser = $result->fields;
            //list($TNG_uid, $TNG_email, $TNG_name, $TNG_website, $TNG_password ) = $result->fields;
            $adding =  "UPDATE " . $TNG['users_table'] . " SET ";
            if ( $theUser['email'] != $u['email'] ) {
                $adding .= " email='".$u['email']."',";
                $TNG_changed = true;
            }
            if ( $theUser['realname'] != $u['name'] ) {
                $adding .= " realname='".$u['name']."',";
                $TNG_changed = true;
            }
            if ( $theUser['website'] != $u['url'] ) {
                $adding .= " website='".$u['url']."',";
                $TNG_changed = true;
            }
            if ( $theUser['password'] != $u['pass'] && $use_password) {
                $adding .= " password='".$u['pass']."',";
                $TNG_changed = true;
            }
            if ($TNG_changed == true) {
                $adding = rtrim($adding, " ,"); // take off last comma and spaces
                $adding .=  " WHERE userID='".$theUser['userID']."'";
                if (!$added = $TNG_conn->Execute($adding) ) {
                    return(false);
                } else {
                    $return_code = 1;
                }
                $added->Close();
            }
        }
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
function TNGz_userapi_CleanEmail($args)
{
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
                if ( ( ($x % $random_max ) == $random_char ) && !(strpos("@.", $char) !== false) ) {
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
                if ( ( ($x % $random_max ) == $random_char ) && !(strpos("@.", $char) !== false) ) {
                    $hex_display .= $char;  // every once in a while, keep it the same to make it harder to decode
                } else {
                    $hex_display .= '&#' . ord($char) . ';';
                }
        }

        // now put it back together
    if ($regex_type === 'text' && $text2link!=true ) {
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
function TNGz_userapi_GetTNGurl($args)
{
    $goto = (isset($args['file'])) ? $args['file']    : 'admin/index.php';
    
    //////////////////////////////////////////////////////
    // Get the TNG configuraiton information
    //////////////////////////////////////////////////////
    if(!$TNGpath = pnModAPIFunc('TNGz','user','GetTNGpaths') ) {
        return LogUtil::registerError("Error accessing TNG config file.");
    }
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
        return LogUtil::registerError("Error accessing TNG config file.");
    }
    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return pnVarPrepHTMLDisplay("Failed to find TNG database");
    }

    //////////////////////////////////////////////////////
    // Get the TNG user informaiton for the person logged in Zikula
    // NOTE: Get password from TNG just in case they are not in sync
    //////////////////////////////////////////////////////
    $username = pnUserGetVar('uname');

    $query = "SELECT * FROM ".$TNG['users_table']." WHERE username = '$username' ";
    if (!$result = $TNG_conn->Execute($query) ) {
            return LogUtil::registerError("Error accessing TNG database.");
    }
    $found = $result->RecordCount();
    if( $found == 1 ) {
        $row = $result->fields;
        $userpass = $row['password'];
    } else {
        $userpass = "";
    }
    $result->Close();

    //////////////////////////////////////////////////////
    // Now encode everything to pass on,
    //////////////////////////////////////////////////////
    $parmcheck = implode('|', array($username,$userpass, $goto) );  // Used just to get the check value
    $check=md5($paramcheck) ;  // Add another check to make sure someone doesn't just edit a few characters.
    $parm = implode('|', array($check,$username,$userpass,$goto) );
    $url = $TNGpath['directory'] . "/index_TNGz.php?parm=$parm";

    return $url;
}

/**
* Generate reference link back to TNGz items
* @param str $args['target'],      to add any special target parameters to the link
* @param str $args['description'], the text for the link
* @param str $args['func'],        the TNG file to run.  If not set, calls TNGz main
* @param str $args['url'],         if set, just provide the URL, not the whole reference
* @param str $args['...],          any other parameters are passed directly to the TNG function
* @return reference link
*/
function TNGz_userapi_MakeRef($args)
{

    unset($args['RefType']); // No longer need RefType, so remove if from the args if it is set

    $url = (isset($args['url'])) ? $args['url'] : false ;
    unset($args['url']);

    $target = (isset($args['target'])) ? $args['target'] : false ;
    unset($args['target']);
    if ( $target ) {
        $target = "target = \"$target\"";
    }

    $description = (isset($args['description'])) ? $args['description'] : "" ;
    unset($args['description']);

    $prog = (isset($args['func'])) ? $args['func'] : false ;
    unset($args['func']);

    $tree = (isset($args['tree'])) ? $args['tree'] : false ;
    unset($args['tree']);
    if ($tree) {
        $args = array_merge(array("tree"=>$tree),$args); // move to the front (so comes out first)
    }

    if ( $prog ) {
        $func = 'main';
        $args = array_merge(array("show"=>$prog),$args); // add show to the front (so comes out first)
    } else {
        $func = $prog;
        $args = array();  // just pass the rest of the parameters
    }

    $ref = DataUtil::formatForDisplay(pnModURL('TNGz', 'user', $func, $args));

    if ($url) {
        return $ref;
    } else {
        return "<a href=\"" . $ref . "\" $target >$description</a>";
    }

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
function TNGz_userapi_PhotoRef($args)
{

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
        if ($height_scale > $width_scale)
        {
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


function TNGz_userapi_getRecords($args)
{
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
    } elseif (!is_numeric($start)) {
        $limit = false;    // not a number
    } elseif ( $start < 0 ) {
        $limit = false;    // Invalid starting value given
    } else {
        $start = intval($start);
    }

    // Check out $count
    if (!isset($count)) {
        $limit = false;    // No starting value given
    } elseif (!is_numeric($count)) {
        $limit = false;    // not a number
    } elseif ( $count < 0 ) {
        $limit = false;    // Invalid starting value given
    } else {
        $count = intval($count);
    }
    // $limit is still true only if $start and $count are valid

    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return false; // can't get to the data
    }
    
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
       return false; // can't get to the data
    }
    
    if ($kind == "people") {
        $query  =  "SELECT gedcom, personID, changedate FROM ".$TNG['people_table'];
    } elseif ( $kind == "family") {
        $query   =  "SELECT gedcom, familyID, changedate FROM ". $TNG['families_table'];
    }
    if ($limit) {
        $query  .= " LIMIT ". $start . ", " . $count;

    }

    if (!$result = $TNG_conn->Execute($query) ) {
        return(false);
    }
    $thelist = array();
    if($result->RecordCount()>0) {
        for (; !$result->EOF; $result->MoveNext()) {
            $items = $result->fields;
            $items['tree'] = $items['gedcom'];
            $items['id']   = $items['personID'];
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
function TNGz_userapi_getRecordsCount($args)
{
    extract($args);

    $MaxPerMap = 2000;  // Maximum number of records to return in a sitemap.
                        // If there are more records, then a sitemapindex file will need to be generated.
                        // * Must be less than 50,000, but fewer is faster.
                        // * Minimum limited by having at most 1000 entries in a sitemapindex file.
                        //   So must be set to more than: (Total People + Total Families) / 1000
                        // * So a value of 2000 is good for TNG databases that have less than
                        //   2 million people and family records -- and is reasonably fast.
                        // * It also creates a single sitemap file for those TNG databases that have
                        //   less than 2000 records to return (People + Families).
                        // * If find value needs to change a lot, then can make a administration setting.

    $facts = array();

    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return false; // can't get to the data
    }
    
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
       return false; // can't get to the data
    }

    $query  =  "SELECT count(id) as pcount FROM ". $TNG['people_table'];
    if (!$result = &$TNG_conn->Execute($query) ) {
        return(false);
    }
    if($result->RecordCount()>0) {
        list( $facts['people'] ) = $result->fields;
    }

    $query  =  "SELECT count(id) as pcount FROM ".$TNG['families_table'];
    if (!$result = &$TNG_conn->Execute($query) ) {
        return(false);
    }
    if($result->RecordCount()>0) {
        list( $facts['family'] ) = $result->fields;
    }

    $facts['total'] = $facts['people'] + $facts['family'];
    $facts['sitemapindex'] = false;

    if ($facts['total']>$MaxPerMap) {
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


/*
* GetSurnames
* @param string args['top'] number of top surnames to return
* @return mixed array.  output['alpha'] is alphabatized.  output['rank'] by surname usage
*/
function TNGz_userapi_GetSurnames($args)
{
    $top = $args['top'];
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 50;  // Get valid value or set default

    $TNGz = pnModAPIFunc('TNGz','user','GetTNGpaths');

    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return false; // can't get to the data
    }
    
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
       return false; // can't get to the data
    }

    $cms['tngpath']    = $TNGz['directory']. "/";

    // First get all unique surnames
    $query = "SELECT ucase( $binary TRIM(CONCAT_WS(' ',lnprefix,lastname) ) ) as surnameuc, TRIM(CONCAT_WS(' ',lnprefix,lastname) ) as surname, count( ucase($binary lastname ) ) as count FROM ".$TNG['people_table']." WHERE lastname<>'' GROUP BY surname ORDER by count DESC ";
    if (!$result = $TNG_conn->Execute($query) ) {
        return pnVarPrepHTMLDisplay("Failed the TNG query");
    }

    $SurnameCount = $result->RecordCount();
    $name         = $result->fields;       // Look at first record, since already sorted by Surname Count
    $SurnameMax   = $name['count'];        // First record should have the most

    $SurnameRank  = array();

    for ($rank=1; !$result->EOF && $rank<=$top; $result->MoveNext(), $rank++) {
        $name = $result->fields;
        $name['surname']   = $name['surname'];
        $name['surnameuc'] = urlencode($name['surnameuc']);
        $name['rank']      = $rank;
        // Now assign a class to each surname based upon relative number to most used surname
        $percent = 100 * $name['count'] / $SurnameMax;
        if ($percent > 98) {
            $class = 1;
        } else if ($percent > 70) {
            $class = 2;
        } else if ($percent > 50) {
            $class = 3;
        } else if ($percent > 30) {
            $class = 4;
        } else if ($percent > 25) {
            $class = 5;
        } else if ($percent > 20) {
            $class = 6;
        } else if ($percent > 15) {
            $class = 7;
        } else if ($percent > 10) {
            $class = 8;
        } else if ($percent > 5 ) {
            $class = 9;
        } else {
            $class = 0;
        }
        $name['class'] = $class;
        $SurnameRank[]  = $name;
    }
    // Now get a array $SurnameAlpha sorted on the upper case surname
    $SurnameAlpha = $SurnameRank;
    foreach ($SurnameAlpha as $key => $row) {
        $surname[$key]  = $row['surnameuc'];
    }
    array_multisort($surname, SORT_ASC, $SurnameAlpha);

    return array( 'alpha' => $SurnameAlpha,
                  'rank'  => $SurnameRank,
                  'count' => $SurnameCount,
                  'max'   => $SurnameMax );
}


/*
* GetPlaces
* @param string args['top']  number of top places to return
* @param string args['sort'] sort order of places.  By alpha or rank
* @return mixed array of places
*/
function TNGz_userapi_GetPlaces($args)
{
    $top = $args['top'];
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 50;  // Get valid value or set default

    $validsorts = array('rank', 'alpha');  // first in list is the default
    $sort = (in_array($args['sort'], $validsorts))? $args['sort'] : $validsorts[0];

    $TNGz = pnModAPIFunc('TNGz','user','GetTNGpaths');

    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return pnVarPrepHTMLDisplay("Failed to find TNG database");
    }
    
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
       return pnVarPrepHTMLDisplay("Failed to find TNG database");
    }

    $cms['tngpath']    = $TNGz['directory']. "/";

    $thePlaces = array();

    $query = "SELECT distinct trim(substring_index(place,',',-1)) as myplace, count(distinct place) as placecount FROM ".$TNG['places_table']." WHERE trim(substring_index(place,',',-1)) != \"\" GROUP BY myplace ORDER by placecount DESC LIMIT $top";
    if (!$result = $TNG_conn->Execute($query) ) {
        return pnVarPrepHTMLDisplay("Failed the TNG query");
    }
    $count = 1;
    for (; !$result->EOF; $result->MoveNext()) {
        $place = $result->fields;
        $place2 = urlencode($place['myplace']);
        if( $place2 != "" ) {
            $query = "SELECT count(distinct place) as placecount FROM ".$TNG['places_table']." WHERE place = \"$place[myplace]\"";
            if (!$result2 = &$TNG_conn->Execute($query) ) {
                return pnVarPrepHTMLDisplay("Failed to TNG query");
            }
            $countrow = $result2->fields;
            $specificcount = $countrow['placecount'];

            $searchlink = ($specificcount) ? " <a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array('show'=>'placesearch', 'psearch'=>$place2))). "\"><img src=\"". $cms['tngpath']. "tng_search_small.gif\" border=\"0\" alt=\"\" width=\"9\" height=\"9\" /></a>" : "";
            $name = ($place['placecount'] > 1 || !$specificcount) ? "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array('show'=>'places-oneletter', 'offset'=>'1', 'psearch'=>$place2)))."\">" . DataUtil::formatForDisplay($place['myplace']) . "</a> (".$place['placecount'].")" : $place['myplace'];
            $thePlaces[$name] = array('rank'=> $count, 'name'=>$name, 'count'=> $place['placecount'], 'link'=>$searchlink, 'place'=>$place['myplace']);
            $count++;
        }
    }

    if ($sort == 'alpha') {
        ksort($thePlaces);
    }

    return $thePlaces;
}


/**
* Change all local TNGz hrefs in page to short URLs (directory style)
* @param  array $args     $matches[0] = whole string
*                           $matches[1] = '<a href="'
*                           $matches[2] = the url to convert
*                           $matches[3] = '" >'
* @return string  html anchor with TNGz href converted to a short URL (directory style)
*/
/* Disable for now 
function TNGz_userapi_ShortURLencode($matches)
{
    global $cms;

    if (strpos($matches[0], $cms[url] ) === false) {
        return $matches[0];   // The URL is not for TNGz, so leave alone
    }

    list($garbage, $params) = explode($cms[url], $matches[2], 2);
    //echo "<pre>".$cms[url].":".$params." |".$matches[0]." ".$matches[1]." ".$matches[2]." ".$matches[3]."</pre>";
    $args = array();
    if ( strpos($params, "=" ) !== false ) { // must have at least one = or just have show by itself
        $params = "show=".ltrim($params, " =");
        $pairs = explode('&', html_entity_decode(urldecode($params)));
        $args=array();
        foreach ($pairs as $pair) {
            $x = explode('=', $pair);
            $args[$x[0]] = $x[1];
        }
    }

    // Just in case, remove those which should not be passed as args
    unset($args['module']);
    unset($args['func']);
    unset($args['type']);

    $url = pnModAPIFunc('TNGz', 'user', 'encodeurl', array('modname' => 'TNGz', 'func' => 'main', 'args' => $args));
    return $matches[1].$url.$matches[3];
}
*/

/**
 * form custom url string
 *
 * @return string custom url string
 */
 /* Disable for now
function TNGz_userapi_encodeurl($args)
{
    // check we have the required input
    if (!isset($args['modname']) || !isset($args['func']) || !isset($args['args'])) {
        return LogUtil::registerError (_MODARGSERROR);
    }

    if (!isset($args['type'])) {
        $args['type'] = 'user';
    }

    // don't display the function name if using main
    if ($args['func'] == 'main') {
        $args['func'] = '';
    }

    // create an empty string to start
    $vars = '';

    // Start with 'show' value if it is set
    if ( isset($args['args']['show'])) {
        $vars = $args['args']['show'];
        unset($args['args']['show']);
    }

    // Next if it is there, show the tree
    if ( isset($args['args']['tree'])) {
        $vars .= "/tree/".$args['args']['tree'];
        unset($args['args']['tree']);
    }

    // Now add the rest of the arguments
    foreach ($args['args'] as $k => $v) {
        if (is_array($v))
        {
            foreach ($v as $k2 => $w) {
                if ($w != '') {
                    $vars .= "/$k[$k2]/$w"; // &$k[$k2]=$w
                }
            }
        } elseif ($v != '') {
            $vars .= "/$k/$v"; // &$k=$v
        }
    }

    // construct the custom url part
    if (empty($args['func']) && empty($vars)) {
        return $args['modname'] . '/';
    } elseif (empty($args['func'])) {
        return $args['modname'] . '/' . $vars . '/';
    } elseif (empty($vars)) {
        return $args['modname'] . '/' . $args['func'] . '/';
    } else {
        return $args['modname'] . '/' . $args['func'] . '/' . $vars . '/';
    }
}
*/

/**
 * decode the custom url string
 *
 * @return bool true if successful, false otherwise
 */
 /* Disable for now
function TNGz_userapi_decodeurl($args)
{
    // check we actually have some vars to work with...
    if (!isset($args['vars'])) {
        return LogUtil::registerError (_MODARGSERROR);
    }

    // define the available user functions
    $funcs = array('main', 'admin', 'sitemap');
    // set the correct function name based on our input
    if (empty($args['vars'][2])) {
        pnQueryStringSetVar('func', 'main');
    } elseif (!in_array($args['vars'][2], $funcs)) {
        pnQueryStringSetVar('func', 'main');
        $nextvar = 2;
    } else {
        pnQueryStringSetVar('func', $args['vars'][2]);
        $nextvar = 3;
    }

    // if it exists, show value should be next
    if (isset($args['vars'][$nextvar])) {
        pnQueryStringSetVar('show', (string)$args['vars'][$nextvar++]);
    }

    // Now just need to expand out the remaining parameters
    $argscount = count($args['vars']);
    for ($i = $nextvar; $i < $argscount; $i = $i + 2) {
        if (isset($args['vars'][$i])) {
            pnQueryStringSetVar($args['vars'][$i], urldecode($args['vars'][$i+1]));
        }
    }

    return true;
}
*/

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
