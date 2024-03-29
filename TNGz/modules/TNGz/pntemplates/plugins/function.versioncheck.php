<?php
/**
 * Zikula Application Framework
 *
 * @copyright  (c) Zikula Development Team
 * @link       http://www.zikula.org
 * @version    $Id$
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     sven schomacker <hilope@gmail.com>
 * @category   Zikula_Extension
 * @package    Utilities
 * @subpackage TNGz
 */

// This plugin checks current version for TNGZ or will
// check if a newer version is available for download.

function smarty_function_versioncheck($args)
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    // check module version
    // some code based on work from Axel Guckelsberger - thanks for this inspiration
    $currentversion = $args['version'];

    $valid_programs = array('TNGz', 'TNG');  // first in list is the default
    $program = (in_array($args['program'], $valid_programs))? $args['program'] : $valid_programs[0];

    if ($program == "TNG"){
       $checksite = "http://code.zikula.org/tngz/browser/trunk/versions/tng_version.txt?format=txt";
       $downloadsite = "http://tng.lythgoes.net/downloads7/index.php";
    }

    if ($program == "TNGz"){
       $checksite = "http://code.zikula.org/tngz/browser/trunk/versions/tngz_version.txt?format=txt";
       $downloadsite = "http://code.zikula.org/tngz/downloads";

       // Get current Version of TNG
       $ModInfo = pnModGetInfo(pnModGetIDFromName('TNGz'));
       $currentversion = trim($ModInfo['version']);
    }


    // get newest version number
    require_once('Snoopy.class.php');
    $snoopy = new Snoopy;
    $snoopy->fetchtext($checksite);

    $newestversion = $snoopy->results;
    $newestversion = (strlen($newestversion) < 40 ) ? trim($newestversion) : ""; // If site returns long error message, just leave it out

    $versionimage = "images/icons/extrasmall/button_ok.gif";

    if ($currentversion < $newestversion) {
        // generate red image if new version is available
        $versionimage = "images/icons/extrasmall/agt_update_recommended.gif";
    }
    echo("<img src='".$versionimage."' alt='status' /> ".$program . " " . "Version" ." ". $currentversion . " ");

    if ($currentversion < $newestversion) {
        // generate link if new version is available
        echo ("<a id=\"versioncheck\" href=\"$downloadsite\" style=\"color:red;\"><strong>". __('Get the latest recommended version', $dom)  . " (".$newestversion.")</strong></a>");
    } else {
        echo (__('This is the latest recommended version', $dom));
    }
    return;
}

