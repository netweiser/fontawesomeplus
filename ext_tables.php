<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE === 'BE') {
	/**********************************************************************************************
	* Register BE-Module for Administration
	**********************************************************************************************/
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Netweiser.' .$_EXTKEY,
		'file',
		'tx_fontawesomeplus_m1',
		'',
		array(
			'Administration' => 'index,newFont,editFont,createFont,viewZipfiles,deleteZipfile,viewIcons,addIcon,testPythonModules',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:fontawesomeplus/Resources/Public/Icons/fontawesomeplus_module_administration.png',
			'labels' => 'LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf',
			'navigationComponentId' => 'typo3-pagetree'
		)
	);
	
	/**********************************************************************************************
	* Table Configuration
	**********************************************************************************************/
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_fontawesomeplus_domain_model_font');
}