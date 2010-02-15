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
 * TNGz thisday
 * Display events for today
 * @param $params['date']  show date (yes, no)
 * @param $params['birth'] show births (living, dead, yes, no) living is only if user logged in.  Yes is all always.
 * @param $params['marriage'] show marriages (living, dead, yes, no)
 * @param $params['death'] show deaths (yes, no)
 * @param $params['sortby']  sort order of entries (ascending, decending, name)
 * @param $params['wikipedia']  add link for today's events listed on Wikipedia (yes, no)
 * @param $params['link']  add link to main TNGz page (no, yes)
 * @param $params['cache']  cache the page (yes, no)
 * @param $params['newwindow']  open links in new windows (no, yes)
 * @param $params['title']  if set, adds the text at the top
 * @return string containing HTML formated display of today's events
 */
function smarty_function_thisday($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

    // Valid answers, default is the first in the list
    $answer_yes    = array('Y', 'yes', 'y', '1', 'all');  // Answers for Yes or All
    $answer_no     = array('N', 'no',  'n', '0', 'none'); // Answers for No or none
    $answer_living = array('L', 'user');                  // Answers for Show Living only if user is logged in
    $answer_dead   = array('D', 'dead');                  // Answers for Show only those that are dead
    $answer_name   = array('N', 'name');                  // Answers for Sort by Name
    $answer_up     = array('D', 'up',  'ascending', '1'); // Answers for Sort by date ascending
    $answer_down   = array('R', 'down','decending', '0'); // Answers for Show by date decending
    
    $answer_YN     = array_merge($answer_yes, $answer_no);
    $answer_YNLD   = array_merge($answer_yes, $answer_no, $answer_living, $answer_dead );
    $answer_NDR    = array_merge($answer_name, $answer_up, $answer_down);
    

    // Get parameters
    $params['date'] = (in_array($params['date'], $answer_YN   ))? $params['date'] : $answer_yes[0];
    $params['date'] = (in_array($params['date'], $answer_no   ))? $answer_no[0]   : $params['date'];    
    $params['date'] = (in_array($params['date'], $answer_yes  ))? $answer_yes[0]  : $params['date']; 

    $params['birth'] = (in_array($params['birth'], $answer_YNLD  ))? $params['birth']  : $answer_living[0];
    $params['birth'] = (in_array($params['birth'], $answer_no    ))? $answer_no[0]     : $params['birth'];    
    $params['birth'] = (in_array($params['birth'], $answer_yes   ))? $answer_yes[0]    : $params['birth']; 
    $params['birth'] = (in_array($params['birth'], $answer_living))? $answer_living[0] : $params['birth'];    
    $params['birth'] = (in_array($params['birth'], $answer_dead  ))? $answer_dead[0]   : $params['birth'];

    $params['marriage'] = (in_array($params['marriage'], $answer_YNLD  ))? $params['marriage']: $answer_no[0];
    $params['marriage'] = (in_array($params['marriage'], $answer_no    ))? $answer_no[0]      : $params['marriage'];    
    $params['marriage'] = (in_array($params['marriage'], $answer_yes   ))? $answer_yes[0]     : $params['marriage']; 
    $params['marriage'] = (in_array($params['marriage'], $answer_living))? $answer_living[0]  : $params['marriage'];    
    $params['marriage'] = (in_array($params['marriage'], $answer_dead  ))? $answer_dead[0]    : $params['marriage'];

    $params['death'] = (in_array($params['death'], $answer_YN  ))? $params['death']: $answer_yes[0];
    $params['death'] = (in_array($params['death'], $answer_no  ))? $answer_no[0]   : $params['death'];    
    $params['death'] = (in_array($params['death'], $answer_yes ))? $answer_yes[0]  : $params['death']; 

    $params['sortby'] = (in_array($params['sortby'], $answer_NDR  ))? $params['sortby'] : $answer_up[0];
    $params['sortby'] = (in_array($params['sortby'], $answer_name ))? $answer_name[0]   : $params['sortby'];
    $params['sortby'] = (in_array($params['sortby'], $answer_up   ))? $answer_up[0]     : $params['sortby'];
    $params['sortby'] = (in_array($params['sortby'], $answer_down ))? $answer_down[0]   : $params['sortby'];
    
    $params['wikipedia'] = (in_array($params['wikipedia'], $answer_YN  ))? $params['wikipedia']: $answer_yes[0];
    $params['wikipedia'] = (in_array($params['wikipedia'], $answer_no  ))? $answer_no[0]       : $params['wikipedia'];    
    $params['wikipedia'] = (in_array($params['wikipedia'], $answer_yes ))? $answer_yes[0]      : $params['wikipedia']; 

    $params['link'] = (in_array($params['link'], $answer_YN  ))? $params['link'] : $answer_yes[0];
    $params['link'] = (in_array($params['link'], $answer_no  ))? $answer_no[0]   : $params['link'];    
    $params['link'] = (in_array($params['link'], $answer_yes ))? $answer_yes[0]  : $params['link']; 

    $params['cache'] = (in_array($params['cache'], $answer_YN  ))? $params['cache'] : $answer_yes[0];
    $params['cache'] = (in_array($params['cache'], $answer_no  ))? $answer_no[0]    : $params['cache'];    
    $params['cache'] = (in_array($params['cache'], $answer_yes ))? $answer_yes[0]   : $params['cache']; 

    $params['newwindow'] = (in_array($params['newwindow'], $answer_YN  ))? $params['newwindow']: $answer_no[0];
    $params['newwindow'] = (in_array($params['newwindow'], $answer_no  ))? $answer_no[0]       : $params['newwindow'];    
    $params['newwindow'] = (in_array($params['newwindow'], $answer_yes ))? $answer_yes[0]      : $params['newwindow']; 

    $params['title'] = (empty($params['title'])) ? "" : DataUtil::formatForDisplay($params['title']);

    $lang = ZLanguage::getLanguageCode(); // get language used in Zikula

    $UserLoggedIn = ( pnUserLoggedIn() ) ? true : false ;

    $TNG = pnModAPIFunc('TNGz','user','TNGconfig'); 
    if ($TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $thisday_error  = __('Error in accessing the TNG tables.', $dom)." " . $TNG_conn->ErrorMsg();
    }

    // Check to see of this user has the permissions to see living conditionally
    $User_Can_See_Living = false;
    if ( $UserLoggedIn ){
        // now check to make sure TNG says user can see the living
        $userid = pnUserGetVar('uname');
        $query = "SELECT allow_living FROM ".$TNG['users_table']." WHERE username = '$userid' ";
        if ($result = $TNG_conn->Execute($query) ) {
            $row = $result->fields;
            if ($row['allow_living'] == "1") {
                $User_Can_See_Living = true;
            }
         }
        $result->Close();
    }

    // Get the date and time values we will need
    $thisday_time  = GetUserTime(time()) ;
    $day           = date('d',   $thisday_time );
    $monthday      = date('m-d', $thisday_time );
    $month         = date('M',   $thisday_time );
    $month         = strtoupper($month) ;

    if ($params['cache'] == "Y") {
        // See if already in the cache
        
        $thedate  = date('Ymd', $thisday_time );
        $Living   = ($User_Can_See_Living)? "Living" : "NoLiving";
        $title_hash = ($params['title'])? md5($params['title']) : "x";
        
        $cachefile    = sprintf("thisday_%s_%s_%s_%s_%s_%s_%s_%s_%s_%s_%s_%s.html",$thedate, $lang, $Living,
                                                               $params['date'],     $params['birth'], 
                                                               $params['marriage'], $params['death'], $params['link'], 
                                                               $params['sortby'],   $params['wikipedia'],
                                                               $params['newwindow'], $title_hash);

        $cacheresults = pnModAPIFunc('TNGz','user','Cache', array( 'item'=> $cachefile ));
        if ($cacheresults) {
            return $cacheresults;
        }
    }

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    $target = ($params['newwindow'] == 'Y' )? "target=_blank" :"" ;

    $thisday_error         = "";
    $thisday_showdate      = false;
    $thisday_showbirth     = false;
    $thisday_birthitems    = array();
    $thisday_showmarriage  = false;
    $thisday_marriageitems = array();
    $thisday_showdeath     = false;
    $thisday_deathitems    = array();
    $thisday_showwiki      = false;
    $thisday_wiki          = "";
    $thisday_mainmenu      = "";


    //////////// SHOW DATE ///////////////////////
    if ($params['date'] == "Y") {
        $thisday_showdate   = true;
    }

    //////////// BIRTH ///////////////////////
    if ($params['birth'] != 'N' && $have_info == 1){
        $thisday_showbirth = true;
        // determine if we show living information
        $showliving = false;  // default to no
        if ($params['birth'] == 'Y') {
            $showliving = true;
        } elseif ( $params['birth'] == 'L' && $User_Can_See_Living ) {
            $showliving = true;
        }

        $query = "SELECT personID,firstname,lastname,birthdatetr,deathdatetr,living,gedcom from ".$TNG['people_table'];
        $query .= " where '$monthday'=substring(birthdatetr,6,5)";
        if ( !$showliving ){
            $query .= " and living = '0' ";
        }
        if ($params['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($params['sortby'] =="R") {
            $query .= " order by birthdate DESC ";
        } elseif ($params['sortby'] =="D") {
            $query .= " order by birthdate ASC";
        } else {
            $query .= " order by birthdate ASC";
        }
        if (!$result = $TNG_conn->Execute($query)  ) {
            $thisday_error  = __('Error in accessing the TNG tables.', $dom)." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
            } else{
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
                    $title1 = $row['lastname'] ;
                    $title1 .= ", " ;
                    $title1 .= $row['firstname'];
                    $title1 .= " [" ;
                    $TNGzyear = substr($row['birthdatetr'],0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    if ($stat == 0) {
                        $title1 .= "-" ;
                        $TNGzyear = substr($row['deathdatetr'],0,4);
                        if ($TNGzyear == "0000" ) {
                            $title1 .= " ? ";
                        } else {
                            $title1 .= $TNGzyear;
                        }
                    }
                    $title1 .= "]" ;
                    if ($params['link']=='Y') {
                        $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "getperson",
                                     'personID'    => $row['personID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                    } else {
                        $temp = $title1;
                    }
                    $thisday_birthitems[] = $temp;
                }
            }
            $result->Close();
        }
    }
    //////////// MARRIAGE ///////////////////////
    if ($params['marriage'] != 'N' && $have_info == 1){
        $thisday_showmarriage = true;
        $showliving = false;  // default to no
        if ($params['marriage'] == 'Y') {
            $showliving = true;
        } elseif ( $params['marriage'] == 'L' && $User_Can_See_Living ) {
            $showliving = true;
        }

        $query =  "SELECT familyID, marrdatetr, divdate, f.living as FLiving, h.lastname AS HLast, h.firstname AS HFirst, h.living as HLiving, w.lastname as WLast, w.firstname as WFirst, w.living as WLiving, f.gedcom as gedcom";
        $query .= " FROM ".$TNG['families_table']." AS f LEFT JOIN ".$TNG['people_table']." AS h ON f.husband=h.personID LEFT JOIN ".$TNG['people_table']." AS w ON f.wife=w.personID";
        $query .= " WHERE '$monthday'=substring(marrdatetr,6,5)";
        if ( !$showliving ){
            $query .= " and f.living = '0' ";
        }

        if ($params['sortby'] =="N") {
            $query .= " order by h.lastname, h.firstname";
        } elseif ($params['sortby'] =="R") {
            $query .= " order by marrdatetr DESC";
        } elseif ($params['sortby'] =="D") {
            $query .= " order by marrdatetr ASC";
        } else {
            $query .= " order by marrdatetr ASC";
        }
        if (!$result = &$TNG_conn->Execute($query) ) {
            $thisday_error  = __('Error in accessing the TNG tables.', $dom)." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
	        } else {
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
 	    		    $title1 = $row['HLast'];
                    $title1 .= ", " ;
                    $title1 .= $row['HFirst'];
                    /* ThisDay plugin marriage 'and' */
                    $title1 .= " " . __('&', $dom /*! ThisDay plugin marriage 'and'*/) . " ";
	    		    $title1 .= $row['WFirst'];
                    if ($row['WLast'] != ""){
                        $title1 .= " " . $row['WLast'];
                    }
                    $title1 .= " [" ;
                    $TNGzyear = substr($row['marrdatetr'],0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "]" ;
                    if ($row['divdate']!="" ){
                        $title1 .= "(" . __('Divorced', $dom) . ")" ;
                    }
                    if ($params['link']=='Y') {
                        $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "familygroup",
                                     'familyID'    => $row['familyID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                    } else {
                        $temp = $title1;
                    }
                    $thisday_marriageitems[] = $temp;
	    	    }
	        }
            $result->Close();
        }
    }

    //////////// DEATH ///////////////////////
    if ($params['death'] != 'N' && $have_info == 1){
        $thisday_showdeath = true;
        $query = "SELECT personID,firstname,lastname,birthdatetr,deathdatetr,living,gedcom from ".$TNG['people_table'];
        $query .= " where '$monthday'=substring(deathdatetr,6,5)";
        if ($params['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($params['sortby'] =="R") {
            $query .= " order by deathdate DESC";
        } elseif ($params['sortby'] =="D") {
            $query .= " order by deathdate ASC";
        } else {
            $query .= " order by deathdate ASC";
        }
        if (!$result = &$TNG_conn->Execute($query) ) {
            $thisday_error  = __('Error in accessing the TNG tables.', $dom)." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
	        } else{
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
                    //list($id,$first,$last,$start,$end,$stat,$gedcom) = $result->fields;
	    		    $title1 = $row['lastname'];
                    $title1 .= ", " ;
                    $title1 .= $row['firstname'];
                    $title1 .= " [" ;
                    $TNGzyear = substr($row['birthdatetr'],0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "-" ;
                    $TNGzyear = substr($row['deathdatetr'],0,4);
                     if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "]" ;
                    if ($params['link']=='Y') {
                        $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "getperson",
                                     'personID'    => $row['personID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                    } else {
                        $temp = $title1;
                    }
                    $thisday_deathitems[] = $temp;
	    	    }
	        }
            $result->Close();
        }
    }


    //////////// WIKI Link ///////////////////////
    if ($params['wikipedia'] == "Y") {
        $thisday_showwiki   = true;
    }

    //////////// TNG Main Menu Link //////////////
    if ($params['link'] == "Y") {
        $thisday_mainmenu = pnModAPIFunc('TNGz','user','MakeRef',
                                          array('func'        => "",
                                                'description' => __('Genealogy Page', $dom),
                                                'target'      => $target
                                               ));
    } else {
        $thisday_mainmenu = false;
    }


    $render = & pnRender::getInstance('TNGz', false);

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $render->assign('todaytime'    , $thisday_time);
    $render->assign('showdate'     , $thisday_showdate);
    $render->assign('showbirth'    , $thisday_showbirth);
    $render->assign('birth'        , $thisday_birthitems);
    $render->assign('showmarriage' , $thisday_showmarriage);
    $render->assign('marriage'     , $thisday_marriageitems);
    $render->assign('showdeath'    , $thisday_showdeath);
    $render->assign('death'        , $thisday_deathitems);
    $render->assign('showwiki'     , $thisday_showwiki);
    $render->assign('mainmenu'     , $thisday_mainmenu);
    $render->assign('thisdayerror' , $thisday_error);
    $render->assign('title'        , $params['title']);

    // Populate block info and pass to theme
    $output = $render->fetch('TNGz_plugin_ThisDay.htm');

    // now update the cache
    if ($params['cache'] == "Y") {
        pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );
    }
    return $output;
}
