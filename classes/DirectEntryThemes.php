<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package directentries
 * @copyright Dominik Zogg <dominik.zogg@gmail.com>
 * @author Dominik Zogg
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace DominikZogg\DirectEntries;

class DirectEntryThemes extends \Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // load backenduser
        $this->import('BackendUser', 'User');

        // check permission
        if($this->User->isAdmin || $this->User->hasAccess('themes', 'modules'))
        {
            // check if table exists
            if (!$this->Database->tableExists('tl_theme')) return;

            // get all existing themes
            $objThemes = \ThemeModel::findAll(array('order' => 'name'));

            // if there is at minimum one theme
            if(!is_null($objThemes) && $objThemes->count())
            {
                // prepare array return
                $arrDirectEntry = array();

                // set counter
                $intCounter = 1;

                // set icons array
                $arrIcons = array
                (
                    'css' => 'tl_style_sheet',
                    'modules' => 'tl_module',
                    'layout' => 'tl_layout',
                );

                // do this foreach theme
                while($objThemes->next())
                {
                    // set the theme url and title
                    $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=themes&act=edit&id=' . $objThemes->id;
                    $arrDirectEntry[$intCounter]['name']['title'] = $objThemes->name;
                    $arrDirectEntry[$intCounter]['name']['link'] = strlen($objThemes->name) > 10 ? substr($objThemes->name, 0, 8) . '...' : $objThemes->name;

                    // foreach icons
                    foreach($arrIcons as $strIcon => $strTableName)
                    {
                        // check detail permissions
                        if($this->User->isAdmin || $this->User->hasAccess($strIcon, 'themes'))
                        {
                            // set the icon url and title
                            $arrDirectEntry[$intCounter]['icons'][$strIcon]['url'] = 'contao/main.php?do=themes&id=' . $objThemes->id . '&table=' . $strTableName;
                            $arrDirectEntry[$intCounter]['icons'][$strIcon]['title'] = $strIcon;
                            $arrDirectEntry[$intCounter]['icons'][$strIcon]['icon'] = $strIcon;
                        }
                    }
                    // add one to counter
                    $intCounter++;
                }
                // add to direcentries service
                $this->import('DirectEntries');
                $this->DirectEntries->addDirectEntry('design', 'themes', $arrDirectEntry);
            }
        }
    }
}
