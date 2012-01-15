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

class DirectEntries extends Backend
{
    /**
     * __construct
     */
    public function __construct()
    {
        // load libraries
        $this->import("Database");
        $this->import('BackendUser', 'User');
    }

    /**
     * inject
     * @param str $strContent rendered template
     * @param str $strTemplate template name
     * @return str rendered template
     */
    public function inject($strContent, $strTemplate)
    {
        if($strTemplate == 'be_main')
        {
            // themes
            $arrThemes = $this->_prepareThemeArray();
            $strThemeHtml = $this->_buildHtml($arrThemes);
            $strContent = $this->_addContent('design', 'themes', $strThemeHtml, $strContent);

            // pages
            $arrPages = $this->_preparePageArray();
            $strPageHtml = $this->_buildHtml($arrPages);
            $strContent = $this->_addContent('design', 'page', $strPageHtml, $strContent);
        }
        // return rendered template
        return($strContent);
    }

    /**
     * _prepareThemeArray
     * @return boolean|array the theme array
     */
    protected function _prepareThemeArray()
    {
        // check permission
        if($this->User->isAdmin || $this->User->hasAccess('themes', 'modules'))
        {
            // get all existing themes
            $objThemes = $this->Database->query("SELECT id,name FROM tl_theme ORDER BY name");

            // if there is at minimum one theme
            if($objThemes->numRows)
            {
                // prepare array return
                $arrReturn = array();

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
                    $arrReturn[$intCounter]['name']['url'] = 'contao/main.php?do=themes&act=edit&id=' . $objThemes->id;
                    $arrReturn[$intCounter]['name']['title'] = $objThemes->name;
                    $arrReturn[$intCounter]['name']['link'] = strlen($objThemes->name) > 10 ? substr($objThemes->name, 0, 8) . '...' : $objThemes->name;

                    // foreach icons
                    foreach($arrIcons as $strIcon => $strTableName)
                    {
                        // check detail permissions
                        if($this->User->isAdmin || $this->User->hasAccess($strIcon, 'themes'))
                        {
                            // set the icon url and title
                            $arrReturn[$intCounter]['icons'][$strIcon]['url'] = 'contao/main.php?do=themes&id=' . $objThemes->id . '&table=' . $strTableName;
                            $arrReturn[$intCounter]['icons'][$strIcon]['title'] = $strIcon;
                            $arrReturn[$intCounter]['icons'][$strIcon]['icon'] = $strIcon;
                        }
                    }
                    // add one to counter
                    $intCounter++;
                }
                // return array
                return($arrReturn);
            }
        }
        // return false if theres no permission or no themes
        return(false);
    }

    /**
     * _preparePageArray
     * @return boolean|array the page array
     */
    protected function _preparePageArray()
    {
        // check permission
        if($this->User->isAdmin || $this->User->hasAccess('page', 'modules'))
        {
            // get all existing root pages
            $objPages = $this->Database->query("SELECT id,title FROM tl_page WHERE type = 'root' ORDER BY title");

            // if there is at minimum one page
            if($objPages->numRows > 1)
            {
                // prepare array return
                $arrReturn = array();

                // set counter
                $intCounter = 1;

                // do this foreach page
                while($objPages->next())
                {
                    // set the icon url and title
                    $arrReturn[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=page&node=' . $objPages->id;
                    $arrReturn[$intCounter]['icons']['page']['title'] = 'page';
                    $arrReturn[$intCounter]['icons']['page']['icon'] = 'page';

                    // set the page url and title
                    $arrReturn[$intCounter]['name']['url'] = 'contao/main.php?do=page&node=' . $objPages->id;
                    $arrReturn[$intCounter]['name']['title'] = $objPages->title;
                    $arrReturn[$intCounter]['name']['link'] = strlen($objPages->title) > 17 ? substr($objPages->title, 0, 15) . '...' : $objPages->title;

                    // add one to counter
                    $intCounter++;
                }
                // return array
                return($arrReturn);
            }
        }
        // return false if theres no permission or no page
        return(false);
    }

    /**
     * _buildHtml
     * @param boolean|array $arrPreparedArray the array to build html from
     * @return string html
     */
    protected function _buildHtml($arrPreparedArray)
    {
        // html string
        $strHtml = '';

        // if the input is an array
        if(is_array($arrPreparedArray))
        {
            // add list open tag
            $strHtml .= '<ul>';

            // forech list element
            foreach($arrPreparedArray as $arrListElement)
            {
                // add list element open tag
                $strHtml .= '<li style="padding-left: 15px;">';

                // check for icons
                if(isset($arrListElement['icons']) && is_array($arrListElement['icons']))
                {
                    // foreach icon
                    foreach($arrListElement['icons'] as $arrTitleAndUrl)
                    {
                        // add icon link
                        $strHtml .= '<a style="padding-right: 2px;" title="' . $arrTitleAndUrl['title'] . '" href="' . $arrTitleAndUrl['url'] . '">';
                        $strHtml .= '<img style="margin:0; padding: 0; width: 16px; height: 16px;" src="system/themes/default/images/' . $arrTitleAndUrl['icon'] . '.gif" alt="' . $arrTitleAndUrl['title'] . '" />';
                        $strHtml .= '</a>';
                    }
                }

                // check for name
                if(isset($arrListElement['name']) && is_array($arrListElement['name']))
                {
                    // add name link
                    $strHtml .= '<a title="' . $arrListElement['name']['title'] . '" href="' . $arrListElement['name']['url'] . '">';
                    $strHtml .= $arrListElement['name']['link'];
                    $strHtml .= '</a>';
                }

                // add list element close tag
                $strHtml .= '</li>';
            }

            // add list close tag
            $strHtml .= '</ul>';
        }

        // return html
        return $strHtml;
    }

    /**
     * _addContent
     * @param string $strToGroup for example design
     * @param string $strToElement for example themes
     * @param string $strToAdd html to add
     * @param string $strContent the old content
     * @return string the new content
     */
    protected function _addContent($strToGroup, $strToElement, $strToAdd, $strContent)
    {
        return(
            preg_replace
            (
                '/(\<li.*?id="' . $strToGroup .'".*?\>[\S\s]*?\<ul.*?\>[\S\s]*?\<li.*?\>[\S\s]*?\<a.*?' . $strToElement . '[\S\s]*?\>.*?\<\/a\>)([\S\s]*?\<\/li\>[\S\s]*?\<\/ul\>[\S\s]*?\<\/li\>)/',
                '${1}' . $strToAdd . '${2}',
                $strContent
            )
        );
    }
}