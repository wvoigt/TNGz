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

/**
* TNG interface in Zikula
*
* Displays the given TNG page either wrapped in Zikula or not, depending upon the page given
*
* @param show TNG page to display.  Additional arguments may be used by TNG
* @return true if the page has already been displayed, othersise the TNG data to be used in Zikula
*/
function TNGz_user_main()
{

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_READ)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    $TNGpage = FormUtil::getPassedValue('show', 'index', 'GETPOST');

    $pnRender =& new pnRender('TNGz');
    
    // Create a possible template name from the input, if it exists, then do than instead of TNG
    $TNGzpage = 'TNGz_show_' . (($TNGpage == 'index' && pnModGetVar('TNGz', '_homepage', 0) ) ? 'home' : $TNGpage ) . '.htm';
    if ( $pnRender->template_exists( $TNGzpage ) ) {
        return $pnRender->fetch( $TNGzpage );
    }

    switch ($TNGpage) {
        case 'gedcom':
        case 'addbookmark':
        case 'findperson':
        case 'findpersonform':
        case 'tngrss':
        case 'tnghelp':
        case 'tentedit':
        case 'pedxml':
        case 'pedjson':
        case 'showmediaxml':
        case 'smallimage':
        case 'timelinexml':
        case 'pdfform':
        case 'rpt_descend':
        case 'rpt_fam':
        case 'rpt_ind':
        case 'rpt_pedigree':
                // for these, just give the output, with no extra stuff wrapped around it
                $TNGrenderpage = false;
                break;
        default:
                // Everything else can be wrapped as usual
                $TNGrenderpage = true;
                break;
    }
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => $TNGpage, 'render' => $TNGrenderpage ));
}

/**
* TNG administration in Zikula
*
* Checks for logged in Zikula user and permissions, then sets up TNG administration in an IFRAME.
*
* @param none
* @return IFRAME page calling TNG administration
*/
function TNGz_user_admin()
{

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_OVERVIEW)) {
        return LogUtil::registerError(_MODULENOAUTH);
    }

    if (!pnUserLoggedIn()) {
        // Must be logged in to even have a chance at getting to the administration page
        pnRedirect(pnModURL('Users','user','loginscreen')) ;
    }

    if (!$url=pnModAPIFunc('TNGz','user','GetTNGurl') ) {
        return LogUtil::registerError("Error accessing TNG config file.");
    }

    //////////////////////////////////////////////////////
    // Now go and display it
    //////////////////////////////////////////////////////
    $TNGzModInfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

    $pnRender =& new pnRender('TNGz');

    $pnRender->assign('TNGzURL'      , $url);
    $pnRender->assign('TNGzVersion'  , $TNGzModInfo['version'] );

    return $pnRender->fetch('TNGz_user_admin.htm');

}

/**
* TNG sitemap generation
*
* Produce three different types of sitemaps
* 1. If no parameters are used, a full sitemap if the number of entries is below a set threshold
* 2. If no parameters are used, a sitemapindex file if the number of entries is above a set threshold
* 3. If parameters are used, a partial sitemap file that can be called by the sitemapindex file.
*
* @param map    (optional) subset record type on which to generate a sitemap (e.g., people, family, etc.)
* @param start  (optional) the first record number to show
* @param count  (optional) the number of records to show
* @return true if sitemap/sitemapindex is displayed, false on error
*/
function TNGz_user_sitemap()
{
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    $none = "-1";  // flag

    $map   = FormUtil::getPassedValue('map',   $none, 'GET');
    $start = FormUtil::getPassedValue('start', $none, 'GET');
    $count = FormUtil::getPassedValue('count', $none, 'GET');

    $records = array();

    switch ($map) {
        case 'people':
                $records[] = array('type' => 'people', 'start' => $start, 'count' => $count );
                $all_site = false;
                break;
        case 'family':
                $records[] = array('type' => 'family', 'start' => $start, 'count' => $count );
                $all_site = false;
                break;
        case 'all':
        default:
                $records[] = array('type' => 'people', 'start' => $none, 'count' => $none );
                $records[] = array('type' => 'family', 'start' => $none, 'count' => $none );
                $all_site = true;
                break;
    }

    if ($all_site) {
        $facts = pnModAPIFunc('TNGz','user','getRecordsCount');
        if ($facts['sitemapindex']) { // too big for a sitemap
            // return a sitemapindex file
            $pnRender =& new pnRender('TNGz');
            $pnRender->assign('sitemaps'  , $facts['sitemapindex']);
            $pnRender->display('TNGz_user_sitemapindex.htm');
            return true;
        }
    }

    // Return a sitemap (either full, or a partial one)
    $pnRender =& new pnRender('TNGz');
    $pnRender->assign('records', $records);
    $pnRender->display('TNGz_user_sitemap.htm');
    return true;

}

