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
 * get available admin panel links
 *
 * @return array array of admin links
 */

function TNGz_adminapi_getlinks()
{
    $links = array();

    $dom = ZLanguage::getModuleDomain('TNGz');

    if (SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'modifyconfig'), 'text' => __('Settings', $dom));
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'TNGadmin'),     'text' => __('TNG Administration', $dom));
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'userlog'),      'text' => __('User Log', $dom));
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'adminlog'),     'text' => __('Administration Log', $dom));
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'Instruct'),     'text' => __('Installation Instructions', $dom));
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'Info'),         'text' => __('Information', $dom));
    }

    return $links;
}

