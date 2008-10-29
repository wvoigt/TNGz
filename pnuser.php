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
* TNG interface in Zikula
* 
* Displays the given TNG page either wrapped in Zikula or not, depending upon the page given
* 
* @param show TNG page to display.  Additional arguments may be used by TNG 
* @return true if the page has already been displayed, othersise the TNG data to be used in Zikula
*/ 
function TNGz_user_main() {

    $TNGpage = FormUtil::getPassedValue('show', 'index', 'GET');

    switch ($TNGpage) {
	    case 'gedcom':
	    case 'addbookmark':
	    case 'findperson':
	    case 'findpersonform':
	    case 'tngrss':
	    case 'tnghelp':
	    case 'tentedit':
	    case 'pedxml':
	    case 'showmediaxml':
        case 'smallimage':
	    case 'pdfform':
	    case 'rpt_descend':
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
function TNGz_user_admin() {

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_OVERVIEW)) {
	return LogUtil::registerError(_MODULENOAUTH);
    }

    if (!pnUserLoggedIn()) {
        // Must be logged in to even have a chance at getting to the administration page
        pnRedirect(pnModURL('Users','user','loginscreen')) ;
    }

    if (!$url=pnModAPIFunc('TNGz','user','GetTNGurl') ){
    	return LogUtil::registerError("Error accessing TNG config file.");
    }

    //////////////////////////////////////////////////////
    // Now go and display it
    //////////////////////////////////////////////////////
    $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

    $pnRender =& new pnRender('TNGz');

    $pnRender->assign('TNGzURL'      , $url);
    $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );

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
function TNGz_user_sitemap() {
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
        if ($facts['sitemapindex']) {  // too big for a sitemap
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


function TNGz_user_worldmap() {
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }   
 
    $image   = FormUtil::getPassedValue('image', false, 'GET');
    
    if (!$image) {
        $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

        $pnRender =& new pnRender('TNGz');
        $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );
        return $pnRender->fetch('TNGz_user_worldmap.htm');
    }
    
    // Just generate the image

    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');

    // Check to be sure we can get to the TNG information
    $have_info = 0;
    if (file_exists($TNG['configfile']) ){
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
    
    // Load the background map
    if (!$im  = imagecreatefromjpeg("modules/TNGz/pnimages/800px-Whole_world_-_land_and_oceans.jpg")){
        echo "Image failed to load";
        exit;
    }
    $red = imagecolorallocate ($im, 255,0,0);

    // find the base image size
    $scale_x = imagesx($im);
    $scale_y = imagesy($im);

   // Now we convert the long/lat coordinates into screen coordinates
   foreach($points as $point){
       $pt = TNGz_map_coordinates($point['latitude'], $point['longitude'], $scale_x, $scale_y);
       // mark the point on the map using a 2 pixel rectangle
       imagefilledellipse($im,$pt["x"],$pt["y"],2,2,$red);      
   }

   // Return the map image. Uing a PNG format since it gives better final image quality

   header ("Content-type: image/png");
   imagepng($im);
   imagedestroy($im); 
   return true;
}

function TNGz_map_coordinates($lat, $lon, $width, $height)
{
   $x = (($lon + 180) * ($width / 360));
   $y = ((($lat * -1) + 90) * ($height / 180));
   return array("x"=>round($x),"y"=>round($y));
}