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
    $dom = ZLanguage::getModuleDomain('TNGz');

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
        $vars['showhistory']  = "N";
    }
    if (empty($vars['showphotos'])) {
        $vars['showphotos']  = "Y";
    }
    if (empty($vars['maxitems']) || !is_numeric($vars['maxitems']) ) {
        $vars['maxitems']    = 10;
    }
    if (empty($vars['howlong'])  || !is_numeric($vars['howlong'])  ) {
        $vars['howlong']     = 30;
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
    
    $TNG = pnModAPIFunc('TNGz','user','TNGconfig');
    $TNG_path = $TNG['SitePath'] . "/" . $TNG['directory'];
    $TNG_ref  = $TNG['directory'];                          // a relative path
    // $TNG_ref  = $TNG['WebRoot']   . "/" . $TNG['directory'];    // absolute path

    
    // Check to be sure we can get to the TNG information
    if (pnModAPIFunc('TNGz','user','TNGquery', array('connect'=>true) ) ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $thisday_error  = __('Error in accessing the TNG tables.', $dom);
    }

    //////////// PEOPLE ///////////////////////
    $view_people = "";
    if ( ($vars['showpeople'] == 'Y') && ($have_info == 1)){
        $whatsnew_showpeople  = true;
	    //select from people where date later than cutoff

	    $query = "SELECT personID, firstname, lastname, living, DATE_FORMAT(changedate,'%d %b') as changedatef, gedcom
                  FROM ".$TNG['people_table']."
                  WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong
                  ORDER BY changedate DESC, lastname, firstname 
                  LIMIT $maxitems";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
            $whatsnew_error = __('Error in accessing the database.', $dom);
        } else {
            foreach($result as $row) {
                $title1 = $row['lastname'];
                $title1 .= ", " ;
                $title1 .= $row['firstname'];
                $temp = pnModAPIFunc('TNGz','user','MakeRef',
                           array('func'        => "getperson",
                                 'personID'    => $row['personID'],
                                 'tree'        => $row['gedcom'],
                                 'description' => $title1,
                                 'target'      => $target
                                 ));
                $whatsnew_showpeopleitems[] = $temp;
            }
        }
    }
    //////////// FAMILY ///////////////////////
    $view_family = "";
    if (($vars['showfamily'] == 'Y')  && ($have_info == 1)){
        $whatsnew_showfamily  = true;

        $query = "SELECT f.familyID   AS familyID,
                         f.husband    AS husband,
                         f.wife       AS wife,
                         f.gedcom     AS gedcom,
                         h.firstname  AS Hfirst,
                         h.lastname   AS Hlast,
                         w.firstname  AS Wfirst,
                         w.lastname   AS Wlast,
                         DATE_FORMAT(f.changedate,'%d %b') as changedatef
		          FROM ".$TNG['families_table']." AS f, ".$TNG['people_table']." AS h, ".$TNG['people_table']." AS w 
                  WHERE TO_DAYS(NOW()) - TO_DAYS(f.changedate) <= $howlong AND h.personID = f.husband AND w.personID = f.wife AND h.gedcom = f.gedcom AND w.gedcom = f.gedcom
		          ORDER BY f.changedate DESC, h.lastname 
                  LIMIT $maxitems";

        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
            $whatsnew_error = __('Error in accessing the database.', $dom);
        } else {
            foreach ($result as $row) {
                $title1 = $row['Hlast'] . " - " . $row['Wlast'];
                $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "familygroup",
                                     'familyID'    => $row['familyID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                $whatsnew_showfamilyitems[] = $temp;
            }
        }
    }

    //////////// PHOTOS ///////////////////////

    if (($vars['showphotos'] == 'Y')  && ($have_info == 1)){
        $whatsnew_showphotos = true;
	    $query = "SELECT DISTINCT mediaID, description, path, thumbpath, DATE_FORMAT(changedate,'%d %b') as changedatef
                  FROM ".$TNG['media_table']." AS p
                  WHERE TO_DAYS(NOW()) - TO_DAYS(changedate) <= $howlong 
                        AND mediatypeID = \"photos\"
                  ORDER BY changedate DESC
                  LIMIT $maxitems";

        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
            $whatsnew_error .= __('Error in accessing the database.', $dom);
        } else {
            foreach ($result as $row) {
                // First try to use thumbnail
		        $photo_file = "$TNG_path/".$TNG['photopath']."/". $row['thumbpath'];
                $photo_ref  = "$TNG_ref/" .$TNG['photopath']."/". str_replace("%2F","/",rawurlencode($row['thumbpath']));
                $photo_path = $row['thumbpath'];
                if (!file_exists($photo_file)){
                    // No thumbnail, so use actual picture
		            $photo_file = "$TNG_path/".$TNG['photopath']."/".$row['path'];
                    $photo_ref  = "$TNG_ref/" .$TNG['photopath']."/". str_replace("%2F","/",rawurlencode($row['path']));
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

                    $temp  = pnModAPIFunc('TNGz','user','MakeRef',
                                                array('func'        => "showmedia",
                                                      'mediaID'     => $row['mediaID'],
                                                      'description' => $temp1
                                                      ));
                    $whatsnew_showphotositems[] = $temp;
                }
            }
        }
    }

    // Can turn off caching by using the following
    if ( $vars['usecache'] == 0 ) {
	    $zcaching = false;
    } else {
            $zcaching = true;
    }

    // Create output object
    // Note that for a block the corresponding module must be passed.

    $render = & pnRender::getInstance('TNGz', $zcaching);

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $render->assign('whatsnewerror'   , $whatsnew_error);
    $render->assign('showpeople'      , $whatsnew_showpeople);
    $render->assign('showpeopleitems' , $whatsnew_showpeopleitems);
    $render->assign('showfamily'      , $whatsnew_showfamily);
    $render->assign('showfamilyitems' , $whatsnew_showfamilyitems);
    $render->assign('showhistory'     , $whatsnew_showhistory);
    $render->assign('showhistoryitems', $whatsnew_showhistoryitems);
    $render->assign('showphotos'      , $whatsnew_showphotos);
    $render->assign('showphotositems' , $whatsnew_showphotositems);
    $render->assign('maxitems'        , $whatsnew_maxitems);
    $render->assign('howlong'         , $whatsnew_howlong);

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('TNGz_block_WhatsNew.htm');

    return themesideblock($blockinfo);
}

