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
 * @original author Cas Nuy
 * @author Wendel Voigt
 * @version $Id$
 */

function TNGz_WhatsNewblock_info()
{
    return array(
        'text_type'      => 'WhatsNewblock',
        'text_type_long' => 'Whats New',
        'module'         => 'TNGz',
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => true,
        'show_preview'   => true

    );
}

function TNGz_WhatsNewblock_init()
{
    // Security
    pnSecAddSchema('TNGz:WhatsNewblock:', 'Block title::');
}

function TNGz_WhatsNewblock_display($blockinfo)
{

    if( !pnSecAuthAction( 0, 'TNGz:WhatsNewblock:', "$blockinfo[title]::", ACCESS_READ ) )
	    return false;

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }


    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showpeople'])) {
        $vars['showpeople'] = "Y";
    }
    if (empty($vars['showfamily'])) {
        $vars['showfamily']  = "Y";
    }
    if (empty($vars['showhistory'])) {
        $vars['showhistory']  = "Y";
    }
    if (empty($vars['showphotos'])) {
        $vars['showphotos']  = "Y";
    }
    if (empty($vars['maxitems']) || !is_numeric($vars['maxitems']) ) {
        $vars['maxitems']    = _TNGZ_WHATSNEW_HOWMANY_NUM;
    }
    if (empty($vars['howlong'])  || !is_numeric($vars['howlong'])  ) {
        $vars['howlong']     = _TNGZ_WHATSNEW_HOWLONG_NUM;
    }
    $howlong  = $vars['howlong'];
    $maxitems = $vars['maxitems'];
    if (empty($vars['usecache'])) {
        $vars['usecache']   = 0;
    //    1 = Yes
    //    0 = No
    }

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
    $whatsnew_maxitems         = $vars['maxitems'];
    $whatsnew_howlong          = $vars['howlong'];

    $target = "" ;
    $window=pnModGetVar('TNGz', '_window');
    if ($window == 1 ) {
        $target = "target=_blank" ;
    }

    $guest  = pnModGetVar('TNGz', '_guest');
    $TNGstyle = pnModGetVar('TNGz', '_style');

    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');
    $TNG_path = $TNG['SitePath'] . "/" . $TNG['directory'];
    $TNG_ref  = $TNG['directory'];                          // a relative path
    // $TNG_ref  = $TNG['WebRoot']   . "/" . $TNG['directory'];    // absolute path

    // Check to be sure we can get to the TNG information
    if (file_exists($TNG['configfile']) ){
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $view_error = "";
        $have_info = 1;
    } else {
        $have_info = 0;
        $whatsnew_error = ""._PEOPLEDBFERROR."";
    }

    //////////// PEOPLE ///////////////////////
    $view_people = "";
    if ( ($vars['showpeople'] == 'Y') && ($have_info == 1)){
        $whatsnew_showpeople  = true;
	//select from people where date later than cutoff, order by changedate descending, limit = 10
	$query = "SELECT personID, firstname, lastname, living, DATE_FORMAT(changedate,'%d %b') as changedatef, gedcom FROM $people_table WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong ORDER BY changedate DESC, lastname, firstname LIMIT $maxitems";
	if (!$result = &$TNG_conn->Execute($query)  ) {
            $whatsnew_error = ""._TNGZ_WHATSNEW_SQLERROR . " " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
            } else{
                for (; !$result->EOF; $result->MoveNext()) {
                    list($id,$first,$last,$living,$change_date,$gedcom) = $result->fields;
                    $title1 = $last ;
                    $title1 .= ", " ;
                    $title1 .= $first ;
//                    $title1 .= " <i>($change_date)</i>";
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "getperson",
                                     'personID'    => $id,
                                     'tree'        => $gedcom,
                                     'description' => $title1,
                                     'target'      => $target,
                                     'RefType'     => $TNGstyle
                                    ));
                    $whatsnew_showpeopleitems[] = $temp;
                }
            }
        }
        $result->Close();
