<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  © Dominik Zogg 2011
 * @author     Dominik Zogg
 * @package    directentrys_theme
 * @license    LGPLv3
 * @filesource
 */

class DirectEntrysTheme extends Backend
{
    /**
     * arrIcons
     * @var array to show icons
     */
    var $arrIcons = array
    (
        'tl_style_sheet' => 'css',
        'tl_module' => 'modules',
        'tl_layout' => 'layout',
    );

    /**
     * injectThemes : HOOK
     * @param str $strContent rendered template
     * @param str $strTemplate template name
     * @return str rendered template
     */
    public function injectThemes($strContent, $strTemplate)
    {
        // import objects
        $this->import("Database");
        $this->import('BackendUser', 'User');
        // check if this is the backend template and the user got the permission for this action
        if($strTemplate == 'be_main' && ($this->User->isAdmin || $this->User->hasAccess('themes', 'modules')))
        {
            // open list
            $strToAdd = '<ul>';
            // get all existing themes
            $objResult = $this->Database->query("SELECT id,name FROM tl_theme ORDER BY name");
            while($row = $objResult->fetchAssoc())
            {
                // prepare the theme name
                $strThemeName = strlen($row['name']) > 10 ? substr($row['name'], 0, 8) . '...' : $row['name'];
                // prepare list element for the theme
                $strToAdd .= '<li><span style="padding-left: 15px;">';
                // foreach icon to build a link for
                foreach($this->arrIcons as $strIconTable => $strIcon)
                {
                    // check if the user got the permission for this action
                    if($this->User->isAdmin || $this->User->hasAccess($strIcon, 'themes'))
                    {
                        $strToAdd .= '<a style="padding-right: 2px;" href="contao/main.php?do=themes&table=' . $strIconTable . '&id=' . $row['id'] . '">';
                        $strToAdd .= '<img src="system/themes/default/images/' . $strIcon . '.gif" alt="' . $strIcon . '" />';
                        $strToAdd .= '</a>';
                    }
                }
                // link for editing the theme metainformation
                $strToAdd .= '<a href="contao/main.php?do=themes&act=edit&id=' . $objResult->id . '">' . $strThemeName . '</a>';
                $strToAdd .= '</span></li>';
            }
            // close list
            $strToAdd .= '</ul>';
            // inject the elements
            $strContent = preg_replace
            (
                '/(\<li.*?id="design".*?\>[\S\s]*?\<ul.*?\>[\S\s]*?\<li.*?\>[\S\s]*?\<a.*?themes[\S\s]*?\>.*?\<\/a\>)([\S\s]*?\<\/li\>[\S\s]*?\<\/ul\>[\S\s]*?\<\/li\>)/',
                '${1}' . $strToAdd . '${2}',
                $strContent
            );
        }
        return($strContent);
    }
}