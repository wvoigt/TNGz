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
 * the main administration function
 */
function TNGz_admin_main()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.  For the
    // main function we want to check that the user has at least edit privilege
    // for some item within this component, or else they won't be able to do
    // anything and so we refuse access altogether.  The lowest level of access
    // for administration depends on the particular module, but it is generally
    // either 'edit' or 'delete'
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_EDIT)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
	$pnRender =& new pnRender('TNGz');

	// As Admin output changes often, we do not want caching.
	$pnRender->caching = false;

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_main.htm');
}

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function TNGz_admin_modifyconfig()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_ADMIN)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    // Get the TNG information based on what we currently have
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // *****************************************************
    // Check to be sure we can get to the TNG information
    // and check to make sure the rootpath is set correctly
    // *****************************************************
    $TNG_config     = false;
    $TNG_found      = _TNGZFOUNDNOT;
    $TNG_foundimage = 'red_dot.gif';

    $TNG_checkpath     = dirname($TNG['configfile']) . "/";
    $TNG_rootpath      = false;
    $TNG_rootpathsay   = _TNGZROOTPATHBAD . " ". $TNG_checkpath;
    $TNG_rootpathimage = 'red_dot.gif';


    if (file_exists($TNG['configfile']) ){
        include($TNG['configfile']);
        if ( $tngconfig ) {
            $TNG_config        = true;
            $TNG_found         = _TNGZFOUND;
            $TNG_foundimage    = 'green_dot.gif';
        }
        if ( $rootpath ==  $TNG_checkpath ) {
            $TNG_rootpath      = true;
            $TNG_rootpathsay   = _TNGZROOTPATHGOOD;
            $TNG_rootpathimage = 'green_dot.gif';
        }
    }

    // *****************************************************
    // Get TNG version and make sure it is the same as last time
    // and check to make sure the rootpath is set correctly
    // *****************************************************
    $TNG_versioncheck = false;
    $TNG_version      = _TNGVERSIONUNKNOWN;
    $TNG_versionsay   = _TNGZVERSIONUPDATE;
    $TNG_versionimage = 'red_dot.gif';
    $TNG_versionlast  =  pnModGetVar('TNGz', '_version');

    $TNGversionfile = dirname($TNG['configfile']) . "/version.php";
    if (file_exists($TNGversionfile) ){
        include($TNGversionfile);
        if ( $tng_version ) {
            $TNG_version = $tng_version ;
	    if ( ($TNG_version == $TNG_versionlast) && ($TNG_version != _TNGVERSIONUNKNOWN) ) {
                $TNG_versioncheck = true;
                $TNG_versionsay  = _TNGZVERSIONSAME;
                $TNG_versionimage  = 'green_dot.gif';
	    }
        }
    }

    // Get TNGz module information
    $ModName = pnModGetName();
    $ModInfo = pnModGetInfo(pnModGetIDFromName($ModName));

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
	$pnRender =& new pnRender('TNGz');

	// As Admin output changes often, we do not want caching.
	$pnRender->caching = false;

    $row = array();

