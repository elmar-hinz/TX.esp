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
 * Plugin 'Mysql Stored Procedure' for the 'esp' extension.
 *
 * @author	Elmar Hinz <elmar.hinz@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_esp
 */
class tx_esp_MysqlStoredProcedure {

	public $cObj;
	private $originalData;
	private $configuration;
	private $storedProcedure;
	private $db;
	private $parameters = array();
	private $procedureParameterList = '';
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
		$this->originalData = $this->cObj->data;
		$this->init($conf);
		// $this->connectDatabase();
		$this->orderAndWrapParameters();
		$this->prepareParametersForQuery();
		$this->submitParameterQuery();
		$this->callStoredProcedure();
		$this->fetchParameterResult();
		$this->processParameterResult();
		$this->renderResult();
		$this->wrapOutput();
		$this->cObj->data = $this->originalData;
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
		$this->originalData = $this->cObj->data;
		$this->configuration = $conf['userFunc.'];
		$this->storedProcedure = $this->makeStdWrap($this->configuration, 'storedProcedure');
		// We need to access the protected internal link because Core/Database/DatabaseConnection doesn't provide
		// the full API we need. There is query() as sql_query(), but there isn't next_result() and use_result()
		// Thats the main reason why this special class for mysql is needed at all
		// else we could go with a general class using DatabaseConnection resp. DBAL.
		$this->db = $this->accessProtected($GLOBALS['TYPO3_DB'], 'link');
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
		$pars = $this->parameters;
		if(count($pars)) {
			foreach($pars as $key => $value) {
				$setters[] = '@'.$key.'=\''.$value.'\'';
				$procedureParameters[] = '@'.$key;
			}
			$this->setParameterQuery = 'SET ' . join(', ', $setters);
			$this->procedureParameterList = join(', ', $procedureParameters);
		}
	}

	function submitParameterQuery() {
		if($this->setParameterQuery) {
			$this->db->query($this->setParameterQuery);
		}
	}

	function callStoredProcedure() {
		$call = 'CALL '.$this->storedProcedure.' ('.$this->getProcedureParameterList().');';
		if(!$this->procedureResult = $this->db->query($call)) {
			$error = '';
			$error .= ' ----- '.chr(10); 
			$error .= 'CallStoredProcedure failed. Error Message: '.chr(10);
			$error .= '"'.$this->db->error . '"'.chr(10);
			$error .= ' ----- '.chr(10); 
			$error .= 'The call was: '.chr(10);
			$error .= '   ' . $call . chr(10);
			$error .= ' ----- '.chr(10); 
			throw new Exception($error);
		}
		// In case the procedure returns multiple results we need to discard the rest
		// else the connection slows down. 
		while($this->db->next_result()) $this->db->use_result();
	}

	function fetchParameterResult() {
		if($ppl = $this->getProcedureParameterList()) {
			$this->parameterResult = $this->db->query('SELECT '. $ppl);
		}
	}

	function processParameterResult() {
		// If parameters returned a result.
		if(is_object($result = $this->getParameterResult())) {
			$data = array();
			if($row = $result->fetch_assoc()) {
				foreach($this->parameters as $key => $value) $data[$key] = $row['@'.$key];
			}
			$this->parameterData = $data;
		} 
	}

	function renderResult() {
		$data = $this->getParameterData();
		$this->cObj->data = $data;
		// If there is a result to render, then render it.
		if(is_object($result = $this->getProcedureResult())) {
			// Set parameter results to cObj data fields,
			// so that it can be accessed by the render object and by wrapOutput
			$this->cObj->data['_resultIterator'] = new tx_esp_MysqliResultIterator($result);
			$this->output = $this->cObj->cObjGetSingle(
				$this->configuration['renderer'], $this->configuration['renderer.']);
			$result->free();
			// Reset it, if changed by renderer. 
			// In fact it should have been done in the renderer already.
			// So just for security and clearness of code.
			$this->cObj->data = $data;
		} 
	}

	function wrapOutput() {
		$this->output =  $this->cObj->stdWrap($this->output, $this->configuration['stdWrap.']);
	}

	private function accessProtected($obj, $prop) {
		$reflection = new ReflectionClass($obj);
		$property = $reflection->getProperty($prop);
		$property->setAccessible(true);
		return $property->getValue($obj);
	}

}

?>
