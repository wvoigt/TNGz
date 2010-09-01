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
 * TNGz showphoto
 * Display a TNG photo
 * @param $params['showliving']  To show living people or not (user, yes/all, no/none, dead)
 * @param $params['phototype']   The type of photo to show (thumbnail, photo)
 * @param $params['max_height']  largest height of picture -- scale to this if bigger
 * @param $params['max_width']   largest width of picture  -- scale to this if bigger
 * @param $params['photolist']   A list of photo ids to choose from (can be just 1).  If none given, choose from all.
 * @param $params['newwindow']   open links in new windows (no, yes)
 * @return string containing HTML for displaying a TNG photo
 */
function smarty_function_showphoto($params, &$smarty)
{
    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    $dom = ZLanguage::getModuleDomain('TNGz');

    // Valid answers, default is the first in the list
    $answer_yes    = array('Y', 'yes', 'y', '1', 'on',  'all');  // Answers for Yes or All
    $answer_no     = array('N', 'no',  'n', '0', 'off', 'none'); // Answers for No or none
    $answer_living = array('L', 'user');                         // Answers for Show Living only if user is logged in
    $answer_dead   = array('D', 'dead');                         // Answers for Show only those that are dead
    $answer_thumb  = array('T', 'thumbnail', 'thumb');           // Answers for Photo Thumbnails
    $answer_photo  = array('P', 'photo',  'photograph', 'full'); // Answers for Full photos
    
    $answer_YN     = array_merge($answer_yes, $answer_no);
    $answer_YNLD   = array_merge($answer_yes, $answer_no, $answer_living, $answer_dead );
    $answer_TP     = array_merge($answer_thumb, $answer_photo);

    $params['showliving'] = (isset($params['showliving'])) ? $params['showliving'] : "";
    $params['showliving'] = (in_array($params['showliving'], $answer_YNLD  ))? $params['showliving'] : $answer_living[0];
    $params['showliving'] = (in_array($params['showliving'], $answer_no    ))? $answer_no[0]         : $params['showliving'];    
    $params['showliving'] = (in_array($params['showliving'], $answer_yes   ))? $answer_yes[0]        : $params['showliving']; 
    $params['showliving'] = (in_array($params['showliving'], $answer_living))? $answer_living[0]     : $params['showliving'];    
    $params['showliving'] = (in_array($params['showliving'], $answer_dead  ))? $answer_dead[0]       : $params['showliving'];

    $params['newwindow'] = (isset($params['newwindow'])) ? $params['newwindow'] : "";
    $params['newwindow'] = (in_array($params['newwindow'], $answer_YN  ))? $params['newwindow']: $answer_no[0];
    $params['newwindow'] = (in_array($params['newwindow'], $answer_no  ))? $answer_no[0]       : $params['newwindow'];    
    $params['newwindow'] = (in_array($params['newwindow'], $answer_yes ))? $answer_yes[0]      : $params['newwindow']; 
    $target = ($params['newwindow'] == 'Y' )? "target=_blank" :"" ;

    $params['phototype'] = (isset($params['phototype'])) ? $params['phototype'] : "";
    $params['phototype'] = (in_array($params['phototype'], $answer_TP    ))? $params['phototype']  : $answer_thumb[0];
    $params['phototype'] = (in_array($params['phototype'], $answer_thumb ))? $answer_thumb[0]  : $params['phototype'];
    $params['phototype'] = (in_array($params['phototype'], $answer_photo ))? $answer_photo[0]  : $params['phototype']; 

    if (empty($params['max_height'])) {
        $params['max_height']  = '150';
    //  max_height = largest height of picture -- scale to this if bigger
    }
    if (empty($params['max_width'])) {
        $params['max_width']    = '150';
    // max_width  = largest width of picture  -- scale to this if bigger
    }

    if (empty($params['photolist'])) {
        $params['photolist']   = "";
    // Empty is no photos in list
    }
    // Now make sure we have a clean photo list in standard format
    $PhotoList   = "";
    $record_sep  = "";
    $entrylist   = preg_split("/[\s ]*[,;\s]+[\s ]*/",trim($params['photolist']));
    foreach ($entrylist as $entry){
        if (preg_match("/^[0-9]+$/",$entry) ){
            $PhotoList .= $record_sep . $entry;
            $record_sep =", ";
        }
    }

    $max_strikes = 3;   // The Maximum times to try and find a photo before giving up
    $photo_ref = "";
    $photo_description = "";
    $photo_error = "";

    $TNG = pnModAPIFunc('TNGz','user','TNGconfig'); 
    $TNG_path = $TNG['SitePath'] . "/" . $TNG['directory'];
    $TNG_ref  = $TNG['directory'];  // a relative path

    if (pnModAPIFunc('TNGz','user','TNGquery', array('connect'=>true) ) ) {
        $have_info = 1;
    } else {
        $have_info = 0;
        $thisday_error  = __('Error in accessing the TNG tables.', $dom);
    }

    if ($params['showliving'] != 'N' && $have_info == 1 ){

        // determine if we show living information
        $showliving = pnModAPIFunc('TNGz','user','CanUserSeeLiving');

        $need_photo = true;
        $photos_with_living = "";   // comma separated list of photoIDs we don't want to display because linked to a living person

        if (!$showliving ){
            // get the list of photoIDs that have at least 1 living person --- don't want to show those
            $query = "SELECT living.mediaID AS mediaID
                      FROM ".$TNG['medialinks_table']." AS living, ".$TNG['people_table']." AS person
                      WHERE living.personID = person.personID AND person.living = 1
                      GROUP BY living.mediaID";
            if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
                $photo_error .= __('Error in accessing the TNG tables.', $dom)." [0] ";
            }
            // now make a comma separated list of the photoIDs
            $record_sep = "";
            foreach($result as $row) {
                $photos_with_living .= $record_sep . $row['mediaID'];
                $record_sep =", ";
            }
        }

        // get a list of photolist IDs --- one per person photo link --- to pick from
        $query = "SELECT photolist.mediaID as linkID
                  FROM ".$TNG['media_table']." AS medialist, ".$TNG['medialinks_table']." AS photolist
                  WHERE photolist.mediaID = medialist.mediaID
                        AND medialist.mediatypeID = \"photos\" ";
        if ($PhotoList != ""){
            // Only include those that are already specified
            $query .= "AND photolist.mediaID IN ( $PhotoList ) ";
        }
        if (!$showliving && $photos_with_living !=""){
            // but don't include photo's of living people
            $query .= "AND photolist.mediaID NOT IN ( $photos_with_living ) ";
        }

        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
                $photo_error .= __('Error in accessing the TNG tables.', $dom)." [1] ";
        } else {
            $num_photos = count($result);  // the number of photo links to pick from

            for ( $strikes = 0 ; $strikes <= $max_strikes && $need_photo; $strikes++ ) {
                // just in case of problems, try at most max_strikes times
                $rand = RAND(0,$num_photos - 1);
                $row = $result[$rand];

                // now get the actual photo link
                $query = "SELECT ".$TNG['media_table'].".path              AS t_path, 
                                 ".$TNG['media_table'].".thumbpath         AS t_thumbpath,
                                 ".$TNG['media_table'].".description       AS t_description,
                                 ".$TNG['media_table'].".notes             AS t_notes,
                                 ".$TNG['medialinks_table'].".medialinkID  AS t_medialinkID,
                                 ".$TNG['medialinks_table'].".personID     AS t_personID,
                                 ".$TNG['medialinks_table'].".gedcom       AS t_gedcom,
                                 ".$TNG['media_table'].".mediaID           AS mediaID,
                                   usecollfolder
                          FROM ".$TNG['media_table'].", ".$TNG['medialinks_table']."
                          WHERE ".$TNG['medialinks_table'].".mediaID = \"".$row['linkID']."\"
                                AND ".$TNG['media_table'].".mediaID = ".$TNG['medialinks_table'].".mediaID ";

                if (false === ($result2 = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
                    $photo_error .= __('Error in accessing the TNG tables.', $dom)." [2] ";
                } else {
                    $row2 = $result2[0];
                    if ($params['phototype']  == 'P'){
                        $picture = $row2['t_path'];
                    } else {
                        $picture = $row2['t_thumbpath'];
                    }
                    $picture_file = "$TNG_path/".$TNG['photopath']."/$picture";
                    $picture_ref  = "$TNG_ref/" .$TNG['photopath']."/". str_replace("%2F","/",rawurlencode($picture));

                    // check to make sure a picture defined and the file exists
                    if ($picture != "" && file_exists($picture_file)) {
                        // Found the photo thumbnail to display
                        $temp1 = pnModAPIFunc('TNGz','user','PhotoRef',
                                          array('photo_file'  => $picture_file,
                                                'web_ref'     => $picture_ref,
                                                'max_height'  => $params['max_height'],
                                                'max_width'   => $params['max_width'],
                                                'text'        => $row2['t_description'],
                                                'description' => "border='0'"));

                        $photo_ref     = pnModAPIFunc('TNGz','user','MakeRef',
                                                   array('func'        => "getperson",
                                                         'personID'    => $row2['t_personID'],
                                                         'tree'        => $row2['t_gedcom'],
                                                         'description' => $temp1,
                                                         'target'      => $target
                                                         ));
                        $photo_description = $row2['t_description'];
                        $need_photo = false;
                    }
                }
            }
        }
        if ( $need_photo ) {
            // Didn't get a photo this time
            $photo_error  .= __('No Photo found', $dom)." ";
        }
    }

    $output  = "<div>";
    if ($photo_error) {
        $output .= pnVarPrepHTMLDisplay($photo_error);
    } else {
        $output .= $photo_ref . "<br />" . pnVarPrepHTMLDisplay($photo_description);
    }
    $output .= "</div>\n";
    return $output;
}
