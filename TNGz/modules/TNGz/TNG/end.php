<?
/**
 * Zikula Application Framework
 *
 * @copyright  (c) Zikula Development Team
 * @link       http://www.zikula.org
 * @version    $Id$
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Wendel Voigt
 * @category   Zikula_Extension
 * @package    Content
 * @subpackage TNGz
 */

if ($cms['TNGz'] == 1){
    // Do nothing, Zikula takes care of things
} else {
    // Finish the page
    echo "</body>";
    echo "</html>";
}
