<?php

	class StoredProcedureTest extends tx_phpunit_testcase {
		
		private $parameterOrder = array ( 'firstParameter', 'secondParameter', 'thirdParameter');
		private $createProcedure = '
CREATE PROCEDURE tx_esp_test_procedure (INOUT firstParameter VARCHAR(100), INOUT secondParameter VARCHAR(100), INOUT thirdParameter VARCHAR(100))
BEGIN
  SET firstParameter = "one";
  SET secondParameter = "two";
  SET thirdParameter = "three";
END
		';

		private $dropProcedure = 'DROP PROCEDURE IF EXISTS tx_esp_test_procedure;';

		function setUp() {
			$mysqli = new mysqli("localhost", "root", "vagrant", "typo3");
			$this->db = $GLOBALS['TYPO3_DB'];
			$this->db->sql_query($this->dropProcedure);	
			$this->db->sql_query($this->createProcedure);	
			$userFunc_ = array(
				'storedProcedure' => 'test_procedure',
				'storedProcedure.' => array('wrap' => 'tx_esp_|'),
				'parameterOrder' => join(', ', $this->parameterOrder),
				'parameters.' => array( 
					'thirdParameter' => '3-3-3', 
					'firstParameter' => 1,
					'firstParameter.' => array( 'wrap' => '1-|-1')
				),
			);
			$this->configuration = array( 'userFunc.' => $userFunc_);
			$this->cand = new tx_esp_StoredProcedure();
			$this->cand->cObj = t3lib_div::makeInstance('tslib_cObj');
		}

		function tearDown() {
			$this->db->sql_query($this->dropProcedure);	
		}
		
		/**
		* @test
		*/
		function object_can_be_constructed() {
			new tx_esp_StoredProcedure();
		}

		/**
		* @test
		*/
		function cObj_is_public() {
			$this->assertInstanceOf('tslib_cObj', $this->cand->cObj);
		}

		/**
		* @test
		*/
		function init_sets_configuration() {
			$this->cand->init($this->configuration);
			$myConf = $this->cand->getConfiguration();
			$this->assertEquals('test_procedure', $myConf['storedProcedure']);
		}

		/**
		* @test
		*/
		function init_sets_storedProcedure() {
			$this->cand->init($this->configuration);
			$this->assertStringEndsWith('test_procedure', $this->cand->getStoredProcedure());
		}

		/**
		* @test
		*/
		function init_wraps_storedProcedure() {
			$this->cand->init($this->configuration);
			$this->assertStringStartsWith('tx_esp_', $this->cand->getStoredProcedure());
		}

		/**
		* @test
		*/
		function init_sets_db() {
			$this->cand->init();
			$this->assertInstanceOf('TYPO3\CMS\Core\Database\DatabaseConnection', $this->cand->getDB());
		}

		/**
		* @test
		*/
		function orderAndWrapParameters_ordersParameters() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->assertSame($this->parameterOrder, array_keys($this->cand->getParameters()));
		}

		/**
		* @test
		*/
		function orderAndWrapParameters_wrapsParameters() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$pars = $this->cand->getParameters();
			$this->assertEquals('1-1-1', $pars['firstParameter']);
		}

		/**
		* @test
		*/
		function prepareParametersForQuery_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$saqs = $this->cand->getSetArgumentQuery();
			$this->assertEquals("SET @firstParameter='1-1-1'; ", $saqs[0]);
			$this->assertEquals("SET @secondParameter=''; ", $saqs[1]);
			$this->assertEquals("SET @thirdParameter='3-3-3'; ", $saqs[2]);
			$this->assertEquals('@firstParameter, @secondParameter, @thirdParameter', $this->cand->getProcedureArgumentsList());
		}

		/**
		* @test
		*/
		function callStoredProcedure_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
		}

		/**
		* @test
		*/
		function fetchArgumentResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchArgumentResult();
			$ar = $this->cand->getArgumentResult();
		}

		/**
		* @test
		*/
		function processArgumentResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchArgumentResult();
			$this->cand->processArgumentResult();
			$this->assertEquals('one', $this->cand->cObj->data['firstParameter']);
			$this->assertEquals('two', $this->cand->cObj->data['secondParameter']);
			$this->assertEquals('three', $this->cand->cObj->data['thirdParameter']);
		}

		/**
		* @test
		*/
		function dummy() {
		}

	}

?>

