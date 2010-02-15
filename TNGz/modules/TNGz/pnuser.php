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
        $dom = ZLanguage::getModuleDomain('TNGz');
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom));
    }

    $TNGpage = FormUtil::getPassedValue('show', 'index', 'GETPOST');

    $render = & pnRender::getInstance('TNGz', false);
    
    // Create a possible template name from the input, if it exists, then do than instead of TNG
    $TNGzpage = 'TNGz_show_' . (($TNGpage == 'index' && pnModGetVar('TNGz', '_homepage', 0) ) ? 'home' : $TNGpage ) . '.htm';
    if ( $render->template_exists( $TNGzpage ) ) {
        return $render->fetch( $TNGzpage );
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
    $dom = ZLanguage::getModuleDomain('TNGz');

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_OVERVIEW)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom));
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

    $render = & pnRender::getInstance('TNGz', false);

    $render->assign('TNGzURL'      , $url);
    $render->assign('TNGzVersion'  , $TNGzModInfo['version'] );

    return $render->fetch('TNGz_user_admin.htm');

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
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(__('Sorry! No authorization to access this module.', $dom));
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
            $render = & pnRender::getInstance('TNGz', false);
            $render->assign('sitemaps'  , $facts['sitemapindex']);
            $render->display('TNGz_user_sitemapindex.htm');
            return true;
        }
    }

    // Return a sitemap (either full, or a partial one)
    $render = & pnRender::getInstance('TNGz', false);
    $render->assign('records', $records);
    $render->display('TNGz_user_sitemap.htm');
    return true;

}

function TNGz_user_view()
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(__('Sorry! No authorization to access this module.', $dom));
    }

    $item   = FormUtil::getPassedValue('item', false, 'GET');
    $validitems = array('places', 'surnames');  // first is default
    $item = (in_array($item, $validitems))? $item : $validitems[0];
    
    $top  = FormUtil::getPassedValue('top', $default_top, 'GET');
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 100;  // use given or set default

    if ($item == "places" ) {

        $thePlaces   = pnModAPIFunc('TNGz','user','GetPlaces', array('top'=> $top));
        $TNGzModInfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

        $render = & pnRender::getInstance('TNGz', false);
        $render->assign('TNGzVersion'  , $TNGzModInfo['version'] );
        $render->assign('top'          , $top);
        $render->assign('places'       , $thePlaces);
        return $render->fetch('TNGz_user_view_places.htm');
    }

    if ($item == "surnames" ) {

        $Surnames =  pnModAPIFunc('TNGz','user','GetSurnames', array('top'=> $top));
        $TNGzModInfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

        $render = & pnRender::getInstance('TNGz', false);
        $render->assign('TNGzVersion'  , $TNGzModInfo['version'] );
        $render->assign('top'          , $top);
        $render->assign('SurnameCount' , $Surnames['count']);
        $render->assign('SurnameRank'  , $Surnames['rank']);
        $render->assign('SurnameAlpha' , $Surnames['alpha']);
        return $render->fetch('TNGz_user_view_surnames.htm');
    }

}


function TNGz_user_worldmap()
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(__('Sorry! No authorization to access this module.', $dom));
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
        return pnVarPrepHTMLDisplay("World Map image failed to load");
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

