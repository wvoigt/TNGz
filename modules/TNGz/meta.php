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

$TNGstyle         = "genstyle.css";
$TNGmystyle       = "mytngstyle.css";
$TNGrss           = "tngrss.php";
$TNGtemplatestyle = "templatestyle.css";

// Look for css in the current Theme directory
$CurrentTheme =  pnVarPrepForOs( pnUserGetTheme() );

if( file_exists( "themes/$CurrentTheme/style/genstyle.css" ) ) {
    $TNGstyle = "themes/$CurrentTheme/style/genstyle.css";
} else {
    $TNGstyle = $cms[tngpath]."genstyle.css";
}

if( file_exists( "themes/$CurrentTheme/style/tngrss.css" ) ) {
    $TNGrss = "themes/$CurrentTheme/style/tngrss.css";
} else {
    $TNGrss = $cms[tngpath]."tngrss.php";
}

if( file_exists( "themes/$CurrentTheme/style/mytngstyle.css" ) ) {
    $TNGmystyle = "themes/$CurrentTheme/style/mytngstyle.css";
} else {
    $TNGmystyle = $cms[tngpath]."mytngstyle.css";
}

if( file_exists( "themes/$CurrentTheme/style/templatestyle.css" ) ) {
    $TNGtemplatestyle = "themes/$CurrentTheme/style/templatestyle.css";
} else {
    $TNGtemplatestyle = $cms[tngpath]."templatestyle.css";
}

// Use Zikula API to add stylesheets correctly to header
PageUtil::AddVar('stylesheet', $TNGstyle);
PageUtil::AddVar('stylesheet', $TNGtemplatestyle);
PageUtil::AddVar('stylesheet', $TNGmystyle);
PageUtil::AddVar('rawtext', '<link rel="alternate" type="application/rss+xml" href="'.$TNGrss.'" />');

