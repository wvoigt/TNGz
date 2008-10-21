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
 * Search plugin info
 **/
function TNGz_searchapi_info()
{
    return array('title' => 'TNGz',
                 'functions' => array('TNGz' => 'search'));
}

/**
 * Search form component
 **/
function TNGz_searchapi_options($args)
{
    if (SecurityUtil::checkPermission( 'TNGz::', '::', ACCESS_READ)) {
        $pnRender = pnRender::getInstance('TNGz');
        return $pnRender->fetch('TNGz_search_options.htm');
    }

    return '';
}

/**
 * Search plugin main function
 **/
function TNGz_searchapi_search($args)
{
    pnModDBInfoLoad('Search');
    $pntable = pnDBGetTables();
    $searchTable   =  $pntable['search_result'];
    $searchColumn  =  $pntable['search_result_column'];

    $sessionId = session_id();

    $TNGstyle = pnModGetVar('TNGz', '_style');

    list($TNG_configfile, $TNG_dir, $TNG_Site_path, $TNG_WebRoot, $TNG_configpath) = pnModAPIFunc('TNGz','user','GetTNGpaths');
    // Check to be sure we can get to the TNG information
    if (file_exists($TNG_configfile) ){
        include($TNG_configfile);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    } else {
        $have_info = 0;
        return LogUtil::registerError (_GETFAILED);
    }
    // Check to see of this user has the permissions to see living conditionally
    $User_Can_See_Living = false;
    if ( pnUserLoggedIn() ){
        // now check to make sure TNG says user can see the living
        $userid = pnUserGetVar('uname');
        $query = "SELECT allow_living FROM $users_table WHERE username = '$userid' ";
        if ($result = &$TNG_conn->Execute($query) ) {
            list($TNG_living) = $result->fields;
            if ($TNG_living == "1") {
                $User_Can_See_Living = true;
            }
         }
        $result->Close();
    }



    $insertSql =
"INSERT INTO $searchTable
  ($searchColumn[title],
   $searchColumn[text],
   $searchColumn[extra],
   $searchColumn[module],
   $searchColumn[session])
VALUES ";

    //============ Surnames =============//
    $where = search_construct_where($args,
                                    array('lastname',
                                          'firstname'),
                                    null);

    $sql ="SELECT personID, lastname, lnprefix, firstname, suffix, birthdate, birthplace, deathdate, deathplace, living, gedcom
           FROM $people_table
           WHERE $where
           ORDER BY lastname, firstname ";


    // get the result set
    $result = &$TNG_conn->Execute($sql);
    //LogUtil::log('TNGz query : '.$sql, 'STRICT');

    if (!$result) {
        LogUtil::log('TNGz query : '.$sql, 'STRICT');
        return LogUtil::registerError (_GETFAILED);
    }

    // Process the result set and insert into search result table
    for (; !$result->EOF; $result->MoveNext()) {
        $item = $result->GetRowAssoc(2);
        if (SecurityUtil::checkPermission('TNGz', "::", ACCESS_READ)) {
            $extra = serialize(array('style'  => $TNGstyle,
                                     'id'     => $item['personID'],
                                     'gedcom' => $item['gedcom']
                                     )
                               );

            $display_title = $item['lastname'] . ', ' . $item['firstname'];

            if ($User_Can_See_Living || $item['living']==0 ) {
                $sep2 = '';
                $display_text = "";
                if ( $item['birthdate'] != '' || $item['birthplace'] != '') {
                    $display_text .= _TNGZ_SEARCH_BORN . ' ';
                    $sep  = '';
                    $sep2 = "; ";
                    if ( $item['birthdate'] != ''){
                        $display_text .= $item['birthdate'];
                        $sep = ', ';
                    }
                    if ( $item['birthplace'] != ''){
                        $display_text .= $sep . $item['birthplace'];
                    }
                }
                if ( $item['deathdate'] != '' || $item['deathplace'] != '') {
                    $display_text .= $sep2 . _TNGZ_SEARCH_DIED . ' ';
                    $sep  = '';
                    if ( $item['deathdate'] != ''){
                        $display_text .= $item['deathdate'];
                        $sep = ', ';
                    }
                    if ( $item['deathplace'] != ''){
                        $display_text .= $sep . $item['deathplace'];
                    }
                }
                $display_text = preg_replace('/(\s)*,(\s|,)+/',', ',$display_text);
            } else {
                $display_text = _TNGZ_SEARCH_LIVING;
            }

            $sql = $insertSql . '('
                       . '\'' . DataUtil::formatForStore($display_title) . '\', '
                       . '\'' . DataUtil::formatForStore($display_text) . '\', '
                       . '\'' . DataUtil::formatForStore($extra) . '\', '
                       . '\'' . 'TNGz'. '\', '
                       . '\'' . DataUtil::formatForStore($sessionId) . '\')';
            $insertResult = DBUtil::executeSQL($sql);
            if (!$insertResult) {
                return LogUtil::registerError (_GETFAILED);
            }
        }
    }
    $result->Close();

    return true;
}

/**
 * Do last minute access checking and assign URL to items
 *
 * Access checking is ignored since access check has
 * already been done. But we do add a URL to the found item
 */
function TNGz_searchapi_search_check(&$args)
{
    $datarow = &$args['datarow'];
    $extra = unserialize($datarow['extra']);
    $datarow['url'] = pnModAPIFunc('TNGz','user','MakeRef',
                                   array('func'      => "getperson",
                                        'personID'   => $extra['id'],
                                        'tree'       => $extra['gedcom'],
                                        'RefType'    => $extra['style'],
                                        'url'        => true
                                    ));
    return true;
}