function TNGz_user_saveid()
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(__('Sorry! No authorization to access this module.', $dom));
    }

    // Get arguments
    $params['show']        = FormUtil::getPassedValue('show',       false, 'GETPOST');
    $params['personID']    = FormUtil::getPassedValue('personID',   false, 'GETPOST');
    $params['primaryID']   = FormUtil::getPassedValue('primaryID',  false, 'GETPOST');
    $params['tree']        = FormUtil::getPassedValue('tree',       false, 'GETPOST');
    $params['generations'] = FormUtil::getPassedValue('generations',false, 'GETPOST');
    $params['display']     = FormUtil::getPassedValue('display',    false, 'GETPOST');
    $delete                = FormUtil::getPassedValue('delete',     false, 'GETPOST');

    $pID  = ($params['personID']) ? $params['personID'] : $params['primaryID'];

    if (!pnUserLoggedIn() || !$pID || !$params['tree'] || !$params['show'] ) {
        return pnVarPrepHTMLDisplay(__('Sorry! No authorization to access this module.', $dom));
    }

    $username = pnUserGetVar('uname');
    $SaveList = unserialize(pnModGetVar('TNGz','SaveList',''));
    
    if ($delete) {
        unset($SaveList[$username]);
    } else {
        $SaveList[$username] = array('id'=>$pID,'tree'=>$params['tree']);
    }
    pnModSetVar('TNGz','SaveList',serialize($SaveList));

    // don't pass empty parameters
    foreach ($params as $key => $value) {
        if (!$value) {
            unset($params[$key]);
        }
    }

    pnRedirect(pnModURL('TNGz','user','main', $params, null, null, true));
    return true;
}

