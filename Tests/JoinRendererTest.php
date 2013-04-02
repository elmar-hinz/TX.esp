<?php

class tx_esp_JoinRendererTest extends tx_phpunit_testcase {

	private $tableName = 'static_tx_esp_test_123';
	private $resultLink = NULL;
	private $createTable = 'CREATE TABLE static_tx_esp_test_123 (f1_1 INT, f1_2 INT, f2_1 INT, f2_2 INT)';
	private $insertTable1 = 'INSERT INTO static_tx_esp_test_123 (f1_1, f1_2, f2_1, f2_2) VALUES (1,1,1,1)';
	private $insertTable2 = 'INSERT INTO static_tx_esp_test_123 (f1_1, f1_2, f2_1, f2_2) VALUES (1,1,2,2)';
	private $insertTable3 = 'INSERT INTO static_tx_esp_test_123 (f1_1, f1_2, f2_1, f2_2) VALUES (2,2,3,3)';
	private $insertTable4 = 'INSERT INTO static_tx_esp_test_123 (f1_1, f1_2, f2_1, f2_2) VALUES (2,2,4,4)';
	private $dropTable = 'DROP TABLE IF EXISTS static_tx_esp_test_123';

	// Table join of two tables loaded inito an array
	private $rawArray = array (
		// tablenr-fieldnr => value 
		array( 'f1_1' => 1, 'f1_2' => 1, 'f2_1' => 1, 'f2_2' => 1),
		array( 'f1_1' => 1, 'f1_2' => 1, 'f2_1' => 2, 'f2_2' => 2),
		array( 'f1_1' => 2, 'f1_2' => 2, 'f2_1' => 3, 'f2_2' => 3),
		array( 'f1_1' => 2, 'f1_2' => 2, 'f2_1' => 4, 'f2_2' => 4),
	);

	// Expected result of grouping by table one
	private $groupedArray = array (
		array(
			'attributes' => array('f1_1' => 1, 'f1_2' => 1), 
			'entries' => array( 	
				array( 'f2_1' => 1, 'f2_2' => 1), 
				array( 'f2_1' => 2, 'f2_2' => 2), 
			),
		),
		array(
			'attributes' => array('f1_1' => 2, 'f1_2' => 2), 
			'entries' => array( 	
				array( 'f2_1' => 3, 'f2_2' => 3), 
				array( 'f2_1' => 4, 'f2_2' => 4), 
			),
		),
	);

	private $typoScriptConfiguration = '
		userFunc {
			levels {
							1.levelFields = f1_1, f1_2
							1.stdWrap {
											preCObject = COA
											preCObject {
												10 = TEXT
												10.field = f1_1
												20 = TEXT
												20.value = --
												30 = TEXT
												30.field = f1_2
												stdWrap.noTrimWrap = |<h1>level 1: |</h1>|
											}
											innerWrap = <ul>|</ul>
											wrap = <section>|</section>
							}
							2.levelFields = f2_1, f2_2
							2.stdWrap {
											preCObject = COA
											preCObject {
												10 = TEXT
												10.field = f2_1
												20 = TEXT
												20.value = --
												30 = TEXT
												30.field = f2_2
												stdWrap.noTrimWrap = |<li>level 2: |</li>|
											}
							}
			}	
			stdWrap.wrap = <header><h1>Header</h1></header>|<footer><h1>Footer</h1></footer>
		} 
	';

	private $expectedLevelResults = '<section><h1>level 1: 1--1</h1><ul><li>level 2: 1--1</li><li>level 2: 2--2</li></ul></section><section><h1>level 1: 2--2</h1><ul><li>level 2: 3--3</li><li>level 2: 4--4</li></ul></section>';

	private $expectedResult = '<header><h1>Header</h1></header><section><h1>level 1: 1--1</h1><ul><li>level 2: 1--1</li><li>level 2: 2--2</li></ul></section><section><h1>level 1: 2--2</h1><ul><li>level 2: 3--3</li><li>level 2: 4--4</li></ul></section><footer><h1>Footer</h1></footer>';

	function setUp() {
		// Setup context
		if (!is_object($GLOBALS['TSFE'])) {
			$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe');
		}
		// Setup databese
		$this->db  = new mysqli(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TYPO3_db);
		assert($this->db->query($this->dropTable));
		assert($this->db->query($this->createTable));
		$this->db->query($this->insertTable1);
		$this->db->query($this->insertTable2);
		$this->db->query($this->insertTable3);
		$this->db->query($this->insertTable4);
		// Setup configuration 
		$this->tsParser = t3lib_div::makeInstance('t3lib_TSparser'); 
		$this->tsParser->parse($this->typoScriptConfiguration);
		$this->configuration = $this->tsParser->setup;
		// Load table into result link
		$query = "SELECT * FROM ".$this->tableName; 
		$this->resultLink = $this->db->query($query);
		$this->resultIterator = new tx_esp_MysqliResultIterator($this->resultLink);
		// Setup candidate
		$this->cand = new tx_esp_JoinRenderer();
		$this->cand->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->cand->cObj->data['_resultIterator'] = $this->resultIterator;
	}
	
	function tearDown() {
		// Remove test table
		assert($this->db->query($this->dropTable));
	}

	/**
	* @test
	*/
	function testing_works() {
		$this->assertTrue(TRUE);
	}
	
	//////////////////////////////////////////////////	
	// Check context
	//////////////////////////////////////////////////	

	/**
	* @test
	*/
	function TSFE_is_up() {
		$this->assertNotNull($GLOBALS['TSFE']);
	}

