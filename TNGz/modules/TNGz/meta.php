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

// Get TNG configuration information
$TNG = pnModAPIFunc('TNGz','user','TNGconfig');

// Look for css in the current Theme directory
$CurrentTheme =  pnVarPrepForOs( pnUserGetTheme() );

if( file_exists( "themes/$CurrentTheme/style/genstyle.css" ) ) {
    $TNGstyle = "themes/$CurrentTheme/style/genstyle.css";
} else {
    $TNGstyle = $cms['tngpath']. $TNG['css_dir'] . 'genstyle.css';
}

if( file_exists( "themes/$CurrentTheme/style/tngrss.css" ) ) {
    $TNGrss = "themes/$CurrentTheme/style/tngrss.css";
} else {
    $TNGrss = $cms['tngpath']."tngrss.php";
}

if( file_exists( "themes/$CurrentTheme/style/mytngstyle.css" ) ) {
    $TNGmystyle = "themes/$CurrentTheme/style/mytngstyle.css";
} else {
    $TNGmystyle = $cms['tngpath']. $TNG['css_dir'] . 'mytngstyle.css';
}

if( file_exists( "themes/$CurrentTheme/style/templatestyle.css" ) ) {
    $TNGtemplatestyle = "themes/$CurrentTheme/style/templatestyle.css";
} else {
    $TNGtemplatestyle = $cms['tngpath']. $TNG['css_dir'] . 'templatestyle.css';
}

// Use Zikula API to add stylesheets correctly to header
PageUtil::AddVar('stylesheet', $TNGstyle);
PageUtil::AddVar('stylesheet', $TNGtemplatestyle);
PageUtil::AddVar('stylesheet', $TNGmystyle);
PageUtil::AddVar('rawtext', '<link rel="alternate" type="application/rss+xml" href="'.$TNGrss.'" />');

