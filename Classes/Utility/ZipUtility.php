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
 * Zip File Utility
 *
 * @package TYPO3
 * @subpackage tx_fontawesomeplus
 */
class Zip {

    /**
     * create zip from created font
     *
     * @param object $currentFont
     * @return string
     */
    public static function createZip($currentFont) {
        // general vars

        $cleanTitle = strtolower(preg_replace(array ('/\s+/','/[^a-zA-Z0-9]/'), array ('-', ''), $currentFont->getTitle()));

        $fontPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $cleanTitle .'/';

        $fileName = $destinationPath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $cleanTitle . '_' . $currentFont->getVersion() . '_' . date('YmdHi', $GLOBALS['EXEC_TIME']) .'.zip';

        $zip = new \ZipArchive();
        $zip->open($fileName, \ZipArchive::CREATE);

        // Get all the files of the extension, but exclude the ones specified in the excludePattern
        $files = GeneralUtility::getAllFilesAndFoldersInPath(
            array(),		// No files pre-added
            $fontPath,		// Start from here
            '',				// Do not filter files by extension
            TRUE,			// Include subdirectories
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
     * delete zip file
     *
     * @param object $currentFont
     * @return bool $result
     */
    public static function deleteZip($currentFont,$filename) {
        $filePath = PATH_site . 'fileadmin/' . $currentFont->getDestination() . $filename;
        $result = unlink($filePath);

        return $result;
    }
}