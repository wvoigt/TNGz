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
 * initialise the TNGz module
 * This function is only ever called once during the lifetime of this module
 */
function TNGz_init()
{

    pnModSetVar(TNGz, '_loc'     ,  'TNG');
    pnModSetVar(TNGz, '_window'  ,   0);
    pnModSetVar(TNGz, '_guest'   ,   0);
    pnModSetVar(TNGz, '_gname'   ,  _TNGZGUESTDEFAULT);
    pnModSetVar(TNGz, '_users'   ,   0);
    pnModSetVar(TNGz, '_living'  ,   0);
    pnModSetVar(TNGz, '_gedcom'  ,   0);
    pnModSetVar(TNGz, '_lds'     ,   0);
    pnModSetVar(TNGz, '_sync'    ,   1);
    pnModSetVar(TNGz, '_style'   ,   0);

    return true;
}
/**
 * update the module
 * This function is only ever called once during the lifetime of this module
 */
function TNGz_upgrade($oldversion)
{
    $successful = false;

    switch($oldversion) {
        case 0.00:
        case 1.00:
        case 2.00:
        default:
              $successful = true;
              break;
    }
    return $successful;
}


/**
 * delete the module
 * This function is only ever called once during the lifetime of this module
 */
function TNGz_delete()
{
    pnModDelVar(TNGz);

    return true;
}

