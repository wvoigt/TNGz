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
 * TNGz surnames
 * Gets the top surnames
 * @param $args['top']  number of surnames to give
 * @param $args['type'] type of output - cloud, list, table
 * @param $args['sort'] order of listing - alpha, rank
 * @param $args['cols'] number of columns (table only)
 * @return string containing HTML formated surnames
 */
function smarty_function_surnames($params, &$smarty)
{
    // Get parameters
    $top = $params['top'];  
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 50;  // Get valid value or set default

    $validtypes = array('list', 'cloud', 'table');  // first in list is the default
    $type = (in_array($params['type'], $validtypes))? $params['type'] : $validtypes[0];
    
    $validsorts = array('alpha', 'rank');  // first in list is the default
    $sort = (in_array($params['sort'], $validsorts))? $params['sort'] : $validsorts[0];

    $cols = $params['cols'];
    $cols  = (is_numeric($cols) && $cols > 0)? intval($cols) : 2;  // Get valid value or set default

    // Get the Surnames
    $Surnames =  pnModAPIFunc('TNGz','user','GetSurnames', array('top'=> $top));
    
    if ($sort == 'alpha'){
        $names = $Surnames['alpha'];
    } else {
        $names = $Surnames['rank'];
    }

    if ($type == 'cloud') {
        $output = "<div class='surnames-cloud'>";
        foreach($names as $name){
            $output .= "<span class='surnames-cloud size" . $name['class'] . "'>";
            $output .= "<a class='surnames-cloud size" . $name['class'] . "' ";
            $output .= "href=\"". pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND')) . "\"";
            $output .= ">" . $name['surname'] . "</a>";
            $output .= "</span> ";
        }
        $output .= "</div>";
    }
    if ($type == 'list'){
        $list = ($sort == 'alpha') ? "ul" : "ol";
        $output = "<$list class=\"surnames-list\">";
        foreach($names as $name){
            $output .= "<li>";
            $output .= "<a ";
            $output .= "href=\"". pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND')) . "\"";
            $output .= ">" . $name['surname']  . "</a>" . " (". $name['count']. ")";
            $output .= "</li>";
        }
        $output .= "</$list>";
    }
    
    if ($type == 'table'){
        $total    = count($names);
        $rows     = floor($total / $cols) + 1;
        $leftover = $total % $cols;
        $rows     = ($leftover==0)? $rows-1 : $rows;

        // Get Surnames in table order
        //First pad the array of names to fill out the table completely
        for($i=$leftover; $i<$cols && $i>0 ; $i++){
                $names[] = null;
        }
        // Now fill out the table so it reads along the longest row or column
        $row   = 0;
        $col   = 0;
        if ($rows >= $cols) {  // Table:  top to bottom, left to right
            foreach($names as $name){
                $table[$row][$col] = $name;
                $row++;
                if ($row >= $rows) {
                    $row = 0;
                    $col++;
                }
            }
        } else {                 // Table : left to right, top to bottom
            foreach($names as $name){
                $table[$row][$col] = $name;
                $col++;
                if ($col >= $cols) {
                    $col = 0;
                    $row++;
                }
            }
        }

        // Now write out the table
        $output = "<table class=\"surnames-table\">";
        for($row=0; $row<$rows; $row++){
            $output .= "<tr>";
            for($col=0; $col<$cols; $col++){
                 $output .= "<td>";
                 $name = $table[$row][$col];
                 if ($name != null) {
                     if ($sort == 'rank'){
                         $output .= $name['rank'] . ". ";
                     }
                     $output .= "<a ";
                     $output .= "href=\"". pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND')) . "\"";
                     $output .= ">" . $name['surname'] . "</a>" . " (". $name['count']. ")" ;
                 }
                 $output .= "</td>";
            }
            $output .= "</tr>";
        }
        $output .= "</table>";
    }
    return $output;
}