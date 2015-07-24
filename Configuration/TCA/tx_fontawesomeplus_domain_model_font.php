<?php
defined('TYPO3_MODE') or die();

$lang = 'LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_tca.xlf:';

$tx_fontawesomeplus_domain_model_font = array(
	'ctrl' => array(
		'title' => $lang . 'tx_fontawesomeplus_domain_model_font',
		'label' => 'title',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
		'hideAtCopy' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'editlock' => 'editlock',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'dividers2tabs' => TRUE,
		'default_sortby' => 'ORDER BY title DESC',		
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('fontawesomeplus') . 'Resources/Public/Icons/fontawesomeplus_domain_model_font.gif',
		'searchFields' => 'uid,title,description',
	),
	'interface' => array(
		'showRecordFieldList' => 'pid,uid,title,description,version,icons'
	),
	'columns' => array(
		'pid' => array(
			'label' => 'pid',
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'tstamp' => array(
			'label' => 'tstamp',
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'crdate' => array(
			'label' => 'crdate',
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'cruser_id' => array(
			'label' => 'cruser_id',
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'title' => array(
			'exclude' => 0,
			'label' => $lang . 'tx_fontawesomeplus_domain_model_font.title',
			'config' => array(
				'type' => 'input',
				'eval' => 'trim,required',
			)
		),
		'version' => array (
			'exclude' => 1,
			'label'  => $lang . 'tx_fontawesomeplus_domain_model_font.version',
			'config' => array (
				'type' => 'input',
				'eval' => 'double2',
			)
		),
		'description' => array (
			'exclude' => 1,
			'label'   => $lang . 'tx_fontawesomeplus_domain_model_font.description',
			'config'  => array (
				'type' => 'text',
				'rows' => 5,
				'cols' => 48
			)
		),	
		'destination' => array (
			'exclude' => 1,
			'label'   => $lang . 'tx_fontawesomeplus_domain_model_font.destination',
			'config'  => array (
				'type' => 'input',
				'eval' => 'trim,required',
				'max'  => '255',
			)
		),	
		'icons' => array(
			'exclude' => 1,
			'label' => $lang . 'tx_fontawesomeplus_domain_model_font.icon',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('icons', array(
				'appearance' => array(
					'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
				),
				'foreign_types' => array(
					\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
						'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;fontawesomeplusIcon,
							--palette--;;filePalette'
					),
				),
				'minitems' => 1,
			), 'svg')
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => '	--palette--;'. $lang . 'tx_fontawesomeplus_domain_model_font.palettes.basic;Basic,
						--div--;'. $lang . 'tx_fontawesomeplus_domain_model_font.tabs.iconset,
							--palette--;'. $lang . 'tx_fontawesomeplus_domain_model_font.palettes.icons;Icons,'
		),
	),
	'palettes' => array(
		'Basic' => array(
			'showitem' => '	title,--linebreak--,
							version,--linebreak--,
							description,--linebreak--,
							destination,--linebreak--,',
			'canNotCollapse' => TRUE,
		),
		'Icons' => array(
			'showitem' => '	icons,--linebreak--,',
			'canNotCollapse' => TRUE,
		),
	)
);
return $tx_fontawesomeplus_domain_model_font;