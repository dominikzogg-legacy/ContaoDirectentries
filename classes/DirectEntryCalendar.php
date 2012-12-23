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

class DirectEntryCalendar extends \Backend
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
                // get all existing root pages
                $objCalendar = \CalendarModel::findAll(array('order' => 'title'));

                // there are at minimumone archiv
                if(!is_null($objCalendar) && $objCalendar->count())
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
                            $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'system/modules/calendar/assets/icon.gif';

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