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
 * TNGz namesearch
 * Add a TNG name search form
 * @return string containing HTML for form
 */
function smarty_function_namesearch($params, &$smarty)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

    $params['title'] = (empty($params['title'])) ? __('Search', $dom) : DataUtil::formatForDisplay($params['title']);

    $output  = "<div class=\"namesearch\">";
    $output .= "<h3 class=\"namesearch\">" . $params['title'] . "</h3>\n";
    $output .= "<form action=\"index.php\" method=\"get\">\n";
    $output .= "<input type=\"hidden\" name=\"module\" value=\"TNGz\"/>\n";
    $output .= "<input type=\"hidden\" name=\"show\"   value=\"search\"/>\n";
    $output .= "<input type=\"hidden\" name=\"mybool\" value=\"AND\"/>\n";
    $output .= "<input type=\"hidden\" name=\"offset\" value=\"0\"/>\n";
    $output .= "<table border=\"0\" cellspacing=\"5\" cellpadding=\"0\">\n";
    $output .= "<tr><td><span class=\"normal\">" . __('Last Name', $dom) . "</span><br/><input type=\"text\" name=\"mylastname\"  size=\"14\"/></td></tr>\n";
    $output .= "<tr><td><span class=\"normal\">" . __('First Name', $dom). "</span><br/><input type=\"text\" name=\"myfirstname\" size=\"14\"/></td></tr>\n";
    $output .= "<tr><td><input type=\"submit\" name=\"search\" value=\"" . __('Search', $dom) . "\"/></td></tr>\n";
    $output .= "<tr><td><span class=\"normal\"><a href=\"" . DataUtil::formatForDisplay(pnModURL('TNGz','user','main', array('show'=>'searchform'))) . "\">" . __('Advanced Search', $dom) . "</a></span></td></tr>\n";
    $output .= "</table></form>\n";
    $output .= "</div>\n";

    return $output;
}

