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

function TNGz_ThisDayblock_info()
{
    return array(
        'text_type'      => 'ThisDayblock',
        'text_type_long' => 'On This Day',
        'module'         => 'TNGz',
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => true,
        'show_preview'   => true
    );
}

function TNGz_ThisDayblock_init()
{
    // Security
    pnSecAddSchema('TNGz:ThisDayblock:', 'Block title::');
}

function TNGz_ThisDayblock_display($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

    if( !pnSecAuthAction( 0, 'TNGz:ThisDayblock:', "$blockinfo[title]::", ACCESS_READ ) )
	    return false;

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showdate'])) {
        $vars['showdate']   = 'Y';
    //    Y = Yes
    //    N = No
    }
    if (empty($vars['showbirth'])) {
        $vars['showbirth']  = 'L';
    //    N = No or None
    //    D = show only dead people (no living)
    //    Y = show all
    //    L = show living only if user is logged in
    }

    if (empty($vars['showmarrige'])) {
        $vars['showmarrige']  = 'N';
    //    N = No or None
    //    D = show only dead people (no living)
    //    L = show living only if user is logged in
    //    Y = show all
    }

    if (empty($vars['showdeath'])) {
        $vars['showdeath']  = 'Y';
    //    N = No or None
    //    Y = show all
    }
    if (empty($vars['sortby'])) {
        $vars['sortby']    = 'D';
    //    N = Name - Lastname, Firstname
    //    D = Date of event, asending
    //    R = Date of event, decending
    }
    if (empty($vars['showwiki'])) {
        $vars['showwiki']   = 'Y';
    //    Y = Yes
    //    N = No
    }
    if (empty($vars['usecache'])) {
        $vars['usecache']   = 0;
    //    1 = Yes
    //    0 = No
    }

    $target = "" ;
    $window=pnModGetVar('TNGz', '_window');
    if ($window == 1 ) {
        $target = "target=_blank" ;
    }

    $guest  = pnModGetVar('TNGz', '_guest');

    $TNGstyle = pnModGetVar('TNGz', '_style');

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


    $TNG = pnModAPIFunc('TNGz','user','TNGconfig');

    // Check to be sure we can get to the TNG information
    if ($TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $thisday_error  = __('Error in accessing the TNG tables.', $dom);
    }

    // Get the date and time values we will need
    $thisday_time  = GetUserTime(time()) ;
    $day           = date('d',   $thisday_time );
    $monthday      = date('m-d', $thisday_time );
    $month         = date('M',   $thisday_time );
    $month         = strtoupper($month) ;

    // Check to see of this user has the permissions to see living conditionally
    $User_Can_See_Living = false;
    if ( pnUserLoggedIn() ){
        // now check to make sure TNG says user can see the living
        $userid = pnUserGetVar('uname');
        $query = "SELECT allow_living FROM ".$TNG['users_table']." WHERE username = '$userid' ";
        if ($result = $TNG_conn->Execute($query) ) {
            $row = $result->fields;
            if ($row['allow_living'] == "1") {
                $User_Can_See_Living = true;
            }
            $result->Close();
         }
    }

    //////////// SHOW DATE ///////////////////////
    if ($vars['showdate'] == "Y") {
        $thisday_showdate   = true;
    }

    //////////// BIRTH ///////////////////////
    if ($vars['showbirth'] != 'N' && $have_info == 1){
        $thisday_showbirth = true;
        // determine if we show living information
        $showliving = false;  // default to no
        if ($vars['showbirth'] == 'Y') {
            $showliving = true;
        } elseif ( $vars['showbirth'] == 'L' && $User_Can_See_Living ) {
            $showliving = true;
        }

        $query = "SELECT personID,firstname,lastname,birthdatetr as birth,deathdatetr as death,living,gedcom
                  FROM ".$TNG['people_table']." 
                  WHERE '$monthday'= substring(birthdatetr,6,5)";
        if ( !$showliving ){
            $query .= " and living = '0' ";
        }
        if ($vars['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($vars['sortby'] =="R") {
            $query .= " order by birthdate DESC ";
        } elseif ($vars['sortby'] =="D") {
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
                    $title1 = $row['lastname'];
                    $title1 .= ", " ;
                    $title1 .= $row['firstname'];
                    $title1 .= " [" ;
                    $TNGzyear = substr($row['birth'],0,4);
                    $title1 .= ($TNGzyear == "0000" ) ? " ? " : $TNGzyear;
                    if ($row['living'] == 0) {
                        $title1 .= "-" ;
                        $TNGzyear = substr($row['death'],0,4);
                        $title1 .= ($TNGzyear == "0000" ) ? " ? " : $TNGzyear;
                    }
                    $title1 .= "]" ;
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "getperson",
                                     'personID'    => $row['personID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                    $thisday_birthitems[] = $temp;
                }
            }
            $result->Close();
        }
    }
    //////////// MARRIAGE ///////////////////////
    if ($vars['showmarriage'] != 'N' && $have_info == 1){
        $thisday_showmarriage = true;
        $showliving = false;  // default to no
        if ($vars['showmarriage'] == 'Y') {
            $showliving = true;
        } elseif ( $vars['showmarriage'] == 'L' && $User_Can_See_Living ) {
            $showliving = true;
        }

        $query =  "SELECT familyID,
                          marrdatetr   AS marriage,
                          divdate      AS divorce,
                          f.living     AS FLiving,
                          h.lastname   AS HLast,
                          h.firstname  AS HFirst,
                          h.living     AS HLiving,
                          w.lastname   AS WLast,
                          w.firstname  AS WFirst,
                          w.living     AS WLiving,
                          f.gedcom     AS gedcom
                   FROM ".$TNG['families_table']." AS f 
                        LEFT JOIN ".$TNG['people_table']." AS h ON f.husband=h.personID
                        LEFT JOIN ".$TNG['people_table']." AS w ON f.wife=w.personID
                   WHERE '$monthday'= substring(marrdatetr,6,5)";
        if ( !$showliving ){
            $query .= " and f.living = '0' ";
        }

        if ($vars['sortby'] =="N") {
            $query .= " order by h.lastname, h.firstname";
        } elseif ($vars['sortby'] =="R") {
            $query .= " order by marrdatetr DESC";
        } elseif ($vars['sortby'] =="D") {
            $query .= " order by marrdatetr ASC";
        } else {
            $query .= " order by marrdatetr ASC";
        }
        if (!$result = $TNG_conn->Execute($query) ) {
            $thisday_error  = __('Error in accessing the TNG tables.', $dom)." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
	        } else {
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
	    		    $title1 = $row['HLast'] . ", " . $row['HFirst'];
                    /*! ThisDay block marriage 'and' */
                    $title1 .= " " . __('&', $dom /*! ThisDay block marriage 'and' */) . " ";
	    		    $title1 .= $row['WFirst'];
                    if ($row['WLast']!= ""){
                        $title1 .= " " . $row['WLast'];
                    }
                    $title1 .= " [" ;
                    $TNGzyear = substr($row['marriage'],0,4);
                    $title1 .= ($TNGzyear == "0000" ) ? " ? " : $TNGzyear;
                    $title1 .= "]" ;
                    if ($row['divorce']!="" ){
                    /*! ThisDay block 'divorce' indicator */
                        $title1 .= "(" . __('Divorced', $dom) . ")" ;
                    }
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "familygroup",
                                     'familyID'    => $row['familyID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                    $thisday_marriageitems[] = $temp;
	    	    }
	        }
            $result->Close();
        }
    }

    //////////// DEATH ///////////////////////
    if ($vars['showdeath'] != 'N' && $have_info == 1){
        $thisday_showdeath = true;
        $query = "SELECT personID,firstname,lastname,birthdatetr as birth,deathdatetr as death,living,gedcom
                  FROM ".$TNG['people_table']."
                  WHERE '$monthday'= substring(deathdatetr,6,5) ";
        if ($vars['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($vars['sortby'] =="R") {
            $query .= " order by deathdate DESC";
        } elseif ($vars['sortby'] =="D") {
            $query .= " order by deathdate ASC";
        } else {
            $query .= " order by deathdate ASC";
        }
        if (!$result = $TNG_conn->Execute($query) ) {
            $thisday_error  = __('Error in accessing the TNG tables.', $dom)." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0){
	        } else{
                for (; !$result->EOF; $result->MoveNext()) {
                    $row = $result->fields;
	    		    $title1 = $row['lastname'];
                    $title1 .= ", " ;
                    $title1 .= $row['firstname'];
                    $title1 .= " [" ;
                    $TNGzyear = substr($row['birth'],0,4);
                    $title1 .= ($TNGzyear == "0000" ) ? " ? " : $TNGzyear;
                    $title1 .= "-" ;
                    $TNGzyear = substr($row['death'],0,4);
                    $title1 .= ($TNGzyear == "0000" ) ? " ? " : $TNGzyear;
                    $title1 .= "]" ;
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                               array('func'        => "getperson",
                                     'personID'    => $row['personID'],
                                     'tree'        => $row['gedcom'],
                                     'description' => $title1,
                                     'target'      => $target
                                    ));
                    $thisday_deathitems[] = $temp;
	    	    }
	        }
            $result->Close();
        }
    }


    //////////// WIKI Link ///////////////////////
    if ($vars['showwiki'] == "Y") {
        $thisday_showwiki   = true;
    }

    //////////// TNG Main Menu Link //////////////
    $thisday_mainmenu = pnModAPIFunc('TNGz','user','MakeRef',
                                                              array('func'        => "",
                                                                    'description' => __('Genealogy Page', $dom),
                                                                    'target'      => $target
                                                                    ));


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

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('TNGz_block_ThisDay.htm');

    return themesideblock($blockinfo);
}

