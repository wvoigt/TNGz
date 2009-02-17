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
    // check module version
    // some code based on work from Axel Guckelsberger - thanks for this inspiration
    $currentversion = $args['version'];

    // get newest version number
    require_once('Snoopy.class.php');
    $snoopy = new Snoopy;
    $snoopy->fetchtext("http://code.zikula.org/tngz/browser/trunk/versions/tng_version.txt?format=txt");

    $newestversion = $snoopy->results;
    $newestversion = trim($newestversion);

    $versionimage = "modules/TNGz/pnimages/green_dot.gif";

    if ($currentversion < $newestversion) {
        // generate red image if new version is available
        $versionimage = "modules/TNGz/pnimages/red_dot.gif";
    }
    echo("<img src='".$versionimage."' width='10' height='10' alt='status' /> ".$currentversion);

    if ($currentversion < $newestversion) {
        // generate link if new version is available
        echo (" (<a id=\"versioncheck\" href=\"http://tng.lythgoes.net/downloads7/index.php\" style=\"color:red;\"><strong>".$newestversion." available</strong></a>)");
    }
    return;
}

