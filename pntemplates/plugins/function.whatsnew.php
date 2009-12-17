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
 * @param $params['people']  show most recently changed people (yes, no)
 * @param $params['family']  show most recently changed families (yes, no)
 * @param $params['photos']  show most recently changed photos (yes, no)
 * @param $params['max']     maximum number of changes to show for each category
 * @param $params['days']    number of days to look for changes
 * @param $params['cache']  cache the page (yes, no)
 * @param $params['newwindow']  open links in new windows (no, yes)
 * @param $params['title']  if set, adds the text at the top
 * @return string containing HTML formated display of the changed items
 */
function smarty_function_whatsnew($params, &$smarty)
{  

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }
    
    // Valid answers, default is the first in the list
    $answer_yes    = array('Y', 'yes', 'y', '1', 'on', 'all');  // Answers for Yes or All
    $answer_no     = array('N', 'no',  'n', '0', 'off','none'); // Answers for No or none   
    $answer_YN     = array_merge($answer_yes, $answer_no);

    // Get parameters
    $params['people'] = (in_array($params['people'], $answer_YN   ))? $params['people'] : $answer_yes[0];
    $params['people'] = (in_array($params['people'], $answer_no   ))? $answer_no[0]     : $params['people'];    
    $params['people'] = (in_array($params['people'], $answer_yes  ))? $answer_yes[0]    : $params['people']; 

    $params['family'] = (in_array($params['family'], $answer_YN  ))? $params['family']: $answer_yes[0];
    $params['family'] = (in_array($params['family'], $answer_no  ))? $answer_no[0]    : $params['family'];    
    $params['family'] = (in_array($params['family'], $answer_yes ))? $answer_yes[0]   : $params['family']; 
   
    $params['photos'] = (in_array($params['photos'], $answer_YN  ))? $params['photos']: $answer_yes[0];
    $params['photos'] = (in_array($params['photos'], $answer_no  ))? $answer_no[0]    : $params['photos'];    
    $params['photos'] = (in_array($params['photos'], $answer_yes ))? $answer_yes[0]   : $params['photos'];

    $params['newwindow'] = (in_array($params['newwindow'], $answer_YN  ))? $params['newwindow']: $answer_no[0];
    $params['newwindow'] = (in_array($params['newwindow'], $answer_no  ))? $answer_no[0]       : $params['newwindow'];    
    $params['newwindow'] = (in_array($params['newwindow'], $answer_yes ))? $answer_yes[0]      : $params['newwindow']; 

    $params['link'] = (in_array($params['link'], $answer_YN  ))? $params['link'] : $answer_yes[0];
    $params['link'] = (in_array($params['link'], $answer_no  ))? $answer_no[0]   : $params['link'];    
    $params['link'] = (in_array($params['link'], $answer_yes ))? $answer_yes[0]  : $params['link']; 

    $params['cache'] = (in_array($params['cache'], $answer_YN  ))? $params['cache'] : $answer_yes[0];
    $params['cache'] = (in_array($params['cache'], $answer_no  ))? $answer_no[0]    : $params['cache'];    
    $params['cache'] = (in_array($params['cache'], $answer_yes ))? $answer_yes[0]   : $params['cache']; 

    $params['title'] = (empty($params['title'])) ? "" : DataUtil::formatForDisplay($params['title']);
    

    if (empty($params['max']) || !is_numeric($params['max']) ) {
        $params['max']    = _TNGZ_WHATSNEW_HOWMANY_NUM;
    }
    if (empty($params['days'])  || !is_numeric($params['days'])  ) {
        $params['days']     = _TNGZ_WHATSNEW_HOWLONG_NUM;
    }
    
    $lang = ZLanguage::getLanguageCode(); // get language used in Zikula

    $TNG = pnModAPIFunc('TNGz','user','TNGconfig');
    if ($TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $thisday_error  = ""._PEOPLEDBFERROR." " . $TNG_conn->ErrorMsg();
    }

    if ($params['cache'] == "Y") {
        // See if already in the cache
        $title_part = ($params['title'])? md5($params['title']) : "x";
        
        $cachefile    = sprintf("whatsnew_%s_%s_%s_%s_%s_%s_%s_%s.html",
                                 $lang, $params['people'], $params['family'], $params['photos'], $params['link'],
                                 $params['max'], $params['days'], $params['newwindow'], $title_part);

        $cacheresults = pnModAPIFunc('TNGz','user','Cache', array( 'item'=> $cachefile ));
        if ($cacheresults) {
            return $cacheresults;
        }
    }


    $target = ($params['newwindow'] == 'Y' )? "target=_blank" :"" ;
    $howlong  = $params['days'];
    $maxitems = $params['max'];

    // Default values for items to send to the template
    $whatsnew_error            = "";
    $whatsnew_showpeople       = false;
    $whatsnew_showpeopleitems  = array();
    $whatsnew_showfamily       = false;
    $whatsnew_showfamilyitems  = array();
    $whatsnew_showhistory      = false;
    $whatsnew_showhistoryitems = array();
    $whatsnew_showphotos       = false;
    $whatsnew_showphotositems  = array();
    $whatsnew_maxitems         = $params['max'];
    $whatsnew_howlong          = $params['days'];


    //////////// PEOPLE ///////////////////////
    if ( ($params['people'] == 'Y') && ($have_info == 1)){
        $whatsnew_showpeople  = true;
	    //select from people where date later than cutoff, order by changedate descending, limit = 10
	    $query = "SELECT personID, firstname, lastname, living, DATE_FORMAT(changedate,'%d %b') as changedatef, gedcom
                  FROM ". $TNG['people_table'] ." 
                  WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong 
                  ORDER BY changedate DESC, lastname, firstname LIMIT $maxitems";
	    if (!$result = $TNG_conn->Execute($query)  ) {
            $whatsnew_error = ""._TNGZ_WHATSNEW_SQLERROR . " " . $TNG_conn->ErrorMsg();
        } else {
            if ( $result->RecordCount() != 0 ){
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
                    $title = $row['lastname'] . ", " . $row['firstname'];
                    if ($params['link'] == 'Y') {
                        $whatsnew_showpeopleitems[] = pnModAPIFunc('TNGz','user','MakeRef',
                                                      array('func'        => "getperson",
                                                            'personID'    => $row['personID'],
                                                            'tree'        => $row['gedcom'],
                                                            'description' => $title,
                                                            'target'      => $target
                                                      ));
                    } else {
                        $whatsnew_showpeopleitems[] = $title;
                    }
                }
            }
            $result->Close();
        }
    }
    //////////// FAMILY ///////////////////////
    if (($params['family'] == 'Y')  && ($have_info == 1)){
        $whatsnew_showfamily  = true;
	    $query = "SELECT f.familyID as familyID, f.husband, f.wife, f.gedcom as gedcom, h.firstname, h.lastname as hlast, w.firstname, w.lastname as wlast, DATE_FORMAT(f.changedate,'%d %b') as changedatef
		FROM ".$TNG['families_table']." f, ".$TNG['people_table']." h, ".$TNG['people_table']." w 
        WHERE TO_DAYS(NOW()) - TO_DAYS(f.changedate) <= $howlong AND h.personID = f.husband AND w.personID = f.wife AND h.gedcom = f.gedcom AND w.gedcom = f.gedcom
		ORDER BY f.changedate DESC, h.lastname LIMIT $maxitems";
	    if (!$result = $TNG_conn->Execute($query)  ) {
            $whatsnew_error = ""._TNGZ_WHATSNEW_SQLERROR." " . $TNG_conn->ErrorMsg();
        } else {
            if ( $result->RecordCount() != 0 ){
	            for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
                    $title = $row['hlast'] . " - " . $row['wlast'];
                    if ($params['link'] == 'Y') {
                        $whatsnew_showfamilyitems[] = pnModAPIFunc('TNGz','user','MakeRef',
                                                                  array('func'        => "familygroup",
                                                                        'familyID'    => $row['familyID'],
                                                                        'tree'        => $row['gedcom'],
                                                                        'description' => $title,
                                                                        'target'      => $target
                                                                        ));
                    } else {
                        $whatsnew_showfamilyitems[] = $title;
                    }
                }
            }
            $result->Close();
        }
    }

    //////////// PHOTOS ///////////////////////

    if (($params['photos'] == 'Y')  && ($have_info == 1)){
        $whatsnew_showphotos = true;
	    $query = "SELECT DISTINCT mediaID, description,path, thumbpath, DATE_FORMAT(changedate,'%d %b') as changedatef
                         FROM ".$TNG['media_table']." p
                         WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong
                               AND mediatypeID = \"photos\"
                         ORDER BY changedate
                         DESC LIMIT $maxitems";

	    if (!$result = &$TNG_conn->Execute($query)  ) {
            $whatsnew_error .= ""._TNGZ_WHATSNEW_SQLERROR." " . $TNG_conn->ErrorMsg();
        } else {
        
            $TNGpaths = pnModAPIFunc('TNGz','user','GetTNGpaths');
            $TNG_path = $TNGpaths['SitePath'] . "/" . $TNGpaths['directory'];
            $TNG_ref  = $TNGpaths['directory'];  // a relative path
            
            for (; !$result->EOF; $result->MoveNext()) {
                $row = $result->fields;

                // First try to use thumbnail
		        $photo_file = "$TNG_path" . "/" .$TNG['photopath']."/". $row['thumbpath'];
                $photo_ref  = "$TNG_ref"  . "/" .$TNG['photopath']."/". str_replace("%2F","/",rawurlencode($row['thumbpath']));
                $photo_path = $row['thumbpath'];
                if (!file_exists($photo_file)){
                    // No thumbnail, so use actual picture
		            $photo_file = "$TNG_path" ."/" .$TNG['photopath']."/". $row['path'];
                    $photo_ref  = "$TNG_ref"  ."/" .$TNG['photopath']."/". str_replace("%2F","/",rawurlencode($row['path']));
                    $photo_path = $row['path'];
                }

               if( $photo_path != "" && file_exists($photo_file) ) {
                    $temp1 = pnModAPIFunc('TNGz','user','PhotoRef',
                                    array('photo_file'  => $photo_file,
                                          'web_ref'     => $photo_ref,
                                          'max_height'  => 50,
                                          'max_width'   => 100,
                                          'text'        => "",
                                          'description' => "border='0'"));
                    if ($params['link'] == 'Y') {
                        $whatsnew_showphotositems[]  = pnModAPIFunc('TNGz','user','MakeRef',
                                                                array('func'        => "showmedia",
                                                                      'mediaID'     => $row['mediaID'],
                                                                      'description' => $temp1
                                                                ));
                    } else {
                        $whatsnew_showphotositems[]  = $temp1;
                    }
                }
            }
            $result->Close();
        }
    }

    $pnRender = pnRender::getInstance('TNGz', false);

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $pnRender->assign('whatsnewerror'   , $whatsnew_error);
    $pnRender->assign('showpeople'      , $whatsnew_showpeople);
    $pnRender->assign('showpeopleitems' , $whatsnew_showpeopleitems);
    $pnRender->assign('showfamily'      , $whatsnew_showfamily);
    $pnRender->assign('showfamilyitems' , $whatsnew_showfamilyitems);
    $pnRender->assign('showhistory'     , $whatsnew_showhistory);
    $pnRender->assign('showhistoryitems', $whatsnew_showhistoryitems);
    $pnRender->assign('showphotos'      , $whatsnew_showphotos);
    $pnRender->assign('showphotositems' , $whatsnew_showphotositems);
    $pnRender->assign('maxitems'        , $whatsnew_maxitems);
    $pnRender->assign('howlong'         , $whatsnew_howlong);
    $pnRender->assign('title'           , $params['title']);

    // Populate block info and pass to theme
    $output = $pnRender->fetch('TNGz_plugin_WhatsNew.htm');

    // now update the cache
    if ($params['cache'] == "Y") {
        pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );
    }
    return $output;
}