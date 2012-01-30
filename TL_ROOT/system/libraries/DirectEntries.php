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

class DirectEntries
{
    /**
     * content
     * @var string the content
     */
    protected $_strContent = '';

    /**
     * csspath
     * @var string path to the css
     */
    protected $_strCssPath = 'system/modules/directentries/html/directentries.css';

    /**
     * directentries
     * @var array multidimension array with all directentries
     */
    protected $_arrDirectEntries = array();

    /**
     * instance
     * @var object  current object instance (Singleton)
     */
    protected static $objInstance;

    /**
     * Return the current object instance (Singleton)
     * @return Session
     */
    public static function getInstance()
    {
        if (!is_object(self::$objInstance))
        {
            self::$objInstance = new self();
        }

        return self::$objInstance;
    }

    /**
     * addDirectEntry
     * @param $strGroup the group name
     * @param $strItem the item name
     * @param $arrDirectEntry the direct entry array
     */
    public function addDirectEntry($strGroup, $strItem, $arrDirectEntry)
    {
        $this->_arrDirectEntries[$strGroup][$strItem] = $arrDirectEntry;
    }

    /**
     * getDirectEntriesForSettings
     * @return array
     */
    public function getDirectEntriesForSettings()
    {
        // prepare return array
        $arrReturn = array();

        // groups
        foreach($this->_arrDirectEntries as $strGroup => $arrGroup)
        {
            // items
            if(is_array($arrGroup))
            {
                // item
                foreach($arrGroup as $strItem => $arrDirectEntry)
                {
                    // add to return array
                    $arrReturn["{$strGroup}_{$strItem}"] =  &$GLOBALS['TL_LANG']['MOD'][$strItem][0];
                }
            }
        }
        // return the array
        return($arrReturn);
    }

    /**
     * inject
     * @param str $strContent rendered template
     * @param str $strTemplate template name
     * @return str rendered template
     */
    public function inject($strContent, $strTemplate)
    {
        // only do something in be_main template
        if($strTemplate != 'be_main')
        {
            return($strContent);
        }

        // start time tracking
        $floatStartTime = microtime(true);

        // fill content
        $this->_strContent = $strContent;

        // add css
        $this->_addCSS();

        // get config
        $arrInactiveDirectEntries = isset($GLOBALS['TL_CONFIG']['inactiveDirectEntries']) && is_array(unserialize($GLOBALS['TL_CONFIG']['inactiveDirectEntries'])) ? unserialize($GLOBALS['TL_CONFIG']['inactiveDirectEntries']) : array();

        // foreach directentry
        foreach($this->_arrDirectEntries as $strGroup => $arrGroup)
        {
            // if there are items
            if(is_array($arrGroup))
            {
                // item
                foreach($arrGroup as $strItem => $arrDirectEntry)
                {
                    // check if disabled
                    if(!in_array("{$strGroup}_{$strItem}", $arrInactiveDirectEntries))
                    {
                        // prepare directentry string
                        $strPreparedDirectEntry = $this->_prepareDirectEntryString($arrDirectEntry);

                        // add directentry
                        $this->_addContent($strGroup, $strItem, $strPreparedDirectEntry);
                    }
                }
            }
        }

        // stop time tracking
        $floatStopTime = microtime(true);

        // check time
        //die($floatStopTime - $floatStartTime);

        // return prepared content
        return($this->_strContent);
    }

    /**
     * _prepareDirectEntryString
     * @param array $arrDirectEntry direct entry array
     * @return string html to add
     */
    protected function _prepareDirectEntryString($arrDirectEntry)
    {
        // empty to add sting
        $strToAdd = '';

        // if the input is an array
        if(is_array($arrDirectEntry))
        {
            // list
            $strToAdd .= '<ul class="tl_level_3">';

            //foreach list element
            foreach($arrDirectEntry as $arrListElement)
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
     * resetCSSPath
     * @param string $strNewPath an alternate path to the css to load
     */
    public function resetCSSPath($strNewPath)
    {
        $this->_strCssPath = $strNewPath;
    }

    /**
     * _addCSS
     */
    protected function _addCSS()
    {
        // css link line
        $strCSSLinkLine = '<link rel="stylesheet" href="' . $this->_strCssPath . '" media="all">';

        // add css to head
        $this->_strContent = preg_replace('/\<\/head\>/', "{$strCSSLinkLine}\n</head>", $this->_strContent);
    }

    /**
     * _addContent
     * @param string $strGroup for example design
     * @param string $strItem for example themes
     * @param string $strPreparedDirectEntry html to add
     */
    protected function _addContent($strGroup, $strItem, $strPreparedDirectEntry)
    {
        // regular expression pattern
        $strPattern = '/(id\="tl_navigation"[\s\S]*?id\="' . $strGroup . '"[\s\S]*?class\=".*?' . $strItem . '.*?a\>)/';

        // add html after thw wished link
        $this->_strContent = preg_replace($strPattern, '${1}' . $strPreparedDirectEntry, $this->_strContent);
    }
}