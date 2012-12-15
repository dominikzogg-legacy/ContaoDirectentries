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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] . ';{directentries_legend:hide},inactiveDirectEntries';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['inactiveDirectEntries'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings_directentries']['inactiveDirectEntries'],
    'inputType'               => 'checkbox',
    'options_callback'        => array('DirectEntries', 'getDirectEntriesForSettings'),
    'eval'                    => array('multiple'=>true)
);