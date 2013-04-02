<?php

	class tx_esp_StoredProcedureTest extends tx_phpunit_testcase {
		
		private $parameterOrder = array ( 'firstParameter', 'secondParameter', 'thirdParameter');
		private $createProcedure = '
CREATE PROCEDURE tx_esp_test_procedure (INOUT firstParameter VARCHAR(100), INOUT secondParameter VARCHAR(100), INOUT thirdParameter VARCHAR(100))
BEGIN
  SET firstParameter = "one";
  SET secondParameter = "two";
  SET thirdParameter = "three";
	DROP TABLE IF EXISTS tx_esp_test;
	CREATE TEMPORARY TABLE tx_esp_test (field1 INT);
	INSERT INTO tx_esp_test SET field1=1111;
	INSERT INTO tx_esp_test SET field1=2222;
	SELECT * FROM tx_esp_test;
END
		';

		private $dropProcedure = 'DROP PROCEDURE IF EXISTS tx_esp_test_procedure;';

		function setUp() {
			if (!is_object($GLOBALS['TSFE'])) {
				$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe');
			}
			if (!is_object($GLOBALS['TSFE']->sys_page)) {
				$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
			}
			if (!is_object($GLOBALS['TT'])) {
				$GLOBALS['TT'] = new t3lib_timeTrack();
				$GLOBALS['TT']->start();
			}
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
				'renderer' => 'USER',
				'renderer.' => array(
					'userFunc' => 'tx_esp_SimpleRenderer->main',
					'userFunc.' => array(
						'stdWrap.' => array(
							'prepend' => 'TEXT',
							'prepend.' => array(
								'field' => 'firstParameter',
								'wrap' => '<parameter1:|>',
							)
						),
						'rowRenderer' => 'TEXT',
						'rowRenderer.' => array( 
							'field' => 'field1',
							'wrap' => '<|>',
						),
					),
				),
				'stdWrap.' => array( 'wrap' => '<wrap>|</wrap>'),
			);
			$this->configuration = array( 'userFunc.' => $userFunc_);
			$this->cand = new tx_esp_StoredProcedure();
			$this->cand->cObj = t3lib_div::makeInstance('tslib_cObj');
		}

		function tearDown() {
			$this->db->sql_query($this->dropProcedure);	
			$rh = $this->db->sql_query('SHOW TABLES');	
			while($row = $this->db->sql_fetch_row($rh)) {
				$table = $row[0];
				if(preg_match('/tx_esp_test_.*/', $table)) {
					$sql = "DROP TABLE IF EXISTS ".$table;
					$this->db->sql_query($sql);	
				}
			}
		}
		
		/**
		* @test
		*/
		function TSFE_exists() {
			$this->assertNotNull($GLOBALS['TSFE']);
			$this->assertNotNull($GLOBALS['TT']);
			$this->assertNotEquals('', $GLOBALS['TSFE']->sys_page);
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
		function cObjGetSingle_works_for_TEXT() {
			$out = $this->cand->cObj->cObjGetSingle('TEXT', array('value' => 'Test'));
			$this->assertEquals('Test', $out);
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
		function db_can_be_connected() {
			$this->cand->connectDatabase();
			$candDb = $this->cand->getDB();
			$this->assertInstanceOf('\mysqli', $candDb);
		}

		/**
		* @test
		*/
		function db_can_be_disconnected() {
			$this->cand->connectDatabase();
			$candDb = $this->cand->getDB();
			$this->assertInstanceOf('\mysqli', $candDb);
			$this->assertTrue($this->cand->disconnectDatabase());
		}

		/**
		* @test
		*/
		function orderAndWrapParameters_turns_an_empty_list_to_an_empty_array() {
			unset($this->configuration['userFunc.']['parameterOrder']);
			unset($this->configuration['userFunc.']['parameters']);
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->assertSame(array(), array_keys($this->cand->getParameters()));
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
			$this->assertEquals('@firstParameter, @secondParameter, @thirdParameter', $this->cand->getProcedureParameterList());
			$query =$this->cand->getSetParameterQuery();
			$expactation = "SET @firstParameter='1-1-1', @secondParameter='', @thirdParameter='3-3-3'";
			$this->assertEquals($expactation, $query);
		}

		/**
		* @test
		*/
		function submitParameterQuery_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->submitParameterQuery();
			$result = $this->cand->getDB()->query('SELECT @thirdParameter, @secondParameter, @firstParameter');
			$this->assertEquals(array('3-3-3', '', '1-1-1'), $result->fetch_row());
		}

		/**
		* @test
		*/
		function callStoredProcedure_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->submitParameterQuery();
			$this->cand->callStoredProcedure();
			$result = $this->cand->getProcedureResult();
			$this->assertInstanceOf('mysqli_result', $result);
			$this->assertTrue(1 < $result->num_rows);
			list($value) = $result->fetch_row(); 
			$this->assertEquals('1111', $value);
			list($value) = $result->fetch_row(); 
			$this->assertEquals('2222', $value);
		}

		/**
		* @test
		*/
		function callStoredProcedure_synchronizesResults() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->submitParameterQuery();
			$this->cand->callStoredProcedure();
			$this->assertFalse($this->cand->getDB()->next_result());
		}

		/**
		* @test
		*/
		function fetchParameterResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->submitParameterQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchParameterResult();
			$result = $this->cand->getParameterResult();
			$this->assertEquals(array('one', 'two', 'three'), $result->fetch_row());
		}

		/**
		* @test
		*/
		function processParameterResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->submitParameterQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchParameterResult();
			$this->cand->processParameterResult();
			$expected = array('firstParameter' => 'one', 'secondParameter' => 'two', 'thirdParameter' => 'three');
			$this->assertEquals($expected, $this->cand->getParameterData());
		}

		/**
		* @test
		*/
		function renderResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->submitParameterQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchParameterResult();
			$this->cand->processParameterResult();
			$this->cand->renderResult();
			$this->assertEquals('<parameter1:one><1111><2222>', $this->cand->getOutput());
		}

		/**
		* @test
		*/
		function ouptput_can_be_wrapped() {
			$this->cand->init($this->configuration);
			$this->cand->wrapOutput();
			$this->assertRegExp('/^<wrap>.*<\/wrap>$/', $this->cand->getOutput());
		}

		/**
		* @test
		*/
		function integration_test() {
			$out = $this->cand->main('', $this->configuration);
			$this->assertEquals('<wrap><parameter1:one><1111><2222></wrap>', $out);
		}

	}

?>
