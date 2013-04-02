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
 * Abstract renderer
 *
 * Renderers extend this class
 *
 * @author	Elmar Hinz <elmar.hinz@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_esp
 */
abstract class tx_esp_AbstractRenderer {

	public $cObj;
	private $configuration;
	/* Parameter result of the stored procedure */
	private $parameters;
	/* Mysqli result of the stored procedures */
	private $result;
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
		$this->render();
		$this->setDataAgain();
		$this->wrapOutput();
		return $this->getOutput();
	}

	// Public accessros, also for testing
	public function getConfiguration() { return $this->configuration; } 
	public function getResult() { return $this->result; } 
	public function getParameters() { return $this->parameters; } 
	public function setOutput($out) { $this->out = $out; }
	public function getOutput() { return $this->out; }

	//////////////////////////////////////////////////	
	// Workers
	//////////////////////////////////////////////////	

	public function init($conf) {
		$this->configuration = $conf['userFunc.'];
		$this->db = $GLOBALS['TYPO3_DB'];
		if(isset($this->cObj->data['_procedureResult'])) {
			$this->result = $this->cObj->data['_procedureResult'];
			unset($this->cObj->data['_procedureResult']);
		} else {
			throw new Exception('No link to result of stored procedure');
		}
		$this->parameters = $this->cObj->data;
	}

	abstract public function render(); 

	public function setDataAgain() {
		$this->cObj->start($this->getParameters());
	}

	public function wrapOutput() {
		$this->out = $this->cObj->stdWrap($this->out, $this->configuration['stdWrap.']); 
	}

}

?>
