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

class DirectEntryNews extends Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // check if the news module is active
        if(in_array('news', $this->Config->getActiveModules()))
        {
            // load backend user
            $this->import('BackendUser', 'User');

            // check permission
            if($this->User->isAdmin || ($this->User->hasAccess('news', 'modules') && isset($this->User->news) && is_array($this->User->news)))
            {
                // load database
                $this->import("Database");

                // get all existing root pages
                $objNews = $this->Database->query("SELECT * FROM tl_news_archive ORDER BY title");

                // there are at minimumone archiv
                if($objNews->numRows)
                {
                    // prepare directentry array
                    $arrDirectEntry = array();

                    // set counter
                    $intCounter = 1;

                    // do this foreach page
                    while($objNews->next())
                    {
                        // check page permission
                        if($this->User->isAdmin || in_array($objNews->id, $this->User->news))
                        {
                            // set the icon url and title
                            $arrDirectEntry[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=news&table=tl_news&id=' . $objNews->id;
                            $arrDirectEntry[$intCounter]['icons']['page']['title'] = 'news';
                            $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'news';

                            // set the page url and title
                            $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=news&table=tl_news&id=' . $objNews->id;
                            $arrDirectEntry[$intCounter]['name']['title'] = $objNews->title;
                            $arrDirectEntry[$intCounter]['name']['link'] = strlen($objNews->title) > 17 ? substr($objNews->title, 0, 15) . '...' : $objNews->title;

                            // add one to counter
                            $intCounter++;
                        }
                    }
                    // add to direcentries service
                    $this->import('DirectEntries');
                    $this->DirectEntries->addDirectEntry('content', 'news', $arrDirectEntry);
                }
            }
        }
    }
}