function TNGz_user_view()
{
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    $item   = FormUtil::getPassedValue('item', false, 'GET');
    $validitems = array('places', 'surnames');  // first is default
    $item = (in_array($item, $validitems))? $item : $validitems[0];
    
    $top  = FormUtil::getPassedValue('top', $default_top, 'GET');
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 100;  // use given or set default

    if ($item == "places" ) {

        $thePlaces   = pnModAPIFunc('TNGz','user','GetPlaces', array('top'=> $top));
        $TNGzModInfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

        $pnRender =& new pnRender('TNGz');
        $pnRender->assign('TNGzVersion'  , $TNGzModInfo['version'] );
        $pnRender->assign('top'          , $top);
        $pnRender->assign('places'       , $thePlaces);
        return $pnRender->fetch('TNGz_user_view_places.htm');
    }

    if ($item == "surnames" ) {

        $Surnames =  pnModAPIFunc('TNGz','user','GetSurnames', array('top'=> $top));
        $TNGzModInfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

        $pnRender =& new pnRender('TNGz');
        $pnRender->assign('TNGzVersion'  , $TNGzModInfo['version'] );
        $pnRender->assign('top'          , $top);
        $pnRender->assign('SurnameCount' , $Surnames['count']);
        $pnRender->assign('SurnameRank'  , $Surnames['rank']);
        $pnRender->assign('SurnameAlpha' , $Surnames['alpha']);
        return $pnRender->fetch('TNGz_user_view_surnames.htm');
    }

}