function TNGz_user_histogram()
{
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(__('Sorry! No authorization to access this module.', $dom));
    }

    // Parameters
    $step    = FormUtil::getPassedValue('step'  , 1, 'GETPOST');
    $step    = ((int)$step > 0) ? (int)$step : 1;
    
    $scale   = FormUtil::getPassedValue('scale'  , 100, 'GETPOST');
    $scale   = ((int)$scale > 0) ? (int)$scale : 100;
    
    $width   = FormUtil::getPassedValue('width'  , 600, 'GETPOST');
    $width   = ((int)$width > 0 && (int)$width < 1200 ) ? (int)$width : 600;
    
    $height  = FormUtil::getPassedValue('height'  , 400, 'GETPOST');
    $height  = ((int)$height > 0 && (int)$height < 1200 ) ? (int)$height : 400;

    $color_default = "004080";
    $color_in     = FormUtil::getPassedValue('color'  , $color_default, 'GETPOST');
    if (!$color = Hex2RGB($color_in)) {
         $color = Hex2RGB($color_default);
    }

    $graph  = FormUtil::getPassedValue('graph'  , "bar", 'GETPOST');
    $graph  = ($graph == "line" ) ? "line" : "bar"; 

    $showvalues = FormUtil::getPassedValue('values'  , true, 'GETPOST');
    $showvalues = ( $showvalues && $showvalues!='no' ) ? true : false;

    $background = FormUtil::getPassedValue('background'  , "Time.png", 'GETPOST');
    $imagefile = "modules/TNGz/pnimages/". $background;  // Need to check the validity of this later
    if (!file_exists($imagefile)) {
        $background="none";
    }

    // See if already in the cache
    $cachefile    = sprintf("histogram_%s_%s_%s_%s_%s_%s_%s_%s.png",$step,$scale,$width,$height,$color_in,$graph,$showvalues,$background);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache',     array( 'item'=> $cachefile ));
    if ($cacheresults) {
        header ("Content-type: image/png");
        echo $cacheresults;
        return true;
    }

    // General variables
    $data      = array(); // holds the results of query
    $values    = array(); // holds final year information to be graphed
    $year_min  = 9999;    // earliest year (start high and work lower)
    $year_max  = 0000;    // latest year (start low and work higher)
    $count_max = 0000;    // greatest number of events


    if (!$TNG_conn = pnModAPIFunc('TNGz','user','DBconnect') ) {
       return false; // can't get to the data
    }
    
    if (!$TNG = pnModAPIFunc('TNGz','user','TNGconfig') ) {
       return false; // can't get to the data
    }

    $query = "select count(A.year) as yearcount, year from (
                (SELECT YEAR(birthdatetr)    as year FROM ".$TNG['people_table']."  WHERE (birthdatetr != '0000-00-00') )
                 UNION ALL
                (SELECT YEAR(deathdatetr)    as year FROM ".$TNG['people_table']."  WHERE (deathdatetr != '0000-00-00') )
                 UNION ALL
                (SELECT YEAR(burialdatetr)   as year FROM ".$TNG['people_table']."  WHERE (burialdatetr != '0000-00-00') )
                 UNION ALL
                (SELECT YEAR(altbirthdatetr) as year FROM ".$TNG['people_table']."  WHERE (altbirthdatetr != '0000-00-00') )
                 UNION ALL
                (SELECT YEAR(baptdatetr)     as year FROM ".$TNG['people_table']."  WHERE (baptdatetr != '0000-00-00') )
                 UNION ALL
                (SELECT YEAR(eventdatetr)    as year FROM ".$TNG['events_table']."  WHERE (eventdatetr != '0000-00-00') )
                 UNION ALL
                (SELECT YEAR(marrdatetr)     as year FROM ".$TNG['families_table']." WHERE (marrdatetr != '0000-00-00') )
               ) as A 
                 GROUP BY year
                 ORDER BY year " ;
                 
    if (!$result = $TNG_conn->Execute($query)  ) {
        return false;
    }

    // Get the raw data
    for (; !$result->EOF; $result->MoveNext()) {
        $row = $result->fields;
        $data[ $row['year'] ] = $row['yearcount'];
        $year_min = ($row['year'] < $year_min)? $row['year'] : $year_min;
        $year_max = ($row['year'] > $year_max)? $row['year'] : $year_max;
    }
    $result->Close();

    // Now lump into the right buckets for display
    for ( $i=$year_min; $i<=$year_max; $i++) {
        $group = $i - ( $i % $step);
        $values[$group] += (isset($data[$i])) ? $data[$i] : 0;
    }

    $margins     = 40;  // > 35
    $border      = 0;
    $pen_legend  = 3;
    $pen_value   = 0;
 
    // Calculate the usable size of the graph
    $graph_width      = $width  - $margins * 2;
    $graph_height     = $height - $margins * 2; 
    $total_bars       = count($values);
    $bar_width        = ($graph_width / ($total_bars + 1)) - 5;
    $bar_width        = ($bar_width > 0)? $bar_width : 5;
    $gap              = ($graph_width- ($total_bars * $bar_width) ) / ($total_bars +1);
    $max_value        = max($values);
    $horizontal_lines = ceil($max_value / $scale );  // Number of horizonal lines (scale)
    $ratio            = $graph_height/ ($horizontal_lines * $scale); // scaling factor for each vaule to fit on graph
    $horizontal_gap   = ceil($graph_height/($horizontal_lines));


    // Create the image
    $have_background = true; // Start off assuming we do, then change if we know we don't
    if (!file_exists($imagefile)) {
        list($width_file, $height_file, $type_file) = array(0,0,0);
        $have_background = false;
    } else {
        $file_info = getimagesize($imagefile);
        list($width_file, $height_file, $type_file) = $file_info;
    }

    // Get the image
    switch ( $type_file ) {
        case IMAGETYPE_GIF:  $image = imagecreatefromgif($imagefile); break;
        case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($imagefile); break;
        case IMAGETYPE_PNG:  $image = imagecreatefrompng($imagefile); break;
        default:             $have_background = false;
    }

    // Create a transparent image to start working on
    $img = imagecreatetruecolor( $width, $height );  // new image
    imagealphablending($img, false); // for now, turn off transparency blending
    $transparency = imagecolorallocatealpha($img, 0, 0, 0, 127); //Create a new transparent color for image
    imagefill($img, 0, 0, $transparency); // fill background with the new color
    imagesavealpha($img, true); // turn on transparency blending

    if ($have_background) {
        if ( ($type_file == IMAGETYPE_GIF) || ($type_file == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);
             if ($transparency >= 0) { // specific transparent color
                $transparent_color = imagecolorsforindex($image, $trnprt_indx); //Get image's transparent color's RGB values
                $transparency      = imagecolorallocate($img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']); // Make same in the new image
                imagefill($img, 0, 0, $transparency); // Fill the background of the new image with the background color
                imagecolortransparent($img, $transparency); //Set the background color for new image to transparent
            }
        }
        imagecopyresampled($img, $image, $margins, $margins, 0, 0, $graph_width, $graph_height, $width_file, $height_file);
    }

    // define the colors to use
    $bar_color        = imagecolorallocate($img,  $color['r'], $color['g'], $color['b']);
    $line_color       = imagecolorallocate($img,220,220,220);

    //$background_color = imagecolorallocate($img,240,240,255);
    //$border_color     = imagecolorallocate($img,240,240,255); // 200, 200, 200

    // Create the Boarder
    //imagefilledrectangle($img,$border,$border,$width-$border,$height-$border,$border_color);

    // Create the Margin
    //imagefilledrectangle($img,$margins,$margins,$width-$margins,$height-$margins,$background_color);

    // Draw the horizontal lines and label them
    for($i=0;$i<=$horizontal_lines;$i++){
        $y=$height - $margins - $horizontal_gap * $i ;
        imageline($img, $margins, $y, $width-$margins, $y, $line_color);        // the line
        imagestring($img, $pen_legend, 5 + $border, $y - 5 , $i * $scale, $bar_color); // the label
    }

    // Now draw the bars or the lines
    reset($values);
    $last_x = $margins + $gap;
    $last_y = $margins +$graph_height;
    for($i=0;$i< $total_bars; $i++){ 
        list($key,$value)=each($values);  // Get key and value for current grouping
        $x1= $margins + $gap + $i * ($gap+$bar_width) ;
        $x2= $x1 + $bar_width;
        $x3= $x1 + ($bar_width/2);
        $y1=$margins +$graph_height- intval($value * $ratio) ;
        $y2=$height-$margins;
        if ($showvalues) {
            imagestring($img, $pen_value, $x1 + 3, $y1 - 10, $value ,$bar_color);             // The value
        }
        imagestringup($img, $pen_legend, $x1+($bar_width/2)-5,$margins + $graph_height + 35,$key,$bar_color); // the year group label
        if ($graph == "bar") {
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $bar_color); // the bar
        } else {
            imageline($img, $last_x, $last_y, $x3, $y1, $bar_color);   // the line
        }
        $last_x = $x3;
        $last_y = $y1;
    }

    // Save the map image using a PNG format since it gives better final image quality
    ob_start();
    imagepng($img);
    $saved_image = ob_get_contents();
    ob_end_clean();
    imagedestroy($img);
    imagedestroy($image);
    
    // Now Display it
    header ("Content-type: image/png");
    echo $saved_image;

    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $saved_image) );
    return true;

}

function Hex2RGB($hex)
{
    $hex = trim($hex);
    if(!eregi("^[0-9ABCDEFabcdef\#]+$", $hex)) {
        return false;
    }
    $hex = ereg_replace("#", "", $hex); // take off leading #
    $color = array();

    if(strlen($hex) == 3) {
        $color[0] = $color['r'] = $color['red']   = hexdec(substr($hex, 0, 1));
        $color[1] = $color['g'] = $color['green'] = hexdec(substr($hex, 1, 1));
        $color[2] = $color['b'] = $color['blue']  = hexdec(substr($hex, 2, 1));
    } else if(strlen($hex) == 6) {
        $color[0] = $color['r'] = $color['red']   = hexdec(substr($hex, 0, 2));
        $color[1] = $color['g'] = $color['green'] = hexdec(substr($hex, 2, 2));
        $color[2] = $color['b'] = $color['blue']  = hexdec(substr($hex, 4, 2));
    } else {
        return false;
    }
    return $color;
}
 
function RGB2Hex($r, $g, $b)
{
    $hex = "#";
    $hex.= dechex($r);
    $hex.= dechex($g);
    $hex.= dechex($b);
    return $hex;
}
