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

class DirectEntryCalendar extends Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // check if the calendar module is active
        if(in_array('calendar', $this->Config->getActiveModules()))
        {
            // load backend user
            $this->import('BackendUser', 'User');

            // check permission
            if($this->User->isAdmin || ($this->User->hasAccess('calendar', 'modules') && isset($this->User->calendars) && is_array($this->User->calendars)))
            {
                // load database
                $this->import("Database");

                // get all existing root pages
                $objCalendar = $this->Database->query("SELECT id,title FROM tl_calendar ORDER BY title");

                // there are at minimumone archiv
                if($objCalendar->numRows)
                {
                    // prepare directentry array
                    $arrDirectEntry = array();

                    // set counter
                    $intCounter = 1;

                    // do this foreach page
                    while($objCalendar->next())
                    {
                        // check page permission
                        if($this->User->isAdmin || in_array($objCalendar->id, $this->User->calendars))
                        {
                            // set the icon url and title
                            $arrDirectEntry[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=calendar&table=tl_calendar_events&id=' . $objCalendar->id;
                            $arrDirectEntry[$intCounter]['icons']['page']['title'] = 'calendar';
                            $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'system/modules/calendar/html/icon.gif';

                            // set the page url and title
                            $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=calendar&table=tl_calendar_events&id=' . $objCalendar->id;
                            $arrDirectEntry[$intCounter]['name']['title'] = $objCalendar->title;
                            $arrDirectEntry[$intCounter]['name']['link'] = strlen($objCalendar->title) > 17 ? substr($objCalendar->title, 0, 15) . '...' : $objCalendar->title;

                            // add one to counter
                            $intCounter++;
                        }
                    }
                    // add to direcentries service
                    $this->import('DirectEntries');
                    $this->DirectEntries->addDirectEntry('content', 'calendar', $arrDirectEntry);
                }
            }
        }
    }
}