function TNGz_user_worldmap()
{
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    $size       = FormUtil::getPassedValue('size'  , false, 'GET');
    $sizetypes  = array('tiny', 'small', 'medium', 'large', 'huge', 'giant');  // first in list is default
    $size       = (in_array($size, $sizetypes)) ? $size : $sizetypes[0];

    $region  = FormUtil::getPassedValue('region', 0,     'GET');
    $region = (in_array($region, array(0,1,2,3,4,5,6)))? $region : 0;

    // See if already in the cache
    $cachefile    = sprintf("worldmap_%s_%s.png",$size,$region);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache',     array( 'item'=> $cachefile ));
    if ($cacheresults) {
        header ("Content-type: image/png");
        echo $cacheresults;
        return true;
    }

    // Not in cache or out of date, so go create it
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // Check to be sure we can get to the TNG information
    $have_info = 0;
    if (file_exists($TNG['configfile']) ) {
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $have_info = 1;
    }
    if (!$have_info) {
        return(false);
    }

    // Now go get all the locations to plot
    $query  = "SELECT placelevel, latitude,longitude,zoom from $places_table ";
    $query .= "WHERE latitude <> 0 AND longitude <> 0 AND latitude is not null AND longitude is not null";

    if (!$result = &$TNG_conn->Execute($query) ) {
        return(false);
    }
    $points = array();
    if($result->RecordCount()>0) {
        for (; !$result->EOF; $result->MoveNext()) {
            $items = array();
            list( $items['placelevel'],$items['latitude'],$items['longitude'],$items['zoom']) = $result->fields;
            $points[] = $items;
        }
    }
    mysql_free_result($result);
    $TNG_conn->Close();

    switch ($size) {
        case 'tiny':
                $ratio = 0.25;
                break;
        case 'small':
                $ratio = 0.50;
                break;
        case 'medium':
                $ratio = 0.75;
                break;
        case 'huge':
                $ratio = 1.25;
                break;
        case 'giant':
                $ratio = 1.50;
                break;
        case 'large':
        default:
                $ratio = 1.0;
                break;
    }

    // Load the background map
    $imagefile = "800px-Whole_world_-_land_and_oceans.jpg";
    if (!$source  = imagecreatefromjpeg("modules/TNGz/pnimages/". $imagefile)) {
        return pnVarPrepHTMLDisplay("aaaWorld Map image failed to load");
    }
    // find the base image size
    $source_x = imagesx($source);
    $source_y = imagesy($source);

    // Create an image based upon the source (same size)
    $im = imagecreatetruecolor($source_x, $source_y);
    imagecopyresized($im, $source, 0, 0, 0, 0, $source_x , $source_y, $source_x, $source_y);
    imagedestroy($source);

    // find the base image size
    $scale_x = imagesx($im);
    $scale_y = imagesy($im);

    $red = imagecolorallocate ($im, 255,0,0);

    // Now we convert the long/lat coordinates into image coordinates
    foreach($points as $point) {
        $pt = TNGz_map_coordinates($point['latitude'], $point['longitude'], $scale_x, $scale_y);
        imagefilledellipse($im,$pt["x"],$pt["y"],2,2,$red); // mark on the map
    }

    // Define the partial regions.  Region 0 is everything.
    // Regions are arranged as:  1  2  3 <= Northern Hemisphere
    //                           4  5  6 <= Southern Hemisphere
    $regions[0]=array('y'=> 0.00, 'h'=> 1.00, 'x'=> 0.00,                              'w'=> 1.00 );
    $regions[1]=array('y'=> 0.00, 'h'=> 0.50, 'x'=> 0.00,                              'w'=> 0.45 );
    $regions[2]=array('y'=> 0.00, 'h'=> 0.50, 'x'=> $regions[1]['w'],                  'w'=> 0.30 );
    $regions[3]=array('y'=> 0.00, 'h'=> 0.50, 'x'=> $regions[1]['w']+$regions[2]['w'], 'w'=> 1- $regions[1]['w']-$regions[2]['w']);
    $regions[4]=array('y'=> 0.50, 'h'=> 0.50, 'x'=> $regions[1]['x'],                  'w'=> $regions[1]['w'] );
    $regions[5]=array('y'=> 0.50, 'h'=> 0.50, 'x'=> $regions[2]['x'],                  'w'=> $regions[2]['w'] );
    $regions[6]=array('y'=> 0.50, 'h'=> 0.50, 'x'=> $regions[3]['x'],                  'w'=> $regions[3]['w'] );

    $part_x = $source_x * $regions[$region]['x'];
    $part_w = $source_x * $regions[$region]['w'];
    $part_y = $source_y * $regions[$region]['y'];
    $part_h = $source_y * $regions[$region]['h'];

    // Get ready for final image with the right size
    $dest_x  = 0;
    $dest_y  = 0;
    $dest_w  = $part_w * $ratio;
    $dest_h  = $part_h * $ratio;

    // Now create the final image, then copy (all or part) and resize
    $im_return = imagecreatetruecolor($dest_w, $dest_h);
    imagecopyresized($im_return, $im, $dest_x, $dest_y, $part_x, $part_y, $dest_w, $dest_h, $part_w, $part_h);
    imagedestroy($im);

    // Save the map image using a PNG format since it gives better final image quality
    ob_start();
    $image = imagepng($im_return);
    $saved_image = ob_get_contents();
    ob_end_clean();
    imagedestroy($im_return);
    
    // Now Display it
    header ("Content-type: image/png");
    echo $saved_image;
    
    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $saved_image) );
    return true;
}

function TNGz_map_coordinates($lat, $lon, $width, $height)
{
   $x = (($lon + 180) * ($width / 360));
   $y = ((($lat * -1) + 90) * ($height / 180));
   return array("x"=>round($x),"y"=>round($y));
}