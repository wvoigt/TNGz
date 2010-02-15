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
 * @param $params['title']  if set, adds the text at the top
 * @return string containing HTML formated surnames
 */
function smarty_function_surnames($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('TNGz');
    
    // Get parameters
    // Valid answers, default is the first in the list
    $answer_yes    = array('Y', 'yes', 'y', '1', 'on', 'all');  // Answers for Yes or All
    $answer_no     = array('N', 'no',  'n', '0', 'off','none'); // Answers for No or none
    $answer_YN     = array_merge($answer_yes, $answer_no);
    
    $top = $params['top'];  
    $top  = (is_numeric($top) && $top > 0)? intval($top) : 50;  // Get valid value or set default

    $validtypes = array('list', 'cloud', 'table');  // first in list is the default
    $type = (in_array($params['type'], $validtypes))? $params['type'] : $validtypes[0];
    
    $validsorts = array('alpha', 'rank');  // first in list is the default
    $sort = (in_array($params['sort'], $validsorts))? $params['sort'] : $validsorts[0];

    $cols = $params['col'];
    $cols  = (is_numeric($cols) && $cols > 0)? intval($cols) : 2;  // Get valid value or set default

    $params['links'] = (in_array($params['links'], $answer_YN  ))? $params['links'] : $answer_yes[0];
    $params['links'] = (in_array($params['links'], $answer_no  ))? $answer_no[0]    : $params['links'];
    $params['links'] = (in_array($params['links'], $answer_yes ))? $answer_yes[0]   : $params['links'];
    
    $params['menu'] = (in_array($params['menu'], $answer_YN  ))? $params['menu']  : $answer_no[0];
    $params['menu'] = (in_array($params['menu'], $answer_no  ))? $answer_no[0]    : $params['menu'];
    $params['menu'] = (in_array($params['menu'], $answer_yes ))? $answer_yes[0]   : $params['menu'];

    $params['title'] = (empty($params['title'])) ? "" : DataUtil::formatForDisplay($params['title']);

    $lang = ZLanguage::getLanguageCode(); // get language used in Zikula
    
    // See if already in the cache
    $title_hash = ($params['title'])? md5($params['title']) : "x";
    $cachefile    = sprintf("surnames_%s_%s_%s_%s_%s_%s_%s_%s_%s.html",$lang,$type,$sort,$top,$cols,$links,$params['menu'],$params['links'],$title_hash);
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

    $output  = ( $params['title'] ) ? "<h3 class=\"surnames\" >" . $params['title'] . "</h3>\n" : "";

    if ($type == 'cloud') {
        $output .= "<div class='surnames-cloud'>";
        foreach($names as $name){
            $output .= "<span class='surnames-cloud size" . $name['class'] . "'>";
            $output .= ($params['links']=='N') ? "" : "<a class='surnames-cloud size" . $name['class'] . "' ";
            $output .= ($params['links']=='N') ? "" : "href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND'))) . "\">";
            $output .= $name['surname'];
            $output .= ($params['links']=='N') ? "" : "</a>";
            $output .= "</span> ";
        }
        $output .= "</div>";
    }
    if ($type == 'list'){
        $list = ($sort == 'alpha') ? "ul" : "ol";
        $output .= "<$list class=\"surnames-list\">";
        foreach($names as $name){
            $output .= "<li>";
            $output .= ($params['links']=='N') ? "" : "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND'))) . "\">";
            $output .= $name['surname'];
            $output .= ($params['links']=='N') ? "" : "</a>";
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
        $output .= "<table class=\"surnames-table\">";
        for($row=0; $row<$rows; $row++){
            $output .= "<tr>";
            for($col=0; $col<$cols; $col++){
                 $output .= "<td>";
                 $name = $table[$row][$col];
                 if ($name != null) {
                     if ($sort == 'rank'){
                         $output .= $name['rank'] . ". ";
                     }
                     $output .= ($params['links']=='N') ? "" : "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'search', 'mylastname' => $name['surnameuc'], 'mybool' => 'AND'))) . "\">";
                     $output .= $name['surname'];
                     $output .= ($params['links']=='N') ? "" : "</a>";
                     $output .= " (". $name['count']. ")" ;
                 }
                 $output .= "</td>";
            }
            $output .= "</tr>";
        }
        $output .= "</table>";
    }
    if ($params['menu'] == 'Y'){
        $output .= "<div class=\"surnames-menu\">";
        $output .= "<form style=\"margin:0px\" action=\"index.php\" method=\"post\">";
        $output .= "<input type=\"hidden\" name=\"module\" value=\"TNGz\" />";
        $output .= "<input type=\"hidden\" name=\"show\" value=\"surnames100\" />";
        $output .= __('Show top', $dom);
        $output .= " <input type=\"text\" name=\"topnum\" value=\"100\" size=\"3\" maxlength=\"3\" /> ";
        $output .= __('ordered by occurrence', $dom);
        //$output .= "<input type=\"hidden\" name=\"tree\" value=\"\" />";
        /*! Submit button */
        $output .= " <input type=\"submit\" value=\"". __('Go', $dom /*! The Submit button */)."\" />";
        $output .= "</form>";
        $output .= "<br />";
        $output .= "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'surnames-all'))) . "\">" . __('Show all surnames alphabetically', $dom) . "</a>";
        $output .= "<br />";
        $output .= "<a href=\"". DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'main', array( 'show' => 'surnames'    ))) . "\">" . __('Main surname page', $dom) . "</a>";
        $output .= "</div>";
    }
    
    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );
    return $output;
}

