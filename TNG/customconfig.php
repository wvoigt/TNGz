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

// Custom settings for TNGz to work with TNG
// Please copy these lines into your customconfig.php file
// or if you do not use the customconfig.php file for anything else
// then you can just use this file
if ($cms[TNGz] == 1){

    $cms[support]    = "zikula";
    $cms[module]     = "TNGz";
    $cms[url]        = "index.php?module=TNGz&func=main&show";
    $cms[tngpath]    = $TNG['directory']. "/";
    $cms[adminurl]   = "index.php?module=TNGz&func=admin";
    $cms[cloaklogin] = "Yes";
    $cms[credits]    = "<!-- TNGz --><br />";

    // Fix up file paths
    $homepage = ($dot = strrchr($homepage, '.')) ? substr($homepage, 0, -strlen($dot)): $homepage;// strip .php or .html
    $rootpath        = $TNG['SitePath'] . "/";

    $gendexfile      = $cms[tngpath] . $gendexfile ;
    $mediapath       = $cms[tngpath] . $mediapath ;
    $headstonepath   = $cms[tngpath] . $headstonepath ;
    $historypath     = $cms[tngpath] . $historypath ;
    $backuppath      = $cms[tngpath] . $backuppath ;
    $documentpath    = $cms[tngpath] . $documentpath ;
    $photopath       = $cms[tngpath] . $photopath ;
    $logname         = $cms[tngpath] . $logname ;


    // Now fix Zikula's $register_globals=off code for TNG
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

}

// HACK for setting the Timeline width without modifying TNG files.
// There is no setting for this in TNG.  It is hardcoded to start at 500
if( !isset($_SESSION[timeline_chartwidth]) ) {
    session_register('timeline_chartwidth');
    $_SESSION[timeline_chartwidth] = 750;  // Change to what you want
}

?>
