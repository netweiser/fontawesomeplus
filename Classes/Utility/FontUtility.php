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
use TYPO3\CMS\Core\Utility\CommandUtility;

/**
 * Font Utility
 *
 * @package TYPO3
 * @subpackage tx_fontawesomeplus
 */
class Font {

    /**
     * create Font via Python with FontForge
     *
     * @param int $fontUid
     * @param object $currentFont
     * @return array
     */
    public static function createFont($fontUid,$currentFont) {
        // general vars
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fontawesomeplus']);
        $pathToPythonBin = escapeshellarg($extConf['pathToPython']);
        $pathToScript = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fontawesomeplus') . 'Resources/Private/Python/fontawesomeplus.py';

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


}