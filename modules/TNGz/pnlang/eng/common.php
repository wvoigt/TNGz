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
 
/*********** Common ***********/
define("_TNGZ_PREFIX",               "index.php?module=TNGz&func=main&show");
define('_USECACHE',                  'Cache the block (if pnRender caching is enabled)');
define('_PEOPLEDBFERROR',            'Error in accessing the TNG tables.');
define('_SELECTYES',                 'Yes');
define('_SELECTNO',                  'No');

/*********** User ***********/
// common defines
define("_TNGZ_SHOWTOP",              "Show top");
define("_TNGZ_BYOCCURRENCE",         " ordered by occurrence");
define("_TNGZ_GO",                   "Go");
define("_TNGZ_SORTEDALPHA",          " sorted alphabetically");

// places item
define("_TNGZ_PLACETITLE",           "Top Locations and Places");
define("_TNGZ_TOPPLACES",            "Top Places");
define("_TNGZ_TOTALPLACES",          "Total Places");
define("_TNGZ_SHOWALLPLACES",        "All places");
define("_TNGZ_MAINPLACESPAGE",       "Main place listing page");

// surnames item
define("_TNGZ_SURNAMETITLE",         "Top Surnames");
define("_TNGZ_TOP",                  "Top");
define("_TNGZ_SURNAMES",             "Surnames");
define("_TNGZ_OUTOF",                "out of");
define("_TNGZ_TOTALSURNAMES",        "Total Unique Surnames");
define("_TNGZ_SHOWALLSURNAMES",      "Show all surnames");
define("_TNGZ_MAINSURNAMESPAGE",     "Main surname listing page");

/*********** RandomPhoto ***********/
define('_SHOWLIVING',                'Show Pictures of People');
define('_SELECTLIVINGY',             'Yes, show all');
define('_SELECTLIVINGN',             'No');
define('_SELECTLIVINGD',             'Yes, but never living people');
define('_SELECTLIVINGL',             'Yes, but show living people only if user is logged in');
//
define('_SHOWPHOTO',                 'Which type of photo to use');
define('_SELECTPHOTOT',              'Thumbnail of Photo');
define('_SELECTPHOTOP',              'Actual Photo');

define('_MAXHEIGHT',                 'Maximum Height of display image');
define('_MAXWIDTH',                  'Maximum Width of display  image');

define('_TNGPHOTO',                  'Photograph');
define('_NOPHOTOFOUND',              'No Photo found');
define('_USECACHE',                  'Cache the block (if pnRender caching is enabled)');
define('_TNGPHOTOLIST',              'Limit Photo selection to the following list of TNG mediaID numbers (default = blank)');

/*********** ThisDay ***********/
define('_SHOWDATE',                  'Show Today\'s Date :');
define('_SHOWBIRTH',                 'Show Birthdays :');
define('_SHOWMARRIAGE',              'Show Marriages :');
define('_SHOWDEATH',                 'Show Who Died :');
define('_SHOWORDER',                 'Sort display entries by :');
define('_SHOWWIKI',                  'Show Wikipedia link :');
define('_SELECTBIRTHY',              'Yes, show all');
define('_SELECTBIRTHN',              'No');
define('_SELECTBIRTHD',              'Yes, but never living people');
define('_SELECTBIRTHL',              'Yes, but show living people only if user is logged in');
define('_SELECTMARRIAGEY',           'Yes, show all');
define('_SELECTMARRIAGEN',           'No');
define('_SELECTMARRIAGED',           'Yes, but never living people');
define('_SELECTMARRIAGEL',           'Yes, but show living people only if user is logged in');
define('_SELECTDEATHY',              'Yes');
define('_SELECTDEATHN',              'No');
define('_SELECTORDERN',              'Last Name, First Name');
define('_SELECTORDERD',              'Event date - earliest to latest');
define('_SELECTORDERR',              'Event date - latest to earliest');
define('_BIRTH',                     'Birthdays');
define('_MARRIAGE',                  'Marriages');
define('_MARRIAGE_AND',              'and');
define('_DIVORCED',                  'D');
define('_DEATH',                     'People Who Died');
define('_ACCESSTNG',                 'Genealogy Page');
define('_NONETNG',                   'Nobody found for today');
define('_WIKITODAY',                 'Go to Wikipedia');

