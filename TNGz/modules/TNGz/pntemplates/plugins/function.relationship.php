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
 * TNGz relationship
 * Display an inline list of relationship links to the current person
 * Only returns values if current page uses 'personID' and 'tree'
 * @return string containing HTML with links
 */
function smarty_function_relationship($params, &$smarty)
{
    // Get current person.  Only continue if there is a valid person
    $personID   = FormUtil::getPassedValue('personID',  false, 'GETPOST');
    $primaryID  = FormUtil::getPassedValue('primaryID', false, 'GETPOST');
    $personTree = FormUtil::getPassedValue('tree',      false, 'GETPOST');

    $pID = ($personID) ? $personID : $primaryID;
    $Valid = true;  // // start off expecting it to be valid
    if ( !$pID || !$personTree) {
        $Valid = false; // need to have both to show anything
    } else {
        $person = pnModAPIFunc('TNGz','user','getperson', array('id'=>$pID, 'tree'=>$personTree)); //false if not exist
        if (!$person) {
            $Valid = false;
        }
    }

    $output .= "\n<!-- --- Start Relationship --- -->\n";
    $output .= "<div id=\"relationship-plugin\">\n";
    $output .= "&nbsp;\n";
    $output .= "<ul>\n";

    if($Valid) {
        // Get primary person info
        $primary = pnModAPIFunc('TNGz','user','getperson'); // Default get person, false if not set or does not exist

        // Get saved person info
        if (pnUserLoggedIn()) {
            $username  = pnUserGetVar('uname');
            $LoggedIn  = true;
            $SaveList  = unserialize(pnModGetVar('TNGz','SaveList',''));
            if (isset($SaveList[$username])){
                $saved     = pnModAPIFunc('TNGz','user','getperson', array('id'=>$SaveList[$username]['id'], 'tree'=>$SaveList[$username]['tree'])); //false if not exist
            } else {
                $saved = false;
            }
        } else {
            $LoggedIn = false;
            $saved = false;
        }

        // TNG text (in the right language)
        $text = pnModAPIFunc('TNGz','user','GetTNGtext',array('textpart'=>'relate'));

        // Get the other inportant parameters so can return here if needed
        $show        = FormUtil::getPassedValue('show',       false, 'GETPOST');
        $generations = FormUtil::getPassedValue('generations',false, 'GETPOST');
        $display     = FormUtil::getPassedValue('display',    false, 'GETPOST');
        
        $saveidArgs = array(); // hold the call back arguments
        if ($show)       { $saveidArgs['show']        = $show; }
        if ($personID)   { $saveidArgs['personID']    = $personID; }
        if ($primaryID)  { $saveidArgs['primaryID']   = $primaryID; }
        if ($personTree) { $saveidArgs['tree']        = $personTree; }
        if ($display)    { $saveidArgs['display']     = $display; }
        if ($generations){ $saveidArgs['generations'] = $generations; }
        
        $generations = ($generations) ? $generations : 15;  // set a default if needed.
        
        // The "Save this person" link
        $showSaveThis = true;  // start off intending to display it
        if (!$LoggedIn){
            $showSaveThis = false; // Can't save if not logged in
        }
        if ($saved && ($saved['personID'] == $person['personID'])) {
            $showSaveThis = false; // Already at the saved person, so no need to do or display anything
        }
        if ($primary && ($primary['personID'] == $person['personID'])) {
            $showSaveThis = false; // Don't save the primary person (again)
        }

        if (!$LoggedIn || !$saved || ($saved['personID'] == $person['personID'])) {
            $showSaved = false;
        } else {
            $showSaved = true;
        } 

        $showPrimary = true;  // start off intending to display it
        if (!$primary || ($primary['personID'] == $person['personID'])){
            $showPrimary = false;
        } else {
            $showPrimary = true;
        }

        // Now, Output the results
        if ($showSaveThis) {
            $output .= "<li>";
            $output .= "<a href=\"".pnModURL('TNGz','user','saveid', $saveidArgs)."\">".$text['save']."&nbsp;".$person['fullname']."</a>";
            $output .= "</li>\n";
        }
        if ($showSaved) {
            $output .= "<li>";
            if ( $saved['tree'] == $person['tree']) {
                $output .= "<a href=\"".pnModURL('TNGz','user','main', array('show'=>'relationship','tree'=>$person['tree'],'primarypersonID'=>$person['personID'],'secondpersonID'=>$saved['personID'], 'generations'=>$generations))."\">".$text['relateto']."</a>&nbsp;";
            }
            $output .= "<a href=\"".pnModURL('TNGz','user','main', array('show'=>'getperson','tree'=>$saved['tree'],'personID'=>$saved['personID']))."\">".$saved['fullname']."</a>";
            $output .= "&nbsp;";
            $output .= "("."<a href=\"".pnModURL('TNGz','user','saveid', $saveidArgs + array('delete'=>'1'))."\">".$text['text_delete']."</a>".")";
            $output .= "</li>\n";
        }
        if ($showPrimary){
            $output .= "<li>";
            if ($primary['tree'] == $person['tree']) {
                $output .= "<a href=\"".pnModURL('TNGz','user','main', array('show'=>'relationship','tree'=>$person['tree'],'primarypersonID'=>$person['personID'],'secondpersonID'=>$primary['personID'], 'generations'=>$generations))."\">".$text['relateto']."</a>&nbsp;";
            }
            $output .= "<a href=\"".pnModURL('TNGz','user','main', array('show'=>'getperson','tree'=>$primary['tree'],'personID'=>$primary['personID']))."\">".$primary['fullname']."</a>";
            $output .= "</li>\n";
        }
    }
    
    $output .= "</ul>\n";
    $output .= "</div>\n";
    $output .= "<!-- --- End Relationship --- -->\n";

    return $output;
}