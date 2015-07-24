<?php
namespace Netweiser\Fontawesomeplus\Domain\Model;

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
 * Font Model
 *
 * @package fontawesomeplus
 * @license http://www.gnu.org/licenses/lgpl.html
 * 			GNU Lesser General Public License, version 3 or later
 */
 
 class Font extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	/**
	 * @var \DateTime
	 */
	protected $tstamp;
	
	/**
	 * @var \DateTime
	 */
	protected $crdate;

	/**
	 * @var bool
	 */
	protected $hidden;
	
	/**
	 * @var string
	 */
	protected $title;
	
	/**
	 * @var double
	 */
	protected $version;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $destination;
	
	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
	 */
	protected $icons;
	
	
	/**
	 * Initialize categories and media relation
	 *
	 * @return \Netweiser\Fontawesomeplus\Domain\Model\Font
	 */
	public function __construct() {
		$this->icons = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Get timestamp
	 *
	 * @return integer
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * Set time stamp
	 *
	 * @param integer $tstamp time stamp
	 * @return void
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
	}
	
	/**
	 * Get creation date
	 *
	 * @return integer
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * Set creation date
	 *
	 * @param integer $crdate
	 * @return void
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
	}
		
	/**
	 * @param bool $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @return bool
	 */
	public function getHidden() {
		return $this->hidden;
	}
		
	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set title
	 *
	 * @param string $title title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * Set version of font
	 *
	 * @param double $version version
	 * @return void
	 */
	public function setVersion($version) {
		$this->version = (double)$version;
	}

	/**
	 * Get version of font
	 *
	 * @return double
	 */
	public function getVersion() {
		$version = $this->version;
		$doubleversion = number_format((float)$version, 2, '.', '');
		return $doubleversion;
	}
	
	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set description
	 *
	 * @param string $description description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Get destination
	 *
	 * @return string
	 */
	public function getDestination() {
		return $this->destination;
	}

	/**
	 * Set destination
	 *
	 * @param string $destination destination
	 * @return void
	 */
	public function setDestination($destination) {
		$this->destination = $destination;
	}
	
	/**
	 * Get the Fal media item
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	public function getIcons() {
		return $this->icons;
	}

	/**
	 * Set Fal media relation
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $Icons
	 * @return void
	 */
	public function setIcons(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $icons) {
		$this->icons = $icons;
	}
	
	/**
	 * Add a Fal media file reference
	 *
	 * @param FileReference $falMedia
	 */
	public function addIcons(FileReference $icons) {
		if ($this->getIcons() === NULL) {
			$this->icons = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		}
		$this->icons->attach($icons);
	}
}