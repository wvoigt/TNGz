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
 * TNGz places
 * Display the top places in various formats
 * @param $params['top']  number of places to show
 * @param $params['type'] type of output - list or table
 * @param $params['sort'] order of listing - alpha, rank
 * @param $params['cols'] number of columns (table only)
 * @param $params['links']  no or yes to add links to place pages
 * @return string containing HTML formated places
 */
function smarty_function_places($params, &$smarty)
{
    // Get parameters
    $top = $params['top'];  
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 50;  // Get valid value or set default

    $validtypes = array('table', 'list');  // first in list is the default
    $type = (in_array($params['type'], $validtypes))? $params['type'] : $validtypes[0];
    
    $validsorts = array('rank', 'alpha');  // first in list is the default
    $sort = (in_array($params['sort'], $validsorts))? $params['sort'] : $validsorts[0];

    $cols = $params['cols'];
    $cols  = (is_numeric($cols) && $cols > 0)? intval($cols) : 2;  // Get valid value or set default

    $validlinks = array('no', 'yes');  // first in list is the default
    $links = (in_array($params['links'], $validlinks))? $params['links'] : $validlinks[0];
    
    // Get the Places
    $Places =  pnModAPIFunc('TNGz','user','GetPlaces', array('top'=> $top, 'sort' => $sort));

    if ($type == 'list'){
        $list = ($sort == 'alpha') ? "ul" : "ol";
        $output = "<$list class=\"place-list\">";
        foreach($Places as $place){
            $output .= "<li>";
            $output .= $place['name'] . " " . $place['link'];
            $output .= "</li>";
        }
        $output .= "</$list>";
    }

    if ($type == 'table'){
        $total    = count($Places);
        $rows     = floor($total / $cols) + 1;
        $leftover = $total % $cols;
        $rows     = ($leftover==0)? $rows-1 : $rows;

        // Get items in table order
        //First pad the array of items to fill out the table completely
        for($i=$leftover; $i<$cols && $i>0 ; $i++){
                $Places[] = null;
        }
        // Now fill out the table so it reads along the longest row or column
        $row   = 0;
        $col   = 0;        
        if ($rows >= $cols) {  // Table:  top to bottom, left to right
            foreach($Places as $place){
                $table[$row][$col] = $place;
                $row++;
                if ($row >= $rows) {
                    $row = 0;
                    $col++;
                }
            }
        } else {                 // Table : left to right, top to bottom
            foreach($Places as $place){
                $table[$row][$col] = $place;
                $col++;
                if ($col >= $cols) {
                    $col = 0;
                    $row++;
                }
            }
        }

        // Now write out the table
        $output = "<table class=\"places-table\">";
        
        for($row=0; $row<$rows; $row++){
            $output .= "<tr>";
            for($col=0; $col<$cols; $col++){
                 $output .= "<td>";
                 $place = $table[$row][$col];
                 if ($place != null) {
                     if ($sort == 'rank'){
                         $output .= $place['rank'] . ". ";
                     }
                     $output .= $place['name'] . " " . $place['link'];
                 }
                 $output .= "</td>";
            }
            $output .= "</tr>";
        }
        $output .= "</table>";
    }
    if ($links == 'yes'){
        $output .= "<form style=\"margin:0px\" action=\"index.php\" method=\"post\">";
        $output .= "<input type=\"hidden\" name=\"module\" value=\"TNGz\" />";
        $output .= "<input type=\"hidden\" name=\"show\" value=\"places100\" />";
        $output .= _TNGZ_FORM_SHOWTOP;
        $output .= " <input type=\"text\" name=\"topnum\" value=\"100\" size=\"3\" maxlength=\"3\" /> ";
        $output .= _TNGZ_FORM_ORDERBYOCC;
        //$output .= "<input type=\"hidden\" name=\"tree\" value=\"\" />";
        $output .= " <input type=\"submit\" value=\"". _TNGZ_FORM_GO ."\" />";
        $output .= "</form>";
        $output .= "<br />";
        $output .= "<a href=\"". pnModURL('TNGz', 'user', 'main', array( 'show' => 'places-all')) . "\">" . _TNGZ_PLACES_LINK_PLACES_ALL  . "</a>";
        $output .= "<br />";
        $output .= "<a href=\"". pnModURL('TNGz', 'user', 'main', array( 'show' => 'places'    )) . "\">" . _TNGZ_PLACES_LINK_PLACES . "</a>";
    }
    return $output;
}