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

class DirectEntryPage extends \Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // load backend user
        $this->import('BackendUser', 'User');

        // check permission
        if($this->User->isAdmin || ($this->User->hasAccess('page', 'modules') && isset($this->User->pagemounts) && is_array($this->User->pagemounts)))
        {
            // load database
            $this->import("Database");

            // get all existing root pages
            $objPages = $this->Database->query("SELECT * FROM tl_page WHERE type = 'root' ORDER BY title");

            // there are at minimum two pages
            if($objPages->numRows > 1)
            {
                // prepare directentry array
                $arrDirectEntry = array();

                // set counter
                $intCounter = 1;

                // do this foreach page
                while($objPages->next())
                {
                    // check page permission
                    if($this->User->isAdmin || in_array($objPages->id, $this->User->pagemounts))
                    {
                        // set the icon url and title
                        $arrDirectEntry[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=page&node=' . $objPages->id;
                        $arrDirectEntry[$intCounter]['icons']['page']['title'] = 'page';
                        $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'page';

                        // set the page url and title
                        $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=page&node=' . $objPages->id;
                        $arrDirectEntry[$intCounter]['name']['title'] = $objPages->title;
                        $arrDirectEntry[$intCounter]['name']['link'] = strlen($objPages->title) > 17 ? substr($objPages->title, 0, 15) . '...' : $objPages->title;

                        // add one to counter
                        $intCounter++;
                    }
                }
                // add to direcentries service
                $this->import('DirectEntries');
                $this->DirectEntries->addDirectEntry('design', 'page', $arrDirectEntry);
            }
        }
    }
}