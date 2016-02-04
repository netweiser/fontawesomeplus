<?php
namespace Netweiser\Fontawesomeplus\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Styles Utility
 *
 * @package TYPO3
 * @subpackage tx_fontawesomeplus
 */
class Styles {

	const FACONTRIB = 'font-awesome-4.5.0';
	const FACSS = 'font-awesome.css';
	const LESS_CORE = 'core.less';
	const LESS_ICONS = 'icons.less';
	const LESS_MIXINS = 'mixins.less';
	const LESS_PATH = 'path.less';
	const LESS_VARIABLES = 'variables.less';
	const SCSS_CORE = '_core.scss';
	const SCSS_ICONS = '_icons.scss';
	const SCSS_MIXINS = '_mixins.scss';
	const SCSS_PATH = '_path.scss';
	const SCSS_VARIABLES = '_variables.scss';

	/**
	 * create CSS
	 *
	 * @param int $fontUid
	 * @param object $currentFont
	 * @return void
	*/
	static public function createCss($fontUid,$currentFont) {

		$fontName = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));
		$originalPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Public/Contrib/' . self::FACONTRIB;
		$destinationPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $fontName;

		// CSS section
		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/css/');
		copy($originalPath . '/css/' . self::FACSS , $destinationPath . '/css/' . str_replace('font-awesome',$fontName,self::FACSS ));
		$stringContent = GeneralUtility::getUrl($destinationPath . '/css/' . $fontName .'.css');
		$renderCss = static::replaceStrings($fontName,$currentFont,$stringContent);
		$newIconsArray = static::buildIconsArray($fontUid);
		foreach ($newIconsArray as $newIcon) {
			$renderCss .= '.fa-'. $newIcon['class'] .':before {'. LF . '   content: "\\' . $newIcon['unicode'] . '";' . LF . '}'. LF;
		}
		GeneralUtility::writeFile($destinationPath . '/css/' . $fontName .'.css', $renderCss);
	}

	/**
	 * create LESS
	 *
	 * @param int $fontUid
	 * @param object $currentFont
	 * @return void
	*/
	static public function createLess($fontUid,$currentFont) {

		$fontName = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));
		$originalPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Public/Contrib/' . self::FACONTRIB;
		$destinationPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $fontName . '/less/';
		$newIconsArray = static::buildIconsArray($fontUid);
		$fontComment = 	'License)' . LF .
			' *  ' . LF .
			' *  Extended with TYPO3 EXT:fontawesomeplus <info@netweiser.com>' . LF .
			' *  ' . $fontName . ' Version: ' . $currentFont->getVersion() . LF .
			' *';

		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/less/');
		$lessFiles = GeneralUtility::getFilesInDir($originalPath . '/less/','less');
		foreach ($lessFiles as $lessFile) {
			copy($originalPath . '/less/' . $lessFile, $destinationPath . str_replace('font-awesome',$fontName,$lessFile));
		}
		$renderLess = str_replace('License)',$fontComment,GeneralUtility::getUrl($destinationPath . $fontName .'.less'));
		GeneralUtility::writeFile($destinationPath . $fontName .'.less', $renderLess);

		$coreLess = str_replace('FontAwesome',ucwords($fontName),GeneralUtility::getUrl($destinationPath . self::LESS_CORE));
		GeneralUtility::writeFile($destinationPath . self::LESS_CORE, $coreLess);

		$iconsLess = GeneralUtility::getUrl($destinationPath . self::LESS_ICONS);

		foreach ($newIconsArray as $newIcon) {
			$iconsLess .= '.@{fa-css-prefix}-'. $newIcon['class'] .':before { content: @fa-var-' . $newIcon['class'] . '; }'. LF;
		}
		GeneralUtility::writeFile($destinationPath . self::LESS_ICONS, $iconsLess);

		$mixinsLess = str_replace('FontAwesome',ucwords($fontName),GeneralUtility::getUrl($destinationPath . self::LESS_MIXINS));
		GeneralUtility::writeFile($destinationPath . self::LESS_MIXINS, $mixinsLess);

		$pathLess = static::replaceStrings($fontName,$currentFont,GeneralUtility::getUrl($destinationPath . self::LESS_PATH));
		GeneralUtility::writeFile($destinationPath . self::LESS_PATH, $pathLess);

		$variablesLess = GeneralUtility::getUrl($destinationPath . self::LESS_VARIABLES);
		foreach ($newIconsArray as $newIcon) {
			$variablesLess .= '@fa-var-' . $newIcon['class'] .': "\\' . $newIcon['unicode'] . '";'. LF;
		}
		GeneralUtility::writeFile($destinationPath . self::LESS_VARIABLES, $variablesLess);
	}


	/**
	 * create SCSS
	 *
	 * @param int $fontUid
	 * @param object $currentFont
	 * @return void
	*/
	static public function createScss($fontUid,$currentFont) {

		$fontName = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));
		$originalPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Public/Contrib/' . self::FACONTRIB;
		$destinationPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $fontName . '/scss/';
		$newIconsArray = static::buildIconsArray($fontUid);
		$fontComment = 	'License)' . LF .
			' *  ' . LF .
			' *  Extended with TYPO3 EXT:fontawesomeplus <info@netweiser.com>' . LF .
			' *  ' . $fontName . ' Version: ' . $currentFont->getVersion() . LF .
			' *';


		GeneralUtility::mkdir_deep(PATH_site . 'fileadmin/' . $currentFont->getDestination(), $fontName . '/scss/');
		$scssFiles = GeneralUtility::getFilesInDir($originalPath . '/scss/','scss');
		foreach ($scssFiles as $scssFile) {
			copy($originalPath . '/scss/' . $scssFile, $destinationPath . str_replace('font-awesome',$fontName,$scssFile));
		}
		$renderScss = str_replace('License)',$fontComment,GeneralUtility::getUrl($destinationPath . $fontName .'.scss'));
		GeneralUtility::writeFile($destinationPath . $fontName .'.scss', $renderScss);

		$coreScss = str_replace('FontAwesome',ucwords($fontName),GeneralUtility::getUrl($destinationPath . self::SCSS_CORE));
		GeneralUtility::writeFile($destinationPath . self::SCSS_CORE, $coreScss);

		$iconsScss = GeneralUtility::getUrl($destinationPath . self::SCSS_ICONS);
		foreach ($newIconsArray as $newIcon) {
			$iconsScss .= '.#{$fa-css-prefix}-'. $newIcon['class'] .':before { content: $fa-var-' . $newIcon['class'] . '; }'. LF;
		}
		GeneralUtility::writeFile($destinationPath . self::SCSS_ICONS, $iconsScss);

		$mixinsScss = str_replace('FontAwesome',ucwords($fontName),GeneralUtility::getUrl($destinationPath . self::SCSS_MIXINS));
		GeneralUtility::writeFile($destinationPath . self::SCSS_MIXINS, $mixinsScss);

		$pathScss = static::replaceStrings($fontName,$currentFont,GeneralUtility::getUrl($destinationPath . self::SCSS_PATH));
		GeneralUtility::writeFile($destinationPath . self::SCSS_PATH, $pathScss);

		$variablesScss = GeneralUtility::getUrl($destinationPath . self::SCSS_VARIABLES);
		foreach ($newIconsArray as $newIcon) {
			$variablesScss .= '$fa-var-' . $newIcon['class'] .': "\\' . $newIcon['unicode'] . '";'. LF;
		}
		GeneralUtility::writeFile($destinationPath . self::SCSS_VARIABLES, $variablesScss);
	}

	/**
	 * Replace with new values
	 *
	 * @param string $stringContent
	 * @param object $currentFont
	 * @return string
	*/
	protected function replaceStrings($fontName,$currentFont,$stringContent) {
		$fontComment = 	'License)' . LF .
			' *  ' . LF .
			' *  Extended with TYPO3 EXT:fontawesomeplus <info@netweiser.com>' . LF .
			' *  ' . $fontName . ' Version: ' . $currentFont->getVersion() . LF .
			' *';

		$searchArray = array('fontawesome-webfont', 'FontAwesome','fontawesomeregular','License)','v=4.5.0');

		$replaceArray = array($fontName, ucwords($fontName), $fontName . 'regular',$fontComment,'v=' . $currentFont->getVersion());

		$newContent = str_replace($searchArray,$replaceArray,$stringContent);

		return $newContent;
	}

	/**
	 * Build a Icon Array
	 *
	 * @param int $fontUid
	 * @return array
	*/
	protected function buildIconsArray($fontUid) {
		/**
		 * @var array $buildIconsArray
		*/
		$buildIconsArray = array();

		// Create array from all Classnames and SVG's
		$iconRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
		$iconReferences = $iconRepository->findByRelation('tx_fontawesomeplus_domain_model_font', 'icons', $fontUid);
		foreach ($iconReferences as $key => $value) {
			$buildIconsArray[$key]['class'] = $value->getReferenceProperty('tx_fontawesomeplus_classname');
		}
		//start with unicode f296 fontAwesome version 4.5 first free in private use area
		$i = hexdec('0xf296');
		foreach ($buildIconsArray as $key => $value) {
			$buildIconsArray[$key]['unicode'] = dechex($i);
			$i++;
		}

		return $buildIconsArray;
	}
}

