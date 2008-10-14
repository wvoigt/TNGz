<?php
/************************************************************************/
/* TNGz                    (pnlang/eng/admin.php)                      */
/************************************************************************/

/*---ADMIN-TEXT--------------------------------------------------------------*/
define("_TNGZNAME"          , "TNGz");
define("_TNGZMODIFYCONFIG"  , "Modify TNGz configuration");
define('_TNGZUPDATECONFIG'  , 'Update TNGz configuration');
define('_TNGZINSTALLINST'   , 'Installation Instructions');
define("_TNGZINSTALL"       , "<ol><li>Install TNG in a directory under your base Zikula directory
                                    <li>Make sure TNG works without Zikula
                                    <li>Install and activate the TNGz module.
                                    <li>Move the items in the TNGz/TNG folder to your TNG directory.
                                    <li>Move the items in the TNGz/Zikula folder to your base Zikula directory.
                                    <li>On this page, give the directory name for TNG from Step #1
                                    <li>Make sure all status indicators are good.  If not, please correct.
                                    <li>Select your options.
                                </ol>");
define("_TNGZMODULESTATUS"  , "Configuration status");
define("_TNGZCONFIGTIP"     , "Want all green status for a good configuration");
define("_TNGZGUESTTIP"      , "If enabled, please make sure in TNG's Users administration that the TNG user specified here has the level of access you want for a guest. Anyone not logged into Zikula will apear to TNG as this user.");
define("_TNGZMODULE"        , "TNG installation location");
define("_TNGZA_IFRAME"      , "Integrate TNG without using IFRAMEs (best for site indexing by search engines)");
define("_TNGZMODWINDOW"     , "Open Application in a new Window (when using IFRAMEs)");
define("_TNGZGUESTACCESS"   , "Guest Access");
define("_TNGZGUEST"         , "Check to allow non-logged in Zikula user to have TNG Guest access ");
define("_TNGZGUESTNAME"     , "  - What username to use for the guest account");
define("_TNGZUSERS"         , "Create TNG users from Zikula user information");
define("_TNGZUSERSTIP"      , "If enabled, the first time a logged in Zikula user accesses TNG, a TNG user with the same username is created ");

define("_TNGZFOUND"         , "TNG files Found");
define("_TNGZFOUNDNOT"      , "TNG files Not Found");

define("_TNGZROOTPATHGOOD"  , "TNG Root Path is correct!");
define("_TNGZROOTPATHBAD"   , "TNG Root Path should be ");

define("_TNGZMODULESINACTIVE"    , "module Inactive:  Please activate.");
define("_TNGZMODULESACTIVE"      , "module Active");
define("_TNGZMODULESFILESMISSING", "module Missing.  Please regenerate your modules, then activeate the module for TNG.");
define("_TNGZMODULESUPGRADED"    , "module Upgraded.  Please activate the module.");
define("_TNGZMODULESUNINIT"      , "module Uninitialized.  Please activate the module.");
define("_TNGZMODULESUNKNOWN"     , "module Unknown:  Please regenerate your modules, then activeate the module for TNG.");

define("_TNGZTNGCONFIG"          , "Configure TNG");

define("_TNGZLIVING"          , "  - Allow created user to view living");
define("_TNGZGEDCOM"          , "  - Allow created user to download GEDCOMs");
define("_TNGZLDS"             , "  - Allow created user to view LDS information");

define("_TNGZSYNCUSERS"       , "Zikula and TNG User Synchronization");
define("_TNGZSYNC"            , "  - Keep TNG user information up to date with changed Zikula user information");
define("_TNGZSYNC2"           , "    (includes changes to Real Name, website, and email address)");


define("_MODMAIL"            , "  - Send an email to created user with new random TNG password (not needed if logging in from Zikula)");
define("_PNNOAUTH"           , "You are not authorised for this action");

define("_TNGZA_LOC"         , "The location of the TNG installation relative to ");
define("_TNGZA_LOC_WARN"    , "   -eg. enter 'TNG' for installation in http://www.sitename.com/TNG");
define("_MODCONFIG"          ,  "The system directory location of the TNG installation");
define("_MODWARNING2"        , "    Use leading / for absolute path (TNG Root Path), no leading / for relative path to Zikula.  No trailing / !!");
define("_TNGZA_BASE"        ,  "The system directory path of ");
define("_TNGZA_SLASH1"      , "   -Should NOT have a leading or trailing /");
define("_TNGZA_SLASH2"      , "   -Should have a leading / and no trailing /");
define("_TNGZA_BASEWARN"    , "   -Should be first part of ");
define("_MODERROR"           , "Error message when logon fails");
define("_MODWINDOW"          , "Open Application in a new Window (when using IFRAMEs)");

define("_TNGZA_IFRAME_WARN" , "  - Requires changes for accessing TNG administration functions -- see instructions");
define("_TNGZA_IF_DEBUG"    , "  - Debug mode if not using IFRAMEs");
define("_MODDB"              , "Name of the default TNG Database (for sideblocks and user creation) ");



define("_TNGZA_CORRECT"     , "Correct:");
define("_TNGZA_ERROR"       , "Trouble:");
define("_TNGZA_TNGURLR"     , "Should match TNG Genealogy URL of ");
define("_TNGZA_TNGCONF"     , "Looking for TNG config.php file in ");
define("_TNGZA_WEBWARN"     , "Settings imply TNG can be accessed directly at");
define("_TNGZA_CHECKWARN"   , "Checks based on last saved settings:");
define("_TNGZA_NOCONFIG"    , "Could not find TNG files");

define("_TNGZ_EMAIL"        , "Enable email address hiding/encoding (if you have email addresses in your TNG files)");
define("_TNGZ_EMAIL_NO"     , "No (faster)");
define("_TNGZ_EMAIL_ENCODE" , "Yes");
define("_TNGZ_EMAIL_ALL"    , "Yes, and make plain email addresses clickable");

define("_TNGVERSIONUNKNOWN" , "Unknown");
define("_TNGZVERSIONUPDATE" , "Please update. Found new TNG Version. Changed from");
define("_TNGZVERSIONSAME"   , "TNG Version");

define("_TNGZSETTINGS"      , "Settings");
define("_TNGZOTHERSETTINGS" , "Other Settings");
define("_TNGCONFG"          , "TNG Administration");
define("_TNGZINSTRUCT"      , "Installation Instructions");
define("_TNGZDEBUGINFO"     , "Information");

define("_TNGZDEBUG"         , "Debug Information");
define("_TNGZDEBUGMSG"      , "Information used to debug problems. This might be useful if you need to report a problem.");
define("_TNGZCHANGELOG"     , "Change Log");
define("_TNGZINSTRBASIC"    , "Basic Instructions");
define("_TNGZREADME"        , "Detail Readme file");
