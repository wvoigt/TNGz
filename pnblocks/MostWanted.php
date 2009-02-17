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

function TNGz_MostWantedblock_info()
{
    return array(
        'text_type'      => 'MostWantedblock',
        'text_type_long' => 'Most Wanted',
        'module'         => 'TNGz',
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => true,
        'show_preview'   => true
    );
}

function TNGz_MostWanted_init()
{
    // Security
    pnSecAddSchema('TNGz:MostWantedblock:', 'Block title::');
}

function TNGz_MostWantedblock_display($blockinfo)
{

    if( !pnSecAuthAction( 0, 'TNGz:MostWantedblock:', "$blockinfo[title]::", ACCESS_READ ) )
        return false;

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['wantedtext'])) {
        $vars['wantedtext']   = "";
    }
    if (!isset($vars['wantedintro'])) {
        $vars['wantedintro'] = array();
    }    
    if (empty($vars['wantedpeoplelist'])) {
        $vars['wantedpeoplelist'] = "";
    }
    if (empty($vars['wantedfamilylist'])) {
        $vars['wantedfamilylist'] = "";
    }
    if (empty($vars['wantedfamilyname'])) {
        $vars['wantedfamilyname'] = "F";
    //    F = Full names
    //    S = Just Surnames
    }
    if (empty($vars['wantedmenulink'])) {
        $vars['wantedmenulink'] = "N";
    //    Y = Yes
    //    N = No
    }
    if (empty($vars['sortby'])) {
        $vars['sortby']    = 'E';
    //    N = Name - Lastname, Firstname
    //    D = Date of event, asending
    //    R = Date of event, decending
    //    E = Order Entered
    }

    // get language and default to eng
    $userlanguage = pnUserGetLang();

    // Upgrade from prior version that used $vars['wantedtext'] (i.e., a single language version)
    // If nothing exists in existing language, use any value that was in 'wantedtext'
    if (!array_key_exists($userlanguage, $vars['wantedintro'])) {
        $vars['wantedintro'][$userlanguage] = $vars['wantedtext'];
    }    

    $Mostwantedpeoplelist     = array();
    $Mostwantedfamilylist     = array();
    $MostWantedMenuLink       = "";
    $MostWanted_error         = "";

    // Get a good SQL clean list of PeopleIDs that are wanted
    $MostWantedPeopleIDs  = "";
    $seperate       = '';
    $entrylist      = preg_split("/[\s ]*[,;\s]+[\s ]*/",trim($vars['wantedpeoplelist']));
    foreach ($entrylist as $entry) {
        if (preg_match("/^[a-zA-Z]+[0-9]+$/",$entry) ) {
            $MostWantedPeopleIDs .= "$seperate'$entry'";
            $seperate        = ', ';
        }
    }

    // Get a good SQL clean list of PeopleIDs that are wanted
    $MostWantedFamilyIDs  = "";
    $seperate       = '';
    $entrylist      = preg_split("/[\s ]*[,;\s]+[\s ]*/",trim($vars['wantedfamilylist']));
    foreach ($entrylist as $entry) {
        if (preg_match("/^[a-zA-Z]+[0-9]+$/",$entry) ) {
    /* if ( true ){        */
            $MostWantedFamilyIDs .= "$seperate'$entry'";
            $seperate        = ', ';
        }
    }

    // Get General TNGz settings
    $target = "" ;
    $window=pnModGetVar('TNGz', '_window');
    if ($window == 1 ) {
        $target = "target=_blank" ;
    }
    $TNGstyle = pnModGetVar('TNGz', '_style');

    // Check to be sure we can get to the TNG information
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');
    if (file_exists($TNG['configfile']) ) {
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    } else {
        $have_info = 0;
        $MostWanted_error  = ""._PEOPLEDBFERROR."";
    }

    // Now go get those IDs and compile the list
    if ( $have_info == 1 && $MostWantedPeopleIDs !="") {
        $query = "SELECT personID,firstname,lastname,birthdatetr,deathdatetr,living,gedcom from $people_table";
        $query .= " WHERE personID IN ($MostWantedPeopleIDs)";
        if ($vars['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($vars['sortby'] =="R") {
            $query .= " order by birthdate DESC ";
        } elseif ($vars['sortby'] =="D") {
            $query .= " order by birthdate ASC";
        } else  {
            $query .= ""; // use the order given
        }
        if (!$result = &$TNG_conn->Execute($query)  ) {
            $MostWanted_error  = ""._PEOPLEDBFERROR." " . $TNG_conn->ErrorMsg();
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
                    $Mostwantedpeoplelist[] = $temp;
                }
            }
            $result->Close();
        }
    }

