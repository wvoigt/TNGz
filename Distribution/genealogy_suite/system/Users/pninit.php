<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_System_Modules
 * @subpackage Users
 *
 * modified version for "Genealogy Suite", because TNG uses "md5" instead of "sha256"
 * based on pninit ref 25128 (Zikula 1.1.1)
*/

/**
 * initialise the users module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance.
 * This function MUST exist in the pninit file for a module
 *
 * @author       Mark West
 * @return       bool       true on success, false otherwise
 */
function users_init()
{
    if (!DBUtil::createTable('session_info')) {
        return false;
    }

    if (!DBUtil::createTable('users')) {
        return false;
    }

    if (!DBUtil::createTable('users_temp')) {
        return false;
    }

    // Set default values for module
    users_defaultdata();

    pnModSetVar('Users','itemsperpage', 25);
    pnModSetVar('Users','reg_allowreg', 1);
    pnModSetVar('Users','reg_verifyemail', 1);
    pnModSetVar('Users','reg_Illegalusername', 'root adm linux webmaster admin god administrator administrador nobody anonymous anonimo');
    pnModSetVar('Users','reg_Illegaldomains', '');
    pnModSetVar('Users','reg_Illegaluseragents', '');
    pnModSetVar('Users','reg_noregreasons', _USER_REGDISABLED);
    pnModSetVar('Users','reg_uniemail', 1);
    pnModSetVar('Users','reg_notifyemail', '');
    pnModSetVar('Users','reg_optitems', 0);
    pnModSetVar('Users','userimg', 'images/menu');
    pnModSetVar('Users','minage', 13);
    pnModSetVar('Users','minpass', 5);
    pnModSetVar('Users','anonymous', 'Guest');
    pnModSetVar('Users','savelastlogindate', 0);
    pnModSetVar('Users','loginviaoption', 0);
    pnModSetVar('Users','moderation', 0);
    pnModSetVar('Users','hash_method', 'md5');
    pnModSetVar('Users','login_redirect', 1);
    pnModSetvar('Users', 'reg_question', '');
    pnModSetvar('Users', 'reg_answer', '');
    pnModSetvar('Users', 'idnnames', 1);

    // Initialisation successful
    return true;
}


/**
 * upgrade the users module from an old version
 *
 * This function can be called multiple times
 * This function MUST exist in the pninit file for a module
 *
 * @author       Mark West
 * @return       bool       true on success, false otherwise
 */
