<?php

	class tx_esp_StoredProcedureTest extends tx_phpunit_testcase {
		
		private $parameterOrder = array ( 'firstParameter', 'secondParameter', 'thirdParameter');
		private $createProcedure = '
CREATE PROCEDURE tx_esp_test_procedure (IN tableName VARCHAR(100), INOUT firstParameter VARCHAR(100), INOUT secondParameter VARCHAR(100), INOUT thirdParameter VARCHAR(100))
BEGIN
  SET firstParameter = "one";
  SET secondParameter = "two";
  SET thirdParameter = "three";

  SET @query = concat("CREATE TABLE ",@tableName, " (uid INT, field1 INT)");
  PREPARE stmt FROM @query;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;  

  SET @query = concat("INSERT INTO ",@tableName, " (uid, field1) VALUES (0, 1111)");
  PREPARE stmt FROM @query;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;  

  SET @query = concat("INSERT INTO ",@tableName, " (uid, field1) VALUES (0, 2222)");
  PREPARE stmt FROM @query;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;  
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
				'renderer' => 'CONTENT',
				'renderer.' => array(
					'table.' => array('field' => 'tableName'),
					'pidInList' => '0',
					'renderObj' => 'TEXT',
					'renderObj.' => array( 
						'field' => 'field1',
						'wrap' => '<|>',
					)
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
					$sql = "DROP TABLE ".$table;
					$this->db->sql_query($sql);	
				}
			}
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
		function TSFE_exists() {
			$this->assertNotNull($GLOBALS['TSFE']);
			$this->assertNotNull($GLOBALS['TT']);
			$this->assertNotEquals('', $GLOBALS['TSFE']->sys_page);
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
		function prependRandomTableToParameters_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$pars = $this->cand->getParameters();
			$this->assertRegExp('/^.*tx_esp_test_procedure_\d+$/', $pars['tableName']);
		}

		/**
		* @test
		*/
		function tableName_is_prefixed_with_static() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->assertRegExp('/^static_.*$/', $this->cand->getRandomTableName());
		}

		/**
		* @test
		*/
		function prepareParametersForQuery_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->prepareParametersForQuery();
			$saqs = $this->cand->getSetArgumentQuery();
			$this->assertStringStartsWith('SET @tableName=\'static_tx_esp_test_procedure_', $saqs[0]);
			$this->assertEquals("SET @firstParameter='1-1-1'; ", $saqs[1]);
			$this->assertEquals("SET @secondParameter=''; ", $saqs[2]);
			$this->assertEquals("SET @thirdParameter='3-3-3'; ", $saqs[3]);
			$this->assertEquals('@tableName, @firstParameter, @secondParameter, @thirdParameter', $this->cand->getProcedureArgumentsList());
		}

		/**
		* @test
		*/
		function callStoredProcedure_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
		}

		/**
		* @test
		*/
		function fetchArgumentResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchArgumentResult();
			$this->cand->getArgumentResult();
		}

		/**
		* @test
		*/
		function processArgumentResult_works() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchArgumentResult();
			$this->cand->processArgumentResult();
			$this->assertStringStartsWith('static_tx_esp_test_procedure_', $this->cand->cObj->data['tableName']); 
			$this->assertEquals('one', $this->cand->cObj->data['firstParameter']);
			$this->assertEquals('two', $this->cand->cObj->data['secondParameter']);
			$this->assertEquals('three', $this->cand->cObj->data['thirdParameter']);
		}

		/**
		* @test
		*/
		function required_TCA_is_set() {
			global $TCA;
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->setUpTCA();
			$tableConf = $TCA[$this->cand->getRandomTableName()];
			$this->assertInternalType('array', $tableConf);
		}

		/**
		* @test
		*/
		function result_can_be_rendered() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchArgumentResult();
			$this->cand->processArgumentResult();
			$this->cand->setUpTCA();
			$this->cand->renderResult();
			$this->assertEquals('<1111><2222>', $this->cand->getOutput());
		}

		/**
		* @test
		*/
		function table_can_be_dropped() {
			$this->cand->init($this->configuration);
			$this->cand->orderAndWrapParameters();
			$this->cand->prependRandomTableToParameters();
			$this->cand->prepareParametersForQuery();
			$this->cand->callStoredProcedure();
			$this->cand->fetchArgumentResult();
			$this->cand->processArgumentResult();
			$this->assertTrue($this->cand->dropResultTable());
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
		function main_full_integration_works() {
			$this->cand->main('', $this->configuration);
			$this->assertEquals('<wrap><1111><2222></wrap>', $this->cand->getOutput());
		}

	}

?>
