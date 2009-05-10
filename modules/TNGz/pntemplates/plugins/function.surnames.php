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
 * @param $params['top']  number of surnames to give
 * @param $params['type'] type of output - cloud, list, table
 * @param $params['sort'] order of listing - alpha, rank
 * @param $params['cols'] number of columns (table only)
 * @param $params['links']  no or yes to add links to surname pages
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

    $validlinks = array('yes', 'no');  // first in list is the default
    $links = (in_array($params['links'], $validlinks))? $params['links'] : $validlinks[0];
    $hidelinks = ($links == "yes") ? false : true;
    
    $validmenus = array('no', 'yes');  // first in list is the default
    $menu = (in_array($params['menu'], $validmenus))? $params['menu'] : $validmenus[0];

    $lang = pnUserGetLang(); // get language used in Zikula
    
    // See if already in the cache
    $cachefile    = sprintf("surnames_%s_%s_%s_%s_%s_%s_%s.html",$lang,$type,$sort,$top,$cols,$links,$menu);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache', array( 'item'=> $cachefile ));
    if ($cacheresults) {
        return $cacheresults;
    }

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
            $output .= ($hidelinks) ? "" : "<a class='surnames-cloud size" . $name['class'] . "' ";
            $output .= ($hidelinks) ? "" : "href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND'))) . "\">";
            $output .= $name['surname'];
            $output .= ($hidelinks) ? "" : "</a>";
            $output .= "</span> ";
        }
        $output .= "</div>";
    }
    if ($type == 'list'){
        $list = ($sort == 'alpha') ? "ul" : "ol";
        $output = "<$list class=\"surnames-list\">";
        foreach($names as $name){
            $output .= "<li>";
            $output .= ($hidelinks) ? "" : "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND'))) . "\">";
            $output .= $name['surname'];
            $output .= ($hidelinks) ? "" : "</a>";
            $output .= " (". $name['count']. ")";
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
                     $output .= ($hidelinks) ? "" : "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND'))) . "\">";
                     $output .= $name['surname'];
                     $output .= ($hidelinks) ? "" : "</a>";
                     $output .= " (". $name['count']. ")" ;
                 }
                 $output .= "</td>";
            }
            $output .= "</tr>";
        }
        $output .= "</table>";
    }
    if ($menu == 'yes'){
        $output .= "<div class=\"surnames-menu\">";
        $output .= "<form style=\"margin:0px\" action=\"index.php\" method=\"post\">";
        $output .= "<input type=\"hidden\" name=\"module\" value=\"TNGz\" />";
        $output .= "<input type=\"hidden\" name=\"show\" value=\"surnames100\" />";
        $output .= _TNGZ_FORM_SHOWTOP;
        $output .= " <input type=\"text\" name=\"topnum\" value=\"100\" size=\"3\" maxlength=\"3\" /> ";
        $output .= _TNGZ_FORM_ORDERBYOCC;
        //$output .= "<input type=\"hidden\" name=\"tree\" value=\"\" />";
        $output .= " <input type=\"submit\" value=\"". _TNGZ_FORM_GO ."\" />";
        $output .= "</form>";
        $output .= "<br />";
        $output .= "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'surnames-all'))) . "\">" . _TNGZ_SURNAMES_LINK_SURNAMES_ALL  . "</a>";
        $output .= "<br />";
        $output .= "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'surnames'    ))) . "\">" . _TNGZ_SURNAMES_LINK_SURNAMES . "</a>";
        $output .= "</div>";
    }
    
    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );
    return $output;
}

