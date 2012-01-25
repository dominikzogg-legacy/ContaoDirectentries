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
            // start time tracking
            $floatStartTime = microtime(true);

            // add css
            $strContent = $this->_addCSS('system/modules/directentries/html/directentries.css', $strContent);

            // get config
            $arrInactiveDirectEntries = isset($GLOBALS['TL_CONFIG']['inactiveDirectEntries']) && is_array(unserialize($GLOBALS['TL_CONFIG']['inactiveDirectEntries'])) ? unserialize($GLOBALS['TL_CONFIG']['inactiveDirectEntries']) : array();

            // foreach backend navigation group
            foreach(self::getDirectEntries() as $strGroupAndNavigationKey => $strNavigationName)
            {
                // check if disabled
                if(!in_array($strGroupAndNavigationKey, $arrInactiveDirectEntries))
                {
                    // explode group and navigation key
                    $arrGroupAndNavigationKey = explode('_', $strGroupAndNavigationKey);

                    // build method name
                    $strMethodName = '_prepare' . ucfirst($arrGroupAndNavigationKey[1]) . 'Array';

                    // build array
                    $arrNavigationElement = $this->$strMethodName();

                    // html to add
                    $strToAddHtml = $this->_buildHtml($arrNavigationElement);

                    // add html to content
                    $strContent = $this->_addContent($arrGroupAndNavigationKey[0], $arrGroupAndNavigationKey[1], $strToAddHtml, $strContent);
                }
            }
            // stop time tracking
            $floatStopTime = microtime(true);

            // check time
            //die($floatStopTime - $floatStartTime);
        }
        // return rendered template
        return($strContent);
    }

    /**
     * _prepareThemesArray
     * @return boolean|array the theme array
     */
    protected function _prepareThemesArray()
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
            $objPages = $this->Database->query("SELECT * FROM tl_page WHERE type = 'root' ORDER BY title");

            // prepare array return
            $arrReturn = array();

            // set counter
            $intCounter = 1;

            // do this foreach page
            while($objPages->next())
            {
                // check page permission
                if($this->User->isAdmin || $this->User->isAllowed(1, $objPages->row()))
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
            }
            // show only if there is more than one page
            if(count($arrReturn) > 1)
            {
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
     * @return string html to add
     */
    protected function _buildHtml($arrPreparedArray)
    {
        // empty to add sting
        $strToAdd = '';

        // if the input is an array
        if(is_array($arrPreparedArray))
        {
            // list
            $strToAdd .= '<ul class="tl_level_3">';

            //foreach list element
            foreach($arrPreparedArray as $arrListElement)
            {
                // list element
                $strToAdd .= '<li>';

                // check for icons
                if(isset($arrListElement['icons']) && is_array($arrListElement['icons']))
                {
                    // foreach icon
                    foreach($arrListElement['icons'] as $arrTitleAndUrl)
                    {
                        // add icon link
                        $strToAdd .= '<a title="' . $arrTitleAndUrl['title'] . '" href="' . $arrTitleAndUrl['url'] . '">';

                        // add icon
                        $strToAdd .= '<img src="system/themes/default/images/' . $arrTitleAndUrl['icon'] . '.gif" alt="' . $arrTitleAndUrl['title'] . '" />';

                        // add icon link close
                        $strToAdd .= '</a>';
                    }
                }
                // check for name
                if(isset($arrListElement['name']) && is_array($arrListElement['name']))
                {
                    // add name link
                    $strToAdd .= '<a title="' . $arrListElement['name']['title'] . '" href="' . $arrListElement['name']['url'] . '">' . $arrListElement['name']['link'] . '</a>';
                }
                // list element close
                $strToAdd .= '</li>';
            }
            // list close
            $strToAdd .= '</ul>';
        }
        // return
        return($strToAdd);
    }

    /**
     * _addCSS
     * @param string $strCssPath the path to the css
     * @param string $strContent content
     * @return string modified content
     */
    protected function _addCSS($strCssPath, $strContent)
    {
        // css link line
        $strCSSLinkLine = '<link rel="stylesheet" href="' . $strCssPath . '" media="all">';

        // add css to head
        $strContent = preg_replace('/\<\/head\>/', "{$strCSSLinkLine}\n</head>", $strContent);

        // return modified content
        return($strContent);
    }

    /**
     * _addContent
     * @param string $strToGroup for example design
     * @param string $strToElement for example themes
     * @param string $strToAdd html to add
     * @param string $strContent content
     * @return string modified content
     */
    protected function _addContent($strToGroup, $strToElement, $strToAddHtml, $strContent)
    {
        // regular expression pattern
        $strPattern = '/(id\="tl_navigation"[\s\S]*?id\="' . $strToGroup . '"[\s\S]*?class\=".*?' . $strToElement . '.*?a\>)/';

        // add html after thw wished link
        $strContent = preg_replace($strPattern, '${1}' . $strToAddHtml, $strContent);

        // return modified content
        return($strContent);
    }

    /**
     * getDirectEntries
     * @return array the directentries
     */
    public static function getDirectEntries()
    {
        return
        (
            array
            (
                'design_themes'  => &$GLOBALS['TL_LANG']['MOD']['themes'][0],
                'design_page' => &$GLOBALS['TL_LANG']['MOD']['page'][0],
            )
        );
    }

}