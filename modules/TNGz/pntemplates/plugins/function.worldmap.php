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
 * TNGz worldmap
 * Display a map image of geocoded locations in TNG
 * @param $args['height']  image height
 * @param $args['width']   image width
 * @param $args['size']    map size (medium, large, giant, huge, small, tiny)
 * @param $args['region']  map region (0-6) 0=whole world
 * @return string containing HTML image reference
 */
function smarty_function_worldmap($params, &$smarty)
{
    // Get parameters
    $height = $params['height'];  
    $height  = (is_numeric($height) && $height > 0)? intval($height) : 100;  // Get valid value or set default

    $width = $params['width'];  
    $width  = (is_numeric($width) && $width > 0)? intval($width) : 200;  // Get valid value or set default

    $validsizes = array('medium', 'large', 'giant', 'huge', 'small', 'tiny');  // first in list is the default
    $size = (in_array($params['size'], $validsizes))? $params['size'] : $validsizes[0];
    
    $validregions = array(0,1,2,3,4,5,6);  // first in list is the default
    $region = (in_array($params['region'], $validregions))? $params['region'] : $validregions[0];

    $output  = "<img class=\"placemap\" ";
    $output .= "height=\"$height\" width=\"$width\" ";
    $output .= "src=\"" . DataUtil::formatForDisplay(pnModURL('TNGz', 'user', 'worldmap', array('size'=>$size, 'region'=>$region), null, null, true )) . "\" ";
    $output .= ">";
    
    return $output;
}