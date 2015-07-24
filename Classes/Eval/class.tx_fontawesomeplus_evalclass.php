<?php

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

/**
 * Evaluation of field tx_fontawesomeplus_classname
 * removes all bumbers, unserscores
 *
 *
 * @package fontawesomeplus
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
class tx_fontawesomeplus_evalclass {

    /**
     * @return string
     */
    function returnFieldJS() {
        return '
        var theVal = "" + value;
	    theVal = theVal.replace(/\s+/, "-");
	    theVal = theVal.replace(/[^a-zA-Z0-9-_]/, "");
        return theVal;';
	}

    /**
     * @param $value
     * @param $is_in
     * @param $set
     * @return string
     */
    function evaluateFieldValue($value, $is_in, &$set) {
	    $searchArray = array ('/\s+/','/[^a-zA-Z0-9-_]/');
	    $replaceArray = array ('-', '');
		$value = preg_replace($searchArray,$replaceArray, $value);
		return $value;
	}
}