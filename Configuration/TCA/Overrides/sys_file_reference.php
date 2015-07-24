<?php
$sysfilereference = array(
	'columns' => array(
		'tx_fontawesomeplus_classname' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:fontawesomeplus/locallang_tca.xlf:sys_file_reference.classname',
			'config' => array(
				'type' => 'input',
				'eval' => 'tx_fontawesomeplus_evalclass,lower,required'
			)
		),
	),
	'palettes' => array(
		'fontawesomeplusIcon' => array(
			'showitem' => 'tx_fontawesomeplus_classname,--linebreak--,',
			'canNotCollapse' => TRUE
		),
	)
);
$GLOBALS['TCA']['sys_file_reference'] = array_replace_recursive($GLOBALS['TCA']['sys_file_reference'], $sysfilereference);