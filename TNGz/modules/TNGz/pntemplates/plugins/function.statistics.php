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
    
    // note, all parameters default to yes unless a valid no is given
    $people   = (in_array($params['people'],   $valid_no))? false : true ;
    $family   = (in_array($params['family'],   $valid_no))? false : true ;
    $surnames = (in_array($params['surnames'], $valid_no))? false : true ;
    $sex      = (in_array($params['sex'],      $valid_no))? false : true ;
    $living   = (in_array($params['living'],   $valid_no))? false : true ;

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

    if ($sex) {
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

    if ($living) {
        $query = "SELECT count(id) as pcount FROM $people_table WHERE living != 0 $wherestr2";
        if (!$result = &$TNG_conn->Execute($query)  ) {
            return "$text[cannotexecutequery]: $query";
        }
        $row = $result->fields;
        $numliving = $row[pcount];
        $result->Close();
        $output .=  "<tr><td valign=\"top\" class=\"databack\"><span class=\"normal\">$text[totliving]</span></td>\n";
        $output .=  "<td valign=\"top\" class=\"databack\"><span class=\"normal\">$numliving &nbsp;</span></td></tr>\n";

    }

    $output .=  "</table>";
    
    $TNG_conn->Close();
    
    return $output;
}