/*********** WhatsNew ***********/
define('_TNGZ_WHATSNEW_RECENT',      'newest in');
define('_TNGZ_WHATSNEW_DAYS',        'days');
define('_TNGZ_WHATSNEW_HEAD_PEOPLE', 'Individuals');
define('_TNGZ_WHATSNEW_PEOPLE',      'Show new or changed Individuals');
define('_TNGZ_WHATSNEW_HEAD_FAMILY', 'Families');
define('_TNGZ_WHATSNEW_FAMILY',      'Show new or changed Families');
define('_TNGZ_WHATSNEW_HEAD_PHOTOS', 'Photos');
define('_TNGZ_WHATSNEW_PHOTOS',      'Show new or changed Photos');
define('_TNGZ_WHATSNEW_HEAD_HISTORY','Histories');
define('_TNGZ_WHATSNEW_HISTORY',     'Show new or changed Histories');
define('_TNGZ_WHATSNEW_HOWMANY',     'Maximum number of entries to display');
define('_TNGZ_WHATSNEW_HOWMANY_NUM', '10');
define('_TNGZ_WHATSNEW_HOWLONG',     'Maximum number of days to to display');
define('_TNGZ_WHATSNEW_HOWLONG_NUM', '30');
define('_TNGZ_WHATSNEW_YES',         'Yes');
define('_TNGZ_WHATSNEW_NO',          'No');
define('_TNGZ_WHATSNEW_NOCHANGES',   'No new or updated records');
define('_TNGZ_WHATSNEW_SQLERROR',    'Error in accessing the database.');

define('_ACCESSTNG',                 'Genealogy Page');
define('_NONETNG',                   'Nobody found for today');
define('_SHOWORDER',                 'Display entries by :');
define('_SELECTORDERN',              'Last Name');
define('_SELECTORDERD',              'Latest to Earliest');
define('_SELECTORDERR',              'Earliest to Latest');
define('_SELECTORDERE',              'Order entered');

define('_SHOWFAMILYNAME',            'Display Families by :');
define('_SELECTFAMILYFULL',          'Full names');
define('_SELECTFAMILYSHORT',         'Surnames only');

/*********** MostWanted ***********/
define('_WANTEDINSTRUCTIONS',        '<strong>All Fields below are optional.</strong><br /> Fields left blank will be ommited in the output.<br />IDs which are not in the right format, are not in TNG, or contain errors will be omitted in the output.');
define('_WANTEDTEXT',                'Explanation text at the top of the block (can included HTML)');
define('_WANTEDPEOPLELIST',          'List of PersonIDs (e.g. I001, I002)');
define('_WANTEDFAMILYLIST',          'List of FamilyIDs (e.g. F001, F002)');
define('_WANTEDMENULINK',            'Show TNG Menu Link at the bottom?');
define('_MARRIAGE_AND',              'and');
define('_MARRIED_ABR',               'm.');
define('_DIVORCED_ABR',              'D');

/*********** Surnames and Places ***********/
define('_TNGZ_SURNAMES_INSTRUCTIONS',  'Select the number top Surnames to display, the type of display, and the ordering.  Ordering can be by most used (rank) or alphabetical.');
define('_TNGZ_PLACES_INSTRUCTIONS',    'Select the number top Places to display, the type of display, and the ordering.  Ordering can be by most used (rank) or alphabetical.');
define('_TNGZ_TEXT',                   'This is optional explanation text at the top of the block (can included HTML)');
define('_TNGZ_TOP',                    'Number of top number to display');
define('_TNGZ_SORTED',                 'Show items');
define('_TNGZ_COLS',                   'Number of columns (used only Table)');
define('_TNGZ_TYPE',                   'Display as');
define('_TNGZ_CLOUD',                  'Surname cloud');
define('_TNGZ_TABLE',                  'Table');
define('_TNGZ_LIST',                   'List');
define('_TNGZ_FORM_SHOWTOP',           'Show top');
define('_TNGZ_FORM_ORDERBYOCC',        'ordered by occurrence');
define('_TNGZ_FORM_GO',                'Go');
define('_TNGZ_SURNAMES_LINK_SURNAMES_ALL', 'Show all surnames alphabetically');
define('_TNGZ_SURNAMES_LINK_SURNAMES', 'Main surname page');
define('_TNGZ_PLACES_LINK_PLACES_ALL', 'Show all places alphabetically');
define('_TNGZ_PLACES_LINK_PLACES',     'Main places page');

