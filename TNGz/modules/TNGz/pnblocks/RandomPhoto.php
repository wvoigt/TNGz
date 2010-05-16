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

function TNGz_RandomPhotoblock_info()
{
    return array(
        'text_type'      => 'RandomPhotoblock',
        'module'         => 'TNGz',
        'text_type_long' => 'Random Genealogy Photos',
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => true,
        'show_preview'   => true
    );
}


function TNGz_RandomPhotoblock_init()
{
    // Security
    pnSecAddSchema('TNGz:RandomPhotoblock:', 'Block title::');
}


function TNGz_RandomPhotoblock_display($blockinfo) {

    $dom = ZLanguage::getModuleDomain('TNGz');

    if( !pnSecAuthAction( 0, 'TNGz:RandomPhotoblock:', "$blockinfo[title]::", ACCESS_READ ) )
	    return;

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showliving'])) {
        $vars['showliving']  = 'L';
    //    N = No or None
    //    D = show only dead people (no living)
    //    Y = show all
    //    L = show living only if user is logged in
    }
    if (empty($vars['phototype'])) {
        $vars['phototype']  = 'T';
    //    T = use Thumbnail
    //    P = use Photo
    }
    if (empty($vars['max_height'])) {
        $vars['max_height']  = '150';
    //  max_height = largest height of picture -- scale to this if bigger
    }
    if (empty($vars['max_width'])) {
        $vars['max_width']    = '150';
   // max_width  = largest width of picture  -- scale to this if bigger
    }
    if (empty($vars['usecache'])) {
        $vars['usecache']   = 0;
    //    1 = Yes
    //    0 = No
    }
    if (empty($vars['photolist'])) {
        $vars['photolist']   = "";
    // Empty is no photos in list
    }

    $PhotoList   = "";
    $record_sep  = "";
    $entrylist   = preg_split("/[\s ]*[,;\s]+[\s ]*/",trim($vars['photolist']));
    foreach ($entrylist as $entry){
        if (preg_match("/^[0-9]+$/",$entry) ){
            $PhotoList .= $record_sep . $entry;
            $record_sep =", ";
        }
    }

    $max_strikes = 3;   // The Maximum times to try and find a photo before giving up
    $target = "" ;
    $photo_ref = "";
    $photo_description = "";
    $photo_error = "";

    $window=pnModGetVar('TNGz', '_window');

    $TNG = pnModAPIFunc('TNGz','user','TNGconfig');   

    $TNG_path = $TNG['SitePath'] . "/" . $TNG['directory'];
    $TNG_ref  = $TNG['directory'];                                 // a relative path

    // Check to be sure we can get to the TNG information
    if (pnModAPIFunc('TNGz','user','TNGquery', array('connect'=>true) ) ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $photo_error  = __('Error in accessing the TNG tables.', $dom);
    }


    if ($window == 1 ) {
	    $target = "target=_blank" ;
    }

    if ($vars['showliving'] != 'N' && $have_info == 1 ){

        // determine if we show living information
        $showliving = false;  // default to no
        if ($vars['showliving'] == 'Y') {
            $showliving = true;
        } elseif ( $vars['showliving'] == 'L' ) {
            $showliving = pnModAPIFunc('TNGz','user','CanUserSeeLiving');
        }

        $need_photo = true;
        $photos_with_living = "";   // comma separated list of photoIDs we don't want to display because linked to a living person

        if (!$showliving ){
                // get the list of photoIDs that have at least 1 living person --- don't want to show those
                $query = "SELECT living.mediaID AS mediaID
                          FROM ".$TNG['medialinks_table']." AS living, ".$TNG['people_table']." AS person
                          WHERE living.personID = person.personID AND person.living = 1
                          GROUP BY living.mediaID";
                if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
                    $photo_error .= __('Error in accessing the TNG tables.', $dom);
                }
                // now make a comma separated list of the photoIDs
                $record_sep = "";
                foreach ($result as $row) {
                     $photos_with_living .= $record_sep . $row['mediaID'];
                     $record_sep =", ";
                }
        }

        // get a list of photolist IDs --- one per person photo link --- to pick from
        $query = "SELECT photolist.mediaID as linkID
                  FROM ".$TNG['media_table']." AS mediatable, ".$TNG['medialinks_table']." AS photolist
                  WHERE photolist.mediaID = mediatable.mediaID
                        AND mediatable.mediatypeID = \"photos\" ";
        if ($PhotoList != ""){
            // Only include those that are already specified
            $query .= "AND photolist.mediaID IN ( $PhotoList ) ";
        }
        if (!$showliving ){
            // but don't include photo's of living people
            $query .= "AND photolist.mediaID NOT IN ( $photos_with_living ) ";
        }

        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
                $photo_error .= __('Error in accessing the TNG tables.', $dom)." [1] ";
        } else {

            $num_photos = count($result);  // the number of photo links to pick from

            for ( $strikes = 0 ; $strikes <= $max_strikes && $need_photo; $strikes++ ) {
                // just in case of problems, try at most max_strikes times
                $rand = RAND(0,$num_photos - 1);
                $row  = $result[$rand];

                // now get the actual photo link
                $query = "SELECT mediatable.path             AS path,
                                 mediatable.thumbpath        AS thumbpath,
                                 mediatable.description      AS description,
                                 mediatable.notes            AS notes,
                                 mediatable.mediaID          AS mediaID,
                                 medialinkstable.medialinkID AS medialinkID,
                                 medialinkstable.personID    AS personID,
                                 medialinkstable.gedcom      AS gedcom,
                                 usecollfolder
                          FROM ".$TNG['media_table']." AS mediatable, ".$TNG['medialinks_table']." AS medialinkstable
                          WHERE medialinkstable.mediaID = \"".$row['linkID']."\"
                                AND mediatable.mediaID = medialinkstable.mediaID ";

                if (false === ($result2 = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) )  ) {
                    $photo_error .= __('Error in accessing the TNG tables.', $dom)." [2] ";
                } else {
                    $row2 = $result2[0];
                    $picture      = ($vars['phototype']  == 'P')? $row2['path'] : $row2['thumbpath'];
                    $picture_file = "$TNG_path/".$TNG['photopath']."/$picture";
                    $picture_ref  = "$TNG_ref/" .$TNG['photopath']."/". str_replace("%2F","/",rawurlencode($picture));

                    // check to make sure a picture defined and the file exists
                    if ($picture != "" && file_exists($picture_file)) {
                        // Found the photo thumbnail to display
                        $temp1 = pnModAPIFunc('TNGz','user','PhotoRef',
                                          array('photo_file'  => $picture_file,
                                                'web_ref'     => $picture_ref,
                                                'max_height'  => $vars['max_height'],
                                                'max_width'   => $vars['max_width'],
                                                'text'        => $row2['description'],
                                                'description' => "border='0'"));

                        $photo_ref     = pnModAPIFunc('TNGz','user','MakeRef',
                                                   array('func'        => "showmedia",
                                                         'personID'    => $row2['personID'],
                                                         'mediaID'     => $row2['mediaID'],
                                                         'medialinkID' => $row2['medialinkID'],
                                                         'description' => $temp1,
                                                         'target'      => $target
                                                         ));
                        $photo_description = $row2['description'];
                        $need_photo = false;
                    }
                }
            }
        }
        if ( $need_photo ) {
            // Didn't get a photo this time
            $photo_error  .= __('No Photo found', $dom);
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

    $render->assign('photo_ref'        , $photo_ref);
    $render->assign('photo_description', $photo_description);
    $render->assign('photo_error'      , $photo_error);

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('TNGz_block_RandomPhoto.htm');

    return themesideblock($blockinfo);
}


