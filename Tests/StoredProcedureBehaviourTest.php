<?php

class tx_esp_StoredProcedureBehaviourTest extends tx_phpunit_testcase {

	private $connection;
	private $mysqli;
	private $createParameterProcedure = '
CREATE PROCEDURE tx_esp_test_procedure_parameter(OUT version VARCHAR(255))
BEGIN
	SELECT VERSION() INTO version;
END
		';
	private $createListProcedure = '
CREATE PROCEDURE tx_esp_test_procedure_list()
BEGIN
	SHOW TABLES LIKE "page%";
END
		';
	private $createCombinedProcedure = '
CREATE PROCEDURE tx_esp_test_procedure_combined(OUT version VARCHAR(255))
BEGIN
	SELECT VERSION() INTO version;
	SHOW TABLES LIKE "page%";
END
		';
		private $dropParameterProcedure = 'DROP PROCEDURE IF EXISTS tx_esp_test_procedure_parameter;';
		private $dropListProcedure = 'DROP PROCEDURE IF EXISTS tx_esp_test_procedure_list;';
		private $dropCombinedProcedure = 'DROP PROCEDURE IF EXISTS tx_esp_test_procedure_combined;';


	function setUp() {
		$this->connection= mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		assert(mysql_select_db(TYPO3_db, $this->connection));
		mysql_query($this->dropParameterProcedure);
		mysql_query($this->dropListProcedure);
		mysql_query($this->dropCombinedProcedure);
		mysql_query($this->createParameterProcedure);
		mysql_query($this->createListProcedure);
		mysql_query($this->createCombinedProcedure);
		$this->mysqli = new mysqli(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TYPO3_db);
	}

	function tearDown() {
		mysql_query($this->dropParameterProcedure);
		mysql_query($this->dropListProcedure);
		mysql_query($this->dropCombinedProcedure);
		assert(mysql_close($this->connection));
		assert($this->mysqli->close());
	}

	/**
	* Calling the procedure works at least one time with mysl and mysqli.
	* @test
	*/
	function call_to_parameter_procedure_works_for_mysql_and_mysqli() {
		$query = 'CALL tx_esp_test_procedure_parameter(@version)';
		$result = mysql_query($query);
		$this->assertTrue($result);
		$result = $this->mysqli->query($query);
		$this->assertTrue($result);
	}

	/**
	* Calling the procedure works at least one time with mysl and mysqli.
	* @test
	*/
	function call_to_list_procedure_works_for_both() {
		$query = 'CALL tx_esp_test_procedure_list()';
		$result= mysql_query($query);
		$this->assertNotNull($result);
		$this->assertTrue(is_resource($result));
		$result = $this->mysqli->query($query);
		$this->assertInstanceOf('mysqli_result', $result);
	}

	/**
	* Calling the procedure works at least one time with mysl and mysqli.
	* @test
	*/
	function call_to_combined_procedure_works_for_both() {
		$query = 'CALL tx_esp_test_procedure_combined(@version)';
		$result= mysql_query($query);
		$this->assertTrue(is_resource($result));
		$result = $this->mysqli->query($query);
		$this->assertInstanceOf('mysqli_result', $result);
	}

	/**
	* mysql_query fetches only one result each call. 
	* The parameter procedure returns only one result, so this works.
	* @test
	*/
	function multiple_parameter_calls_work_in_row_for_mysql() {
		$query = 'CALL tx_esp_test_procedure_parameter(@version)';
		$result = mysql_query($query);
		$this->assertTrue($result);
		$query = 'CALL tx_esp_test_procedure_parameter(@version)';
		$result = mysql_query($query);
		$this->assertTrue($result);
	}

	/**
	* The list procedure returns 2 results. 
	* Because mysql_query only fetches one of them, it is out of sync thereafter.
	* @test
	*/
	function mysql_list_calls_dont_work_in_row_not_even_with_free_result() {
		$query = 'CALL tx_esp_test_procedure_list()';
		$result= mysql_query($query);
		$this->assertNotNull($result);
		$this->assertTrue(is_resource($result));
		$freed = mysql_free_result($result);
		$this->assertTrue($freed);
		// again
		$result= mysql_query($query);
		// failed
		$this->assertFalse($result);
		$this->assertEquals(2014, mysql_errno());
		$this->assertStringStartsWith('Commands out of sync', mysql_error());
	}

	/**
	* If the second result isn't fetched mysqli_query also gets out of sync.
	* @test
	*/
	function mysqli_list_calls_dont_work_in_row_without_synchronization() {
		$query = 'CALL tx_esp_test_procedure_list()';
		$result = $this->mysqli->query($query);
		$this->assertInstanceOf('mysqli_result', $result);
		$result->free_result();
		// again
		$result = $this->mysqli->query($query);
		// failed
		$this->assertFalse($result);
		$this->assertEquals(2014, $this->mysqli->errno);
	}

	/**
	* If the second result is fetched everything works fine.
	* @test
	*/
	function mysqli_list_calls_work_in_row_with_synchronization() {
		$query = 'CALL tx_esp_test_procedure_list()';
		$result = $this->mysqli->query($query);
		$this->assertInstanceOf('mysqli_result', $result);
		// synchronize rest of 1 empty result
		$this->mysqli->next_result();
		$result = $this->mysqli->use_result();
		$this->assertFalse($result);
		$this->assertEquals(0, $this->mysqli->errno);
		// works again
		$result = $this->mysqli->query($query);
		$this->assertInstanceOf('mysqli_result', $result);
		// synchronize rest
		$this->mysqli->next_result();
		$result = $this->mysqli->use_result();
		$this->assertFalse($result);
		$this->assertEquals(0, $this->mysqli->errno);
	}

	/**
	* @test
	*/
	function mysqli_can_fetch_combined_procedure() {
		// query list
		$query = 'CALL tx_esp_test_procedure_combined(@version)';
		$result = $this->mysqli->query($query);
		$this->assertInstanceOf('mysqli_result', $result);
		$this->assertTrue(1 < $result->num_rows);
		list($table) = $result->fetch_row(); 
		$this->assertStringStartsWith('pages', $table);
		// synchronize rest 
		while($this->mysqli->next_result()) $this->mysqli->use_result();
		// query version
		$query = 'SELECT @version';
		$result = $this->mysqli->query($query);
		$this->assertEquals(0, $this->mysqli->errno);
		list($version) = $result->fetch_row(); 
		$this->assertStringStartsWith('5.', $version);
	}

	/**
	* @test
	*/
	function mysqli_can_process_combined_procedure_in_reversed_order() {
		$query1 = 'CALL tx_esp_test_procedure_combined(@version)';
		$query2 = 'SELECT @version';
		// query in oringinal order 
		$result1 = $this->mysqli->query($query1);
		while($this->mysqli->next_result()) $this->mysqli->use_result();
		$result2 = $this->mysqli->query($query2);
		// process version first
		list($version) = $result2->fetch_row(); 
		$result2->free();
		$this->assertStringStartsWith('5.', $version);
		// process list second
		list($table) = $result1->fetch_row(); 
		$result1->free();
		$this->assertStringStartsWith('pages', $table);
	}

}

?>

