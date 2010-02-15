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

    $params['showliving'] = (in_array($params['showliving'], $answer_YNLD  ))? $params['showliving'] : $answer_living[0];
    $params['showliving'] = (in_array($params['showliving'], $answer_no    ))? $answer_no[0]         : $params['showliving'];    
    $params['showliving'] = (in_array($params['showliving'], $answer_yes   ))? $answer_yes[0]        : $params['showliving']; 
    $params['showliving'] = (in_array($params['showliving'], $answer_living))? $answer_living[0]     : $params['showliving'];    
    $params['showliving'] = (in_array($params['showliving'], $answer_dead  ))? $answer_dead[0]       : $params['showliving'];

    $params['newwindow'] = (in_array($params['newwindow'], $answer_YN  ))? $params['newwindow']: $answer_no[0];
    $params['newwindow'] = (in_array($params['newwindow'], $answer_no  ))? $answer_no[0]       : $params['newwindow'];    
    $params['newwindow'] = (in_array($params['newwindow'], $answer_yes ))? $answer_yes[0]      : $params['newwindow']; 
    $target = ($params['newwindow'] == 'Y' )? "target=_blank" :"" ;

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

    $TNGpaths = pnModAPIFunc('TNGz','user','GetTNGpaths');
    $TNG_path = $TNGpaths['SitePath'] . "/" . $TNGpaths['directory'];
    $TNG_ref  = $TNGpaths['directory'];  // a relative path

    // Check to be sure we can get to the TNG information
    if (file_exists($TNGpaths['configfile']) ){
        include($TNGpaths['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    } else {
        $have_info = 0;
        $photo_error  = __('Error in accessing the TNG tables.', $dom);
    }

    if ($params['showliving'] != 'N' && $have_info == 1 ){

        // determine if we show living information
        $showliving = false;  // default to no
        if ($params['showliving'] == 'Y') {
            $showliving = true;
        } elseif ( $params['showliving'] == 'L' && pnUserLoggedIn() ) {
            // now check to make sure TNG says user can see the living
            $userid = pnUserGetVar('uname');
            $query = "SELECT allow_living FROM $users_table WHERE username = '$userid' ";
            if ($result = &$TNG_conn->Execute($query) ) {
                list($TNG_living) = $result->fields;
                if ($TNG_living == "1") {
                    $showliving = true;
                }
            }
            $result->Close();
        }

        $need_photo = true;
        $photos_with_living = "";   // comma separated list of photoIDs we don't want to display because linked to a living person

        if (!$showliving ){
                // get the list of photoIDs that have at least 1 living person --- don't want to show those
                $query = "SELECT living.mediaID AS mediaID
                          FROM $medialinks_table AS living, $people_table AS person
                          WHERE living.personID = person.personID AND person.living = 1
                          GROUP BY living.mediaID";
                if (!$result = &$TNG_conn->Execute($query) ) {
                    $photo_error .= __('Error in accessing the TNG tables.', $dom)." [0] " . $TNG_conn->ErrorMsg() . " ";
                }
                // now make a comma separated list of the photoIDs
                $record_sep = "";
                for (; !$result->EOF; $result->MoveNext()) {
                    list($mediaID) = $result->fields;
                     $photos_with_living .= $record_sep . $mediaID;
                     $record_sep =", ";
                }
                $result->Close();
        }

        // get a list of photolist IDs --- one per person photo link --- to pick from
        $query = "SELECT photolist.mediaID as linkID
                  FROM $media_table, $medialinks_table AS photolist
                  WHERE photolist.mediaID = $media_table.mediaID
                        AND $media_table.mediatypeID = \"photos\" ";
        if ($PhotoList != ""){
            // Only include those that are already specified
            $query .= "AND photolist.mediaID IN ( $PhotoList ) ";
        }
        if (!$showliving ){
            // but don't include photo's of living people
            $query .= "AND photolist.mediaID NOT IN ( $photos_with_living ) ";
        }

       	if (!$result = &$TNG_conn->Execute($query)  ) {
                $photo_error .= __('Error in accessing the TNG tables.', $dom)." [1] " . $TNG_conn->ErrorMsg() . " ";
        } else {

            $num_photos = $result->RecordCount();  // the number of photo links to pick from

            for ( $strikes = 0 ; $strikes <= $max_strikes && $need_photo; $strikes++ ) {
                // just in case of problems, try at most max_strikes times
                $result->Move( RAND(0,$num_photos - 1) );
                list($linkID) = $result->fields;

                // now get the actual photo link
                $query = "SELECT $media_table.path, $media_table.thumbpath, $media_table.description, $media_table.notes, $medialinks_table.medialinkID, $medialinks_table.personID, $medialinks_table.gedcom as gedcom, $media_table.mediaID as mediaID, usecollfolder
                          FROM $media_table, $medialinks_table
                          WHERE $medialinks_table.mediaID = \"$linkID\"
                                AND $media_table.mediaID = $medialinks_table.mediaID ";

                if (!$result2 = &$TNG_conn->Execute($query)) {
                    $photo_error .= __('Error in accessing the TNG tables.', $dom)." [2] " . $TNG_conn->ErrorMsg()  . " ";
                } else {
                    list($t_path,$t_thumbpath,$t_description,$t_notes,$t_medialinkID,$t_personID,$t_gedcom,$t_mediaID,$usecollfolder) = $result2->fields;
                    $result2->Close();

                    if ($params['phototype']  == 'P'){
                        $picture = $t_path;
                    } else {
                        $picture = $t_thumbpath;
                    }
                    $picture_file = "$TNG_path/$photopath/$picture";
                    $picture_ref  = "$TNG_ref/$photopath/". str_replace("%2F","/",rawurlencode($picture));

                    // check to make sure a picture defined and the file exists
                    if ($picture != "" && file_exists($picture_file)) {
                        // Found the photo thumbnail to display
                        $temp1 = pnModAPIFunc('TNGz','user','PhotoRef',
                                          array('photo_file'  => $picture_file,
                                                'web_ref'     => $picture_ref,
                                                'max_height'  => $params['max_height'],
                                                'max_width'   => $params['max_width'],
                                                'text'        => $t_description,
                                                'description' => "border='0'"));

                        $photo_ref     = pnModAPIFunc('TNGz','user','MakeRef',
                                                   array('func'        => "getperson",
                                                         'personID'    => $t_personID,
                                                         'tree'        => $t_gedcom,
                                                         'description' => $temp1,
                                                         'target'      => $target
                                                         ));
                        $photo_description = $t_description;
                        $need_photo = false;
                    }
                }
            }
            $result->Close();
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
