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
 *
 * @author sven schomacker
 * @version $Id$
 *
 * @param        bool        $cloud       Show surnames as cloud
 * @param        bool        $linked      Show surnames with link
 * @param        string      $maxnames    max surnames
 * @return       string      the results of the module function
 *
 */

function smarty_function_surnameslist($params, &$smarty)
{
    extract($params);
    unset($params);

    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return;
    }

	if (!isset($cloud)) {
		$cloud = false;
	}
	if (!isset($linked)) {
		$linked = true;
	}
        if (!isset($maxnames)) {
	        $maxnames = '10';
	}

        $top  = (is_numeric($top) && $top > 0)? intval($top) : $maxnames;

        // First get all unique surnames
        $query = "SELECT ucase( $binary TRIM(CONCAT_WS(' ',lnprefix,lastname) ) ) as surnameuc, TRIM(CONCAT_WS(' ',lnprefix,lastname) ) as surname, count( ucase($binary lastname ) ) as count FROM $people_table WHERE lastname<>'' GROUP BY surname ORDER by count DESC ";
        $saved_fetch_mode = &$TNG_conn->SetFetchMode(ADODB_FETCH_ASSOC);
        if (!$result = &$TNG_conn->Execute($query) ) {
            return pnVarPrepHTMLDisplay("Failed the TNG query");
        }

        $SurnameCount = $result->RecordCount();
        $name         = $result->fields;       // Look at first record, since already sorted by Surname Count
        $SurnameMax   = $name['count'];        // First record should have the most
        $SurnameRank  = array();
        for ($rank=1; !$result->EOF && $rank<=$top; $result->MoveNext(), $rank++) {
            $name = $result->fields;
            // removed urlencode in order to show names correctly (problem with german umlauts)
            //$name['surname']   = urlencode($name['surname']);
            $name['surname']   = $name['surname'];
            $name['surnameuc'] = urlencode($name['surnameuc']);            
            $name['rank']      = $rank;
            // Now assign a class to each surname based upon relative number to most used surname
            $percent = 100 * $name['count'] / $SurnameMax;
            if ($percent > 98) {
                $class = 1;
            } else if ($percent > 70) {
                $class = 2;
            } else if ($percent > 50) {
                $class = 3;
            } else if ($percent > 30) {
                $class = 4;
            } else if ($percent > 25) {
                $class = 5;
            } else if ($percent > 20) {
                $class = 6;
            } else if ($percent > 15) {
                $class = 7;
            } else if ($percent > 10) {
                $class = 8;
            } else if ($percent > 5 ) {
                $class = 9;
            } else {
                $class = 0;
            }
            $name['class'] = $class;
            
            $SurnameRank[]  = $name;
        }
        
        // Now get a array $SurnameAlpha sorted on the upper case surname
        $SurnameAlpha = $SurnameRank;
        foreach ($SurnameAlpha as $key => $row) {
            $surname[$key]  = $row['surnameuc'];
        }
        array_multisort($surname, SORT_ASC, $SurnameAlpha);

        // Clean up
        $saved_fetch_mode= &$TNG_conn->SetFetchMode($saved_fetch_mode);
        $TNG_conn->Close();
        
        $output = '';
       
        if ($cloud) {
           // generate code for cloud
           $output .= '<div class="wordcloud">';
           foreach ($SurnameAlpha as $element) {
//              $output .= '<span class="wordcloud size<!--[$SurnameAlpha[element].class]-->"><a class="wordcloud size<!--[$SurnameAlpha[element].class]-->" href="<!--[pnmodurl modname=TNGz show=search mylastname=$SurnameAlpha[element].surnameuc mybool=AND  ]-->"><!--[$SurnameAlpha[element].surname]--></a></span>';
              print_r ($element);
           }
           $output .= '</div>';
        } else {
        // generate code for names table
           $numCols = 5;
           $output .= '<div>';
           $output .= '<table>';
           $output .= '<tr>';
           foreach ($SurnameRank as $element) {
              $col = 0;
              if ($col eq $numCols) {
                 $output .= '</tr><tr>';
                 $col = 0;
              }
//              $output .= '<td><!--[$SurnameRank[element].rank|string_format:"%2s"]-->. <a href="<!--[pnmodurl modname=TNGz show=search mylastname=$SurnameRank[element].surnameuc mybool=AND  ]-->"><!--[$SurnameRank[element].surname]--></a> (<!--[$SurnameRank[element].count]-->)</td>';
              print_r ($element);
              $col = $col++1;
           }
           $remainder = $numCols-$col;
           foreach ($SurnameRank as $element) {
              $output .= '<td>&nbsp;</td>';
           }
           $output .= '</tr>';
           $output .= '</table>';
        }

        // return code for output
        return $output;

}