function TNGz_RandomPhotoblock_modify($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showliving'])) {
        $vars['showliving'] = "L";
    }
    if (empty($vars['phototype'])) {
        $vars['phototype'] = "T";
    }
    if (empty($vars['max_height'])) {
        $vars['max_height']  = "150";
    }
    if (empty($vars['max_width'])) {
        $vars['max_width']   = "150";
    }
    if (empty($vars['usecache'])) {
        $vars['usecache']   = 0;
    }
    if (empty($vars['photolist'])) {
        $vars['photolist']   = "";
    }

    // Create output object
    $render = & pnRender::getInstance('TNGz', false);

	// As Admin output changes often, we do not want caching.
	$render->caching = false;

    // assign the approriate values
    $render->assign('showlivinglist', array(
                                               Y => pnVarPrepHTMLDisplay(__('Yes, show all', $dom)),
                                               D => pnVarPrepHTMLDisplay(__('Yes, but never living people', $dom)),
                                               L => pnVarPrepHTMLDisplay(__('Yes, but show living people only if user is logged in', $dom)),
                                               N => pnVarPrepHTMLDisplay(__('No', $dom))
                                              ) );
    $render->assign('phototypelist', array(
                                               T => pnVarPrepHTMLDisplay(__('Thumbnail of Photo', $dom)),
                                               P => pnVarPrepHTMLDisplay(__('Actual Photo', $dom))
                                              ) );

	$render->assign('showliving'   , $vars['showliving']);
	$render->assign('phototype'    , $vars['phototype']);
	$render->assign('max_height'   , $vars['max_height']);
	$render->assign('max_width'    , $vars['max_width']);
	$render->assign('usecache'     , $vars['usecache']);
	$render->assign('photolist'    , $vars['photolist']);


    // Return the output that has been generated by this function
	return $render->fetch('TNGz_block_RandomPhoto_modify.htm');

}


function TNGz_RandomPhotoblock_update($blockinfo)
{
    //Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['showliving'] = pnVarCleanFromInput('showliving');
    $vars['phototype']  = pnVarCleanFromInput('phototype');
    $vars['max_height'] = pnVarCleanFromInput('max_height');
    $vars['max_width']  = pnVarCleanFromInput('max_width');
    $vars['usecache']   = pnVarCleanFromInput('usecache');
    $vars['photolist']  = pnVarCleanFromInput('photolist');

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);

	// clear the block cache
    $render = & pnRender::getInstance('TNGz', false);
	$render->clear_cache('TNGz_block_RandomPhoto.htm');

    return $blockinfo;
}

