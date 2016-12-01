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

class DirectEntryNewsletter extends \Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // check if the newsletter module is active
        if(in_array('newsletter', $this->Config->getActiveModules()))
        {
            // load backend user
            $this->import('BackendUser', 'User');

            // check permission
            if($this->User->isAdmin || ($this->User->hasAccess('newsletter', 'modules') && isset($this->User->newsletters) && is_array($this->User->newsletters)))
            {
                // check if table exists
                if (!$this->Database->tableExists('tl_newsletter')) return;

                // get all newsletter channels
                $objNewsletter = \NewsletterChannelModel::findAll(array('order' => 'title'));

                // there are at minimum one newletter
                if(!is_null($objNewsletter) && $objNewsletter->count())
                {
                    // prepare directentry array
                    $arrDirectEntry = array();

                    // set counter
                    $intCounter = 1;

                    // do this foreach page
                    while($objNewsletter->next())
                    {
                        // check page permission
                        if($this->User->isAdmin || in_array($objNewsletter->id, $this->User->newsletters))
                        {
                            // set the icon url and title
                            $arrDirectEntry[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=newsletter&table=tl_newsletter&id=' . $objNewsletter->id;
                            $arrDirectEntry[$intCounter]['icons']['page']['title'] = 'newsletter';
                            $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'system/modules/newsletter/assets/icon.gif';

                            // set the page url and title
                            $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=newsletter&table=tl_newsletter&id=' . $objNewsletter->id;
                            $arrDirectEntry[$intCounter]['name']['title'] = $objNewsletter->title;
                            $arrDirectEntry[$intCounter]['name']['link'] = strlen($objNewsletter->title) > 17 ? substr($objNewsletter->title, 0, 15) . '...' : $objNewsletter->title;

                            // add one to counter
                            $intCounter++;
                        }
                    }
                    // add to direcentries service
                    $this->import('DirectEntries');
                    $this->DirectEntries->addDirectEntry('content', 'newsletter', $arrDirectEntry);
                }
            }
        }
    }
}