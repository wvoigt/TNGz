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
* TNG interface in Zikula
* 
* Displays the given TNG page either wrapped in Zikula or not, depending upon the page given
* 
* @param show TNG page to display.  Additional arguments may be used by TNG 
* @return true if the page has already been displayed, othersise the TNG data to be used in Zikula
*/ 
function TNGz_user_main() {

    $TNGpage = FormUtil::getPassedValue('show', 'index', 'GET');

    switch ($TNGpage) {
	    case 'gedcom':
	    case 'addbookmark':
	    case 'findperson':
	    case 'findpersonform':
	    case 'tngrss':
	    case 'tnghelp':
	    case 'tentedit':
	    case 'pedxml':
	    case 'showmediaxml':
        case 'smallimage':
	    case 'pdfform':
	    case 'rpt_descend':
	    case 'rpt_ind':
	    case 'rpt_pedigree':
                  // for these, just give the output, with no extra stuff wrapped around it
                  $TNGrenderpage = false;
	              break;
	    default:
	              // Everything else can be wrapped as usual
                  $TNGrenderpage = true;
	    break;
    }
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => $TNGpage, 'render' => $TNGrenderpage ));
}

/**
* TNG administration in Zikula
* 
* Checks for logged in Zikula user and permissions, then sets up TNG administration in an IFRAME.
* 
* @param none 
* @return IFRAME page calling TNG administration
*/
function TNGz_user_admin() {

    if (!SecurityUtil::checkPermission('TNGz::', '::', ACCESS_OVERVIEW)) {
	return LogUtil::registerError(_MODULENOAUTH);
    }

    if (!pnUserLoggedIn()) {
        // Must be logged in to even have a chance at getting to the administration page
        pnRedirect(pnModURL('Users','user','loginscreen')) ;
    }

    if (!$url=pnModAPIFunc('TNGz','user','GetTNGurl') ){
    	return LogUtil::registerError("Error accessing TNG config file.");
    }

    //////////////////////////////////////////////////////
    // Now go and display it
    //////////////////////////////////////////////////////
    $pnTNGmodinfo = pnModGetInfo(pnModGetIDFromName('TNGz'));

    $pnRender =& new pnRender('TNGz');

    $pnRender->assign('TNGzURL'      , $url);
    $pnRender->assign('TNGzVersion'  , $pnTNGmodinfo['version'] );

    return $pnRender->fetch('TNGz_user_admin.htm');

}

/**
* TNG sitemap generation
* 
* Produce three different types of sitemaps
* 1. If no parameters are used, a full sitemap if the number of entries is below a set threshold
* 2. If no parameters are used, a sitemapindex file if the number of entries is above a set threshold
* 3. If parameters are used, a partial sitemap file that can be called by the sitemapindex file.
* 
* @param map    (optional) subset record type on which to generate a sitemap (e.g., people, family, etc.)
* @param start  (optional) the first record number to show
* @param count  (optional) the number of records to show
* @return true if sitemap/sitemapindex is displayed, false on error
*/
function TNGz_user_sitemap() {
    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_READ)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }   

    $none = "-1";  // flag
   
    $map   = FormUtil::getPassedValue('map',   $none, 'GET');
    $start = FormUtil::getPassedValue('start', $none, 'GET');
    $count = FormUtil::getPassedValue('count', $none, 'GET');
    
    $records = array();

    switch ($map) {
        case 'people':
	              $records[] = array('type' => 'people', 'start' => $start, 'count' => $count );
		          $all_site = false;
	              break;
 	    case 'family':
	              $records[] = array('type' => 'family', 'start' => $start, 'count' => $count );
		          $all_site = false;
		          break;
        case 'all':
	    default:
	              $records[] = array('type' => 'people', 'start' => $none, 'count' => $none );
	              $records[] = array('type' => 'family', 'start' => $none, 'count' => $none );
		          $all_site = true;
		          break;
    }

    if ($all_site) {
        $facts = pnModAPIFunc('TNGz','user','getRecordsCount');
        if ($facts['sitemapindex']) {  // too big for a sitemap
            // return a sitemapindex file
            $pnRender =& new pnRender('TNGz');
            $pnRender->assign('sitemaps'  , $facts['sitemapindex']);
            $pnRender->display('TNGz_user_sitemapindex.htm');
            return true;
        }
    }

    // Return a sitemap (either full, or a partial one)
    $pnRender =& new pnRender('TNGz');
    $pnRender->assign('records', $records);
    $pnRender->display('TNGz_user_sitemap.htm');
    return true;
    
}
