<?php if(!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Dominik Zogg 2012
 * @author     Dominik Zogg <dominik.zogg@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

class DirectEntryThemes extends Backend
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
            // load database
            $this->import("Database");

            // get all existing themes
            $objThemes = $this->Database->query("SELECT id,name FROM tl_theme ORDER BY name");

            // if there is at minimum one theme
            if($objThemes->numRows >= 1)
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