function users_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        // version 0.2 shipped with PN .72x, .75 and version 0.3 with .76x
        case 0.2:
        case 0.3:
            pnModSetVar('Users', 'itemsperpage', 25);
            // At the end of the successful completion of this function we
            // recurse the upgrade to handle any other upgrades that need
            // to be done.  This allows us to upgrade from any version to
            // the current version with ease
            return Users_upgrade(0.9);
        // version 0.9 has been used in cvs for a while
        case 0.9:
            pnModSetVar('Users', 'reg_allowreg',        pnConfigGetVar('reg_allowreg'));
            pnModSetVar('Users', 'reg_verifyemail',     pnConfigGetVar('reg_verifyemail'));
            pnModSetVar('Users', 'reg_Illegalusername', pnConfigGetVar('reg_Illegalusername'));
            pnModSetVar('Users', 'reg_noregreasons',    pnConfigGetVar('reg_noregreasons'));
            pnModSetVar('Users', 'reg_uniemail',        pnConfigGetVar('reg_uniemail'));
            pnModSetVar('Users', 'userimg',             pnConfigGetVar('userimg'));
            pnModSetVar('Users', 'minage',              pnConfigGetVar('minage'));
            pnModSetVar('Users', 'minpass',             pnConfigGetVar('minpass'));
            pnModSetVar('Users', 'anonymous',           pnConfigGetVar('anonymous'));
            pnModSetVar('Users', 'reg_Illegaldomains',  pnConfigGetVar('reg_Illegaldomains'));
            pnModSetvar('Users', 'reg_question',        pnConfigGetVar('reg_question'));
            pnModSetvar('Users', 'reg_answer',          pnConfigGetVar('reg_answer'));
            pnModSetvar('Users', 'idnnames',            pnConfigGetVar('idnnames'));
            pnModSetVar('Users', 'loginviaoption',      0);
            pnModSetVar('Users', 'savelastlogindate',   0);
            pnModSetVar('Users', 'moderation',          0);
            pnModSetVar('Users', 'hash_method',         'md5');
            pnConfigDelVar('reg_allowreg');
            pnConfigDelVar('reg_verifyemail');
            pnConfigDelVar('reg_Illegalusername');
            pnConfigDelVar('reg_Illegaldomains');
            pnConfigDelVar('reg_noregreasons');
            pnConfigDelVar('reg_uniemail');
            pnConfigDelVar('userimg');
            pnConfigDelVar('usergraphic');
            pnConfigDelVar('minage');
            pnConfigDelVar('minpass');
            pnConfigDelVar('anonymous');
            pnConfigDelVar('idnnames');
            return Users_upgrade(1.0);
        case 1.0:
            return Users_upgrade(1.1);
        case 1.1:
            $pntable = pnDBGetTables();
            // update the reg date to a time stamp
            $sql = "UPDATE $pntable[users] SET pn_user_regdate = FROM_UNIXTIME(pn_user_regdate)";
            if (!DBUtil::executeSQL($sql)) {
                return LogUtil::registerError (_UPDATETABLEFAILED);
            }
            // update the users table schema
            if (!DBUtil::changeTable('users')) {
                return false;
            }
            // create the temp users tables
            if (!DBUtil::createTable('users_temp')) {
                return false;
            }

            // add new module vars
            pnModSetVar('Users','savelastlogindate', 0);
            pnModSetVar('Users','loginviaoption', 0);
            pnModSetVar('Users','moderation', 0);
            pnModSetVar('Users','hash_method', 'md5');

            pnModSetVar('Users','login_redirect', 1);
            pnConfigDelVar('login_redirect');
            return users_upgrade(1.2);
        case 1.2:
            return users_upgrade(1.6);
        case 1.6:
            DBUtil::dropColumn('users', array('pn_umode', 'pn_uorder', 'pn_thold', 'pn_noscore', 'pn_commentmax'));
            return users_upgrade(1.7);
        case 1.7:
            // remove the NewUser and LostPassword modules
            $oldmods = array('NewUser', 'LostPassword');
            foreach ($oldmods as $oldmod) {
                if (pnModAvailable($oldmod)) {
                    $modid = pnModGetIDFromName($oldmod);
                    pnModAPIFunc('Modules', 'admin', 'remove', array('id' => $modid));
                }
            }
            return users_upgrade(1.8);
        case 1.8:
            // remove the MailUsers module
            $oldmod = 'MailUsers';
            if (pnModAvailable($oldmod)) {
                $modid = pnModGetIDFromName($oldmod);
                pnModAPIFunc('Modules', 'admin', 'remove', array('id' => $modid));
            }
            return users_upgrade(1.9);
    }

    // Update successful
    return true;
}


/**
 * delete the users module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * This function MUST exist in the pninit file for a module
 *
 * Since the users module should never be deleted we'all always return false here
 * @author       Mark West
 * @return       bool       false
 */
function users_delete()
{
    // Deletion not allowed
    return false;
}

/**
 * create the default data for the users module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * Since the users module should never be deleted we'all always return false here
 * @author       Mark West
 * @return       bool       false
 */
