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
 * @original author Cas Nuy
 * @author Wendel Voigt
 * @version $Id$
 */

function TNGz_Surnamesblock_info()
{
    return array(
        'text_type'      => 'Surnamesblock',
        'text_type_long' => 'Surnames',
        'module'         => 'TNGz',
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => true,
        'show_preview'   => true

    );
}

function TNGz_Surnamesblock_init()
{
    // Security
    pnSecAddSchema('TNGz:Surnamesblock:', 'Block title::');
}

function TNGz_Surnamesblock_display($blockinfo)
{

    if( !pnSecAuthAction( 0, 'TNGz:Surnamesblock:', "$blockinfo[title]::", ACCESS_READ ) )
        return false;

    if( !pnModAPILoad('TNGz','user',true) ) {
        return false;
    }

    $userlanguage = ZLanguage::getLanguageCode(); //get the user's language

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['top'])  || !is_numeric($vars['top'])) {
        $vars['top'] = "10";
    }
    if (empty($vars['cols']) || !is_numeric($vars['cols'])) {
        $vars['cols']  = "1";
    }
    if (!isset($vars['intro'][$userlanguage])) {
        $vars['intro'][$userlanguage] = '';
    }
    $valid_stypes = array('list', 'cloud', 'table');  // first in list is the default
    $stype = (in_array($vars['stype'], $valid_stypes))? $vars['stype'] : $valid_stypes[0];
    
    $valid_sorts = array('alpha', 'rank');  // first in list is the default
    $sort = (in_array($vars['sort'], $valid_sorts))? $vars['sort'] : $valid_sorts[0];
    

    // Can turn off caching by using the following
    if ( $vars['usecache'] == 0 ) {
        $zcaching = false;
    } else {
        $zcaching = true;
    }

    // Create output object
    // Note that for a block the corresponding module must be passed.
    $render = & pnRender::getInstance('TNGz', $zcaching);

    PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('TNGz'));

    $render->assign('top',   $vars['top']);
    $render->assign('cols',  $vars['cols']);
    $render->assign('stype', $vars['stype']);
    $render->assign('sort',  $vars['sort']);
    $render->assign('intro', $vars['intro'][$userlanguage]);

    // Populate block info and pass to theme
    $blockinfo['content'] = $render->fetch('TNGz_block_Surnames.htm');

    return themesideblock($blockinfo);
}

function TNGz_Surnamesblock_modify($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('TNGz');

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['top'])  || !is_numeric($vars['top'])) {
        $vars['top'] = "10";
    }
    if (empty($vars['cols']) || !is_numeric($vars['cols'])) {
        $vars['cols']  = "1";
    }

    if (!isset($vars['intro'])) {
        $vars['intro'] = array();
    }

    Loader::loadClass('ZLanguage');
    $languages    = ZLanguage::getInstalledLanguages();

    // make sure each language has an initial value
    foreach($languages as $lang) {
        if (!array_key_exists($lang, $vars['intro'])) {
            $vars['intro'][$lang] = '';
        }
    }

    $validstypes = array('list', 'cloud', 'table');  // first in list is the default
    $stype = (in_array($vars['stype'], $validstypes))? $vars['stype'] : $validstypes[0];
    
    $validsorts = array('alpha', 'rank');  // first in list is the default
    $sort = (in_array($vars['sort'], $validsorts))? $vars['sort'] : $validsorts[0];

    // Create output object
    $render = & pnRender::getInstance('TNGz', false);

    // As Admin output changes often, we do not want caching.
    $render->caching = false;

    // assign the approriate values
    $render->assign('stypelist',   array( 'cloud' => pnVarPrepHTMLDisplay(__('Surname cloud', $dom)),
                                          'table' => pnVarPrepHTMLDisplay(__('Table', $dom)),
                                          'list'  => pnVarPrepHTMLDisplay(__('List', $dom))
                                        ) );
    $render->assign('sortlist',   array(  'rank'  => pnVarPrepHTMLDisplay(__(' ordered by occurrence', $dom)),
                                          'alpha' => pnVarPrepHTMLDisplay(__(' sorted alphabetically', $dom))
                                        ) );

    $render->assign('top',      $vars['top']);
    $render->assign('cols',     $vars['cols']);
    $render->assign('stype',    $vars['stype']);
    $render->assign('sort',     $vars['sort']);
    $render->assign('intro',    $vars['intro']);

    // Return the output that has been generated by this function
    return $render->fetch('TNGz_block_Surnames_modify.htm');
}

function TNGz_Surnamesblock_update($blockinfo)
{
    //Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // alter the corresponding variable
    $vars['top']   = FormUtil::getPassedValue('top',    10,      'POST');
    $vars['cols']  = FormUtil::getPassedValue('cols',    1,      'POST');
    $vars['stype'] = FormUtil::getPassedValue('stype', 'list',   'POST');
    $vars['sort']  = FormUtil::getPassedValue('sort',  'alpha',  'POST');
    $vars['intro'] = FormUtil::getPassedValue('intro',  null,    'POST');

    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);

    return $blockinfo;
}
