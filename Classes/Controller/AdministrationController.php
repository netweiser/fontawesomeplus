<?php
namespace Netweiser\Fontawesomeplus\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alexander Kontos <info@netweiser.com>
 *  	netweiser - your way to the internet!
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Administration controller
 *
 * @package TYPO3
 * @subpackage tx_fontawesomeplus
 */
class AdministrationController  extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * @var int Current page id
	 */
	protected $pageUid = 0;

	/**
	 * @var int fontUid
	 */
	protected $fontUid = 0;
	
	/**
	 * @var \Netweiser\Fontawesomeplus\Domain\Repository\FontRepository
	 * @return void
	 */
	protected $fontRepository;
	
	/**
	 * count addresses on current page
	 *
	 * @var int
	 */
	protected $countFontRecordsOnCurrentPage;
	
	/**
	 * create zip
	 *
	 * @return string
	 */
	protected $createZip;
	
	/**
	 * create css of font 
	 *
	 * @return void
	 */
	protected $createCss;
	
	/**
	 * Inject the addressbook repository
	 *
	 * @param \Netweiser\Fontawesomeplus\Domain\Repository\FontRepository $fontRepository
	 */
	public function injectFontRepository(\Netweiser\Fontawesomeplus\Domain\Repository\FontRepository $fontRepository) {
		$this->fontRepository = $fontRepository;
	}
	
	/**
	 * init all actions
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->pageUid = (int)GeneralUtility::_GET('id');
	}
	
	/**
	 * Function will be called when 'show Adressbooks' is selected, on start of the extension and as fallback if nothing is selected
	 *
	 * @return void
	 */
	public function indexAction() {
		$fontRecordsAvailable = $this->countFontRecordsOnCurrentPage();
		if (!empty($fontRecordsAvailable)) {
			$assignedValues = array(
				'page' => $this->pageUid,
				'fonts' => $this->fontRepository->findAllInPid($this->pageUid),
				'countfonts' => $fontRecordsAvailable,
			);
			$this->view->assignMultiple($assignedValues);
		} else {
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.noFont.body', 'fontawesomeplus'),
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.noFont.title', 'fontawesomeplus'),
				FlashMessage::ERROR
			);
		}		
	}
	
	/**
	 * Redirect to form to create a font record
	 *
	 * @return void
	 */
	public function newFontAction() {
		$this->redirectToCreateNewRecord('tx_fontawesomeplus_domain_model_font');
	}
	
	/**
	 * Redirect to form to create a font record
	 *
	 * @return void
	 */
	public function createFontAction() {
		$fontRecordsAvailable = $this->countFontRecordsOnCurrentPage();
		$feedback = $this->createFont($this->request->getArgument('uid'));
		$this->createCss($this->request->getArgument('uid'));
		if (!empty($feedback)) {
			$errorMessage = \TYPO3\CMS\Core\Utility\ArrayUtility::arrayExport($feedback);
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.creatingFont.body', 'fontawesomeplus') .'<br>'.
				$errorMessage,
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.creatingFont.title', 'fontawesomeplus'),
				FlashMessage::ERROR
			);
			$this->forward('viewZipfiles');
		} else {
			$zipFile = $this->createZip($this->request->getArgument('uid'));
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.creatingFont.body', 'fontawesomeplus'),
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.creatingFont.title', 'fontawesomeplus'),
				FlashMessage::OK
			);
			$this->forward('viewZipfiles');
		}
	}
	
	/**
	 * Redirect to form to create a font record
	 *
	 * @return void
	 */
	public function editFontAction() {
		$this->redirectToEditRecord('tx_fontawesomeplus_domain_model_font');
	}
	
	/**
	 * Show all attached Icons to font
	 *
	 * @return void
	 */
	public function viewIconsAction() {
		$fontRecordsAvailable = $this->countFontRecordsOnCurrentPage();
		$pid = $this->pageUid;
		$fontUid = $this->request->getArgument('uid');
		
		$assignedValues = array(
			'page' => $this->pageUid,
			'fonts' => $this->fontRepository->findAllInPid($this->pageUid),
			'countfonts' => $fontRecordsAvailable,
			'activeFontUid' => $fontUid,
			'activeFont' => $this->fontRepository->findByUid($fontUid),
		);
		
		$this->view->assignMultiple($assignedValues);
	}
	
	/**
	 * Show all created ZIP Archives of this font
	 *
	 * @return void
	 */
	public function viewZipfilesAction() {
		$fontRecordsAvailable = $this->countFontRecordsOnCurrentPage();
		$fontUid = $this->request->getArgument('uid');
		$currentFont = $this->fontRepository->findByUid($fontUid);
		$fontPath = PATH_site . 'fileadmin/' . $currentFont->getDestination();
		$filelist = $lessFiles = GeneralUtility::getFilesInDir($fontPath,'zip');
		$fontWebPath = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'fileadmin/' . $currentFont->getDestination();
		$assignedValues = array(
			'page' => $this->pageUid,
			'fontWebpath' => $fontWebPath,
			'fonts' => $this->fontRepository->findAllInPid($this->pageUid),
			'countfonts' => $fontRecordsAvailable,
			'activeFontUid' => $fontUid,
			'activeFont' => $this->fontRepository->findByUid($fontUid),
			'zipFiles' => $filelist,
		);
		
		$this->view->assignMultiple($assignedValues);
	}
	
	/**
	 * Delete selected ZIP Archive
	 *
	 * @return void
	 */
	public function deleteZipfileAction() {		
		$fontUid = $this->request->getArgument('uid');
		$currentFont = $this->fontRepository->findByUid($fontUid);
		$filePath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $this->request->getArgument('filename');
		$result = unlink($filePath);
		
		if ($result === FALSE) {
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.deleteZip.body', 'fontawesomeplus'),
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.deleteZip.title', 'fontawesomeplus'),
				FlashMessage::ERROR
			);
			$this->forward('viewZipfiles');
		} else {
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.deleteZip.body', 'fontawesomeplus'),
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.deleteZip.title', 'fontawesomeplus'),
				FlashMessage::OK
			);
			$this->forward('viewZipfiles');
		}
	}
	
	/**
	 * Test if all necessary Modules in Python are available
	 *
	 * @return void
	 */
	public function testPythonModulesAction() {
		// Extension Configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fontawesomeplus']);
		$pathToPythonBin = $extConf['pathToPython'];
		$pathToScript = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Private/Python/test_environment.py';
		
		CommandUtility::exec("$pathToPythonBin $pathToScript 2>&1", $pythonReturnArray, $returnCode);

		$countAnswer = count($pythonReturnArray);

		if ($countAnswer < 3) {
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.pythonVersion.body', 'fontawesomeplus'),
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.pythonVersion.title', 'fontawesomeplus'),
				FlashMessage::ERROR
			);
		} else {
			$fontforgeModule = (int)$pythonReturnArray[0];
			$jsonModule = (int)$pythonReturnArray[1];
			$pythonVersion = $pythonReturnArray[2];

			$this->addFlashMessage(
				$pythonVersion . ' ' . $pythonReturnArray[1],
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.pythonVersion.title', 'fontawesomeplus'),
				FlashMessage::OK
			);

			if (!empty($fontforgeModule)) {
				$this->addFlashMessage(
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.pythonModuleFontforge.body', 'fontawesomeplus'),
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.pythonModuleFontforge.title', 'fontawesomeplus'),
					FlashMessage::OK
				);
			} else {
				$this->addFlashMessage(
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.pythonModuleFontforge.body', 'fontawesomeplus'),
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.pythonModuleFontforge.title', 'fontawesomeplus'),
					FlashMessage::ERROR
				);

			}
			if (!empty($jsonModule)) {
				$this->addFlashMessage(
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.pythonModuleJson.body', 'fontawesomeplus'),
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.pythonModuleJson.title', 'fontawesomeplus'),
					FlashMessage::OK
				);
			} else {
				$this->addFlashMessage(
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:error.pythonModuleJson.body', 'fontawesomeplus'),
					LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:message.pythonModuleJson.title', 'fontawesomeplus'),
					FlashMessage::ERROR
				);
			}
		}
		$this->forward('index');
	}
	
	/**
	 * count Addressbooks on current page
	 *
	 * @return int
	 */
	private function countFontRecordsOnCurrentPage() {
		$pageUid = $this->pageUid;
		/* @var $db \TYPO3\CMS\Core\Database\DatabaseConnection */
		$db = $GLOBALS['TYPO3_DB'];
		$countFonts = $db->exec_SELECTcountRows(
			'*',
			'tx_fontawesomeplus_domain_model_font',
			'deleted= 0 AND pid=' . $pageUid . \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('tx_fontawesomeplus_domain_model_font')
		);
		return $countFonts;
	}
	
	/**
	 * create Font via Python with FontForge
	 *
	 * @return array
	 */
	protected function createFont($fontUid) {
		// general vars
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fontawesomeplus']);
		$pathToPythonBin = $extConf['pathToPython'];
		$pathToScript = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Private/Python/fontawesomeplus.py';
		$currentFont = $this->fontRepository->findByUid($fontUid);		
		$iconRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
		
		$iconReferences = $iconRepository->findByRelation('tx_fontawesomeplus_domain_model_font', 'icons', $fontUid);
		$svgArray = array();		
		foreach ($iconReferences as $key => $value) {
			$svgArray[$key] = PATH_site . 'fileadmin' . $value->getIdentifier();
		}
		$unicodeArray = array();
		$i = hexdec('0xf23b');
		foreach ($svgArray as $key => $value) {
			$unicodeArray[$key] = $i .',uni'. dechex($i);
			$i++;
		}
		$fontPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Public/Contrib/font-awesome-4.3.0/fonts/fontawesome-webfont.svg';
		$fontForgeArray = CommandUtility::escapeShellArgument(json_encode(array_combine($svgArray, $unicodeArray),JSON_UNESCAPED_SLASHES));
		$fontName = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));
		$comment = CommandUtility::escapeShellArgument(str_replace(array("\r\n", "\n", "\r"), ' ', $currentFont->getDescription()));
		$copyright = CommandUtility::escapeShellArgument('netweiser');
		$version = CommandUtility::escapeShellArgument($currentFont->getVersion());
		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/fonts/');
		$savedir = PATH_site . 'fileadmin/' . $currentFont->getDestination() .$fontName . '/fonts/';
		CommandUtility::exec("$pathToPythonBin $pathToScript $fontPath $fontForgeArray $fontName $comment $copyright $version $savedir 2>&1", $feedback, $returnCode);
		if ((int)$returnCode !== 0) {
			return $feedback;
		}		
	}
	
	/**
	 * create css and scss and less
	 *
	 * @return void
	 */
	protected function createCss($fontUid) {
		// General vars
		$newIconsArray = array();
		$fontAwesomeContrib = 'font-awesome-4.3.0';
		$currentFont = $this->fontRepository->findByUid($fontUid);
		$fontName = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));
		$iconRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
		// Path
		$originalPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Public/Contrib/' . $fontAwesomeContrib;
		$destinationPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $fontName;
		$fontComment = 	'License)' . LF . 
						' *  ' . LF . 
						' *  Extended with TYPO3 EXT:fontawesomeplus <info@netweiser.com>' . LF . 
						' *  ' . $fontName . ' Version: ' . $currentFont->getVersion() . LF .
						' *';
		$searchArray = array('fontawesome-webfont', 'FontAwesome','fontawesomeregular','License)','v=4.3.0');
		$replaceArray = array($fontName, ucwords($fontName), $fontName . 'regular',$fontComment,'v=' . $currentFont->getVersion());
		// Files that need attention
		$originalFACss = 'font-awesome.css';
		$originalCoreLess = 'core.less';
		$originalIconsLess = 'icons.less';
		$originalMixinsLess = 'mixins.less';
		$originalPathLess = 'path.less';
		$originalVariablesLess = 'variables.less';
		$originalCoreScss = '_core.scss';
		$originalIconsScss = '_icons.scss';
		$originalMixinsScss = '_mixins.scss';
		$originalPathScss = '_path.scss';
		$originalVariablesScss = '_variables.scss';
		
		// Create array from all Classnames and SVG's
		$iconReferences = $iconRepository->findByRelation('tx_fontawesomeplus_domain_model_font', 'icons', $fontUid);
		foreach ($iconReferences as $key => $value) {
			$newIconsArray[$key]['class'] = $value->getReferenceProperty('tx_fontawesomeplus_classname');
		}		
		//start with unicode f23b; fontAwesome version 4.3 first free in private use area
		$i = hexdec('0xf23b');
		foreach ($newIconsArray as $key => $value) {
			$newIconsArray[$key]['unicode'] = dechex($i);
			$i++;
		}

		// CSS section
		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/css/');
		copy($originalPath . '/css/' . $originalFACss , $destinationPath . '/css/' . str_replace('font-awesome',$fontName,$originalFACss ));
		$renderCss = GeneralUtility::getUrl($destinationPath . '/css/' . $fontName .'.css');
		$renderCss = str_replace($searchArray,$replaceArray,$renderCss);
		foreach ($newIconsArray as $newIcon) {
			$renderCss .= '.fa-'. $newIcon['class'] .':before {'. LF . '   content: "\\' . $newIcon['unicode'] . '";' . LF . '}'. LF;
		}
		GeneralUtility::writeFile($destinationPath . '/css/' . $fontName .'.css', $renderCss);
		
		// LESS section
		$destinationLessPath = $destinationPath . '/less/';
		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/less/');
		$lessFiles = GeneralUtility::getFilesInDir($originalPath . '/less/','less');
		foreach ($lessFiles as $lessFile) {
			copy($originalPath . '/less/' . $lessFile, $destinationLessPath . str_replace('font-awesome',$fontName,$lessFile));
		}
		$renderLess = str_replace('License)',$fontComment,GeneralUtility::getUrl($destinationLessPath . $fontName .'.less'));
		GeneralUtility::writeFile($destinationLessPath . $fontName .'.less', $renderLess);		
		
		$coreLess = str_replace($searchArray[1],ucwords($fontName),GeneralUtility::getUrl($destinationLessPath . $originalCoreLess));
		GeneralUtility::writeFile($destinationLessPath . $originalCoreLess, $coreLess);
				
		$iconsLess = GeneralUtility::getUrl($destinationLessPath . $originalIconsLess);
		foreach ($newIconsArray as $newIcon) {
			$iconsLess .= '.@{fa-css-prefix}-'. $newIcon['class'] .':before { content: @fa-var-' . $newIcon['class'] . '; }'. LF;
		}
		GeneralUtility::writeFile($destinationLessPath . $originalIconsLess, $iconsLess);
		
		$mixinsLess = str_replace($searchArray[1],ucwords($fontName),GeneralUtility::getUrl($destinationLessPath . $originalMixinsLess));
		GeneralUtility::writeFile($destinationLessPath . $originalMixinsLess, $mixinsLess);
		
		$pathLess = str_replace($searchArray,$replaceArray,GeneralUtility::getUrl($destinationLessPath . $originalPathLess));
		GeneralUtility::writeFile($destinationLessPath . $originalPathLess, $pathLess);
				
		$variablesLess = GeneralUtility::getUrl($destinationLessPath . $originalVariablesLess);
		foreach ($newIconsArray as $newIcon) {
			$variablesLess .= '@fa-var-' . $newIcon['class'] .': "\\' . $newIcon['unicode'] . '";'. LF;
		}
		GeneralUtility::writeFile($destinationLessPath . $originalVariablesLess, $variablesLess);
				
		// SCSS section
		$destinationScssPath = $destinationPath . '/scss/';
		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/scss/');
		$scssFiles = GeneralUtility::getFilesInDir($originalPath . '/scss/','scss');
		foreach ($scssFiles as $scssFile) {
			copy($originalPath . '/scss/' . $scssFile, $destinationScssPath . str_replace('font-awesome',$fontName,$scssFile));
		}
		$renderScss = str_replace('License)',$fontComment,GeneralUtility::getUrl($destinationScssPath . $fontName .'.scss'));
		GeneralUtility::writeFile($destinationScssPath . $fontName .'.scss', $renderScss);		
		
		$coreScss = str_replace($searchArray[1],ucwords($fontName),GeneralUtility::getUrl($destinationScssPath . $originalCoreScss));
		GeneralUtility::writeFile($destinationScssPath . $originalCoreScss, $coreScss);
		
		$iconsScss = GeneralUtility::getUrl($destinationScssPath . $originalIconsScss);
		foreach ($newIconsArray as $newIcon) {
			$iconsScss .= '.#{fa-css-prefix}-'. $newIcon['class'] .':before { content: $fa-var-' . $newIcon['class'] . '; }'. LF;
		}
		GeneralUtility::writeFile($destinationScssPath . $originalIconsScss, $iconsScss);
		
		$mixinsScss = str_replace('FontAwesome',ucwords($fontName),GeneralUtility::getUrl($destinationScssPath . $originalMixinsScss));
		GeneralUtility::writeFile($destinationScssPath . $originalMixinsScss, $mixinsScss);

		$pathScss = str_replace($searchArray,$replaceArray,GeneralUtility::getUrl($destinationScssPath . $originalPathScss));
		GeneralUtility::writeFile($destinationScssPath . $originalPathScss, $pathScss);
		
		$variablesScss = GeneralUtility::getUrl($destinationScssPath . $originalVariablesScss);
		foreach ($newIconsArray as $newIcon) {
			$variablesScss .= '$fa-var-' . $newIcon['class'] .': "\\' . $newIcon['unicode'] . '";'. LF;
		}
		GeneralUtility::writeFile($destinationScssPath . $originalVariablesScss, $variablesScss);
	}
	
	/**
	 * create zip from created font
	 *
	 * @return string
	 */
	protected function createZip($fontUid) {
		// general vars
		$currentFont = $this->fontRepository->findByUid($fontUid);
		$cleanTitle = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));

		$fontPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $cleanTitle .'/';

		$fileName = $destinationPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $cleanTitle . '_' . $currentFont->getVersion() . '_' . date('YmdHi', $GLOBALS['EXEC_TIME']) .'.zip';
		
		$zip = new \ZipArchive();
		$zip->open($fileName, \ZipArchive::CREATE);

		// Get all the files of the extension, but exclude the ones specified in the excludePattern
		$files = GeneralUtility::getAllFilesAndFoldersInPath(
			array(),			// No files pre-added
			$fontPath,		// Start from here
			'',					// Do not filter files by extension
			TRUE,				// Include subdirectories
			PHP_INT_MAX		// Recursion level
		);
		

		// Make paths relative to extension root directory.
		$files = GeneralUtility::removePrefixPathFromList($files, $fontPath);

		// Remove the one empty path that is the extension dir itself.
		$files = array_filter($files);

		foreach ($files as $file) {
			$fullPath = $fontPath . $file;
			// Distinguish between files and directories, as creation of the archive
			// fails on Windows when trying to add a directory with "addFile".
			if (is_dir($fullPath)) {
				$zip->addEmptyDir($file);
			} else {
				$zip->addFile($fullPath, $file);
			}
		}

		$zip->close();
		return $fileName;
	}
	
	/**
	 * Redirect to tceform creating a new record
	 *
	 * @param string $table table name
	 * @return void
	 */
	private function redirectToCreateNewRecord($table) {
		$pid = $this->pageUid;
		
		$returnUrl = 'mod.php?M=file_FontawesomeplusTxFontawesomeplusM1&id=' . $this->pageUid . $this->getToken();
		$url = BackendUtility::getModuleUrl('record_edit', array(
			'edit[' . $table . '][' . $pid . ']' => 'new',
			'returnUrl' => $returnUrl
		));

		HttpUtility::redirect($url);
	}
		
	/**
	 * Redirect to tceform editing record
	 *
	 * @param string $table table name
	 * @return void
	 */
	private function redirectToEditRecord($table) {
		$pid = $this->pageUid;
		$uid =  $this->request->getArgument('uid');

		$returnUrl = 'mod.php?M=file_FontawesomeplusTxFontawesomeplusM1&id=' . $this->pageUid . $this->getToken();
		$url = BackendUtility::getModuleUrl('record_edit', array(
			'edit[' . $table . '][' . $uid . ']' => 'edit',
			'returnUrl' => $returnUrl
		));
		HttpUtility::redirect($url);
	}
		
	/**
	 * Get a CSRF token
	 *
	 * @param bool $tokenOnly Set it to TRUE to get only the token, otherwise including the &moduleToken= as prefix
	 * @return string
	 */
	protected function getToken($tokenOnly = FALSE) {
		$token = \TYPO3\CMS\Core\FormProtection\FormProtectionFactory::get()->generateToken('moduleCall', 'file_FontawesomeplusTxFontawesomeplusM1');
		if ($tokenOnly) {
			return $token;
		} else {
			return '&moduleToken=' . $token;
		}
	}
}