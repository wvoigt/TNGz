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

 /**
 * get available admin panel links
 *
 * @return array array of admin links
 */

function TNGz_adminapi_getlinks()
{
    $links = array();

    pnModLangLoad('TNGz', 'admin');

    if (SecurityUtil::checkPermission('TNGz::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'modifyconfig'), 'text' => _TNGZSETTINGS);
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'TNGadmin'),     'text' => _TNGCONFG);
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'Instruct'),     'text' => _TNGZINSTRUCT);
        $links[] = array('url' => pnModURL('TNGz', 'admin', 'Info'),         'text' => _TNGZDEBUGINFO);
    }

    return $links;
}

