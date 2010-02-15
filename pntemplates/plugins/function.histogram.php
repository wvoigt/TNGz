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
 * TNGz histogram
 * Displays a date histogram of the events in the TNG database.  Shows the time frames of the events in the database.
 * @param $params['step']       the number of years to group events together (20)
 * @param $params['scale']      the scale increment on the number of events per horizontal line (500)
 * @param $params['width']      the width of the impage in pixels (300)
 * @param $params['height']     the height of the image in pixals (200)
 * @param $params['color']      the hex color for the bars, lines, and numbers (000000)
 * @param $params['graph']      the type of graph (bar, line)
 * @param $params['values']     show values on top of each bar or line point (no, yes)
 * @param $params['background'] name of background image found modules/TNGz/pnimages/ (Sundial.png)
 * @param $params['title']      if set, adds the text at the top
 * @return string containing HTML formated display of the histogram
 */
function smarty_function_histogram($params, &$smarty)
{  

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    // Valid answers, default is the first in the list
    $answer_yes    = array('Y', 'yes', 'y', '1', 'on', 'all');  // Answers for Yes or All
    $answer_no     = array('N', 'no',  'n', '0', 'off','none'); // Answers for No or none   
    $answer_YN     = array_merge($answer_yes, $answer_no);

    // Parameters
    if (empty($params['step']) || !is_numeric($params['step']) || ((int)$params['step'] <= 0)) {
        $params['step'] = 20;
    } else {
        $params['step'] = (int)$params['step'];
    }

    if (empty($params['scale']) || !is_numeric($params['scale']) || ((int)$params['scale'] <= 0)) {
        $params['scale'] = 500;
    } else {
        $params['scale'] = (int)$params['scale'];
    }
    
    if (empty($params['width']) || !is_numeric($params['width']) || ((int)$params['width'] <= 0)) {
        $params['width'] = 300;
    } else {
        $params['width'] = (int)$params['width'];
        $params['width'] = ($params['width']  > 0 && $params['width']  <= 1200 ) ? $params['width']  : 600;
    }

    if (empty($params['height']) || !is_numeric($params['height']) || ((int)$params['height'] <= 0)) {
        $params['height'] = 200;
    } else {
        $params['height'] = (int)$params['height'];
        $params['height'] = ($params['height']  > 0 && $params['height']  <= 1200 ) ? $params['height']  : 400;
    }
    

    if (empty($params['color']) || strlen($params['color']) != 6 ) {
        $params['color'] = "000000";
    }

    $params['graph']  = ($params['graph'] == "line" ) ? "line" : "bar"; 


    $params['values'] = (in_array($params['values'], $answer_YN  ))? $params['values']: $answer_no[0];
    $params['values'] = (in_array($params['values'], $answer_no  ))? $answer_no[0]    : $params['values'];
    $params['values'] = (in_array($params['values'], $answer_yes ))? $answer_yes[0]   : $params['values'];
    $params['values'] = ( $params['values'] == 'Y') ? 1 : 0;

    if (empty($params['background']) ) {
        $params['background'] = "Sundial.png";
    }     
    if (!file_exists("modules/TNGz/pnimages/". $params['background'])) {
        $params['background'] = "none";
    }

    $params['title'] = (empty($params['title'])) ? "" : DataUtil::formatForDisplay($params['title']);

    // See if already in the cache
    $title_hash = ($params['title'])? md5($params['title']) : "x";
    $background_hash = ($params['background'])? md5($params['background']) : "x";
    
    $cachefile    = sprintf("histogram_%s_%s_%s_%s_%s_%s_%s_%s_%s.htm",
                             $params['step'], $params['scale'],$params['width'],$params['height'], $params['graph'],
                             $params['color'],$params['values'],$background_hash,$title_hash);
    $cacheresults = pnModAPIFunc('TNGz','user','Cache',     array( 'item'=> $cachefile ));
    if ($cacheresults) {
        return $cacheresults;
    }
 

    $render = & pnRender::getInstance('TNGz', false);

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $render->assign('histogram', $params);

    // Populate block info and pass to theme
    $output = $render->fetch('TNGz_plugin_histogram.htm');

    // now update the cache
    pnModAPIFunc('TNGz','user','CacheUpdate', array( 'item'=> $cachefile, 'data' => $output) );

    return $output;
}
