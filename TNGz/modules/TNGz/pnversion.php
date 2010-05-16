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
 
$dom = ZLanguage::getModuleDomain('TNGz');

$modversion['name']           = 'TNGz';   //Module name
$modversion['version']        = '1.5';    //Version number
/*! module display name */
$modversion['displayname']    =  __('Genealogy', $dom );
/*! module description */
$modversion['description']    =  __('TNG genealogy website integration module for Zikula', $dom /*! module description */);
/*! module name that appears in the URL */
$modversion['url']            =  __('TNGz', $dom /*! module name that appears in the URL */); 
$modversion['credits']        = 'pndocs/credits.txt';  //Credits file
$modversion['help']           = 'pndocs/readme.html';  //Help file
$modversion['changelog']      = 'pndocs/changelog.txt';  //Change log file
$modversion['license']        = 'pndocs/license.txt';  //License file
$modversion['official']       = '0';  //Official Zikula Approved Module 1 = yes, 0 = no
$modversion['author']         = 'Wendel Voigt';  //Author
$modversion['contact']        = 'http://code.zikula.org/tngz';  //The authors website or contact email address
$modversion['admin']          = 1;
$modversion['securityschema'] = array('TNGz::' => '::');