/*
    These are no longer used.
	$_config   = pnVarCleanFromInput('_config');
	$_db       = pnVarCleanFromInput('_db');
	$_mail     = pnVarCleanFromInput('_mail');
    $_basepath = pnVarCleanFromInput('_basepath');
    $_debug    = pnVarCleanFromInput('_debug');
*/

    // What version of TNGz
    $pnRender->assign('TNGzVersion', $ModInfo['version']);

    // Is the TNG setup found?
    $pnRender->assign('tngconfig'     ,$TNG_config);
    $pnRender->assign('tngfound'     , $TNG_found);
    $pnRender->assign('tngfoundimage', $TNG_foundimage);

    // Is the TNG $rootpath set to the same directory
    $pnRender->assign('tngrootpath'     , $TNG_rootpath);
    $pnRender->assign('tngrootpathsay'  , $TNG_rootpathsay);
    $pnRender->assign('tngrootpathimage', $TNG_rootpathimage);

    // Is the TNG version the same as last time
    $pnRender->assign('TNG_version'       , $TNG_version);
    $pnRender->assign('TNG_versioncheck'  , $TNG_versioncheck);
    $pnRender->assign('TNG_versionsay'    , $TNG_versionsay);
    $pnRender->assign('TNG_versionimage'  , $TNG_versionimage);
    $pnRender->assign('TNG_versionlast'   , $TNG_versionlast);


    // Allow non-logged in PostNuke users as a Guest
    $pnRender->assign('tngguest', pnModGetVar('TNGz', '_guest'));

    // If non-logged in PostNuke users as a Guest, under what name?
    $pnRender->assign('tngguestname', pnModGetVar('TNGz', '_gname'));

    // Create TNG users from PostNuke user information
    $pnRender->assign('tngusers', pnModGetVar('TNGz', '_users'));

    // Can a created user see Living?
    $pnRender->assign('tngliving', pnModGetVar('TNGz', '_living'));

    // Add Email Filter
    $email_options = array( _TNGZ_EMAIL_NO, _TNGZ_EMAIL_ENCODE, _TNGZ_EMAIL_ALL );
    $email_values = array( "N", "E", "A" );
    $pnRender->assign('tngemailoptions', $email_options );
    $pnRender->assign('tngemailvalues', $email_values );
    $pnRender->assign('tngemail', pnModGetVar('TNGz', '_email'));

    // Can a created user download GEDCOMs?
    $pnRender->assign('tnggedcom', pnModGetVar('TNGz', '_gedcom'));

    // Can a created user see LDS information?
    $pnRender->assign('tnglds', pnModGetVar('TNGz', '_lds'));

    // For created users, Sync TNG info with PostNuke info?
    $pnRender->assign('tngsync', pnModGetVar('TNGz', '_sync'));

    // TNG location
    $pnRender->assign('tngmodule'    , pnModGetVar('TNGz', '_loc'));
    $pnRender->assign('zikula'       , dirname(dirname(dirname(realpath(__FILE__))))."/" );

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_modifyconfig.htm');
}

function TNGz_admin_updateconfig()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_ADMIN)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from pnVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of PostNuke
    // Get parameters from whatever input we need.
	$_loc      = pnVarCleanFromInput('tngmodule');
	$_guest    = pnVarCleanFromInput('tngguest');
	$_users    = pnVarCleanFromInput('tngusers');
        $_living   = pnVarCleanFromInput('tngliving');
        $_email    = pnVarCleanFromInput('tngemail');
	$_gedcom   = pnVarCleanFromInput('tnggedcom');
        $_lds      = pnVarCleanFromInput('tnglds');
        $_sync     = pnVarCleanFromInput('tngsync');
        $_gname    = pnVarCleanFromInput('tngguestname');
	$_version  = pnVarCleanFromInput('tngversion');

    /*
	$_config   = pnVarCleanFromInput('_config');
   	$_db       = pnVarCleanFromInput('_db');
    $_debug    = pnVarCleanFromInput('_debug');
	$_mail     = pnVarCleanFromInput('_mail');
    $_basepath = pnVarCleanFromInput('_basepath');
    */

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('TNGz', 'admin', 'main'));
        return true;
    }

    // Update module variables.
    if (empty($_loc)) { $_loc = ""; }
    pnModSetVar('TNGz', '_loc'      , $_loc);

    if (empty($_guest)) { $_guest = 0; }
    pnModSetVar('TNGz', '_guest'    , $_guest);

    if (empty($_gname) || ($_gname=="") ){ $_gname = _TNGZGUESTDEFAULT;}
    pnModSetVar('TNGz', '_gname'    , $_gname);

    if (empty($_users)) { $_users = 0; }
    pnModSetVar('TNGz', '_users'    , $_users);

    if (empty($_living)) { $_living = 0; }
    pnModSetVar('TNGz', '_living'   , $_living);

    if (empty($_email)) { $_email = _TNGZ_EMAIL_NO; }
    pnModSetVar('TNGz', '_email'   , $_email);

    if (empty($_gedcom)) { $_gedcom = 0; }
    pnModSetVar('TNGz', '_gedcom'   , $_gedcom);

    if (empty($_lds)) { $_lds = 0; }
    pnModSetVar('TNGz', '_lds'      , $_lds);

    if (empty($_sync)) { $_sync = 0; }
    pnModSetVar('TNGz', '_sync'     , $_sync);

    if (empty($_version) || ($_version=="") ){ $_version = _TNGVERSIONUNKNOWN;}
    pnModSetVar('TNGz', '_version'  , $_version);


   /*
    pnModSetVar('TNGz', '_config'   , $_config);
    pnModSetVar('TNGz', '_db'       , $_db);
    pnModSetVar('TNGz', '_mail'     , $_mail);
    pnModSetVar('TNGz', '_basepath' , $_basepath);
    pnModSetVar('TNGz', '_debug'    , $_debug);
   */

    //////////////////////////////////////////////////////////////////
    // Scan the TNG php files to record all the global variables
    /////////////////////////////////////////////////////////////////
    // Note: the TNG location informaiton was saved above, so we can use it now
    // Also, we do this every time to make sure we have the latest, just in case files are changed
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    if ($TNGglobals = pnModAPIFunc('TNGz','user','GetTNGglobals', array('dir' => $TNG['SitePath']."/".$TNG['directory']))){
        pnModSetVar('TNGz', '_globals'     , $TNGglobals);
    }

    // the module configuration has been updated successfuly
    pnSessionSetVar('statusmsg', _CONFIGUPDATED);

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    pnRedirect(pnModURL('TNGz', 'admin', 'main'));

    // Return
    return true;
}

