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

class DirectEntryForm extends \Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // load backend user
        $this->import('BackendUser', 'User');

        // check permission
        if($this->User->isAdmin || ($this->User->hasAccess('form', 'modules') && isset($this->User->forms) && is_array($this->User->forms)))
        {
            // check if table exists
            if (!$this->Database->tableExists('tl_form')) return;

            // get all forms
            $objForm = \FormModel::findAll(array('order' => 'title'));

            // there are at minimum one form
            if(!is_null($objForm) && $objForm->count())
            {
                // prepare directentry array
                $arrDirectEntry = array();

                // set counter
                $intCounter = 1;

                // do this foreach page
                while($objForm->next())
                {
                    // check page permission
                    if($this->User->isAdmin || in_array($objForm->id, $this->User->forms))
                    {
                        // set the icon url and title
                        $arrDirectEntry[$intCounter]['icons']['page']['url'] = 'contao/main.php?do=form&table=tl_form_field&id=' . $objForm->id;
                        $arrDirectEntry[$intCounter]['icons']['page']['title'] = 'form';
                        $arrDirectEntry[$intCounter]['icons']['page']['icon'] = 'form';

                        // set the page url and title
                        $arrDirectEntry[$intCounter]['name']['url'] = 'contao/main.php?do=form&table=tl_form_field&id=' . $objForm->id;
                        $arrDirectEntry[$intCounter]['name']['title'] = $objForm->title;
                        $arrDirectEntry[$intCounter]['name']['link'] = strlen($objForm->title) > 17 ? substr($objForm->title, 0, 15) . '...' : $objForm->title;

                        // add one to counter
                        $intCounter++;
                    }
                }
                // add to direcentries service
                $this->import('DirectEntries');
                $this->DirectEntries->addDirectEntry('content', 'form', $arrDirectEntry);
            }
        }
    }
}