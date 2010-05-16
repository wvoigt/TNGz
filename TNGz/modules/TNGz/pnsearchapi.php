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
       
    $pntable       = pnDBGetTables();
    $searchTable   = $pntable['search_result'];
    $searchColumn  = $pntable['search_result_column'];

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

    if (!pnModAPIFunc('TNGz','user','TNGquery', array('connect'=>true) ) ) {
        return LogUtil::registerError (__('Error! Could not load items.', $dom) . " (#1)");
    }
    $TNG = pnModAPIFunc('TNGz','user','TNGconfig');
   
    // Check to see of this user has the permissions to see living conditionally
    $User_Can_See_Living = false;
    if (pnUserLoggedIn()) {
        // now check to make sure TNG says user can see the living
        $userid = pnUserGetVar('uname');
        $query = "SELECT allow_living FROM ". $TNG['users_table'] ." WHERE username = '$userid' ";
        if (false !== ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            if (count($result) > 0) {
                $row = $result[0];
                if ($row['allow_living'] == "1") {
                    $User_Can_See_Living = true;
                }
            }
        }
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

    $query="SELECT personID, lastname, lnprefix, firstname, suffix, title, nameorder, birthdate, birthplace, deathdate, deathplace, living, gedcom
            FROM ". $TNG['people_table'] ."
            WHERE $where
            ORDER BY lastname ASC, firstname ASC";

    if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
        return LogUtil::registerError (__('Error! Could not load items.', $dom). " (#2)");
    }

    // Process the result set and insert into search result table
    if (count($result)>0){
        foreach($result as $item) {
            if (    SecurityUtil::checkPermission('TNGz', "::", ACCESS_READ) 
                && ($User_Can_See_Living || $item['living']==0  || $TNG['nonames']==0) ) {
            
                // The Extra Stuff for the resulting search link
                $extra = serialize(array('id'     => $item['personID'],
                                         'gedcom' => $item['gedcom']
                                         )
                                   );

                // Display name as the title, with the proper form based upon the TNG settings
                // Similar to getNameRev() in genlib.php
	            $locnameorder = $item['nameorder'] ? $item['nameorder'] : ($TNG['nameorder'] ? $TNG['nameorder'] : 1);
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
                        /*! Search born prefix */
                        $display_text .= __('born:', $dom) . ' ';
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
                        /*! Search died prefix*/
                        $display_text .= $sep2 . __('died:', $dom) . ' ';
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
                    return LogUtil::registerError ( __('Error! Could not load items.', $dom) . " (#3)");
                }
            }
        }
    }
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
