<?php
/**
 * Zikula Application Framework
 *
 * @copyright  (c) Zikula Development Team
 * @link       http://www.zikula.org
 * @version    $Id$
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Wendel Voigt
 * @original author Cas Nuy
 * @category   Zikula_Extension
 * @package    Content
 * @subpackage TNGz
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


    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // Check to be sure we can get to the TNG information
    if (file_exists($TNG['configfile']) ) {
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    } else {
        $have_info = 0;
        $thisday_error  = ""._PEOPLEDBFERROR."";
    }

    // Get the date and time values we will need
    $thisday_time  = GetUserTime(time()) ;
    $day           = date('d',   $thisday_time );
    $monthday      = date('m-d', $thisday_time );
    $month         = date('M',   $thisday_time );
    $month         = strtoupper($month) ;

    // Check to see of this user has the permissions to see living conditionally
    $User_Can_See_Living = false;
    if ( pnUserLoggedIn() ) {
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

    //////////// SHOW DATE ///////////////////////
    if ($vars['showdate'] == "Y") {
        $thisday_showdate   = true;
    }

    //////////// BIRTH ///////////////////////
    if ($vars['showbirth'] != 'N' && $have_info == 1) {
        $thisday_showbirth = true;
        // determine if we show living information
        $showliving = false;  // default to no
        if ($vars['showbirth'] == 'Y') {
            $showliving = true;
        } elseif ( $vars['showbirth'] == 'L' && $User_Can_See_Living ) {
            $showliving = true;
        }

        $query = "SELECT personID,firstname,lastname,birthdatetr,deathdatetr,living,gedcom from $people_table";
        $query .= " where '$monthday'=substring(birthdatetr,6,5)";
        if ( !$showliving ) {
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
        if (!$result = &$TNG_conn->Execute($query)  ) {
            $thisday_error  = ""._PEOPLEDBFERROR." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0) {

            } else {
                for (; !$result->EOF; $result->MoveNext()) {
                    list($id,$first,$last,$start,$end,$stat,$gedcom) = $result->fields;
                    $title1 = $last ;
                    $title1 .= ", " ;
                    $title1 .= $first ;
                    $title1 .= " [" ;
                    $TNGzyear = substr($start,0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    if ($stat == 0) {
                        $title1 .= "-" ;
                        $TNGzyear = substr($end,0,4);
                        if ($TNGzyear == "0000" ) {
                            $title1 .= " ? ";
                        } else {
                            $title1 .= $TNGzyear;
                        }
                    }
                    $title1 .= "]" ;
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                            array('func'        => "getperson",
                                    'personID'    => $id,
                                    'tree'        => $gedcom,
                                    'description' => $title1,
                                    'target'      => $target,
                                    'RefType'     => $TNGstyle
                                    ));
                    $thisday_birthitems[] = $temp;
                }
            }

        }
        $result->Close();
    }
    //////////// MARRIAGE ///////////////////////
    if ($vars['showmarriage'] != 'N' && $have_info == 1) {
        $thisday_showmarriage = true;
        $showliving = false;  // default to no
        if ($vars['showmarriage'] == 'Y') {
            $showliving = true;
        } elseif ( $vars['showmarriage'] == 'L' && $User_Can_See_Living ) {
            $showliving = true;
        }

        $query =  "SELECT familyID, marrdatetr, divdate, f.living as FLiving, h.lastname AS HLast, h.firstname AS HFirst, h.living as HLiving, w.lastname as WLast, w.firstname as WFirst, w.living as WLiving, f.gedcom as gedcom";
        $query .= " FROM $families_table AS f LEFT JOIN $people_table AS h ON f.husband=h.personID LEFT JOIN $people_table AS w ON f.wife=w.personID";
        $query .= " WHERE '$monthday'=substring(marrdatetr,6,5)";
        if ( !$showliving ) {
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
        if (!$result = &$TNG_conn->Execute($query) ) {
            $thisday_error  = ""._PEOPLEDBFERROR." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0) {

            } else {
                for (; !$result->EOF; $result->MoveNext()) {
                    list($id,$marrdatetr,$divdate,$FLiving,$HLast,$HFirst, $HLiving, $WLast, $WFirst, $WLiving, $gedcom) = $result->fields;
                    $title1 = $HLast ;
                    $title1 .= ", " ;
                    $title1 .= $HFirst ;
                    $title1 .= " " . _MARRIAGE_AND . " ";
                    $title1 .= $WFirst ;
                    if ($WLast != "") {
                        $title1 .= " " . $WLast ;
                    }
                    $title1 .= " [" ;
                    $TNGzyear = substr($marrdatetr,0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "]" ;
                    if ($divdate !="" ) {
                        $title1 .= "(" . _DIVORCED . ")" ;
                    }
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                            array('func'        => "familygroup",
                                    'familyID'    => $id,
                                    'tree'        => $gedcom,
                                    'description' => $title1,
                                    'target'      => $target,
                                    'RefType'     => $TNGstyle
                                    ));
                    $thisday_marriageitems[] = $temp;
                }
            }
            $result->Close();
        }
    }

    //////////// DEATH ///////////////////////
    if ($vars['showdeath'] != 'N' && $have_info == 1) {
        $thisday_showdeath = true;
        $query = "SELECT personID,firstname,lastname,birthdatetr,deathdatetr,living,gedcom from $people_table ";
        $query .= " where '$monthday'=substring(deathdatetr,6,5)";
        if ($vars['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($vars['sortby'] =="R") {
            $query .= " order by deathdate DESC";
        } elseif ($vars['sortby'] =="D") {
            $query .= " order by deathdate ASC";
        } else {
            $query .= " order by deathdate ASC";
        }
        if (!$result = &$TNG_conn->Execute($query) ) {
            $thisday_error  = ""._PEOPLEDBFERROR." " . $TNG_conn->ErrorMsg();
        } else {
            $found = $result->RecordCount();
            if ($found == 0) {

            } else {
                for (; !$result->EOF; $result->MoveNext()) {
                    list($id,$first,$last,$start,$end,$stat,$gedcom) = $result->fields;
                    $title1 = $last ;
                    $title1 .= ", " ;
                    $title1 .= $first ;
                    $title1 .= " [" ;
                    $TNGzyear = substr($start,0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "-" ;
                    $TNGzyear = substr($end,0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "]" ;
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                            array('func'        => "getperson",
                                    'personID'    => $id,
                                    'tree'        => $gedcom,
                                    'description' => $title1,
                                    'target'      => $target,
                                    'RefType'     => $TNGstyle
                                    ));
                    $thisday_deathitems[] = $temp;
                }
            }
        }
        $result->Close();
    }


    //////////// WIKI Link ///////////////////////
    if ($vars['showwiki'] == "Y") {
        $thisday_showwiki   = true;
    }

    //////////// TNG Main Menu Link //////////////
    $thisday_mainmenu = pnModAPIFunc('TNGz','user','MakeRef',
                                                            array('func'        => "main",
                                                                    'description' => ""._ACCESSTNG."",
                                                                    'target'      => $target,
                                                                    'RefType'     => $TNGstyle
                                                                    ));

    if ($have_info == 1) {
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

    $pnRender->assign('todaytime'    , $thisday_time);
    $pnRender->assign('showdate'     , $thisday_showdate);
    $pnRender->assign('showbirth'    , $thisday_showbirth);
    $pnRender->assign('birth'        , $thisday_birthitems);
    $pnRender->assign('showmarriage' , $thisday_showmarriage);
    $pnRender->assign('marriage'     , $thisday_marriageitems);
    $pnRender->assign('showdeath'    , $thisday_showdeath);
    $pnRender->assign('death'        , $thisday_deathitems);
    $pnRender->assign('showwiki'     , $thisday_showwiki);
    $pnRender->assign('mainmenu'     , $thisday_mainmenu);
    $pnRender->assign('thisdayerror' , $thisday_error);

    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('TNGz_block_ThisDay.htm');

    return themesideblock($blockinfo);
}

function TNGz_ThisDayblock_modify($blockinfo)
{
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
	$pnRender =& new pnRender('TNGz');

	// As Admin output changes often, we do not want caching.
	$pnRender->caching = false;

    // assign the approriate values
    $pnRender->assign('showdatelist', array(
                                               Y => pnVarPrepHTMLDisplay(_SELECTYES),
                                               N => pnVarPrepHTMLDisplay(_SELECTNO)
                                              ) );
    $pnRender->assign('showbirthlist', array(
                                               Y => pnVarPrepHTMLDisplay(_SELECTBIRTHY),
                                               D => pnVarPrepHTMLDisplay(_SELECTBIRTHD),
                                               L => pnVarPrepHTMLDisplay(_SELECTBIRTHL),
                                               N => pnVarPrepHTMLDisplay(_SELECTBIRTHN)
                                              ) );
    $pnRender->assign('showmarriagelist', array(
                                               Y => pnVarPrepHTMLDisplay(_SELECTMARRIAGEY),
                                               D => pnVarPrepHTMLDisplay(_SELECTMARRIAGED),
                                               L => pnVarPrepHTMLDisplay(_SELECTMARRIAGEL),
                                               N => pnVarPrepHTMLDisplay(_SELECTMARRIAGEN)
                                              ) );
    $pnRender->assign('showdeathlist', array(
                                               Y => pnVarPrepHTMLDisplay(_SELECTDEATHY),
                                               N => pnVarPrepHTMLDisplay(_SELECTDEATHN)
                                              ) );
    $pnRender->assign('sortbylist', array(
                                               N => pnVarPrepHTMLDisplay(_SELECTORDERN),
                                               D => pnVarPrepHTMLDisplay(_SELECTORDERD),
                                               R => pnVarPrepHTMLDisplay(_SELECTORDERR)
                                              ) );

    $pnRender->assign('showwikilist', array(
                                               Y => pnVarPrepHTMLDisplay(_SELECTYES),
                                               N => pnVarPrepHTMLDisplay(_SELECTNO)
                                              ) );

	$pnRender->assign('showdate'    , $vars['showdate']);
	$pnRender->assign('showbirth'   , $vars['showbirth']);
	$pnRender->assign('showmarriage', $vars['showmarriage']);
	$pnRender->assign('showdeath'   , $vars['showdeath']);
	$pnRender->assign('sortby'      , $vars['sortby']);
	$pnRender->assign('showwiki'    , $vars['showwiki']);
	$pnRender->assign('usecache'    , $vars['usecache']);

    // Return the output that has been generated by this function
	return $pnRender->fetch('TNGz_block_ThisDay_modify.htm');
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

    // clear the block cache
    //	$pnRender =& new pnRender('TNGz');
    //	$pnRender->clear_cache('example_block_first.htm');

    return $blockinfo;
}