/*********** NameSearch ***********/
define('_TNGZ_NAMESEARCH_SEARCH',        'Search');
define('_TNGZ_NAMESEARCH_LASTNAME',      'Last Name');
define('_TNGZ_NAMESEARCH_FIRSTNAME',     'First Name');
define('_TNGZ_NAMESEARCH_ADVANCED',      'Advanced Search');

/*********** Search ***********/
define('_TNGZ_SEARCH_LIVING',        'The following individual is flagged as living - Details withheld.');
define('_TNGZ_SEARCH',               'Genealogy People Search');
define('_TNGZ_SEARCH_BORN',          'b.');
define('_TNGZ_SEARCH_DIED',          'd.');
define('_TNGZ_SEARCH_RESULTS',       'people found');
define('_TNGZ_SEARCH_NONEFOUND',     'None found');

/*********** admin ***********/
define("_TNGZNAME"          ,        'TNGz');
define('_TNGZUPDATECONFIG'  ,        'Update TNGz configuration');
define("_TNGZMODULESTATUS"  ,        'Configuration status');
define("_TNGZCONFIGTIP"     ,        "Want all green status for a good configuration");
define("_TNGZMODULE"        ,        "TNG installation location");
define("_TNGZGUESTACCESS"   ,        "Guest Access");
define("_TNGZGUEST"         ,        "Check to allow non-logged in Zikula user to have TNG Guest access ");
define("_TNGZGUESTTIP"      ,        "If enabled, please make sure in TNG's Users administration that the TNG user specified here has the level of access you want for a guest. Anyone not logged into Zikula will apear to TNG as this user.");
define("_TNGZGUESTNAME"     ,        "  - What username to use for the guest account");
define("_TNGZGUESTDEFAULT"  ,        "Guest");
define("_TNGZUSERS"         ,        "Create TNG users from Zikula user information");
define("_TNGZUSERSTIP"      ,        "If enabled, the first time a logged in Zikula user accesses TNG, a TNG user with the same username is created ");

define("_TNGZFOUND"         ,        "TNG files Found");
define("_TNGZFOUNDNOT"      ,        "TNG files Not Found");

define("_TNGZROOTPATHGOOD"  ,        "TNG Root Path is correct!");
define("_TNGZROOTPATHBAD"   ,        "TNG Root Path should be ");

define("_TNGZLIVING"        ,        "  - Allow created user to view living");
define("_TNGZGEDCOM"        ,        "  - Allow created user to download GEDCOMs");
define("_TNGZLDS"           ,        "  - Allow created user to view LDS information");

define("_TNGZSYNCUSERS"     ,        "Zikula and TNG User Synchronization");
define("_TNGZSYNC"          ,        "  - Keep TNG user information up to date with changed Zikula user information");
define("_TNGZSYNC2"         ,        "    (includes changes to Real Name, website, and email address)");

define("_TNGZ_EMAIL"        ,        "Enable email address hiding/encoding (if you have email addresses in your TNG files)");
define("_TNGZ_EMAIL_NO"     ,        "No (faster)");
define("_TNGZ_EMAIL_ENCODE" ,        "Yes");
define("_TNGZ_EMAIL_ALL"    ,        "Yes, and make plain email addresses clickable");

define("_TNGZVERSIONSAME"   ,        "TNGz is in sync with TNG Version");
define("_TNGVERSIONUNKNOWN" ,        "Unknown");
define("_TNGZVERSIONUPDATE" ,        "TNGz is not in sync with TNG version. Please update. Found new TNG Version. Changed from");
define("_TNGZVERSIONNEW"    ,        "There is a new version available");
define("_TNGZVERSIONLATEST" ,        "This is the latest recommended version");

define("_TNGZSETTINGS"      ,        "Settings");
define("_TNGZOTHERSETTINGS" ,        "Other Settings");
define("_TNGCONFG"          ,        "TNG Administration");
define("_TNGZINSTRUCT"      ,        "Installation Instructions");
define("_TNGZDEBUGINFO"     ,        "Information");

define("_TNGZDEBUG"         ,        "Debug Information");
define("_TNGZDEBUGMSG"      ,        "Information used to debug problems. This might be useful if you need to report a problem.");
define("_TNGZCHANGELOG"     ,        "Change Log");
define("_TNGZREADME"        ,        "Detail Readme file");

define("_TNGZ_HOMEPAGE"     ,        "Use TNGz's homepage instead of TNG's index page");


