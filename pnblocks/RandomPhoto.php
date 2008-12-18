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
 * @version
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

    if( !pnSecAuthAction( 0, 'TNGz:RandomPhotoblock:', "$blockinfo[title]::", ACCESS_READ ) )
        return;

    if( !pnModAPILoad('TNGz','user',true) )
    {
        return false;
    }

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showliving']))
    {
        $vars['showliving']  = 'L';
    //    N = No or None
    //    D = show only dead people (no living)
    //    Y = show all
    //    L = show living only if user is logged in
    }
    if (empty($vars['phototype']))
    {
        $vars['phototype']  = 'T';
    //    T = use Thumbnail
    //    P = use Photo
    }
    if (empty($vars['max_height']))
    {
        $vars['max_height']  = '150';
    //  max_height = largest height of picture -- scale to this if bigger
    }
    if (empty($vars['max_width']))
    {
        $vars['max_width']    = '150';
// max_width  = largest width of picture  -- scale to this if bigger
    }
    if (empty($vars['usecache']))
    {
        $vars['usecache']   = 0;
    //    1 = Yes
    //    0 = No
    }
    if (empty($vars['photolist']))
    {
        $vars['photolist']   = "";
    // Empty is no photos in list
    }

    $PhotoList   = "";
    $record_sep  = "";
    $entrylist   = preg_split("/[\s ]*[,;\s]+[\s ]*/",trim($vars['photolist']));
    foreach ($entrylist as $entry)
    {
        if (preg_match("/^[0-9]+$/",$entry) )
        {
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
    $TNGstyle = pnModGetVar('TNGz', '_style');

    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');
    $TNG_path = $TNG['SitePath'] . "/" . $TNG['directory'];
    $TNG_ref  = $TNG['directory'];                          // a relative path
    // $TNG_ref  = $TNG['WebRoot']   . "/" . $TNG['directory'];    // absolute path

    // Check to be sure we can get to the TNG information
    if (file_exists($TNG['configfile']) )
    {
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    } else
    {
        $have_info = 0;
        $photo_error  = ""._PEOPLEDBFERROR."";
    }


    if ($window == 1 )
    {
        $target = "target=_blank" ;
    }

    if ($vars['showliving'] != 'N' && $have_info == 1 )
    {

        // determine if we show living information
        $showliving = false;  // default to no
        if ($vars['showliving'] == 'Y')
        {
            $showliving = true;
        } elseif ( $vars['showliving'] == 'L' && pnUserLoggedIn() )
        {
            // now check to make sure TNG says user can see the living
            $userid = pnUserGetVar('uname');
            $query = "SELECT allow_living FROM $users_table WHERE username = '$userid' ";
            if ($result = &$TNG_conn->Execute($query) )
            {
                list($TNG_living) = $result->fields;
                if ($TNG_living == "1")
                {
                    $showliving = true;
                }
            }
            $result->Close();
        }

        $need_photo = true;
        $photos_with_living = "";   // comma separated list of photoIDs we don't want to display because linked to a living person

        if (!$showliving )
        {
                // get the list of photoIDs that have at least 1 living person --- don't want to show those
                $query = "SELECT living.mediaID AS mediaID
                        FROM $medialinks_table AS living, $people_table AS person
                        WHERE living.personID = person.personID AND person.living = 1
                        GROUP BY living.mediaID";
                if (!$result = &$TNG_conn->Execute($query) )
                {
                    $photo_error .= ""._PEOPLEDBFERROR." [0] " . $TNG_conn->ErrorMsg() . " ";
                }
                // now make a comma separated list of the photoIDs
                $record_sep = "";
                for (; !$result->EOF; $result->MoveNext())
                {
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
        if ($PhotoList != "")
        {
            // Only include those that are already specified
            $query .= "AND photolist.mediaID IN ( $PhotoList ) ";
        }
        if (!$showliving )
        {
            // but don't include photo's of living people
            $query .= "AND photolist.mediaID NOT IN ( $photos_with_living ) ";
        }

        if (!$result = &$TNG_conn->Execute($query)  )
        {
                $photo_error .= ""._PEOPLEDBFERROR." [1] " . $TNG_conn->ErrorMsg() . " ";
        } else
        {

            $num_photos = $result->RecordCount();  // the number of photo links to pick from

            for ( $strikes = 0 ; $strikes <= $max_strikes && $need_photo; $strikes++ )
            {
                // just in case of problems, try at most max_strikes times
                $result->Move( RAND(0,$num_photos - 1) );
                list($linkID) = $result->fields;

                // now get the actual photo link
                $query = "SELECT $media_table.path, $media_table.thumbpath, $media_table.description, $media_table.notes, $medialinks_table.medialinkID, $medialinks_table.personID, $medialinks_table.gedcom as gedcom, $media_table.mediaID as mediaID, usecollfolder
                        FROM $media_table, $medialinks_table
                        WHERE $medialinks_table.mediaID = \"$linkID\"
                                AND $media_table.mediaID = $medialinks_table.mediaID ";

                if (!$result2 = &$TNG_conn->Execute($query))
                {
                    $photo_error .= ""._PEOPLEDBFERROR." [2] " . $TNG_conn->ErrorMsg()  . " ";
                } else
                {
                    list($t_path,$t_thumbpath,$t_description,$t_notes,$t_medialinkID,$t_personID,$t_gedcom,$t_mediaID,$usecollfolder) = $result2->fields;
                    $result2->Close();

                    if ($vars['phototype']  == 'P')
                    {
                        $picture = $t_path;
                    } else
                    {
                        $picture = $t_thumbpath;
                    }
                    $picture_file = "$TNG_path/$photopath/$picture";
                    $picture_ref  = "$TNG_ref/$photopath/". str_replace("%2F","/",rawurlencode($picture));

                    // check to make sure a picture defined and the file exists
                    if ($picture != "" && file_exists($picture_file))
                    {
                        // Found the photo thumbnail to display
                        $temp1 = pnModAPIFunc('TNGz','user','PhotoRef',
                                        array('photo_file'  => $picture_file,
                                                'web_ref'     => $picture_ref,
                                                'max_height'  => $vars['max_height'],
                                                'max_width'   => $vars['max_width'],
                                                'text'        => $t_description,
                                                'description' => "border='0'"));

                        $photo_ref     = pnModAPIFunc('TNGz','user','MakeRef',
                                                array('func'        => "showmedia",
                                                        'personID'    => $t_personID,
                                                        'mediaID'     => $t_mediaID,
                                                        'medialinkID' => $t_medialinkID,
                                                        'description' => $temp1,
                                                        'target'      => $target,
                                                        'RefType'     => $TNGstyle
                                                        ));


                        $photo_description = $t_description;
                        $need_photo = false;
                    }

                }
            }
            $result->Close();
        }
        if ( $need_photo )
        {
            // Didn't get a photo this time
            $photo_error  .= ""._NOPHOTOFOUND." ";
        }

    }
    if ($have_info == 1 )
    {
        $TNG_conn->Close();
    }

    // Can turn off caching by using the following
    if ( $vars['usecache'] == 0 )
    {
        $zcaching = false;
    } else
    {
        $zcaching = true;
    }

    // Create output object
    // Note that for a block the corresponding module must be passed.

    $pnRender = pnRender::getInstance('TNGz', $zcaching);

    $pnRender->assign('photo_ref'        , $photo_ref);
    $pnRender->assign('photo_description', $photo_description);
    $pnRender->assign('photo_error'      , $photo_error);

    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('TNGz_block_RandomPhoto.htm');

    return themesideblock($blockinfo);
}


function TNGz_RandomPhotoblock_modify($blockinfo)
{

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['showliving']))
    {
        $vars['showliving'] = "L";
    }
    if (empty($vars['phototype']))
    {
        $vars['phototype'] = "T";
    }
    if (empty($vars['max_height']))
    {
        $vars['max_height']  = "150";
    }
    if (empty($vars['max_width']))
    {
        $vars['max_width']   = "150";
    }
    if (empty($vars['usecache']))
    {
        $vars['usecache']   = 0;
    }
    if (empty($vars['photolist']))
    {
        $vars['photolist']   = "";
    }

    // Create output object
    $pnRender =& new pnRender('TNGz');

    // As Admin output changes often, we do not want caching.
    $pnRender->caching = false;

    // assign the approriate values
    $pnRender->assign('showlivinglist', array(
                                            Y => pnVarPrepHTMLDisplay(_SELECTLIVINGY),
                                            D => pnVarPrepHTMLDisplay(_SELECTLIVINGD),
                                            L => pnVarPrepHTMLDisplay(_SELECTLIVINGL),
                                            N => pnVarPrepHTMLDisplay(_SELECTLIVINGN)
                                            ) );
    $pnRender->assign('phototypelist', array(
                                            T => pnVarPrepHTMLDisplay(_SELECTPHOTOT),
                                            P => pnVarPrepHTMLDisplay(_SELECTPHOTOP)
                                            ) );

    $pnRender->assign('showliving'   , $vars['showliving']);
    $pnRender->assign('phototype'    , $vars['phototype']);
    $pnRender->assign('max_height'   , $vars['max_height']);
    $pnRender->assign('max_width'    , $vars['max_width']);
    $pnRender->assign('usecache'     , $vars['usecache']);
    $pnRender->assign('photolist'    , $vars['photolist']);


    // Return the output that has been generated by this function
    return $pnRender->fetch('TNGz_block_RandomPhoto_modify.htm');

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
    $pnRender =& new pnRender('TNGz');
    $pnRender->clear_cache('TNGz_block_RandomPhoto.htm');

    return $blockinfo;
}

