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
    protected $_strCssPath = 'system/modules/directentries/assets/directentries.css';

    /**
     * directentries
     * @var array multidimension array with all directentries
     */
    protected $_arrDirectEntries = array();

    /**
     * @var DirectEntries
     */
    protected static $objInstance;

    /**
     * Return the current object instance (Singleton)
     * @return DirectEntries
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
                        $strToAdd .= '<a title="' . $arrTitleAndUrl['title'] . '" href="' . $arrTitleAndUrl['url'] . '&amp;rt=' . REQUEST_TOKEN . '">';

                        // add icon if there is no path given
                        if(strpos($arrTitleAndUrl['icon'], '/') === false)
                        {
                            $strToAdd .= '<img src="system/themes/default/images/' . $arrTitleAndUrl['icon'] . '.gif" alt="' . $arrTitleAndUrl['title'] . '" />';
                        }
                        // add image with full path
                        else
                        {
                            $strToAdd .= '<img src="' . $arrTitleAndUrl['icon'] . '" alt="' . $arrTitleAndUrl['title'] . '" />';
                        }

                        // add icon link close
                        $strToAdd .= '</a>';
                    }
                }
                // check for name
                if(isset($arrListElement['name']) && is_array($arrListElement['name']))
                {
                    // add name link
                    $strToAdd .= '<a title="' . $arrListElement['name']['title'] . '" href="' . $arrListElement['name']['url'] . '&amp;rt=' . REQUEST_TOKEN . '">' . $arrListElement['name']['link'] . '</a>';
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
