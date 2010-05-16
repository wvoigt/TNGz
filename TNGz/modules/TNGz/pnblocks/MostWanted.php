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
    $dom = ZLanguage::getModuleDomain('TNGz');

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

    // get language and default to en
    $userlanguage = ZLanguage::getLanguageCode();

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

    // Check to be sure we can get to the TNG information
    if (pnModAPIFunc('TNGz','user','TNGquery', array('connect'=>true) ) ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $MostWanted_error  = __('Error in accessing the TNG tables.', $dom);
    }
    $TNG = pnModAPIFunc('TNGz','user','TNGconfig');

    // Now go get those IDs and compile the list
    if ( $have_info == 1 && $MostWantedPeopleIDs !="") {
        $query = "SELECT personID,firstname,lastname,birthdatetr AS birth,deathdatetr AS death,living,gedcom from ". $TNG['people_table'] . " ";
        $query .= "WHERE personID IN ($MostWantedPeopleIDs)";
        if ($vars['sortby'] =="N") {
            $query .= " order by lastname,firstname ";
        } elseif ($vars['sortby'] =="R") {
            $query .= " order by birthdate DESC ";
        } elseif ($vars['sortby'] =="D") {
            $query .= " order by birthdate ASC";
        } else  {
            $query .= ""; // use the order given
        }
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
            $MostWanted_error  = __('Error in accessing the TNG tables.', $dom);
        } else {
            foreach ($result as $row ) {                   
                $title1 = $row['lastname'];
                $title1 .= ", " ;
                $title1 .= $row['firstname'];
                $title1 .= " [" ;
                $TNGzyear = substr($row['birth'],0,4);
                $title1 .=  ($TNGzyear == "0000" )? " ? " : $TNGzyear;
                if ($row['living'] == 0) {
                    $title1 .= "-" ;
                    $TNGzyear = substr($row['death'],0,4);
                    $title1 .= ($TNGzyear == "0000" )? " ? " : $TNGzyear;
                    $title1 .= "]" ;
                    $temp = pnModAPIFunc('TNGz','user','MakeRef',
                            array('func'        => "getperson",
                                    'personID'    => $row['personID'],
                                    'tree'        => $row['gedcom'],
                                    'description' => $title1,
                                    'target'      => $target
                                    ));
                    $Mostwantedpeoplelist[] = $temp;
                }
            }
        }
    }

//////////// MARRIAGE ///////////////////////
    if ( $have_info == 1 && $MostWantedFamilyIDs !="") {
        $query =  "SELECT familyID, marrdatetr, divdate, f.living as FLiving, h.lastname AS HLast, h.firstname AS HFirst, h.living as HLiving, w.lastname as WLast, w.firstname as WFirst, w.living as WLiving, f.gedcom as gedcom";
        $query .= " FROM ".$TNG['families_table']." AS f LEFT JOIN ".$TNG['people_table']." AS h ON f.husband=h.personID LEFT JOIN ".$TNG['people_table']." AS w ON f.wife=w.personID";
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
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
            $thisday_error  = __('Error in accessing the TNG tables.', $dom);
        } else {
            foreach ($result as $row ) {
                $title1 = $row['HLast'];
                if ($vars['wantedfamilyname'] == "F") {
                    $title1 .= ", ". $row['HFirst'] ;
                }
                /*!Block MostWanted marrange 'and' */
                $title1 .= " " . __('and', $dom) . " ";
                if ($vars['wantedfamilyname'] == "F") {
                    $title1 .= $row['WFirst'] . " ";
                }
                if ($row['WLast'] != "") {
                    $title1 .= $row['WLast'] ;
                } else {
                    $title1 .= "?";
                }
                /*!Block MostWanted marriage abbriviation */
                $title1  .= " [" . __('m.', $dom)  . "" ;
                $TNGzyear = substr($row['marrdatetr'],0,4);
                $title1  .= ($TNGzyear == "0000" )? " ? " : $TNGzyear;
                $title1  .= "]" ;
                if ($row['divdate'] !="" ) {
                    /*!Block MostWanted divorce abbriviation */                    
                    $title1 .= "(" . __('divorced', $dom) . ")" ;
                }
                $temp = pnModAPIFunc('TNGz','user','MakeRef',
                        array('func'        => "familygroup",
                              'familyID'    => $row['familyID'],
                              'tree'        => $row['gedcom'],
                              'description' => $title1,
                              'target'      => $target
                               ));
                $Mostwantedfamilylist[] = $temp;
            }
        }
    }

    //////////// TNG Main Menu Link //////////////
    if ($vars['wantedmenulink'] == "Y") {
        $MostWantedMenuLink = pnModAPIFunc('TNGz','user','MakeRef',
                                            array('func'        => "main",
                                                /*! Genealogy Page link name */
                                                'description' => __('Genealogy Page', $dom /*! Genealogy Page link name*/),
                                                'target'      => $target
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

    $render = pnRender::getInstance('TNGz', $zcaching);

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $render->assign('WantedText',         $vars['wantedintro'][$userlanguage]);
    $render->assign('WantedPeopleList',   $Mostwantedpeoplelist);
    $render->assign('WantedFamilyList',   $Mostwantedfamilylist);
    $render->assign('WantedMenuLink',     $MostWantedMenuLink);

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('TNGz_block_MostWanted.htm');

    return themesideblock($blockinfo);
}

function TNGz_MostWantedblock_modify($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

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

    // get language and default to en
    Loader::loadClass('ZLanguage');
    $languages    = ZLanguage::getInstalledLanguages();
    $userlanguage = ZLanguage::getLanguageCode();

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
    $render = & pnRender::getInstance('TNGz', false);

    // As Admin output changes often, we do not want caching.
    $render->caching = false;

    // assign the approriate values
    $render->assign('sortbylist', array(
                                            N => pnVarPrepHTMLDisplay(__('Last Name, First Name', $dom)),
                                            D => pnVarPrepHTMLDisplay(__('Event date - earliest to latest', $dom)),
                                            R => pnVarPrepHTMLDisplay(__('Event date - latest to earliest', $dom)),
                                            E => pnVarPrepHTMLDisplay(__('Order entered', $dom))
                                            ) );
    $render->assign('wantednamelist', array(
                                            F => pnVarPrepHTMLDisplay(__('Full names', $dom)),
                                            S => pnVarPrepHTMLDisplay(__('Surnames only', $dom))
                                            ) );

    $render->assign('yeslist', array(
                                            Y => pnVarPrepHTMLDisplay(__('Yes', $dom)),
                                            N => pnVarPrepHTMLDisplay(__('No', $dom))
                                            ) );

    $render->assign('sortby'           , $vars['sortby']);
    $render->assign('wantedintro'      , $vars['wantedintro']);    
    $render->assign('wantedpeoplelist' , $vars['wantedpeoplelist']);
    $render->assign('wantedfamilylist' , $vars['wantedfamilylist']);
    $render->assign('wantedfamilyname' , $vars['wantedfamilyname']);
    $render->assign('wantedmenulink'   , $vars['wantedmenulink']);

    // Return the output that has been generated by this function
    return $render->fetch('TNGz_block_MostWanted_modify.htm');
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
    //	$render =& new pnRender('TNGz');
    //	$render->clear_cache('example_block_first.htm');

    return $blockinfo;
}
