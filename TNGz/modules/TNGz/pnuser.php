<?php
// $Id: pnuser.php, v 1.01 2008/10/06 13:08:28 wvoigt Exp $

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

/*
Files to Render:
Evaluate:
addbookmark.php,addnewacct.php,ahnentafel.php,albumlib.php,anniversaries.php,anniversaries2.php,
begin.php,bookmarks.php,browsealbums.php,browsedocs.php,browseheadstones.php,browsemedia.php,
browsenotes.php,browsephotos.php,browserepos.php,browserinfo.php,browsesources.php,browsetrees-old.php,
browsetrees.php,cemeteries.php,changelanguage.php,checklogin.php,config.php,customconfig.php,
deletebookmark.php,deleteentity.php,descend.php,descendtext.php,desctracker.php,end.php,end7.php,
extrastree.php,familygroup.php,findperson.php,findpersonform.php,fpdf.php,fpdf_cellfit.php,
functions.php,gedcom.php,gedform.php,genlib-bak.php,genlib.php,getglobals.php,getlang.php,
getperson.php,globallib.php,headstones.php,historytemplate.php,importconfig.php,index-backup.php,
index-bak.php,index-new.php,index.php,index_new.php,index_plus_header_footer.php,index_pn-bak.php,
index_pn-bak2.php,index_pn-bak3.php,index_pn.php,index_pnTNG.php,log.php,logconfig.php,login.php,
logout.php,logxml.php,maint.php,mapconfig.php,mediatypes.php,menu-top.php,menu.php,meta.php,
mostwanted.php,newacctform.php,pdfform.php,pedconfig.php,pedigree.php,pedigreetext.php,
pedxml.php,personlib.php,photoblock.php,places-all.php,places-oneletter.php,places.php,
places100.php,placesearch.php,pnversion.php,processlogin.php,register.php,reglib.php,relateform.php,
relationship.php,relationship2.php,reports.php,rpt_descend.php,rpt_ind.php,rpt_pedigree.php,
rss2html.php,savelanguage.php,savelanguage2.php,savetentedit.php,search.php,searchform.php,sendlogin.php,
setpermissions1.php,showalbum.php,showheadstone.php,showhistory.php,showlog.php,showmap.php,
showmedia.php,showmedialib.php,showmediaxml.php,showphoto.php,showrepo.php,showreport.php,
showsource.php,showtree.php,smallimage.php,subroot.php,suggest.php,surnames-all.php,surnames-oneletter.php,
surnames.php,surnames100.php,switchcolor.php,switchcolor2.php,tentedit.php,timeline.php,timeline2.php,
tngfiletypes.php,tnghelp.php,tnginstall.php,tngmenu.php,tngpdf.php,tngrobots.php,tngrss.php,tngsendmail.php,
topmenu.php,ufpdf.php,ultraped.php,version.php,whatsnew.php,index_TNGz.php
Files that don't get rendered:
*/


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
?>