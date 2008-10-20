<?php
// $Id: pnuser.php, v 1.01 2008/10/06 13:08:28 wvoigt Exp $
//*****************************************************************************
// The generic way to call TNG functions (RefType=0)
// Generically they have the form:
// index.php?module=TNGz&type=user&func=main&show=
//*****************************************************************************
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

//*****************************************************************************
// TNG administration
// *****************************************************************************
function TNGz_user_admin() {

    if (!pnSecAuthAction(0, 'TNGz::', '::', ACCESS_OVERVIEW)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    if (!pnUserLoggedIn()) {
        // Must be logged in to even have a chance at getting to the administration page
        pnRedirect(pnModURL('Users','user','loginscreen')) ;
    }

    if (!$url=pnModAPIFunc('TNGz','user','GetTNGurl') ){
        return pnVarPrepHTMLDisplay("Error accessing TNG config file.");
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

//*****************************************************************************
// The itemized way to call TNG functions. (RefType=1)
// Generically they have the form:
// index.php?module=TNGz&type=user&func=
//
//*****************************************************************************
// TNG functions that get called and should have Zikula wrapped around it
//*****************************************************************************
/*
function TNGz_user_addnewacct() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'addnewacct', 'render' => true ));
}
function TNGz_user_ahnentafel() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'ahnentafel', 'render' => true ));
}
function TNGz_user_anniversaries() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'anniversaries', 'render' => true ));
}
function TNGz_user_anniversaries2() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'anniversaries2', 'render' => true ));
}
function TNGz_user_bookmarks() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'bookmarks', 'render' => true ));
}
function TNGz_user_browsealbums() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsealbums', 'render' => true ));
}
function TNGz_user_browsemedia() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsemedia', 'render' => true ));
}
function TNGz_user_browsenotes() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsenotes', 'render' => true ));
}
function TNGz_user_browserepos() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browserepos', 'render' => true ));
}
function TNGz_user_browsesources() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsesources', 'render' => true ));
}
function TNGz_user_browsedocs() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsedocs', 'render' => true ));
}
function TNGz_user_browseheadstones() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browseheadstones', 'render' => true ));
}
function TNGz_user_browsephotos() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsephotos', 'render' => true ));
}
function TNGz_user_browserinfo() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browserinfo', 'render' => true ));
}
function TNGz_user_browsetrees() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'browsetrees', 'render' => true ));
}
function TNGz_user_cemeteries() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'cemeteries', 'render' => true ));
}
function TNGz_user_changelanguage() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'changelanguage', 'render' => true ));
}
function TNGz_user_descend() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'descend', 'render' => true ));
}
function TNGz_user_descendtext() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'descendtext', 'render' => true ));
}
function TNGz_user_desctracker() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'desctracker', 'render' => true ));
}
function TNGz_user_deletebookmark() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'deletebookmark', 'render' => true ));
}
function TNGz_user_extrastree() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'extrastree', 'render' => true ));
}
function TNGz_user_familygroup() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'familygroup', 'render' => true ));
}
function TNGz_user_gedform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'gedform', 'render' => true ));
}
function TNGz_user_getperson() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'getperson', 'render' => true ));
}
function TNGz_user_headstones() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'headstones', 'render' => true ));
}
function TNGz_user_historytemplate() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'historytemplate', 'render' => true ));
}
function TNGz_user_login() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'login', 'render' => true ));
}
function TNGz_user_logout() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'logout', 'render' => true ));
}
function TNGz_user_maint() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'maint', 'render' => true ));
}
function TNGz_user_mostwanted() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'mostwanted', 'render' => true ));
}
function TNGz_user_newacctform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'newacctform', 'render' => true ));
}
function TNGz_user_pedigree() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'pedigree', 'render' => true ));
}
function TNGz_user_pedigreetext() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'pedigreetext', 'render' => true ));
}
function TNGz_user_places-all() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'places-all', 'render' => true ));
}
function TNGz_user_places-oneletter() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'places-oneletter', 'render' => true ));
}
function TNGz_user_places() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'places', 'render' => true ));
}
function TNGz_user_places100() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'places100', 'render' => true ));
}
function TNGz_user_placesearch() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'placesearch', 'render' => true ));
}
function TNGz_user_register() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'register', 'render' => true ));
}
function TNGz_user_relateform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'relateform', 'render' => true ));
}
function TNGz_user_relationship() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'relationship', 'render' => true ));
}
function TNGz_user_reports() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'reports', 'render' => true ));
}
function TNGz_user_search() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'search', 'render' => true ));
}
function TNGz_user_searchform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'searchform', 'render' => true ));
}
function TNGz_user_sendlogin() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'sendlogin', 'render' => true ));
}
function TNGz_user_showalbum() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showalbum', 'render' => true ));
}
function TNGz_user_showhistory() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showhistory', 'render' => true ));
}
function TNGz_user_showlog() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showlog', 'render' => true ));
}
function TNGz_user_showmap() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showmap', 'render' => true ));
}
function TNGz_user_showmedia() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showmedia', 'render' => true ));
}
function TNGz_user_showreport() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showreport', 'render' => true ));
}
function TNGz_user_showsource() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showsource', 'render' => true ));
}
function TNGz_user_showtree() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showtree', 'render' => true ));
}
function TNGz_user_suggest() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'suggest', 'render' => true ));
}
function TNGz_user_surnames-all() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'surnames-all', 'render' => true ));
}
function TNGz_user_surnames-oneletter() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'surnames-oneletter', 'render' => true ));
}
function TNGz_user_surnames() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'surnames', 'render' => true ));
}
function TNGz_user_surnames100() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'surnames100', 'render' => true ));
}
function TNGz_user_timeline() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'timeline', 'render' => true ));
}
function TNGz_user_timeline2() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'timeline2', 'render' => true ));
}
function TNGz_user_ultraped() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'ultraped', 'render' => true ));
}
function TNGz_user_whatsnew() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'whatsnew', 'render' => true ));
}
function TNGz_user_logxml() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'logxml', 'render' => true ));
}
function TNGz_user_mapconfig() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'mapconfig', 'render' => true ));
}
function TNGz_user_photoblock() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'photoblock', 'render' => true ));
}
function TNGz_user_processlogin() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'processlogin', 'render' => true ));
}
function TNGz_user_register() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'register', 'render' => true ));
}
function TNGz_user_relateform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'relateform', 'render' => true ));
}
function TNGz_user_relationship2() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'relationship2', 'render' => true ));
}
function TNGz_user_savelanguage() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'savelanguage', 'render' => true ));
}
function TNGz_user_savelanguage2() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'savelanguage2', 'render' => true ));
}
function TNGz_user_savetentedit() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'savetentedit', 'render' => true ));
}
function TNGz_user_search() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'search', 'render' => true ));
}
function TNGz_user_searchform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'searchform', 'render' => true ));
}
function TNGz_user_sendlogin() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'sendlogin', 'render' => true ));
}
function TNGz_user_setpermissions1() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'setpermissions1', 'render' => true ));
}
function TNGz_user_showheadstone() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showheadstone', 'render' => true ));
}
function TNGz_user_showphoto() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showphoto', 'render' => true ));
}
function TNGz_user_switchcolor() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'switchcolor', 'render' => true ));
}
function TNGz_user_switchcolor2() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'switchcolor2', 'render' => true ));
}
*/

//*****************************************************************************
// TNG functions that get called and should NOT have Zikula wrapped around it
//*****************************************************************************
/*
function TNGz_user_addbookmark() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'addbookmark', 'render' => false ));
}

function TNGz_user_findperson() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'findperson', 'render' => false ));
}

function TNGz_user_findpersonform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'findpersonform', 'render' => false ));
}

function TNGz_user_gedcom() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'gedcom', 'render' => false ));
}

function TNGz_user_pdfform() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'pdfform', 'render' => false ));
}

function TNGz_user_pedxml() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'pedxml', 'render' => false ));
}

function TNGz_user_rpt_descend() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'rpt_descend', 'render' => false ));
}

function TNGz_user_rpt_ind() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'rpt_ind', 'render' => false ));
}

function TNGz_user_rpt_pedigree() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'rpt_pedigree', 'render' => false ));
}

function TNGz_user_showmediaxml() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'showmediaxml', 'render' => false ));
}

function TNGz_user_smallimage() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'smallimage', 'render' => false ));
}

function TNGz_user_atentedit() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'tentedit', 'render' => false ));
}

function TNGz_user_tnghelp() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'tnghelp', 'render' => false ));
}

function TNGz_user_tngrss() {
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'tngrss', 'render' => false ));
}
*/
