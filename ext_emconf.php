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
	'title' => 'Font Awesome +',
    'description' => 'extend "Font Awesome - The iconic font and CSS toolkit" with your own icons and make the new font available in Frontend',
	'category' => 'module',
	'version' => '0.2.0',
	'state' => 'alpha',
	'uploadfolder' => true,
	'createDirs' => 'uploads/tx_fontawesomeplus',
	'clearCacheOnLoad' => true,
	'author' => 'Alexander Kontos',
	'author_email' => 'info@netweiser.com',
	'author_company' => 'netweiser',
	'constraints' => array (
		'depends' => array (
			'typo3' => '6.2.10-7.99.99',
		),
		'conflicts' => array (
		),
		'suggests' => array (
		),
	),
);
