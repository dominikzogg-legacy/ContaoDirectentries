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

class DirectEntryFaq extends \Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // check if the faq module is active
        if(in_array('faq', $this->Config->getActiveModules()))
        {
            // load backend user
            $this->import('BackendUser', 'User');

            // check permission
            if($this->User->isAdmin || ($this->User->hasAccess('faq', 'modules') && isset($this->User->faqs) && is_array($this->User->faqs)))
            {
                // load database
                $this->import("Database");

                // get all existing root pages
                $objFaq = $this->Database->query("SELECT * FROM tl_faq_category ORDER BY title");

                // there are at minimumone archiv
                if($objFaq->numRows)
                {
                    // prepare directentry array
                    $arrDirectEntry = array();

                    // set counter
                    $intCounter = 1;

                    // do this foreach page
                    while($objFaq->next())
                    {
                        // check page permission
                        if($this->User->isAdmin || in_array($objFaq->id, $this->User->faqs))
                        {
                            // set the icon url and title
                            $arrDirectEntry[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=faq&table=tl_faq&id=' . $objFaq->id;
                            $arrDirectEntry[$intCounter]['icons']['page']['title'] = 'faq';
                            $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'system/modules/faq/html/icon.gif';

                            // set the page url and title
                            $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=faq&table=tl_faq&id=' . $objFaq->id;
                            $arrDirectEntry[$intCounter]['name']['title'] = $objFaq->title;
                            $arrDirectEntry[$intCounter]['name']['link'] = strlen($objFaq->title) > 17 ? substr($objFaq->title, 0, 15) . '...' : $objFaq->title;

                            // add one to counter
                            $intCounter++;
                        }
                    }
                    // add to direcentries service
                    $this->import('DirectEntries');
                    $this->DirectEntries->addDirectEntry('content', 'faq', $arrDirectEntry);
                }
            }
        }
    }
}