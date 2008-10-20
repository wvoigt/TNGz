<?php
// $Id: pnadminapi.php, v 1.01 2008/10/06 13:08:28 wvoigt Exp $

/**
 * get available admin panel links
 *
 * @author Wendel Voigt
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