//      $view_people .= "<br>";
    }
    //////////// FAMILY ///////////////////////
    $view_family = "";
    if (($vars['showfamily'] == 'Y')  && ($have_info == 1)){
        $whatsnew_showfamily  = true;
	$query = "SELECT f.familyID, f.husband, f.wife, f.gedcom, h.firstname, h.lastname, w.firstname, w.lastname, DATE_FORMAT(f.changedate,'%d %b') as changedatef
		FROM $families_table f, $people_table h, $people_table w WHERE TO_DAYS(NOW()) - TO_DAYS(f.changedate) <= $howlong AND h.personID = f.husband AND w.personID = f.wife AND h.gedcom = f.gedcom AND w.gedcom = f.gedcom
		ORDER BY f.changedate DESC, h.lastname LIMIT $maxitems";

	if (!$result = &$TNG_conn->Execute($query)  ) {
            $whatsnew_error = ""._TNGZ_WHATSNEW_SQLERROR." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
            } else{
	            for (; !$result->EOF; $result->MoveNext()) {
                    list($familyID,$husbandID,$wifeID,$gedcom,$husband_first, $husband_last, $wife_first, $wife_last,$change_date) = $result->fields;
                    $title1 = "$husband_last - $wife_last ";
//                  $title1 .= " <i>($change_date)</i>";
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "familygroup",
                                     'familyID'    => $familyID,
                                     'tree'        => $gedcom,
                                     'description' => $title1,
                                     'target'      => $target,
                                     'RefType'     => $TNGstyle
                                    ));
                    $whatsnew_showfamilyitems[] = $temp;
                }
            }
        }
        $result->Close();
    }
    //////////// HISTORY ///////////////////////
/*
    $view_history = "";
    if (($vars['showhistory'] == 'Y')  && ($have_info == 1)){
        $view_history = "<center><b>" . _TNGZ_WHATSNEW_HEAD_HISTORY . "<br></b></center>";
	$query = "SELECT DISTINCT ht.docID, description, path, newwindow, DATE_FORMAT(changedate,'%d %b') as changedatef
		FROM $histories_table ht WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong
		ORDER BY changedate DESC LIMIT $maxitems";
	if (!$result = &$TNG_conn->Execute($query)  ) {
            $view_history .= ""._TNGZ_WHATSNEW_SQLERROR." " . $TNG_conn->ErrorMsg();
        } else {
            $history_count == 0;
            for (; !$result->EOF; $result->MoveNext()) {
                list($docID,$description,$the_path,$newwindow,$change_date) = $result->fields;
                if( $newwindow ) {
                    $the_window = " target=\"_blank\"";
                } else {
                    $the_window = "";
                }
                if( $the_path ) {
                    $history_count++;
                    $the_ref  = pnModAPIFunc('TNGz','user','MakeRef',
                                                array('func'        => "url",
                                                      'url'         => "$historypath/". rawurlencode($the_path),
                                                      'tree'        => $gedcom,
                                                      'description' => $description,
                                                      'target'      => $the_window,
                                                      'RefType'     => $TNGstyle
                                                      ));
                } else {
                     $history_count++;
                     $the_ref  = pnModAPIFunc('TNGz','user','MakeRef',
                                                array('func'        => "showhistory",
                                                      'docID'       => $docID,
                                                      'description' => $description,
                                                      'target'      => $the_window,
                                                      'RefType'     => $TNGstyle
                                                      ));
                }

                $view_history .= "<strong><big>&middot;</big></strong>$the_ref<br>";
            }
            if ($history_count == 0){
                $view_history .= _TNGZ_WHATSNEW_NOCHANGES . "<br>";
            }
        }
        $result->Close();
//      $view_history .= "<br>";
    }
*/
    //////////// PHOTOS ///////////////////////

    if (($vars['showphotos'] == 'Y')  && ($have_info == 1)){
        $whatsnew_showphotos = true;
	$query = "SELECT DISTINCT mediaID, description,path, thumbpath, DATE_FORMAT(changedate,'%d %b') as changedatef
                         FROM $media_table p
                         WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong
                               AND mediatypeID = \"photos\"
                         ORDER BY changedate
                         DESC LIMIT $maxitems";

	if (!$result = &$TNG_conn->Execute($query)  ) {
            $whatsnew_error .= ""._TNGZ_WHATSNEW_SQLERROR." " . $TNG_conn->ErrorMsg();
        } else {
            for (; !$result->EOF; $result->MoveNext()) {
                list($mediaID,$description,$picpath, $thumbpath,$change_date) = $result->fields;

                // First try to use thumbnail
		        $photo_file = "$TNG_path/$photopath/$thumbpath";
                $photo_ref  = "$TNG_ref/$photopath/". str_replace("%2F","/",rawurlencode($thumbpath));
                $photo_path = $thumbpath;
                if (!file_exists($photo_file)){
                    // No thumbnail, so use actual picture
		            $photo_file = "$TNG_path/$photopath/$picpath";
                    $photo_ref  = "$TNG_ref/$photopath/". str_replace("%2F","/",rawurlencode($picpath));
                    $photo_path = $picpath;
                }

               if( $photo_path != "" && file_exists($photo_file) ) {
                    $temp1 = pnModAPIFunc('TNGz','user','PhotoRef',
                                    array('photo_file'  => $photo_file,
                                          'web_ref'     => $photo_ref,
                                          'max_height'  => 50,
                                          'max_width'   => 100,
                                          'text'        => "",
                                          'description' => "border='0'"));

                    $temp  = pnModAPIFunc('TNGz','user','MakeRef',
                                                array('func'        => "showmedia",
                                                      'mediaID'     => $mediaID,
                                                      'description' => $temp1,
                                                      'RefType'     => $TNGstyle
                                                      ));
                    $whatsnew_showphotositems[] = $temp;
                }
            }
        }
        $result->Close();
    }


    if ( have_info == 1) {
        $TNG_conn->Close();
    }

    // Can turn off caching by using the following
    if ( $vars['usecache'] == 0 ) {
	    $zcaching = false;
    } else {
            $zcaching = true;
    }

    // Create output object
    // Note that for a block the corresponding module must be passed.

    $pnRender = pnRender::getInstance('TNGz', $zcaching);

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

    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('TNGz_block_WhatsNew.htm');

    return themesideblock($blockinfo);
}