function users_defaultdata()
{
    // Anonymous
    $record = array();
    $record['uid']             = ''._USER_1_a.'';
    $record['uname']           = ''._USER_1_c.'';
    $record['pass']            = ''._USER_1_r.'';
    $record['storynum']        = ''._USER_1_s.'';
    $record['umode']           = ''._USER_1_t.'';
    $record['uorder']          = ''._USER_1_u.'';
    $record['thold']           = ''._USER_1_v.'';
    $record['noscore']         = ''._USER_1_w.'';
    $record['bio']             = ''._USER_1_x.'';
    $record['ublockon']        = ''._USER_1_y.'';
    $record['ublock']          = ''._USER_1_aa.'';
    $record['theme']           = ''._USER_1_ab.'';
    $record['commentmax']      = ''._USER_1_ac.'';
    $record['counter']         = ''._USER_1_ad.'';
    $record['timezone_offset'] = ''._USER_1_ae.'';
    $record['hash_method']     = ''._USER_1_af.'';
    $record['activated']       = '1';
    DBUtil::insertObject($record, 'users', 'uid', true);

    // Admin
    $record = array();
    $record['uid']             = ''._USER_2_a.'';
    $record['uname']           = ''._USER_2_c.'';
    $record['email']           = ''._USER_2_d.'';
    $record['pass']            = ''._USER_2_r.'';
    $record['storynum']        = ''._USER_2_s.'';
    $record['umode']           = ''._USER_2_t.'';
    $record['uorder']          = ''._USER_2_u.'';
    $record['thold']           = ''._USER_2_v.'';
    $record['noscore']         = ''._USER_2_w.'';
    $record['bio']             = ''._USER_2_x.'';
    $record['ublockon']        = ''._USER_2_y.'';
    $record['ublock']          = ''._USER_2_aa.'';
    $record['theme']           = ''._USER_2_ab.'';
    $record['commentmax']      = ''._USER_2_ac.'';
    $record['counter']         = ''._USER_2_ad.'';
    $record['timezone_offset'] = ''._USER_2_ae.'';
    $record['activated']       = '1';

    DBUtil::insertObject($record, 'users', 'uid', true);
}

/**
 * for the upgrade script, 0.8MS1 to 0.8.MS2 update
 *
 * @return boot
 */
function users_changestructure()
{
    // Easy drop and recreate for session table
    DBUtil::dropTable('session_info');
    DBUtil::createTable('session_info');

    return true;
}

/**
 * for the upgrade script, upgrade users from 0.76x to 0.8 framework
 *
 * @return bool
 */
function users_76xupgrade_activate()
{
    // Easy drop and recreate for session table
    DBUtil::dropTable('session_info');
    DBUtil::createTable('session_info');

    // activate all users so they can be logged in
    $obj = array('hash_method' => 1,
                 'activated'   => 1);
    $pntable = pnDBGetTables();
    $column = $pntable['users_column'];
    $where = "$column[hash_method] = 8";
    DBUtil::updateObject($obj, 'users', $where, '');

    pnModSetVar('Users','savelastlogindate', 0);
    pnModSetVar('Users','loginviaoption', 0);
    pnModSetVar('Users','moderation', 0);
    pnModSetVar('Users','hash_method', 'md5');

    return true;
}


/**
 * Preprocess .7 series users table upgrade
 *
 * @return bool true
 */
function users_migrate_userdatafields76x_pre()
{
    // here we need to backup the data because the user table will be truncated
    // and we require the new structure for the update process
    // due to all sorts of dependency problems we use some SQL shorthand.
    // unsure if it's cross DB compliant but doesnt matter since all .7 series
    // is MySQL only anyhow - drak
    $pntables = pnDBGetTables();
    // update the timezone offset to reflect the new formatting (-12->+12)
    // we do this at this point as it's a lot let processing now than later - we can do it all in sql
    $sql = "UPDATE $pntables[users] SET pn_timezone_offset = pn_timezone_offset-12";
    DBUtil::executeSQL($sql);
    $sql = "CREATE TABLE $pntables[users76x] SELECT * FROM $pntables[users]";
    DBUtil::executeSQL($sql);
    return true;
}

/**
 * Postprocess .7 series user tables upgrade
 *
 * @return bool true
 */
