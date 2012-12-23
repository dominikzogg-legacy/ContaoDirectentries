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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'DominikZogg\DirectEntries',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'DominikZogg\DirectEntries\DirectEntryArticle'      => 'system/modules/directentries/classes/DirectEntryArticle.php',
    'DominikZogg\DirectEntries\DirectEntryCalendar'     => 'system/modules/directentries/classes/DirectEntryCalendar.php',
    'DominikZogg\DirectEntries\DirectEntryFaq'          => 'system/modules/directentries/classes/DirectEntryFaq.php',
    'DominikZogg\DirectEntries\DirectEntryForm'         => 'system/modules/directentries/classes/DirectEntryForm.php',
    'DominikZogg\DirectEntries\DirectEntryNews'         => 'system/modules/directentries/classes/DirectEntryNews.php',
    'DominikZogg\DirectEntries\DirectEntryNewsletter'   => 'system/modules/directentries/classes/DirectEntryNewsletter.php',
    'DominikZogg\DirectEntries\DirectEntryPage'         => 'system/modules/directentries/classes/DirectEntryPage.php',
    'DominikZogg\DirectEntries\DirectEntryThemes'       => 'system/modules/directentries/classes/DirectEntryThemes.php',

    // Library
    'DominikZogg\DirectEntries\DirectEntries'       => 'system/modules/directentries/library/DirectEntries.php',
));