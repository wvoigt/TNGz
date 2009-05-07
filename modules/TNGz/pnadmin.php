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
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_EDIT)) {
        return LogUtil::registerError(_MODULENOAUTH);
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
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    // Get the TNG information based on what we currently have
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // *****************************************************
    // Check to be sure we can get to the TNG information
    // and check to make sure the rootpath is set correctly
    // *****************************************************
    $TNG_config     = false;
    $TNG_found      = _TNGZFOUNDNOT;
    $TNG_foundimage = 'error.gif';

    $TNG_checkpath     = dirname($TNG['configfile']) . "/";
    $TNG_rootpath      = false;
    $TNG_rootpathsay   = _TNGZROOTPATHBAD . " ". $TNG_checkpath;
    $TNG_rootpathimage = 'error.gif';


    if (file_exists($TNG['configfile']) ) {
        include($TNG['configfile']);
        if ( $tngconfig ) {
            $TNG_config        = true;
            $TNG_found         = _TNGZFOUND;
            $TNG_foundimage    = 'button_ok.gif';
        }
        if ( $rootpath ==  $TNG_checkpath ) {
            $TNG_rootpath      = true;
            $TNG_rootpathsay   = _TNGZROOTPATHGOOD;
            $TNG_rootpathimage = 'button_ok.gif';
        }
    }

    // *****************************************************
    // Get TNG version and make sure it is the same as last time
    // and check to make sure the rootpath is set correctly
    // *****************************************************
    $TNG_versioncheck = false;
    $TNG_version      = _TNGVERSIONUNKNOWN;
    $TNG_versionsay   = _TNGZVERSIONUPDATE;
    $TNG_versionimage = 'error.gif';
    $TNG_versionlast  =  pnModGetVar('TNGz', '_version');

    $TNGversionfile = dirname($TNG['configfile']) . "/version.php";
    if (file_exists($TNGversionfile) ) {
        include($TNGversionfile);
        if ( $tng_version ) {
            $TNG_version = $tng_version ;
        if ( ($TNG_version == $TNG_versionlast) && ($TNG_version != _TNGVERSIONUNKNOWN) ) {
                $TNG_versioncheck = true;
                $TNG_versionsay  = _TNGZVERSIONSAME;
                $TNG_versionimage  = 'button_ok.gif';
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


    // Allow non-logged in Zikula users as a Guest
    $pnRender->assign('tngguest', pnModGetVar('TNGz', '_guest'));

    // If non-logged in Zikula users as a Guest, under what name?
    $pnRender->assign('tngguestname', pnModGetVar('TNGz', '_gname'));

    // Create TNG users from Zikula user information
    $pnRender->assign('tngusers', pnModGetVar('TNGz', '_users'));

    // Can a created user see Living?
    $pnRender->assign('tngliving', pnModGetVar('TNGz', '_living'));

    // Add Email Filter
    $email_options = array( _TNGZ_EMAIL_NO, _TNGZ_EMAIL_ENCODE, _TNGZ_EMAIL_ALL );
    $email_values = array( "N", "E", "A" );
    $pnRender->assign('tngemailoptions', $email_options );
    $pnRender->assign('tngemailvalues',  $email_values );
    $pnRender->assign('tngemail', pnModGetVar('TNGz', '_email'));

    // Can a created user download GEDCOMs?
    $pnRender->assign('tnggedcom', pnModGetVar('TNGz', '_gedcom'));

    // Can a created user see LDS information?
    $pnRender->assign('tnglds', pnModGetVar('TNGz', '_lds'));

    // For created users, Sync TNG info with Zikula info?
    $pnRender->assign('tngsync', pnModGetVar('TNGz', '_sync'));

    // For TNGz homepage for TNG index
    $pnRender->assign('tngzhomepage', pnModGetVar('TNGz', '_homepage'));

    // For TNGz caching options
    $cache_db_options = array( _ONOFF_OFF, _ONOFF_ON );
    $cache_db_values  = array(    "0"    ,   "1"     );
    $pnRender->assign('tngzcachedb',        pnModGetVar('TNGz', '_cachedb'));
    $pnRender->assign('tngzcachedboptions', $cache_db_options);
    $pnRender->assign('tngzcachedbvalues',  $cache_db_values );

    $minute = 60; // 60 seconds
    $hour   = 60 * $minute;
    $day    = 24 * $hour;
    $cache_sec_options[] = _ONOFF_OFF          ; $cache_sec_values[] =  0;
    $cache_sec_options[] = '30 ' . _MINUTES    ; $cache_sec_values[] = 30 * $minute;
    $cache_sec_options[] = ' 1 ' . _TNGZ_HOUR  ; $cache_sec_values[] =  1 * $hour;
    $cache_sec_options[] = ' 2 ' . _TNGZ_HOURS ; $cache_sec_values[] =  2 * $hour;
    $cache_sec_options[] = ' 4 ' . _TNGZ_HOURS ; $cache_sec_values[] =  4 * $hour;
    $cache_sec_options[] = '12 ' . _TNGZ_HOURS ; $cache_sec_values[] = 12 * $hour;
    $cache_sec_options[] = ' 1 ' . _DAY        ; $cache_sec_values[] =  1 * $day;
    $cache_sec_options[] = ' 2 ' . _DAYS       ; $cache_sec_values[] =  2 * $day;
    $cache_sec_options[] = ' 3 ' . _DAYS       ; $cache_sec_values[] =  3 * $day;
    $cache_sec_options[] = ' 4 ' . _DAYS       ; $cache_sec_values[] =  4 * $day;
    $cache_sec_options[] = ' 5 ' . _DAYS       ; $cache_sec_values[] =  5 * $day;
    $cache_sec_options[] = ' 6 ' . _DAYS       ; $cache_sec_values[] =  6 * $day;
    $cache_sec_options[] = ' 7 ' . _DAYS       ; $cache_sec_values[] =  7 * $day;

    $pnRender->assign('tngzcachesec', pnModGetVar('TNGz', '_cachesec'));
    $pnRender->assign('tngzcachesecoptions', $cache_sec_options );
    $pnRender->assign('tngzcachesecvalues',  $cache_sec_values  );    

    if (pnModGetVar('TNGz', '_cachedb') != 0 && pnModGetVar('TNGz', '_cachesec') !=0 ) {
        pnModAPIFunc('TNGz','user','CacheInit');  // If needed, initialize the cache
    }

    $cache_exists = (pnModAPIFunc('TNGz','user','CacheExists'))? "1" : "0";
    $pnRender->assign('tngzcacheexists',  $cache_exists  );

    // Primary Person ID
    // Note: To uniquely identify a person, need personID and gedcom/tree name
    $personID    = pnModGetVar('TNGz', '_personID',   '' );
    $person_tree = pnModGetVar('TNGz', '_persontree', '0');
    $treeoptions[] = _ONOFF_OFF;
    $treevalues[]  = "0";
        
    // Get the possible trees 
    if ($TNG_config){
        PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/net.js');
        PageUtil::AddVar('javascript', 'javascript/ajax/prototype.js');
        PageUtil::AddVar('javascript', 'javascript/ajax/scriptaculous.js');
        PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/net.js');
        PageUtil::AddVar('javascript', pnGetBaseURL().$TNG['directory'].'/litbox.js');

        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $TNG_conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
        if ($result = &$TNG_conn->Execute($query) ) {
            if ($result->RecordCount() > 0) {
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
                    $treeoptions[] = $row['treename'];
                    $treevalues[]  = $row['gedcom'];
                }
            }
            $result->Close();
        }       
    }
    $person = pnModAPIFunc('TNGz','user','getperson', array('id'=> $personID, 'tree'=> $person_tree));
    $personmsg = (!$person) ? "" : $personID . " = " . $person['fullname'];
    $pnRender->assign('tngzid',            $personID    );
    $pnRender->assign('tngzidtree',        $person_tree );
    $pnRender->assign('tngzidtreeoptions', $treeoptions );
    $pnRender->assign('tngzidtreevalues',  $treevalues  );
    $pnRender->assign('tngzidname',        $personmsg   );

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
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from pnVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Zikula
    // Get parameters from whatever input we need.
    $_loc         = FormUtil::getPassedValue('tngmodule',    null, 'REQUEST');
    $_guest       = FormUtil::getPassedValue('tngguest',     null, 'REQUEST');
    $_users       = FormUtil::getPassedValue('tngusers',     null, 'REQUEST');
    $_living      = FormUtil::getPassedValue('tngliving',    null, 'REQUEST');
    $_email       = FormUtil::getPassedValue('tngemail',     null, 'REQUEST');
    $_gedcom      = FormUtil::getPassedValue('tnggedcom',    null, 'REQUEST');
    $_lds         = FormUtil::getPassedValue('tnglds',       null, 'REQUEST');
    $_sync        = FormUtil::getPassedValue('tngsync',      null, 'REQUEST');
    $_gname       = FormUtil::getPassedValue('tngguestname', null, 'REQUEST');
    $_version     = FormUtil::getPassedValue('tngversion',   null, 'REQUEST');
    $_homepage    = FormUtil::getPassedValue('tngzhomepage', null, 'REQUEST');
    $_cachedb     = FormUtil::getPassedValue('tngzcachedb',  null, 'REQUEST');
    $_cachesec    = FormUtil::getPassedValue('tngzcachesec', null, 'REQUEST');
    $_personid    = FormUtil::getPassedValue('tngzid',       null, 'REQUEST');
    $_personidtree= FormUtil::getPassedValue('tngzidtree',   null, 'REQUEST');

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
    if (!SecurityUtil::confirmAuthKey()) {
        LogUtil::registerStatus(_BADAUTHKEY);
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
        
    if (empty($_homepage)) { $_homepage = 0; }
        pnModSetVar('TNGz', '_homepage'     , $_homepage);
        
    if (empty($_cachedb)) { $_cachedb = 0; }
        pnModSetVar('TNGz', '_cachedb'     , $_cachedb);
        
    if (empty($_cachesec)) { $_cachesec = 0; }
        pnModSetVar('TNGz', '_cachesec'     , $_cachesec);
        
    if (empty($_personid)) { $_personid = ""; }
    $_personid = trim($_personid);
    if (!preg_match("/^[a-zA-Z]+[0-9]+$/",$_personid) ) {$_personid="";}
    pnModSetVar('TNGz', '_personID'     , $_personid);

    if (empty($_personidtree)) { $_personidtree = "0"; }
        pnModSetVar('TNGz', '_persontree'     , $_personidtree);

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

    if ($TNGglobals = pnModAPIFunc('TNGz','user','GetTNGglobals', array('dir' => $TNG['SitePath']."/".$TNG['directory']))) {
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

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    if (!pnUserLoggedIn()) {
        // Must be logged in to even have a chance at getting to the administration page
        pnRedirect(pnModURL('Users','user','loginscreen')) ;
    }

    if (!$url=pnModAPIFunc('TNGz','user','GetTNGurl') ) {
        return LogUtil::registerError("Error accessing TNG config file.");
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
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
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
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
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

function TNGz_admin_cachedelete()
{
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_EDIT)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    $success = pnModAPIFunc('TNGz','user','CacheDelete');
    
    // the module configuration has been updated successfuly
    $msg = ($success) ? _TNGZ_CACHE_DELETED : _TNGZ_CACHE_ERROR;
    pnSessionSetVar('statusmsg', $msg);

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    pnRedirect(pnModURL('TNGz', 'admin', 'main'));
    
    // Return
    return true;
}

function TNGz_admin_userlog()
{
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    $userlog = 'genlog.txt';

    if (file_exists($userlog)) {
        $userlog = "<pre>" . implode('',file($userlog)) . "</pre>";
        $userlog = DataUtil::formatForDisplayHTML($userlog);
    }

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender =& new pnRender('TNGz');

    $pnRender->assign('userlog'  , $userlog );

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_userlog.htm');

}

function TNGz_admin_adminlog()
{
    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    // Get TNGz module information
    $TNGfolder = pnModAPIFunc('TNGz','user','GetTNGpaths');

    $adminlog = $TNGfolder[directory].'/admin/genlog.txt';
    if (file_exists($adminlog)) {
        $adminlog = "<pre>" . implode('',file($adminlog)) . "</pre>";
        $adminlog = DataUtil::formatForDisplayHTML($adminlog);
    }

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender =& new pnRender('TNGz');

    $pnRender->assign('adminlog'  , $adminlog );

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_admin_adminlog.htm');

}