	/**
	* @test
	*/
	function demo_table_can_be_created_and_dropped() {
		$this->db->query($this->dropTable);
		$this->assertTrue($this->db->query($this->createTable));
	}

	/**
	* @test
	*/
	function data_can_be_inserted() {
		# prepare
		$this->db->query($this->dropTable);
		$this->assertTrue($this->db->query($this->createTable));
		# test
		$this->assertTrue($this->db->query($this->insertTable1));
		$res = $this->db->query('SELECT * FROM '.$this->tableName);
		$this->assertEquals(array(1,1,1,1), $res->fetch_row());
	}

	/**
	* @test
	*/
	function data_is_correctly_inserted_by_setup() {
		$res = $this->db->query('SELECT * FROM '.$this->tableName);
		$this->assertEquals(array(1,1,1,1), $res->fetch_row());
		$this->assertEquals(array(1,1,2,2), $res->fetch_row());
		$this->assertEquals(array(2,2,3,3), $res->fetch_row());
		$this->assertEquals(array(2,2,4,4), $res->fetch_row());
	}

	//////////////////////////////////////////////////	
	// Check candidate initialization
	//////////////////////////////////////////////////	

	/**
	* @test
	*/
	function test_candidate_can_be_constructed() {
		new tx_esp_JoinRenderer();
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
	function initLevelStack_works() {
		$this->cand->init($this->configuration);
		$this->cand->initLevelStack();
		$this->assertArrayHasKey('1.', $this->cand->getLevelStack());
		$this->assertArrayHasKey('2.', $this->cand->getLevelStack());
	}
	
	//////////////////////////////////////////////////	
	// Helpers configuration stack
	//////////////////////////////////////////////////	

	/**
	* @test
	*/
	function goDownLevelStack_works() {
		$this->cand->init($this->configuration);
		$this->cand->initLevelStack();
		$this->assertEquals(1, $this->cand->getCurrentLevel());
		$this->assertEquals(2, $this->cand->goDownLevelStack());
	}
	
	/**
	* @test
	*/
	function goUpLevelStack_works() {
		$this->cand->init($this->configuration);
		$this->cand->initLevelStack();
		$this->assertEquals(1, $this->cand->getCurrentLevel());
		$this->cand->goDownLevelStack();
		$this->assertEquals(2, $this->cand->getCurrentLevel());
		$this->assertEquals(1, $this->cand->goUpLevelStack());
	}
	
	/**
	* @test
	*/
	function getCurrentFields_works() {
		$this->cand->init($this->configuration);
		$this->cand->initLevelStack();
		$this->assertEquals(array('f1_1', 'f1_2'), $this->cand->getCurrentFields());
		$this->cand->goDownLevelStack();
		$this->assertEquals(array('f2_1', 'f2_2'), $this->cand->getCurrentFields());
	}
	
	/**
	* @test
	*/
	function getCurrentStdWrap_works() {
		$this->cand->init($this->configuration);
		$this->cand->initLevelStack();
		$expected = $this->configuration['userFunc.']['levels.']['1.']['stdWrap.'];
		$this->assertArrayHasKey('preCObject.', $expected);
		$this->assertEquals($expected, $this->cand->getCurrentStdWrap());
	}
	
	//////////////////////////////////////////////////	
	// Other helpers 
	//////////////////////////////////////////////////	

	/**
	* @Xtest
	*/
	function groupLevel_works() {
		$this->assertEquals($this->groupedArray, $this->cand->groupLevel($this->rawArray, array('f1_1', 'f1_2')));
	}
	
	/**
	* @test
	*/
	function wrapGroup_works() {
		$sublevelContent = '<div>x,y,z</div>';
		$ts = '
			preCObject = TEXT
			preCObject {
				field = header
				wrap = <h1>|</h1>
			}
			postCObject = TEXT
			postCObject {
				field = author
				noTrimWrap = |<p>author: |</p>|
			}
			wrap = <section>|</section>
		';
		$this->tsParser->parse($ts);
		$levelConfig= $this->tsParser->setup;
		$levelAttributes = array('header' => '3 entries', 'author' => 'John Doe');
		$expectedResult = '<section><h1>3 entries</h1><div>x,y,z</div><p>author: John Doe</p></section>';
		$result = $this->cand->wrapGroup($sublevelContent, $levelConfig,$levelAttributes);
		$this->assertEquals($expectedResult, $result);
	}

	/**
	* @test
	*/
	function wrapOutput_works() {
		$expected = '<header><h1>Header</h1></header><div>x.y.z</div><footer><h1>Footer</h1></footer>';
		$this->cand->init($this->configuration);
		$this->cand->setOutput('<div>x.y.z</div>');
		$this->cand->wrapOutput();
		$this->assertEquals($expected, $this->cand->getOutput());
	}
	
	//////////////////////////////////////////////////	
	// Final integration checks
	//////////////////////////////////////////////////	

	/**
	* @test
	*/
	function renderTable_works() {
		$this->cand->init($this->configuration);
		$this->cand->initLevelStack();
		$table = $this->rawArray;
		$out = $this->cand->renderTable($table);
		$this->assertEquals($this->expectedLevelResults, $out);
	}

	/**
	* @test
	*/
	function render_works() {
		$this->cand->init($this->configuration);
		$this->cand->render();
		$this->assertEquals($this->expectedLevelResults, $this->cand->getOutput());
	}
	
	/**
	* @test
	*/
	function main_works() {
		$this->cand->main('', $this->configuration);
		$this->assertEquals($this->expectedResult, $this->cand->getOutput());
	}
	
}

?>

