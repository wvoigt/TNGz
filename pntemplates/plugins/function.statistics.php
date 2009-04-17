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
 * @return string containing HTML formated statistics in a table
 */
function smarty_function_statistics($params, &$smarty)
{
    // Get parameters
    $valid_no  = array( 'no',  'n', '0');  // first in list is the default
    $valid_yes = array( 'yes', 'y', '1');  // first in list is the default
    
    // default yes unless a valid no is given
    $people   = (in_array($params['people'],   $valid_no))? false : true ;
    $family   = (in_array($params['family'],   $valid_no))? false : true ;
    $surnames = (in_array($params['surnames'], $valid_no))? false : true ;
    $sex      = (in_array($params['sex'],      $valid_no))? false : true ;
    $living   = (in_array($params['living'],   $valid_no))? false : true ;
    $places   = (in_array($params['places'],   $valid_no))? false : true ;
    $geocode  = (in_array($params['geocode'],  $valid_no))? false : true ;
    
    // default no unless a valid yes is given
    //$people   = (in_array($params['people'],   $valid_yes))? true : false ;
    //$family   = (in_array($params['family'],   $valid_yes))? true : false ;
    //$surnames = (in_array($params['surnames'], $valid_yes))? true : false ;
    //$sex      = (in_array($params['sex'],      $valid_yes))? true : false ;
    //$living   = (in_array($params['living'],   $valid_yes))? true : false ;
    //$places   = (in_array($params['places'],   $valid_yes))? true : false ;
    //$geocode  = (in_array($params['geocode'],  $valid_yes))? true : false ;

    // See if already in the cache
    $cachefile    = sprintf("statistics-%s-%s-%s-%s-%s-%s-%s.html",$people,$family,$surnames,$sex,$living,$places,$geocode);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache', array( 'item'=> $cachefile ));
    if ($cacheresults) {
        return $cacheresults;
    }

    // Check to be sure we can get to the TNG information
    $TNG = pnModAPIFunc('TNGz','user','GetTNGpaths');
    if (file_exists($TNG['configfile']) ){
        include($TNG['configfile']);
        $TNG_conn = &ADONewConnection('mysql');
        $TNG_conn->NConnect($database_host, $database_username, $database_password, $database_name);
        $TNG_conn->SetFetchMode(ADODB_FETCH_ASSOC);
    } else {
        return ""._PEOPLEDBFERROR."";
    }

    $text = pnModAPIFunc('TNGz','user','GetTNGtext', array('textpart' => 'stats'));
    $text = pnModAPIFunc('TNGz','user','GetTNGtext', array('textpart' => 'places'));
    
    $output  = "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" >";
	$output .= "<tr>";
	$output .= "<td class=\"fieldnameback\"><span class=\"fieldname\">&nbsp;<strong>" . $text[description] . "</strong>&nbsp;</span></td>";
	$output .= "<td class=\"fieldnameback\"><span class=\"fieldname\">&nbsp;<strong>" . $text[quantity] .    "</strong>&nbsp;</span></td>";
	$output .= "</tr>";

    if ($people) {
        $query = "SELECT count(id) as pcount FROM $people_table $wherestr";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $totalpeople = $row[pcount];
        $result->Close();
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totindividuals]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$totalpeople &nbsp;</span></td></tr>\n";

    }
    
    if ($family) {
        $query = "SELECT count(id) as fcount FROM $families_table $wherestr";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $totalfamilies = $row[fcount];
        $result->Close();
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totfamilies]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$totalfamilies &nbsp;</span></td></tr>\n";
    }

    if ($living & $people) {
        $query = "SELECT count(id) as pcount FROM $people_table WHERE living != 0 $wherestr2";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $numliving = $row[pcount];
        $result->Close();
        $percentliving  = $totalpeople ? round(100 * $numliving / $totalpeople, 2) : 0;
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totliving]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$numliving ($percentliving%) &nbsp;</span></td></tr>\n";

    }
    
    if ($surnames) {
        $query = "SELECT ucase( lastname) as lastname, count( ucase( lastname ) ) as lncount 
                  FROM $people_table 
                  GROUP BY lastname ORDER by lastname";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $uniquesurnames = $result->RecordCount();
        $result->Close();
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totuniquesn]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$uniquesurnames&nbsp;</span></td></tr>\n";      
    }

    if ($sex && $people) {
        $query = "SELECT count(id) as pcount FROM $people_table WHERE sex = 'M' $wherestr2";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $males = $row[pcount];
        $result->Close();

        $query = "SELECT count(id) as pcount FROM $people_table WHERE sex = 'F' $wherestr2";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $females = $row[pcount];
        $result->Close();

        $query = "SELECT count(id) as pcount FROM $people_table WHERE sex != 'F' AND sex != 'M' $wherestr2";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $unknownsex = $row[pcount];
        $result->Close();

        $percentmales      = $totalpeople ? round(100 * $males / $totalpeople, 2) : 0;
        $percentfemales    = $totalpeople ? round(100 * $females / $totalpeople, 2) : 0;
        $percentunknownsex = $totalpeople ? round(100 * $unknownsex / $totalpeople, 2) : 0;

        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totmales]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$males ($percentmales%) &nbsp;</span></td></tr>\n";

        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totfemales]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$females ($percentfemales%) &nbsp;</span></td></tr>\n";

        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totunknown]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$unknownsex ($percentunknownsex%) &nbsp;</span></td></tr>\n";

    }
    
    if ($places) {
        $query = "SELECT count(id) as pcount FROM $places_table";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $numplaces = $row[pcount];
        $result->Close();
        
        
        $query = "select sum(A.used) as TheTotal from (
                   (SELECT count(birthplace) as used, birthplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(deathplace) as used, deathplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(baptplace) as used, baptplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(burialplace) as used, burialplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(eventplace) as used, eventplace as place FROM $events_table GROUP BY place )
                     UNION ALL
                   (SELECT count(marrplace) as used, marrplace as place FROM $families_table GROUP BY place )
                  ) as A, $places_table as PlaceTable
                 WHERE ( A.place <> \"\" ) AND (A.place = PlaceTable.place)";

        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $refplaces = $row[TheTotal];
        $result->Close();


    }
    
    if ($geocode && $places) {
        $query = "SELECT count(id) as pcount FROM $places_table WHERE latitude <> \"\" and longitude <> \"\"";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $geocoded = $row[pcount];
        $result->Close();
        $query = "select sum(A.used) as TheTotal from (
                   (SELECT count(birthplace) as used, birthplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(deathplace) as used, deathplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(baptplace) as used, baptplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(burialplace) as used, burialplace as place FROM $people_table GROUP BY place)
                     UNION ALL
                   (SELECT count(eventplace) as used, eventplace as place FROM $events_table GROUP BY place )
                     UNION ALL
                   (SELECT count(marrplace) as used, marrplace as place FROM $families_table GROUP BY place )
                  ) as A, $places_table as PlaceTable
                 WHERE ( A.place <> \"\" ) AND (A.place = PlaceTable.place)
                 AND ( PlaceTable.latitude <> \"\" and PlaceTable.longitude <> \"\")";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $refgeocoded = $row[TheTotal];
        $result->Close();        
        
        $percentgeocoded    = $numplaces ? round(100 * $geocoded    / $numplaces, 2) : 0;
        $percentrefgeocoded = $refplaces ? round(100 * $refgeocoded / $refplaces, 2) : 0;
        
    }
    if ($places) {
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[total] $text[places]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$numplaces &nbsp;</span></td></tr>\n";
      if ($geocode) {
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[total] Geocode $text[places]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$geocoded ($percentgeocoded%) &nbsp;</span></td></tr>\n";
      }
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[total] $text[gmapevent] $text[places]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$refplaces &nbsp;</span></td></tr>\n";

      if ($geocode) {
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[total] Geocode $text[gmapevent] $text[places]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$refgeocoded ($percentrefgeocoded%) &nbsp;</span></td></tr>\n";
      } 
    }

    $output .=  "</table>";
    
    $TNG_conn->Close();

    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );
    return $output;
}