function TNGz_admin_TNGadmin()
{

    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_ADMIN)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    if (!pnUserLoggedIn()) {
        // Must be logged in to even have a chance at getting to the administration page
        pnRedirect(pnModURL('Users','user','loginscreen')) ;
    }

    if (!$url=pnModAPIFunc('TNGz','user','GetTNGurl') ){
        return pnVarPrepHTMLDisplay("Error accessing TNG config file.");
    }

    // Get TNGz module information
    $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender =& new pnRender('TNGz');

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    $pnRender->assign('TNGzURL'      , $url);
    $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_tngadmin.htm');
}

function TNGz_admin_Instruct()
{

    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_ADMIN)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    // Get TNGz module information
    $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

    $helpfile = 'modules/'.DataUtil::formatForOS($pnTNGmodinfo['directory']).'/'.DataUtil::formatForOS($pnTNGmodinfo['help']);
    if (file_exists($helpfile)) {
        $helpfile = "<pre>" . implode('',file($helpfile)) . "</pre>";
        $helpfile = DataUtil::formatForDisplayHTML($helpfile);
    }

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender =& new pnRender('TNGz');

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );
    $pnRender->assign('helpfile'     , $helpfile);

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_install.htm');

}

function TNGz_admin_Info()
{
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_ADMIN)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    // Get TNGz module information
    $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));
    $pnTNGmodvars = pnModGetVar('TNGz');


    $changelog = 'modules/'.DataUtil::formatForOS($pnTNGmodinfo['directory']).'/'.DataUtil::formatForOS($pnTNGmodinfo['changelog']);
    if (file_exists($changelog)) {
        $changelog = "<pre>" . implode('',file($changelog)) . "</pre>";
        $changelog = DataUtil::formatForDisplayHTML($changelog);
    }

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender =& new pnRender('TNGz');

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );

    $pnRender->assign('changelog'  , $changelog );

    foreach ($pnTNGmodinfo as $key => $value) {
        $debuglist[] = array( 'key' => $key, 'value' => $value );
    }
    foreach ($pnTNGmodvars as $key => $value) {
        $debuglist[] = array( 'key' => $key, 'value' => $value );
    }
    $pnRender->assign('debuglist'  , $debuglist);

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_info.htm');

}

