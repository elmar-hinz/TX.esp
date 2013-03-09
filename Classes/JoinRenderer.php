<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2012 Elmar Hinz <elmar.hinz@gmail.com>
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
 * @package	TYPO3
 * @subpackage	tx_esp
 */
class tx_esp_JoinRenderer {

	public $cObj;
	private $configuration;
	private $levelStack;
	private $currentLevel;
	private $tableName;
	private $out;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->init($conf);
		$this->initLevelStack();
		$array = &$this->loadTable();
		$this->renderTable(&$array);
		$this->wrapOutput();
		return $this->getOutput();
	}

	// Public accessos, some only for testing
	public function getConfiguration() { return $this->configuration; } 
	public function getLevelStack() { return $this->levelStack; }
	public function getCurrentLevel() { return $this->currentLevel; }
	public function getTableName() { return $this->tableName; }
	public function setOutput($out) { $this->out = $out; }
	public function getOutput() { return $this->out; }

	//////////////////////////////////////////////////	
	// Workers
	//////////////////////////////////////////////////	

	public function init($conf) {
		$this->configuration = $conf['userFunc.'];
		$this->tableName = $this->cObj->data['tableName'];
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	public function initLevelStack() {
		$this->currentLevel = 1;
		$this->levelStack = $this->configuration['levels.'];
	}

	public function loadTable() {
		return $this->db->exec_SELECTgetRows('*', $this->getTableName(), '');
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
		return $this->out = $out;
	}

	public function wrapOutput() {
		$this->out = $this->cObj->stdWrap($this->out, $this->configuration['stdWrap.']); 
	}

	//////////////////////////////////////////////////	
	// Helpers configuration stack
	//////////////////////////////////////////////////	

	// ---------
	// $keys = t3lib_TStemplate::sortedKeyList($this->configurationStack, TRUE);
	// ---------

	public function goDownLevelStack() {
		return ++$this->currentLevel;
	}

	public function goUpLevelStack() {
		return --$this->currentLevel;
	}

	public function getCurrentFields() {
		$fieldList = $this->levelStack[$this->currentLevel.'.']['levelFields'];
		return t3lib_div::trimExplode(',', $fieldList);
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
			unset($row[$field]);
		}
		return array($row, (array) $attributes);
	}

	public function wrapGroup($sublevelContent, $stdWrapConf, $levelAttributes) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($levelAttributes);
		return $cObj->stdWrap($sublevelContent, $stdWrapConf); 
	}

}

?>