function users_migrate_userdatafields76x_post()
{
    if (!pnModAvailable('Profile')) {
        return true;
    }

    // migrate DUD from original users table new DUD
    $count = 1;
    $norecords = DBUtil::selectObjectCount('users76x');

	$names = array('_UREALNAME', '_UREALEMAIL', '_UFAKEMAIL', '_YOURHOMEPAGE', '_TIMEZONEOFFSET', '_YOURAVATAR', '_YICQ', '_YAIM',
					'_YYIM', '_YMSNM', '_YLOCATION', '_YOCCUPATION', '_YINTERESTS', '_SIGNATURE', '_EXTRAINFO', '_PASSWORD');
	$name = '';
	$properties = array();
	foreach ($names as $name) {
		$property = DBUtil::selectObjectByID('user_property', $name, 'prop_label');
		if (!$property) {
			return false;
		}
		$properties[$name] = $property['prop_id'];
	}

	pnModDBInfoLoad('Profile');
	$pntable = pnDBGetTables();
	$datatable = $pntable['user_data'];
	$datacolumns = $pntable['user_data_column'];
    while ($count < $norecords) {
        $columnArray = array('uid', 'name', 'femail', 'url', 'timezone_offset', 'user_avatar', 'user_intrest', 'bio', 'user_yim', 'user_aim', 'user_icq', 'user_msnm', 'user_occ', 'user_sig', 'user_from');
        $userArray = DBUtil::selectObjectArray('users76x', '', '', $count, 50, '', null, null, $columnArray);
        unset($columnArray['uid']);
        foreach ($userArray as $user) {
            foreach ($columnArray as $column) {
                if ($column != 'uid' &&
                    (!in_array($column, array('url', 'user_avatar', 'timezone_offset')) && $user[$column] != '') ||
                    ($column == 'url' && $user[$column] != 'http://' && $user[$column] != '') ||
                    ($column == 'user_avatar' && $user[$column] != 'blank.gif' && $user[$column] != '') ||
                    ($column == 'timezone_offset' && $user[$column] != 0.0 && $user[$column] != '')) {

					$var = pnUserDynamicAlias($column);
					$sql = 'INSERT INTO '.$pntable['user_data'].' SET '.$datacolumns['uda_propid'].'='.$properties[$var].', '.$datacolumns['uda_uid'].'='.$user[uid].', '.$datacolumns['uda_value'].'=\''.pnVarPrepForStore($user[$column]).'\'';
					$result = DBUtil::executeSQL($sql);
					$result->Close();
                }
            }
            $count++;
        }
    }
    // cleanup
    // drop the fields now in dud
    DBUtil::dropColumn('users', array('pn_name', 'pn_femail', 'pn_url', 'pn_timezone_offset', 'pn_user_avatar', 'pn_user_intrest', 'pn_bio', 'pn_user_yim', 'pn_user_aim', 'pn_user_icq', 'pn_user_msnm', 'pn_user_occ', 'pn_user_sig', 'pn_user_from'));
    DBUtil::dropTable('users76x');
    $textValidationString = 'a:6:{s:8:"required";s:1:"0";s:6:"viewby";s:1:"0";s:11:"displaytype";s:1:"1";s:11:"listoptions";s:0:"";s:4:"note";s:0:"";s:10:"validation";s:0:"";}';
    // update the field type for those field(s)that have moved out of the users table
    // currently timezone only
    $objArray[] = array('prop_label' => '_TIMEZONEOFFSET', 'prop_dtype' => 1);
    // set the correct format for some fields
    $objArray[] = array('prop_label' => '_EXTRAINFO', 'prop_dtype' => 2, 'prop_validation' => $textValidationString);
    $objArray[] = array('prop_label' => '_SIGNATURE', 'prop_dtype' => 2, 'prop_validation' => $textValidationString);
    $objArray[] = array('prop_label' => '_YINTERESTS', 'prop_dtype' => 2, 'prop_validation' => $textValidationString);
    DBUtil::updateObjectArray($objArray, 'user_property', 'prop_label');
    return true;
}