function TNGz_ThisDayblock_modify($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showdate'])) {
        $vars['showdate']   = "Y";
    }
    if (empty($vars['showbirth'])) {
        $vars['showbirth'] = "L";
    }
    if (empty($vars['showmarriage'])) {
        $vars['showmarriage'] = "N";
    }
    if (empty($vars['showdeath'])) {
        $vars['showdeath']  = "Y";
    }
    if (empty($vars['sortby'])) {
        $vars['sortby']     = "D";
    }
    if (empty($vars['showwiki'])) {
        $vars['showwiki']   = "Y";
    }
    if (empty($vars['usecache'])) {
        $vars['usecache']   = 0;
    }

    // Create output object
    $render = & pnRender::getInstance('TNGz', false);

	// As Admin output changes often, we do not want caching.
	$render->caching = false;

    // assign the approriate values
    $render->assign('showdatelist', array(
                                               Y => pnVarPrepHTMLDisplay(__('Yes', $dom)),
                                               N => pnVarPrepHTMLDisplay(__('No', $dom))
                                              ) );
    $render->assign('showbirthlist', array(
                                               Y => pnVarPrepHTMLDisplay(__('Yes, show all', $dom)),
                                               D => pnVarPrepHTMLDisplay(__('Yes, but never living people', $dom)),
                                               L => pnVarPrepHTMLDisplay(__('Yes, but show living people only if user is logged in', $dom)),
                                               N => pnVarPrepHTMLDisplay(__('No', $dom))
                                              ) );
    $render->assign('showmarriagelist', array(
                                               Y => pnVarPrepHTMLDisplay(__('Yes, show all', $dom)),
                                               D => pnVarPrepHTMLDisplay(__('Yes, but never living people', $dom)),
                                               L => pnVarPrepHTMLDisplay(__('Yes, but show living people only if user is logged in', $dom)),
                                               N => pnVarPrepHTMLDisplay(__('No', $dom))
                                              ) );
    $render->assign('showdeathlist', array(
                                               Y => pnVarPrepHTMLDisplay(__('Yes', $dom)),
                                               N => pnVarPrepHTMLDisplay(__('No', $dom))
                                              ) );
    $render->assign('sortbylist', array(
                                               N => pnVarPrepHTMLDisplay(__('Last Name, First Name', $dom)),
                                               D => pnVarPrepHTMLDisplay(__('Event date - earliest to latest', $dom)),
                                               R => pnVarPrepHTMLDisplay(__('Event date - latest to earliest', $dom))
                                              ) );

    $render->assign('showwikilist', array(
                                               Y => pnVarPrepHTMLDisplay(__('Yes', $dom)),
                                               N => pnVarPrepHTMLDisplay(__('No', $dom))
                                              ) );

	$render->assign('showdate'    , $vars['showdate']);
	$render->assign('showbirth'   , $vars['showbirth']);
	$render->assign('showmarriage', $vars['showmarriage']);
	$render->assign('showdeath'   , $vars['showdeath']);
	$render->assign('sortby'      , $vars['sortby']);
	$render->assign('showwiki'    , $vars['showwiki']);
	$render->assign('usecache'    , $vars['usecache']);

    // Return the output that has been generated by this function
	return $render->fetch('TNGz_block_ThisDay_modify.htm');
}

function TNGz_ThisDayblock_update($blockinfo)
{
    //Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['showdate']     = pnVarCleanFromInput('showdate');
    $vars['showbirth']    = pnVarCleanFromInput('showbirth');
    $vars['showmarriage'] = pnVarCleanFromInput('showmarriage');
    $vars['showdeath']    = pnVarCleanFromInput('showdeath');
    $vars['sortby']       = pnVarCleanFromInput('sortby');
    $vars['showwiki']     = pnVarCleanFromInput('showwiki');
    $vars['usecache']     = pnVarCleanFromInput('usecache');

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);


    return $blockinfo;
}
