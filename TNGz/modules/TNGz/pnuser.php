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
function TNGz_user_getperson() {  
    return pnModAPIFunc('TNGz','user','ShowPage', array('showpage' => 'getperson', 'render' => true ));
}
/*
Need to finish this....
Files to Render:
addnewacct
ahnentafel
anniversaries
bookmarks
browsealbums
browsemedia
browsenotes
browserepos
browsesources
browsetrees-old
browsetrees
cemeteries
changelanguage
descend
descendtext
desctracker
extrastree
familygroup
gedform
getperson
headstones
historytemplate
login
maint
mostwanted
newacctform
pedigree
pedigreetext
places-all
places-oneletter
places
places100
placesearch
register
relateform
relationship
reports
search
searchform
sendlogin
showalbum
showhistory
showlog
showmap
showmedia
showrepo
showreport
showsource
showtree
suggest
surnames-all
surnames-oneletter
surnames
surnames100
timeline
timeline2
ultraped
whatsnew 


Need to Evaluate:
anniversaries2, browsedocs,browseheadstones,browsephotos,browserinfo,deletebookmark,deleteentity,,fpdf,fpdf_cellfit,
functions,getlang,headstones,historytemplate,logout,logxml,mapconfig,mediatypes,meta,,photoblock,processlogin,
register,relateform,relationship2,rss2html,savelanguage,savelanguage2,savetentedit,search,searchform,sendlogin,
setpermissions1,showheadstone,showphoto,,switchcolor,switchcolor2
*/


//*****************************************************************************
// TNG functions that get called and should NOT have Zikula wrapped around it
//*****************************************************************************
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

?>