//////////// MARRIAGE ///////////////////////
    if ( $have_info == 1 && $MostWantedFamilyIDs !="") {
        $query =  "SELECT familyID, marrdatetr, divdate, f.living as FLiving, h.lastname AS HLast, h.firstname AS HFirst, h.living as HLiving, w.lastname as WLast, w.firstname as WFirst, w.living as WLiving, f.gedcom as gedcom";
        $query .= " FROM $families_table AS f LEFT JOIN $people_table AS h ON f.husband=h.personID LEFT JOIN $people_table AS w ON f.wife=w.personID";
        $query .= " WHERE familyID IN ($MostWantedFamilyIDs)";
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

            } else  {
                for (; !$result->EOF; $result->MoveNext()) {
                    list($id,$marrdatetr,$divdate,$FLiving,$HLast,$HFirst, $HLiving, $WLast, $WFirst, $WLiving, $gedcom) = $result->fields;
                    $title1 = $HLast ;
                    if ($vars['wantedfamilyname'] == "F") {
                        $title1 .= ", $HFirst" ;
                    }
                    $title1 .= " " . _MARRIAGE_AND . " ";
                    if ($vars['wantedfamilyname'] == "F") {
                        $title1 .= $WFirst . " ";
                    }
                    if ($WLast != "") {
                        $title1 .= $WLast ;
                    } else {
                        $title1 .= "?";
                    }
                    $title1 .= " [" . _MARRIED_ABR . "" ;
                    $TNGzyear = substr($marrdatetr,0,4);
                    if ($TNGzyear == "0000" ) {
                        $title1 .= " ? ";
                    } else {
                        $title1 .= $TNGzyear;
                    }
                    $title1 .= "]" ;
                    if ($divdate !="" ) {
                        $title1 .= "(" . _DIVORCED_ABR . ")" ;
                    }
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                            array('func'        => "familygroup",
                                    'familyID'    => $id,
                                    'tree'        => $gedcom,
                                    'description' => $title1,
                                    'target'      => $target,
                                    'RefType'     => $TNGstyle
                                    ));
                    $Mostwantedfamilylist[] = $temp;
                }
            }
            $result->Close();
        }
    }

    if ($have_info == 1) {
        $TNG_conn->Close();
    }

    //////////// TNG Main Menu Link //////////////
    if ($vars['wantedmenulink'] == "Y") {
        $MostWantedMenuLink = pnModAPIFunc('TNGz','user','MakeRef',
                                            array('func'        => "main",
                                                'description' => ""._ACCESSTNG."",
                                                'target'      => $target,
                                                'RefType'     => $TNGstyle
                                                ));
    } else {
        $MostWantedMenuLink="";
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

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $pnRender->assign('WantedText',         $vars['wantedintro'][$userlanguage]);
    $pnRender->assign('WantedPeopleList',   $Mostwantedpeoplelist);
    $pnRender->assign('WantedFamilyList',   $Mostwantedfamilylist);
    $pnRender->assign('WantedMenuLink',     $MostWantedMenuLink);

    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('TNGz_block_MostWanted.htm');

    return themesideblock($blockinfo);
}

