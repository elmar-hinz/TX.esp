<?php

class tx_esp_TemporaryTableBehaviourTest extends tx_phpunit_testcase {

	private $createTemporaryTable = 'CREATE TEMPORARY TABLE tx_esp_temporary_table (column1 varchar(255))';
	private $dropTemporaryTable = 'DROP TABLE tx_esp_temporary_table';

	function setUp() {
		$this->nonPermanentResource1 = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		assert(mysql_select_db(TYPO3_db, $this->nonPermanentResource1));
		$this->nonPermanentResource2 = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		assert(mysql_select_db(TYPO3_db, $this->nonPermanentResource2));
		$this->permanentResource1 = mysql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		assert(mysql_select_db(TYPO3_db, $this->permanentResource1));
		$this->permanentResource2= mysql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		assert(mysql_select_db(TYPO3_db, $this->permanentResource2));
	}

	function tearDown() {
		assert(mysql_close($this->nonPermanentResource1));
		assert(mysql_close($this->nonPermanentResource2));
		assert(mysql_close($this->permanentResource1));
		assert(mysql_close($this->permanentResource2));
	}

	/**
	* @test
	*/
	function connection_parameters_are_given() {
		$this->assertTrue(strlen(TYPO3_db) > 0);
		$this->assertTrue(strlen(TYPO3_db_host) > 0);
		$this->assertTrue(strlen(TYPO3_db_username) > 0);
		$this->assertTrue(strlen(TYPO3_db_password) > 0);
	}

	/**
	* @test
	*/
	function mysql_connection_can_be_established_and_closed() {
		$link = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password);
		$this->assertSame('mysql link', get_resource_type($link));
		$this->assertTrue(mysql_select_db(TYPO3_db, $link));
		$this->assertTrue(mysql_close($link));
	}

	/**
	* @test
	*/
	function mysql_connect_returns_same_resource_without_flag() {
		$db1 = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password);
		$db2 = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password);
		$this->assertSame($db1, $db2);
		$this->assertTrue(mysql_close($db1));
		$this->assertTrue(mysql_close($db2));
	}

	/**
	* @test
	*/
	function mysql_connect_returns_distinct_resources_with_flag() {
		$db1 = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		$db2 = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		$this->assertNotSame($db1, $db2);
		$this->assertTrue(mysql_close($db1));
		$this->assertTrue(mysql_close($db2));
	}

	/**
	* @test
	*/
	function temporary_table_can_be_created() {
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->nonPermanentResource1));
		
	}

	/**
	* @test
	*/
	function temporary_table_cant_be_created_twice() {
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertFalse(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->nonPermanentResource1));
	}

	/**
	* @testX
	*/
	function temporary_table_can_be_dropped() {
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->nonPermanentResource1));
		// if dropping workds we can create it twice
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->nonPermanentResource1));
	}

	/**
	* @test
	*/
	function temporary_tables_in_distinct_connections_dont_conflict() {
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->nonPermanentResource2));
		// again twice in same connection does conflicts
		$this->assertFalse(mysql_query($this->createTemporaryTable, $this->nonPermanentResource1));
		$this->assertFalse(mysql_query($this->createTemporaryTable, $this->nonPermanentResource2));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->nonPermanentResource1));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->nonPermanentResource2));
	}

	/**
	* @test
	*/
	function the_two_permanent_resources_are_not_the_same() {
		$this->assertNotSame($this->permanentResource1, $this->permanentResource2);
	}

	/**
	* @test
	*/
	function temporary_table_conflicts_between_both_permanent_resources() {
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->permanentResource1));
		// r2 cant create it
		$this->assertFalse(mysql_query($this->createTemporaryTable, $this->permanentResource2));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->permanentResource1));
		$this->assertFalse(mysql_query($this->dropTemporaryTable, $this->permanentResource2));
		
	}

	/**
	* @test
	*/
	function temporary_table_can_even_be_dropped_by_the_other_permanent_resource() {
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->permanentResource1));
		// r1 cant create it twice
		$this->assertFalse(mysql_query($this->createTemporaryTable, $this->permanentResource1));
		// r2 can drop it and r1 can create it again
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->permanentResource2));
		$this->assertTrue(mysql_query($this->createTemporaryTable, $this->permanentResource1));
		// cleanup
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $this->permanentResource1));
		$this->assertFalse(mysql_query($this->dropTemporaryTable, $this->permanentResource2));
	}

	/**
	* @test
	*/
	function temporary_table_doesnt_survive_in_temporary_connection_part1() {
		$link = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password);
		$this->assertTrue(mysql_select_db(TYPO3_db, $link));
		$this->assertTrue(mysql_query($this->createTemporaryTable, $link));
	}
	
	/**
	* @test
	* @depends temporary_table_doesnt_survive_in_temporary_connection_part1
	*/
	function temporary_table_doesnt_survive_in_temporary_connection_part2() {
		$link = mysql_connect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password);
		$this->assertTrue(mysql_select_db(TYPO3_db, $link));
		$this->assertTrue(mysql_query($this->createTemporaryTable, $link));
		$this->assertTrue(mysql_close($link));
	}
	
	/**
	* @test
	*/
	function temporary_table_does_survive_in_permanent_connection_part1() {
		$link = mysql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		$this->assertTrue(mysql_select_db(TYPO3_db, $link));
		$this->assertTrue(mysql_query($this->createTemporaryTable, $link));
		$this->assertTrue(mysql_close($link));
	}
	
	/**
	* @test
	* @depends temporary_table_does_survive_in_permanent_connection_part1
	*/
	function temporary_table_does_survive_in_permanent_connection_part2() {
		$link = mysql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password, TRUE);
		$this->assertTrue(mysql_select_db(TYPO3_db, $link));
		$this->assertFalse(mysql_query($this->createTemporaryTable, $link));
		$this->assertTrue(mysql_query($this->dropTemporaryTable, $link));
		$this->assertTrue(mysql_close($link));
	}

}

?>
