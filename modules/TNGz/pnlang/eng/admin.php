<?php
/************************************************************************/
/* TNGz                    (pnlang/eng/admin.php)                      */
/************************************************************************/

define("_TNGZNAME"          , "TNGz");
define('_TNGZUPDATECONFIG'  , 'Update TNGz configuration');
define("_TNGZMODULESTATUS"  , "Configuration status");
define("_TNGZCONFIGTIP"     , "Want all green status for a good configuration");
define("_TNGZMODULE"        , "TNG installation location");
define("_TNGZGUESTACCESS"   , "Guest Access");
define("_TNGZGUEST"         , "Check to allow non-logged in Zikula user to have TNG Guest access ");
define("_TNGZGUESTTIP"      , "If enabled, please make sure in TNG's Users administration that the TNG user specified here has the level of access you want for a guest. Anyone not logged into Zikula will apear to TNG as this user.");
define("_TNGZGUESTNAME"     , "  - What username to use for the guest account");
define("_TNGZGUESTDEFAULT"  , "Guest");
define("_TNGZUSERS"         , "Create TNG users from Zikula user information");
define("_TNGZUSERSTIP"      , "If enabled, the first time a logged in Zikula user accesses TNG, a TNG user with the same username is created ");

define("_TNGZFOUND"         , "TNG files Found");
define("_TNGZFOUNDNOT"      , "TNG files Not Found");

define("_TNGZROOTPATHGOOD"  , "TNG Root Path is correct!");
define("_TNGZROOTPATHBAD"   , "TNG Root Path should be ");

define("_TNGZLIVING"        , "  - Allow created user to view living");
define("_TNGZGEDCOM"        , "  - Allow created user to download GEDCOMs");
define("_TNGZLDS"           , "  - Allow created user to view LDS information");

define("_TNGZSYNCUSERS"     , "Zikula and TNG User Synchronization");
define("_TNGZSYNC"          , "  - Keep TNG user information up to date with changed Zikula user information");
define("_TNGZSYNC2"         , "    (includes changes to Real Name, website, and email address)");

define("_TNGZ_EMAIL"        , "Enable email address hiding/encoding (if you have email addresses in your TNG files)");
define("_TNGZ_EMAIL_NO"     , "No (faster)");
define("_TNGZ_EMAIL_ENCODE" , "Yes");
define("_TNGZ_EMAIL_ALL"    , "Yes, and make plain email addresses clickable");

define("_TNGZVERSIONSAME"   , "TNG Version");
define("_TNGVERSIONUNKNOWN" , "Unknown");
define("_TNGZVERSIONUPDATE" , "Please update. Found new TNG Version. Changed from");


define("_TNGZSETTINGS"      , "Settings");
define("_TNGZOTHERSETTINGS" , "Other Settings");
define("_TNGCONFG"          , "TNG Administration");
define("_TNGZINSTRUCT"      , "Installation Instructions");
define("_TNGZDEBUGINFO"     , "Information");

define("_TNGZDEBUG"         , "Debug Information");
define("_TNGZDEBUGMSG"      , "Information used to debug problems. This might be useful if you need to report a problem.");
define("_TNGZCHANGELOG"     , "Change Log");
define("_TNGZREADME"        , "Detail Readme file");

?>
