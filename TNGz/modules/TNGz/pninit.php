<?php
// $Id: pninit.php
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Filename: 1.0
// Based on : pnTNG
// Postnuked  by Cas Nuy
// Purpose of file:  Initialisation functions for pnTNGeague
// ----------------------------------------------------------------------

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

