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
 * TNGz placemap
 * Display the geocoded locations on a googlemap with sidebar and legend
 * @param $params['width']  width of the map in px
 * @param $params['height'] height of the map in px
 * @param $params['cluster'] use cluster markers ( no=default, yes)
 * @param $params['zoom']  initial googlemap zoom level.  [0..19, default=1]
 * @param $params['lat']   initial latitude   [-180 .. 180, default=0]
 * @param $params['lng']   initial longtitued [-180 .. 180, default=0]
 * @return string containing HTML with table for googlemap
 */
function smarty_function_placemap($params, &$smarty)
{
    // Get parameters
    $width = $params['width'];  
    $width  = (is_numeric($width) && $width > 0)? intval($width) : 600;  // Get valid value or set default

    $height = $params['height'];  
    $height  = (is_numeric($height) && $height > 0)? intval($height) : 450;  // Get valid value or set default

    $validcluster = array("no", "yes");  // first in list is the default
    $cluster = (in_array($params['cluster'], $validcluster))? $params['cluster'] : $validcluster[0];
    $usecluster = ($cluster == "yes") ? "true" : "false";

    $zoom  = (is_numeric($params['zoom'])) ? intval($params['zoom']) : 1 ;  // 1 is the default
    $zoom  = ( $zoom <  0) ?  0 : $zoom ; // check min
    $zoom  = ( $zoom > 19) ? 19 : $zoom ; // check max
    
    $lat   = (is_numeric($params['lat'])) ? $params['lat'] : 0 ;  // 0 is default
    $lat   = ($lat >  180 ) ?  180 : $lat ; // check max
    $lat   = ($lat < -180 ) ? -180 : $lat ; // check min

    $lng   = (is_numeric($params['lng'])) ? $params['lng'] : 0 ;  // 0 is default
    $lng   = ($lng >  180 ) ?  180 : $lng ;   // check max
    $lng   = ($lng < -180 ) ? -180 : $lng ;   // check min

    $lang = pnUserGetLang(); // get language used in Zikula

    // Check to be sure we can get to the TNG information
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');
    if (file_exists($TNG['configfile']) ){
        include($TNG['configfile']);
        include($TNG['configpath'] . 'mapconfig.php');
    } else {
        return ""._PEOPLEDBFERROR."";
    }

    $text = pnModAPIFunc('TNGz','user','GetTNGtext');

    // Make sure the javascript files are loaded
    if( $map[key] ){
        PageUtil::AddVar('javascript', "http://maps.google.com/maps?file=api&v=2$text[glang]&key=$map[key]");
        PageUtil::AddVar('javascript', 'modules/TNGz/pnjavascript/placemap.js');
        PageUtil::AddVar('javascript', 'modules/TNGz/pnjavascript/placemap-ClusterMarker.js');
        PageUtil::AddVar('javascript', 'modules/TNGz/pnjavascript/placemap-mapiconmaker.js');
    } else {
        return "ERROR: ". $text['mapkey'];
    }
    
    // See if already in the cache
    $cachefile    = sprintf("placemap_%s_%s_%s_%s_%s_%s_%s.html",$lang,$width,$height,$cluster,$zoom,$lat,$lng);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache', array( 'item'=> $cachefile ));
    if ($cacheresults) {
        return $cacheresults;
    }

    // Constants
    $id_sidebar           = "side_bar";
    $id_sidebar_li_prefix = "placeID_";
    $id_placelevel_prefix = "placelevelID_";
    $placesearch_url      = DataUtil::formatForDisplay(pnModURL('TNGz','user','main', array('show'=>'placesearch')) . "&");
    $cmspath              = $TNG['directory'].'/';
       
    $output .= "<table border=\"1\">\n";
    $output .= "<tr>\n";
    $output .= "<td>\n";
    $output .= "<!-- =========== output map  ============================= -->\n";
    $output .= "<div id=\"map\" style=\"width:'".$width."'px; height:'".$height."px';\"></div>\n";
    $output .= "</td>\n";
    $output .= "<td width=\"225\" valign=\"top\" style=\"text-decoration: underline; color: #4444ff;\">\n";
    $output .= "<!-- =========== side_bar with scroll bar ================= -->\n";
    $output .= "<div id=\"".$id_sidebar."\"  style=\"overflow:auto; height:'".$height."px';\"></div>\n";
    $output .= "<!-- ===================================================== -->\n";
    $output .= "</td>\n";
    $output .= "</tr>\n";
    $output .= "<tr>\n";
    $output .= "<!-- =========== legend across the bottom================= -->\n";
    $output .= "<td colspan=\"2\">\n";
    $output .= "<form name=\"legend\" action=\"\">\n";
    $output .= "<table cellpadding=\"10%\"><tr>\n";
    $output .= "<td align=\"center\">\n";
    $output .=  "<input type=\"checkbox\" name=\"LegendPlaceLevelList\" id=\"".$id_placelevel_prefix."cluster"."\" onclick=\"toggleClustering()\" ";
    $output .=  ($usecluster === "true") ? " checked=\"checked\"" : "";
    $output .=  " />" . "<br />". "Clusters"."\n";
    $output .=  "</td>\n";
    $output .=  "<td align=\"center\">\n";
    $output .=  "<input type=\"checkbox\" name=\"LegendPlaceLevelList\" id=\"".$id_placelevel_prefix."all"."\" onclick=\"showhide()\" checked=\"checked\" />" . "<br />". $text['all']."\n";
    $output .=  "</td>\n";
    foreach (array(1,2,3,4,5,6,0) as $i) {
        $output .=  "<td align=\"center\">\n";
        $output .=  "<img src=\"$cmspath" . "googlemaps/" . ${"pinplacelevel" .$i} . ".png\" alt=\"\" height=\"34\" width=\"20\" /><br /><input type=\"checkbox\" name=\"LegendPlaceLevelList\" id=\"".$id_placelevel_prefix.$i."\" onclick=\"showhide('".$i."')\" checked=\"checked\" />" . "<br />".$text["level$i"]."\n";
        $output .=  "</td>\n";                
    }
    $output .= "</tr></table>\n";
    $output .= "</form>\n";
    $output .= "</td>\n";
    $output .= "</tr>\n";
    $output .= "</table>\n";

    
    $output .= "\n";
    $output .= "<script type=\"text/javascript\">\n";
    $output .= "//<![CDATA[\n";

    $output .= "if (!GBrowserIsCompatible()) {\n";
    $output .= "alert(\"Sorry, the Google Maps API is not compatible with this browser\");\n";
    $output .= "} else {\n";
    $output .= "var placemap                  = {}; //Holds all the variables\n";   
    $output .= "placemap.cmspath              = '".$cmspath."'; \n";
    $output .= "placemap.id_placelevel_prefix = '$id_placelevel_prefix';\n";
    $output .= "placemap.placesearch_url      = '$placesearch_url';\n";
    $output .= "placemap.id_sidebar           = '$id_sidebar';\n";
    $output .= "placemap.id_sidebar_li_prefix = '$id_sidebar_li_prefix';\n";
    $output .= "placemap.usecluster           =  $usecluster;\n";
    $output .= "placemap.center_zoom          =  $zoom;\n";
    $output .= "placemap.center_lat           =  $lat;\n";
    $output .= "placemap.center_lng           =  $lng;\n";
    $output .= "placemap.pinplacelevelfile    = [];\n";
    
    $validplacelevels = array(0,1,2,3,4,5,6);  // Valid place levels, first is default   
    foreach ($validplacelevels as $i) {
        $output .= "placemap.pinplacelevelfile[$i]=\"". ${"pinplacelevel" .$i} . ".png\";\n";
    }

    $output .= "InitializeMap();\n";

    // Get the data and setup all the marker points
    $TNG_conn = &ADONewConnection('mysql');
    $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
    $TNG_conn->SetFetchMode(ADODB_FETCH_ASSOC);
    
    $query = "SELECT gedcom, place, longitude, latitude, placelevel FROM `". $places_table . "` WHERE longitude <> '' AND latitude <> '' order by place";
    if (!$result = &$TNG_conn->Execute($query) ) {
        $TNG_conn->Close();
        return pnVarPrepHTMLDisplay("Failed TNG query");
    }
    if ($result->RecordCount() > 0) {
        for (; !$result->EOF; $result->MoveNext()) {
            $row = $result->fields;
            // Make sure placelevel is valid
            $placelevel = (in_array($row['placelevel'], $validplacelevels))? $row['placelevel'] : $validplacelevels[0];
            //add points to javascript
            $output .= "createMarker(\"".$row['latitude']."\",\"".$row['longitude']."\",\"".$row['place']."\",\"".$placelevel."\",\"".$row['gedcom']."\");\n";
        }
    } else {
        // no, print status message
        $output .= "alert('Sorry, No places found.');";
    }

    // Database cleanup
    $TNG_conn->Close();

    $output .= "}\n";
    $output .= "//]]>\n";
    $output .= "</script>\n";

    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );    
    return $output;
}