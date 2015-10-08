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

class DirectEntryNews extends \Backend
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
                // check if table exists
                if (!$this->Database->tableExists('tl_news')) return;

                // get all news archives
                $objNews = \NewsArchiveModel::findAll(array('order' => 'title'));

                // there are at minimum one archiv
                if(!is_null($objNews) && $objNews->count())
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