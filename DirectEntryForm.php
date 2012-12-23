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

class DirectEntryForm extends Backend
{
    /**
     * prepare
     */
    public function prepare()
    {
        // load backend user
        $this->import('BackendUser', 'User');

        // check permission
        if($this->User->isAdmin || ($this->User->hasAccess('form', 'modules') && isset($this->User->forms) && is_array($this->User->forms)))
        {
            // load database
            $this->import("Database");

            // get all forms
            $objForm = $this->Database->query("SELECT * FROM tl_form ORDER BY title");

            // there are at minimum one form
            if($objForm->numRows)
            {
                // prepare directentry array
                $arrDirectEntry = array();

                // set counter
                $intCounter = 1;

                // do this foreach page
                while($objForm->next())
                {
                    // check page permission
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