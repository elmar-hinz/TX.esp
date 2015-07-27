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
	/* ResultIterator of the stored procedure */
	private $resultIterator;
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
		// This should happen before wrapOutput() to have the original data available.
		$this->setDataAgain(); 
		$this->wrapOutput();
		return $this->getOutput();
	}

	// Public accessros, also for testing
	public function getConfiguration() { return $this->configuration; }
	public function getResultIterator() { return $this->resultIterator; }
	public function getParameters() { return $this->parameters; }
	public function setOutput($out) { $this->out = $out; }
	public function getOutput() { return $this->out; }

	//////////////////////////////////////////////////
	// Workers
	//////////////////////////////////////////////////

	public function init($conf) {
		$this->configuration = $conf['userFunc.'];
		if(
			isset($this->cObj->data['_resultIterator'])
			&& $this->cObj->data['_resultIterator'] instanceOf tx_esp_ResultIteratorInterface
		) {
			$this->resultIterator = $this->cObj->data['_resultIterator'];
			unset($this->cObj->data['_resultIterator']);
		} else {
			throw new Exception('No resultIterator of stored procedure');
		}
		$this->parameters = $this->cObj->data;
	}

	abstract public function render();

	protected function setDataAgain() {
		$this->cObj->data = $this->getParameters();
	}

	public function wrapOutput() {
		$this->out = $this->cObj->stdWrap($this->out, $this->configuration['stdWrap.']);
	}

}

?>
