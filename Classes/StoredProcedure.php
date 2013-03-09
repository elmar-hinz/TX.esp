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
 * Plugin 'Stored Procedure' for the 'esp' extension.
 *
 * @author	Elmar Hinz <elmar.hinz@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_esp
 */
class tx_esp_StoredProcedure {

	public $cObj;
	private $configuration;
	private $storedProcedure;
	private $randomTableName;
	private $db;
	private $parameters = array();
	private $procedureArgumentsList = array();
	private $setArgumentQuery = '';
	private $argumentResult = NULL;
	private $tableResult = NULL;
	private $output = '';

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->init($conf);
		$this->orderAndWrapParameters();
		$this->prependRandomTableToParameters();
		$this->prepareParametersForQuery();
		$this->callStoredProcedure();
		$this->fetchArgumentResult();
		$this->processArgumentResult();
		$this->setUpTCA();
		$this->renderResult();
		$this->dropResultTable();
		$this->wrapOutput();
		return $this->output;
	}

	// Public accessros for testing
	public function getConfiguration() { return $this->configuration; }
	public function getStoredProcedure() { return $this->storedProcedure; }
	public function getDB() { return $this->db; }
	public function getParameters() { return $this->parameters; }
	public function getRandomTableName() { return $this->randomTableName; }
	public function getProcedureArgumentsList() { return $this->procedureArgumentsList; }
	public function getSetArgumentQuery() {return $this->setArgumentQuery; }
	public function getArgumentResult() { return $this->argumentResult; }
	public function getOutput() { return $this->output; }

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$conf: ...
	 * @return	[type]		...
	 */
	function init($conf) {
		$this->configuration = $conf['userFunc.'];
		$this->storedProcedure = $this->makeStdWrap($this->configuration, 'storedProcedure');
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$configuration: ...
	 * @param	[type]		$configurationKey: ...
	 * @return	[type]		...
	 */
	private function makeStdWrap($configuration, $configurationKey) {
		$value = $configuration[$configurationKey];
		$conf = $configuration[$configurationKey.'.'];
		return $this->cObj->stdWrap((string)$value, $conf);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function orderAndWrapParameters() {
		$keys = t3lib_div::trimExplode(',', $this->configuration['parameterOrder']);
		$keys = array_combine($keys, $keys);
		$this->parameters = array_map(array($this, 'wrapParameter'), $keys);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$key: ...
	 * @return	[type]		...
	 */
	private function wrapParameter($key) {
		$parameters = $this->configuration['parameters.'];
		return $this->makeStdWrap($parameters, $key);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function prependRandomTableToParameters() {
		$this->randomTableName = 'static_' . $this->storedProcedure .'_' . rand(0,9999999999);
		$this->parameters = array('tableName' => $this->randomTableName) + $this->parameters;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function prepareParametersForQuery() {
		$procedureArguments = array();
		foreach($this->parameters as $key => $value) {
			$this->setArgumentQuery[] = 'SET @'.$key.'=\''.$value.'\'; ';
			$procedureArguments[] = '@'.$key;
		}
		$this->procedureArgumentsList = implode(', ', $procedureArguments);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function callStoredProcedure() {
		// TODO optimize into batched call
		array_walk($this->setArgumentQuery, array($this->db, 'sql_query'));
		$call = 'CALL '.$this->storedProcedure.' ('.$this->procedureArgumentsList.');';
		assert($this->db->sql_query($call));
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function fetchArgumentResult() {
		$this->argumentResult = $this->db->sql_query('SELECT '. $this->procedureArgumentsList);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function processArgumentResult() {
		$result = array();
		if($row = $this->db->sql_fetch_assoc($this->argumentResult)) {
			foreach($this->parameters as $key => $value) $result[$key] = $row['@'.$key];
		}
		// Set argument results to objects.fields,
		//   so that it can be accessed by the render Object and by wrapOutput
		$this->cObj->start($result, $this->storedProcedure);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function setUpTCA() {
		global $TCA;
		$TCA[$this->randomTableName] = array(
			'ctrl' => array( 'enablecolumns' => array()),
		);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function renderResult() {
		$this->output = $this->cObj->cObjGetSingle(
			$this->configuration['renderer'], $this->configuration['renderer.']);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function dropResultTable() {
		$query = "DROP TABLE " . $this->randomTableName;
		return $this->db->sql_query($query);
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function wrapOutput() {
		$this->output =  $this->cObj->stdWrap($this->output, $this->configuration['stdWrap.']);
	}

}

?>
