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
        $render = & pnRender::getInstance('TNGz', false);
        return $render->fetch('TNGz_search_options.htm');
    }

    return '';
}

/**
 * Search plugin main function
 **/
function TNGz_searchapi_search($args)
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    pnModDBInfoLoad('Search');
       
    $pntable = pnDBGetTables();
    $searchTable   =  $pntable['search_result'];
    $searchColumn  =  $pntable['search_result_column'];

    $sessionId = session_id();

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    // Get information for the module
    $TNGz_modinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));
    
    // Set module display name
    $TNGz_modname = "TNGz";
    // set the module name to the display name if this is present
    if (isset($TNGz_modinfo['displayname']) && !empty($TNGz_modinfo['displayname'])) {
        $TNGz_modname = rawurlencode($TNGz_modinfo['displayname']);
    } 

    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');  // GetTNGpaths returns an associative array of values. Fixed #1
    // Check to be sure we can get to the TNG information
    if (file_exists($TNG['configfile']) ) {
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
    } else {
        return LogUtil::registerError (__('Error! Could not load items.', $dom) . " (#1)");
    }
    
    // Save a few TNG config variables for later use
    $tngconfig['nonames']   = $nonames;
    $tngconfig['nameorder'] = $nameorder;
    
    // Check to see of this user has the permissions to see living conditionally
    $User_Can_See_Living = false;
    if (pnUserLoggedIn()) {
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

    $sql ="SELECT personID, lastname, lnprefix, firstname, suffix, title, nameorder, birthdate, birthplace, deathdate, deathplace, living, gedcom
           FROM $people_table
           WHERE $where
           ORDER BY lastname, firstname ";


    // get the result set
    $result = &$TNG_conn->Execute($sql);
    //LogUtil::log('TNGz query : '.$sql, 'STRICT');

    if (!$result) {
        LogUtil::log('TNGz query : '.$sql, 'STRICT');
        return LogUtil::registerError (__('Error! Could not load items.', $dom). " (#2)");
    }

    // Process the result set and insert into search result table
    for (; !$result->EOF; $result->MoveNext()) {
        $item = $result->GetRowAssoc(2);
        if (    SecurityUtil::checkPermission('TNGz', "::", ACCESS_READ) 
            && ($User_Can_See_Living || $item['living']==0  || $tngconfig['nonames']==0) ) {
            
            // The Extra Stuff for the resulting search link
            $extra = serialize(array('id'     => $item['personID'],
                                     'gedcom' => $item['gedcom']
                                     )
                               );

            // Display name as the title, with the proper form based upon the TNG settings
            // Similar to getNameRev() in genlib.php
	        $locnameorder = $item['nameorder'] ? $item['nameorder'] : ($tngconfig['nameorder'] ? $tngconfig['nameorder'] : 1);
	        $lastname = trim( $item['lnprefix']." ".$item['lastname'] );
	        $title = trim($item['title']);
	        $firstname = trim( $title . " " .$item['firstname'] );
	        if( $locnameorder == 1 ) {
		        $namestr = $lastname;
		        if($firstname) {
			        if($lastname) {
                        $namestr .= ", ";
                    }
			        $namestr .= $firstname;
		        }
		        if($item['suffix']) {
                    $namestr .= " ".$item['suffix'];
                }
	        } else {
		        $namestr = trim( "$lastname $firstname" );
		        if( $item['suffix'] ) {
                    $namestr .= ", ".$item['suffix'];
                }
	        }
	        $display_title = $namestr;

            // Display other information
            if ($User_Can_See_Living || $item['living']==0 ) {
                $sep2 = '';
                $display_text = "";
                if ( $item['birthdate'] != '' || $item['birthplace'] != '') {
                    $display_text .= _TNGZ_SEARCH_BORN . ' ';
                    $sep  = '';
                    $sep2 = "; ";
                    if ( $item['birthdate'] != '') {
                        $display_text .= $item['birthdate'];
                        $sep = ', ';
                    }
                    if ( $item['birthplace'] != '') {
                        $display_text .= $sep . $item['birthplace'];
                    }
                }
                if ( $item['deathdate'] != '' || $item['deathplace'] != '') {
                    $display_text .= $sep2 . _TNGZ_SEARCH_DIED . ' ';
                    $sep  = '';
                    if ( $item['deathdate'] != '') {
                        $display_text .= $item['deathdate'];
                        $sep = ', ';
                    }
                    if ( $item['deathplace'] != '') {
                        $display_text .= $sep . $item['deathplace'];
                    }
                }
                $display_text = preg_replace('/(\s)*,(\s|,)+/',', ',$display_text);
            } else {
                $display_text = __('The following individual is flagged as living - Details withheld.', $dom);
            }

            $sql = $insertSql . '('
                       . '\'' . DataUtil::formatForStore($display_title) . '\', '
                       . '\'' . DataUtil::formatForStore($display_text) . '\', '
                       . '\'' . DataUtil::formatForStore($extra) . '\', '
                       . '\'' . $TNGz_modname . '\', '
                       . '\'' . DataUtil::formatForStore($sessionId) . '\')';
            $insertResult = DBUtil::executeSQL($sql);
            if (!$insertResult) {
                return LogUtil::registerError (_GETFAILED . " (#3)");
            }
        }
    }
    $result->Close();
    $TNG_conn->Close();

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
                                        'url'        => true
                                    ));
    return true;
}
