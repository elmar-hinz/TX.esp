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
	private $db;
	private $parameters = array();
	private $procedureParameterList = array();
	private $setParameterQuery = '';
	private $procedureResult = NULL;
	private $parameterResult = NULL;
	private $parameterData = array();
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
		// $this->connectDatabase();
		$this->orderAndWrapParameters();
		$this->prepareParametersForQuery();
		$this->callStoredProcedure();
		$this->fetchParameterResult();
		$this->processParameterResult();
		$this->renderResult();
		$this->disconnectDatabase();
		$this->wrapOutput();
		return $this->output;
	}

	// Public accessros for testing
	public function getConfiguration() { return $this->configuration; }
	public function getStoredProcedure() { return $this->storedProcedure; }
	public function getDB() { return $this->db; }
	public function getParameters() { return $this->parameters; }
	public function getProcedureParameterList() { return $this->procedureParameterList; }
	public function getSetParameterQuery() {return $this->setParameterQuery; }
	public function getProcedureResult() { return $this->procedureResult; }
	public function getParameterResult() { return $this->parameterResult; }
	public function getParameterData() { return $this->parameterData; }
	public function getOutput() { return $this->output; }

	function init($conf) {
		$this->configuration = $conf['userFunc.'];
		$this->storedProcedure = $this->makeStdWrap($this->configuration, 'storedProcedure');
		$this->connectDatabase();
	}

	function connectDatabase() {
		$this->db  = new mysqli(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TYPO3_db);
		if ($this->mysqli->connect_errno) {
		    throw(new Exception("Connect failed: " . $mysqli->connect_error));
		} 
	}

	function disconnectDatabase() {
		$success = $this->db->close();
		assert($success);
		return $success;
	}

	private function makeStdWrap($configuration, $configurationKey) {
		$value = $configuration[$configurationKey];
		$conf = $configuration[$configurationKey.'.'];
		return $this->cObj->stdWrap((string)$value, $conf);
	}

	function orderAndWrapParameters() {
		$order = trim($this->configuration['parameterOrder']);
		if($order == '') {
			$this->parameters = array();
		} else {
			$keys = t3lib_div::trimExplode(',', $order);
			$keys = array_combine($keys, $keys);
			$this->parameters = array_map(array($this, 'wrapParameter'), $keys);
		}
	}

	private function wrapParameter($key) {
		$parameters = $this->configuration['parameters.'];
		return $this->makeStdWrap($parameters, $key);
	}

	function prepareParametersForQuery() {
		$setters = array();
		$procedureParameters = array();
		foreach($this->parameters as $key => $value) {
			$setters[] = '@'.$key.'=\''.$value.'\'';
			$procedureParameters[] = '@'.$key;
		}
		$this->setParameterQuery = 'SET ' . join(', ', $setters);
		$this->procedureParameterList = join(', ', $procedureParameters);
	}

	function submitParameterQuery() {
		$this->db->query($this->setParameterQuery);
	}

	function callStoredProcedure() {
		$call = 'CALL '.$this->storedProcedure.' ('.$this->procedureParameterList.');';
		$this->procedureResult = $this->db->query($call);
		while($this->db->next_result()) $this->db->use_result();
	}

	function fetchParameterResult() {
		$this->parameterResult = $this->db->query('SELECT '. $this->procedureParameterList);
	}

	function processParameterResult() {
		$data = array();
		if($row = $this->getParameterResult()->fetch_assoc()) {
			foreach($this->parameters as $key => $value) $data[$key] = $row['@'.$key];
		}
		$this->parameterData = $data;
	}

	function renderResult() {
		// Set parameter results to objects.fields,
		// so that it can be accessed by the render Object and by wrapOutput
		$data = $this->getParameterData();
		$data['_procedureResult'] = $this->getProcedureResult();
		$this->cObj->start($data);
		$this->output = $this->cObj->cObjGetSingle(
			$this->configuration['renderer'], $this->configuration['renderer.']);
		// reset it, if changed by renderer
		$this->cObj->start($data);
		$this->getProcedureResult()->free();
	}

	function wrapOutput() {
		$this->output =  $this->cObj->stdWrap($this->output, $this->configuration['stdWrap.']);
	}

}

?>
