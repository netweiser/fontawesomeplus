<?php
namespace Netweiser\Fontawesomeplus\Domain\Repository;

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
 
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Font Repository
 *
 * @package fontawesomeplus
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
 
class FontRepository extends Repository {
	
	/**
	 * Find fonts by a given pid
	 *
	 * @param integer $pid pid
	 * @return QueryInterface
	 */
	public function findAllInPid($pid) {
		
		$query = $this->createQuery();
		$and = $query->equals('pid', $pid);
		$constraint = $query->logicalAnd($and);
		$query->matching($constraint);
		return $query->execute();
	}
}