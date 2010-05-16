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
 * TNGz statistics
 * Gets the top surnames
 * @param $params['people']    show number of people in database
 * @param $params['family']    show number of families in database
 * @param $params['surnames']  show number unique surnames
 * @param $params['sex']       show gender information (must have people)
 * @param $params['living']    show number of living individuals in database
 * @param $params['title']  if set, adds the text at the top
 * @return string containing HTML formated statistics in a table
 */
function smarty_function_statistics($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    // Get parameters
    $valid_no  = array( 'no',  'n', '0');  // first in list is the default
    $valid_yes = array( 'yes', 'y', '1');  // first in list is the default
    
    // Clean up parameters
    $params['people']   = (isset($params['people']))  ? $params['people']  : "";
    $params['family']   = (isset($params['family']))  ? $params['family']  : "";
    $params['surnames'] = (isset($params['surnames']))? $params['surnames']: "";
    $params['sex']      = (isset($params['sex']))     ? $params['sex']     : "";
    $params['living']   = (isset($params['living']))  ? $params['living']  : "";
    $params['places']   = (isset($params['places']))  ? $params['places']  : "";
    $params['geocode']  = (isset($params['geocode'])) ? $params['geocode'] : "";
    $params['title']    = (isset($params['title']))   ? $params['title']   : "";
    $params['updated']  = (isset($params['updated'])) ? $params['updated'] : "";
    
    // default yes unless a valid no is given
    $people   = (in_array($params['people'],   $valid_no))? false : true ;
    $family   = (in_array($params['family'],   $valid_no))? false : true ;
    $surnames = (in_array($params['surnames'], $valid_no))? false : true ;
    $sex      = (in_array($params['sex'],      $valid_no))? false : true ;
    $living   = (in_array($params['living'],   $valid_no))? false : true ;
    $places   = (in_array($params['places'],   $valid_no))? false : true ;
    $geocode  = (in_array($params['geocode'],  $valid_no))? false : true ;
    $updated  = (in_array($params['updated'],  $valid_no))? false : true ;
    
    // default no unless a valid yes is given
    //$people   = (in_array($params['people'],   $valid_yes))? true : false ;
    //$family   = (in_array($params['family'],   $valid_yes))? true : false ;
    //$surnames = (in_array($params['surnames'], $valid_yes))? true : false ;
    //$sex      = (in_array($params['sex'],      $valid_yes))? true : false ;
    //$living   = (in_array($params['living'],   $valid_yes))? true : false ;
    //$places   = (in_array($params['places'],   $valid_yes))? true : false ;
    //$geocode  = (in_array($params['geocode'],  $valid_yes))? true : false ;

    $params['title'] = (empty($params['title'])) ? "" : DataUtil::formatForDisplay($params['title']);

    $lang = ZLanguage::getLanguageCode(); // get language used in Zikula

    // See if already in the cache
    $title_part = ($params['title'])? md5($params['title']) : "x";
    $cachefile    = sprintf("statistics_%s_%s_%s_%s_%s_%s_%s_%s_%s.html",$lang,$people,$family,$surnames,$sex,$living,$places,$geocode,$title_part);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache', array( 'item'=> $cachefile ));
    if ($cacheresults) {
        return $cacheresults;
    }

    // Check to be sure we can get to the TNG information
    $TNG = pnModAPIFunc('TNGz','user','TNGconfig'); 
    if (!pnModAPIFunc('TNGz','user','TNGquery', array('connect'=>true) ) ) {
        return __('Error in accessing the TNG tables.', $dom);
    } 

    $text = pnModAPIFunc('TNGz','user','GetTNGtext', array('textpart' => 'stats'));
    $text = pnModAPIFunc('TNGz','user','GetTNGtext', array('textpart' => 'places'));
    
    $output  = ( $params['title'] ) ? "<h3 style=\"statistics\" >" . $params['title'] . "</h3>\n" : "";
    $output .= "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" >";
    $output .= "<tr>";
    $output .= "<td class=\"statistics-cell\"><span class=\"statistics-plugin-header\">" . $text['description'] . "</span></td>";
    $output .= "<td class=\"statistics-cell\"><span class=\"statistics-plugin-header\">" . $text['quantity'] .    "</span></td>";
    $output .= "</tr>";

    if ($people) {
        $query = "SELECT count(id) as pcount FROM ".$TNG['people_table']." $wherestr";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $totalpeople = $row['pcount'];
        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totindividuals']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$totalpeople &nbsp;</span></td></tr>\n";
    }

    if ($family) {
        $query = "SELECT count(id) as fcount FROM ".$TNG['families_table']." $wherestr";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $totalfamilies = $row['fcount'];
        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totfamilies']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$totalfamilies &nbsp;</span></td></tr>\n";
    }

    if ($living & $people) {
        $query = "SELECT count(id) as pcount FROM ".$TNG['people_table']." WHERE living != 0 $wherestr2";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $numliving = $row['pcount'];
        $percentliving  = $totalpeople ? round(100 * $numliving / $totalpeople, 2) : 0;
        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totliving']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$numliving ($percentliving%) &nbsp;</span></td></tr>\n";

    }
    
    if ($surnames) {
        $query = "SELECT ucase( lastname) as lastname, count( ucase( lastname ) ) as lncount 
                  FROM ".$TNG['people_table']." 
                  GROUP BY lastname ORDER by lastname";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $uniquesurnames = count($result);
        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totuniquesn']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$uniquesurnames&nbsp;</span></td></tr>\n";      
    }

    if ($sex && $people) {
        $query = "SELECT count(id) as pcount FROM ".$TNG['people_table']." WHERE sex = 'M' $wherestr2";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $males = $row['pcount'];

        $query = "SELECT count(id) as pcount FROM ".$TNG['people_table']." WHERE sex = 'F' $wherestr2";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $females = $row['pcount'];

        $query = "SELECT count(id) as pcount FROM ".$TNG['people_table']." WHERE sex != 'F' AND sex != 'M' $wherestr2";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $unknownsex = $row['pcount'];
        
        $percentmales      = $totalpeople ? round(100 * $males / $totalpeople, 2) : 0;
        $percentfemales    = $totalpeople ? round(100 * $females / $totalpeople, 2) : 0;
        $percentunknownsex = $totalpeople ? round(100 * $unknownsex / $totalpeople, 2) : 0;

        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totmales']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$males ($percentmales%) &nbsp;</span></td></tr>\n";

        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totfemales']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$females ($percentfemales%) &nbsp;</span></td></tr>\n";

        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['totunknown']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$unknownsex ($percentunknownsex%) &nbsp;</span></td></tr>\n";
    }

    if ($places) {
        $query = "SELECT count(id) as pcount FROM ".$TNG['places_table']."";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $numplaces = $row['pcount'];

        $query = "select sum(A.used) as TheTotal from (
                   (SELECT count(birthplace) as used, birthplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(deathplace) as used, deathplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(altbirthplace) as used, altbirthplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(baptplace) as used, baptplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(burialplace) as used, burialplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(eventplace) as used, eventplace as place FROM ".$TNG['events_table']." GROUP BY place )
                     UNION ALL
                   (SELECT count(marrplace) as used, marrplace as place FROM ".$TNG['families_table']." GROUP BY place )
                  ) as A, ".$TNG['places_table']." as PlaceTable
                 WHERE ( A.place <> \"\" ) AND (A.place = PlaceTable.place)";

        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $refplaces = $row['TheTotal'];
    }

    if ($geocode && $places) {
        $query = "SELECT count(id) as pcount FROM ".$TNG['places_table']." WHERE latitude <> \"\" and longitude <> \"\"";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $geocoded = $row['pcount'];
        
        $query = "select sum(A.used) as TheTotal from (
                   (SELECT count(birthplace) as used, birthplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(deathplace) as used, deathplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(altbirthplace) as used, altbirthplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(baptplace) as used, baptplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(burialplace) as used, burialplace as place FROM ".$TNG['people_table']." GROUP BY place)
                     UNION ALL
                   (SELECT count(eventplace) as used, eventplace as place FROM ".$TNG['events_table']." GROUP BY place )
                     UNION ALL
                   (SELECT count(marrplace) as used, marrplace as place FROM ".$TNG['families_table']." GROUP BY place )
                  ) as A, ".$TNG['places_table']." as PlaceTable
                 WHERE ( A.place <> \"\" ) AND (A.place = PlaceTable.place)
                 AND ( PlaceTable.latitude <> \"\" and PlaceTable.longitude <> \"\")";
        if (false === ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) ) ) ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result[0];
        $refgeocoded = $row['TheTotal'];
        
        $percentgeocoded    = $numplaces ? round(100 * $geocoded    / $numplaces, 2) : 0;
        $percentrefgeocoded = $refplaces ? round(100 * $refgeocoded / $refplaces, 2) : 0;
        
    }
    if ($places) {
        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['total']." ".$text['places']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$numplaces &nbsp;</span></td></tr>\n";
        if ($geocode) {
            $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['total']." Geocode ".$text['places']."</span></td>\n";
            $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$geocoded ($percentgeocoded%) &nbsp;</span></td></tr>\n";
        }
        $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['total']." ".$text['gmapevent']." ".$text['places']."</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$refplaces &nbsp;</span></td></tr>\n";
        if ($geocode) {
            $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['total']." Geocode ".$text['gmapevent']." ".$text['places']."</span></td>\n";
            $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">$refgeocoded ($percentrefgeocoded%) &nbsp;</span></td></tr>\n";
        } 
    }


    if ($updated) {
        $time_zero = '0000-00-00 00:00:00';
        $TNG_updated = $time_zero; // Initialize, also a flag if stays at $time_zero

        // Set the tables we want to check
        $TNG_tables['people']   = $TNG['people_table'];
        $TNG_tables['family']   = $TNG['families_table'];
        $TNG_tables['children'] = $TNG['children_table'];
        $TNG_tables['places']   = $TNG['places_table'];
        $TNG_tables['events']   = $TNG['events_table'];
        /* Others that could be checked include:
          albums_table, album2entities_table, albumlinks_table, media_table, medialinks_table,
          mediatypes_table, address_table, languages_table, cemeteries_table, states_table,
          countries_table, sources_table, repositories_table, citations_table
        */

        // Now actually go find the last update time stamp on various TNG tables
        foreach($TNG_tables as $table){
            // now get the update time for the table
            $query = "SHOW TABLE STATUS LIKE '$table'";           
            if ($result = pnModAPIFunc('TNGz','user','TNGquery', array('query'=>$query) )  ) {
                if (count($result)>0) {
                    foreach ($result as $row) {
                        $table_updated  = $row['Update_time'];
                        $TNG_updated    =  ($table_updated > $TNG_updated) ? $table_updated : $TNG_updated;
                    }
                }
            }
        }

        if ( $TNG_updated != $time_zero ) { // only show if we actually have information
            $TNG_updated_timestamp = DateUtil::makeTimestamp($TNG_updated);
            $TNG_updated           = DateUtil::getDatetime($TNG_updated_timestamp, 'datetimebrief');
            $output .=  "<tr><td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-lable\">".$text['lastmodified']."</span></td>\n";
            $output .=  "<td valign=\"top\" class=\"statistics-cell\"><span class=\"statistics-plugin-data\">".$TNG_updated."&nbsp;</span></td></tr>\n";
        }
    }
    
    $output .=  "</table>";

    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );
    return $output;
}