function TNGz_WhatsNewblock_modify($blockinfo)
{

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showpeople'])) {
        $vars['showpeople'] = "Y";
    }
    if (empty($vars['showfamily'])) {
        $vars['showfamily']  = "Y";
    }
    if (empty($vars['showhistory'])) {
        $vars['showhistory']  = "Y";
    }
    if (empty($vars['showphotos'])) {
        $vars['showphotos']  = "Y";
    }
    if (empty($vars['maxitems']) || !is_numeric($vars['maxitems']) ) {
        $vars['maxitems']    = 10;
    }
    if (empty($vars['howlong'])  || !is_numeric($vars['howlong'])  ) {
        $vars['howlong']     = 60;
    }
    if (empty($vars['usecache'])) {
        $vars['usecache']   = 0;
    }

    // Create output object
	$pnRender =& new pnRender('TNGz');

	// As Admin output changes often, we do not want caching.
	$pnRender->caching = false;

    // assign the approriate values
    $pnRender->assign('yesnolist', array(
                                          Y => pnVarPrepHTMLDisplay(_TNGZ_WHATSNEW_YES),
                                          N => pnVarPrepHTMLDisplay(_TNGZ_WHATSNEW_NO)
                                         ) );

	$pnRender->assign('showpeople'  , $vars['showpeople']);
	$pnRender->assign('showfamily'  , $vars['showfamily']);
	$pnRender->assign('showphotos'  , $vars['showphotos']);
	$pnRender->assign('showhistory' , $vars['showhistory']);
	$pnRender->assign('maxitems'    , $vars['maxitems']);
	$pnRender->assign('howlong'     , $vars['howlong']);
	$pnRender->assign('usecache'   , $vars['usecache']);

    // Return the output that has been generated by this function
	return $pnRender->fetch('TNGz_block_WhatsNew_modify.htm');
}

function TNGz_WhatsNewblock_update($blockinfo)
{
    //Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['showpeople'] = pnVarCleanFromInput('showpeople');
    $vars['showfamily'] = pnVarCleanFromInput('showfamily');
    $vars['showphotos'] = pnVarCleanFromInput('showphotos');
    $vars['showhistory'] = pnVarCleanFromInput('showhistory');
    $vars['maxitems']   = pnVarCleanFromInput('maxitems');
    $vars['howlong']    = pnVarCleanFromInput('howlong');
    $vars['usecache']  = pnVarCleanFromInput('usecache');

    if (!is_numeric($vars['maxitems']) ){
        $vars['maxitems'] = _TNGZ_WHATSNEW_HOWMANY_NUM;
    }
    if (!is_numeric($vars['howlong']) ) {
        $vars['howlong']   = _TNGZ_WHATSNEW_HOWLONG_NUM;
    }

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);


    return $blockinfo;
}

