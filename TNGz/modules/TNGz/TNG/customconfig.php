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

// Custom settings for TNGz to work with TNG
// Please copy these lines into your customconfig.php file
// or if you do not use the customconfig.php file for anything else
// then you can just use this file

// OPTIONAL HACK for setting the Timeline width without modifying TNG files.
// There is no setting for this in TNG.  It is hardcoded to start at 500
if( !isset($_SESSION[timeline_chartwidth]) ) {
    session_register('timeline_chartwidth');
    $_SESSION[timeline_chartwidth] = 750;  // Change to what you want
}

?>