function TNGz_MostWantedblock_modify($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['wantedtext'])) {  // This will no longer be used in the future
        $vars['wantedtext']   = "";    // Will always be "" after the first save
    }
    if (!isset($vars['wantedintro'])) {
        $vars['wantedintro'] = array();
    }
    if (empty($vars['wantedpeoplelist'])) {
        $vars['wantedpeoplelist'] = "";
    }
    if (empty($vars['wantedfamilylist'])) {
        $vars['wantedfamilylist'] = "";
    }
    if (empty($vars['wantedfamilyname'])) {
        $vars['wantedfamilyname'] = "F";
    }
    if (empty($vars['wantedmenulink'])) {
        $vars['wantedmenulink'] = "N";
    }
    if (empty($vars['sortby'])) {
        $vars['sortby']     = "E";
    }

    // get language and default to eng
    Loader::loadClass('LanguageUtil');
    $languages = LanguageUtil::getLanguages();
    $userlanguage = pnUserGetLang();

    // Upgrade from prior version that used $vars['wantedtext'] (i.e., a single language version)
    // If nothing exists in existing language, use any value that was in 'wantedtext'
    // Note: This only works the first time, after that, wantedtext will be empty
    if (!array_key_exists($userlanguage, $vars['wantedintro'])) {
        $vars['wantedintro'][$userlanguage] = $vars['wantedtext'];
    }    

    // make sure each language has an initial value
    foreach($languages as $lang) {
        if (!array_key_exists($lang, $vars['wantedintro'])) {
            $vars['wantedintro'][$lang] = '';
        }
    }

    // Create output object
    $pnRender =& new pnRender('TNGz');

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    // assign the approriate values
    $pnRender->assign('sortbylist', array(
                                            N => pnVarPrepHTMLDisplay(_SELECTORDERN),
                                            D => pnVarPrepHTMLDisplay(_SELECTORDERD),
                                            R => pnVarPrepHTMLDisplay(_SELECTORDERR),
                                            E => pnVarPrepHTMLDisplay(_SELECTORDERE)
                                            ) );
    $pnRender->assign('wantednamelist', array(
                                            F => pnVarPrepHTMLDisplay(_SELECTFAMILYFULL),
                                            S => pnVarPrepHTMLDisplay(_SELECTFAMILYSHORT)
                                            ) );

    $pnRender->assign('yeslist', array(
                                            Y => pnVarPrepHTMLDisplay(_SELECTYES),
                                            N => pnVarPrepHTMLDisplay(_SELECTNO)
                                            ) );

    $pnRender->assign('sortby'           , $vars['sortby']);
    $pnRender->assign('wantedintro'      , $vars['wantedintro']);    
    $pnRender->assign('wantedpeoplelist' , $vars['wantedpeoplelist']);
    $pnRender->assign('wantedfamilylist' , $vars['wantedfamilylist']);
    $pnRender->assign('wantedfamilyname' , $vars['wantedfamilyname']);
    $pnRender->assign('wantedmenulink'   , $vars['wantedmenulink']);

    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_block_MostWanted_modify.htm');
}

function TNGz_MostWantedblock_update($blockinfo)
{
    //Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['sortby']            = pnVarCleanFromInput('sortby');
    $vars['wantedintro']       = pnVarCleanFromInput('wantedintro');
    $vars['wantedmenulink']    = pnVarCleanFromInput('wantedmenulink');
    $vars['wantedpeoplelist']  = pnVarCleanFromInput('wantedpeoplelist');
    $vars['wantedfamilylist']  = pnVarCleanFromInput('wantedfamilylist');
    $vars['wantedfamilyname']  = pnVarCleanFromInput('wantedfamilyname');

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);

    // clear the block cache
    //	$pnRender =& new pnRender('TNGz');
    //	$pnRender->clear_cache('example_block_first.htm');

    return $blockinfo;
}
