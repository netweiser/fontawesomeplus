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

use Netweiser\Fontawesomeplus\Utility\Font;
use Netweiser\Fontawesomeplus\Utility\Styles;
use Netweiser\Fontawesomeplus\Utility\Zip;
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
	 * Inject the font repository
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
		$currentPageID = $this->pageUid;
		$fontRecordsAvailable = $this->fontRepository->findAllInPid($currentPageID)->count();

		if (!empty($fontRecordsAvailable)) {
			$assignedValues = [
				'page' => $currentPageID,
				'fonts' => $this->fontRepository->findAllInPid($currentPageID),
				'countfonts' => $fontRecordsAvailable
			];
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
	 * Create a font
	 *
	 * @return void
	 */
	public function createFontAction() {
		$fontUid = $this->request->getArgument('uid');
		$currentFont = $this->fontRepository->findByUid($fontUid);
		$feedback = Font::createFont($fontUid,$currentFont);
		Styles::createCss($fontUid,$currentFont);
		Styles::createLess($fontUid,$currentFont);
		Styles::createScss($fontUid,$currentFont);
		//$this->createCss($this->request->getArgument('uid'));

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
			Zip::createZip($currentFont);
			$this->addFlashMessage(
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.creatingFont.body', 'fontawesomeplus'),
				LocalizationUtility::translate('LLL:EXT:fontawesomeplus/Resources/Private/Language/locallang_be_module.xlf:ok.creatingFont.title', 'fontawesomeplus'),
				FlashMessage::OK
			);
			$this->forward('viewZipfiles');
		}
	}
	
	/**
	 * Redirect to form to edit a font record
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
		$currentPageID = $this->pageUid;
		$fontUid = $this->request->getArgument('uid');

		$assignedValues = array(
			'page' => $currentPageID,
			'fonts' => $this->fontRepository->findAllInPid($currentPageID),
			'countfonts' => $this->fontRepository->findAllInPid($currentPageID)->count(),
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
		$currentPageID = $this->pageUid;
		$fontUid = $this->request->getArgument('uid');
		$currentFont = $this->fontRepository->findByUid($fontUid);
		$filelist = GeneralUtility::getFilesInDir(PATH_site . 'fileadmin/' . $currentFont->getDestination(),'zip');
		$fontWebPath = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'fileadmin/' . $currentFont->getDestination();

		$assignedValues = array(
			'page' => $currentPageID,
			'fontWebpath' => $fontWebPath,
			'fonts' => $this->fontRepository->findAllInPid($currentPageID),
			'countfonts' => $this->fontRepository->findAllInPid($currentPageID)->count(),
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
		$currentFont = $this->fontRepository->findByUid($this->request->getArgument('uid'));
		$filename = $this->request->getArgument('filename');
		$result = Zip::deleteZip($currentFont,$filename);

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
		$pathToPythonBin = escapeshellarg($extConf['pathToPython']);
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
	 * Redirect to tceform creating a new record
	 *
	 * @param string $table table name
	 * @return void
	 */
	private function redirectToCreateNewRecord($table) {
		$currentPageID = $this->pageUid;
		
		$returnUrl = 'mod.php?M=file_FontawesomeplusTxFontawesomeplusM1&id=' . $currentPageID . $this->getToken();
		$url = BackendUtility::getModuleUrl('record_edit', array(
			'edit[' . $table . '][' . $currentPageID . ']' => 'new',
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
		$currentPageID = $this->pageUid;
		$uid =  $this->request->getArgument('uid');

		$returnUrl = 'mod.php?M=file_FontawesomeplusTxFontawesomeplusM1&id=' . $currentPageID . $this->getToken();
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