function TNGz_WhatsNewblock_modify($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

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
        $vars['showhistory']  = "N";
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
    $render = & pnRender::getInstance('TNGz', false);

	// As Admin output changes often, we do not want caching.
	$render->caching = false;

    // assign the approriate values
    $render->assign('yesnolist', array(
                                          Y => pnVarPrepHTMLDisplay(__('Yes', $dom)),
                                          N => pnVarPrepHTMLDisplay(__('No', $dom))
                                         ) );

	$render->assign('showpeople'  , $vars['showpeople']);
	$render->assign('showfamily'  , $vars['showfamily']);
	$render->assign('showphotos'  , $vars['showphotos']);
	$render->assign('showhistory' , $vars['showhistory']);
	$render->assign('maxitems'    , $vars['maxitems']);
	$render->assign('howlong'     , $vars['howlong']);
	$render->assign('usecache'   , $vars['usecache']);

    // Return the output that has been generated by this function
	return $render->fetch('TNGz_block_WhatsNew_modify.htm');
}

function TNGz_WhatsNewblock_update($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
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
    //! WhatsNewBlock default maximum number of items
        $vars['maxitems'] = 10;
    }
    if (!is_numeric($vars['howlong']) ) {
    //! WhatsNewBlock default for how many days
        $vars['howlong']   = 30;
    }

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);


    return $blockinfo;
}

