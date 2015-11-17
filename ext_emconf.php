<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "fontawesomeplus".
 *
 * netweiser.com - your way to the internet <info@netweiser.com>
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Font Awesome Plus',
    'description' => 'extend "Font Awesome - The iconic font and CSS toolkit" with your own icons and create an own font with all necessary files',
	'category' => 'module',
	'version' => '1.0.0',
	'state' => 'stable',
	'uploadfolder' => true,
	'createDirs' => 'uploads/tx_fontawesomeplus',
	'clearCacheOnLoad' => true,
	'author' => 'Alexander Kontos',
	'author_email' => 'info@netweiser.com',
	'author_company' => 'netweiser',
	'constraints' => array (
		'depends' => array (
			'typo3' => '7.2.0-7.99.99',
		)
	),
);
