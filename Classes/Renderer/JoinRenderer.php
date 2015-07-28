<?php

namespace ElmarHinz\Esp\Renderer;

/***************************************************************
*  Copyright notice
*
*  (c) 2012 - 2015 Elmar Hinz <elmar.hinz@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * Renderer for joined table queries
 *
 * Groups the data table by table into a hierarchy
 *
 * @author	Elmar Hinz <elmar.hinz@gmail.com>
 */
class JoinRenderer extends AbstractRenderer {

	private $levelStack;
	private $currentLevel;

	// Public accessos, some only for testing
	public function getLevelStack() { return $this->levelStack; }
	public function getCurrentLevel() { return $this->currentLevel; }

	//////////////////////////////////////////////////	
	// Workers
	//////////////////////////////////////////////////	

	public function render() {
		$this->initLevelStack();
		$array = array();
		while($row = $this->getResultIterator()->fetchAssociative()) $array[] = $row;
		$out = $this->renderTable($array);
		$this->setOutput($out);
	}

	public function initLevelStack() {
		$this->currentLevel = 1;
		$configuration = $this->getConfiguration();
		$this->levelStack = $configuration['levels.'];
	}

	public function renderTable($ungroupedArray) {
		$groupedArray = $this->groupLevel($ungroupedArray, $this->getCurrentFields());
		foreach($groupedArray as $group) {
			$this->goDownLevelStack();
			if($this->currentLevelExists()) 
				$sub = $this->renderTable($group['entries']);
			else
				$sub = '';
			$this->goUpLevelStack();
			$out .= $this->wrapGroup($sub, $this->getCurrentStdWrap(), $group['attributes']);
		}
		return $out;
	}

	//////////////////////////////////////////////////	
	// Helpers configuration stack
	//////////////////////////////////////////////////	

	public function goDownLevelStack() {
		return ++$this->currentLevel;
	}

	public function goUpLevelStack() {
		return --$this->currentLevel;
	}

	public function getCurrentFields() {
		$fieldList = $this->levelStack[$this->currentLevel.'.']['levelFields'];
		return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $fieldList);
	}

	public function getCurrentStdWrap() {
		return $this->levelStack[$this->currentLevel.'.']['stdWrap.'];
	}

	public function currentLevelExists() {
		return isset($this->levelStack[$this->currentLevel.'.']);
	}

	//////////////////////////////////////////////////	
	// Helpers
	//////////////////////////////////////////////////	

	public function groupLevel($rawArray, $fields) {
		$array = array();
		foreach($rawArray as $row) {
			list($rest, $attributes) = $this->prepRow($row, $fields);
			if(count($array) > 0 && $attributes == $array[count($array)- 1]['attributes']) {
				$array[count($array)- 1]['entries'][] = $rest;
			} else {
				$array[] = array (
					'attributes' => $attributes,
					'entries' => array($rest),
				);
			}
		}
		return $array;
	}

	private function prepRow($row, $fields) {
		foreach($fields as $field) {
			$attributes[$field] = $row[$field];
		}
		return array($row, (array) $attributes);
	}

	public function wrapGroup($sublevelContent, $stdWrapConf, $levelAttributes) {
		$cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_cObj');
		$cObj->start($levelAttributes);
		return $cObj->stdWrap($sublevelContent, $stdWrapConf); 
	}

}

?>

