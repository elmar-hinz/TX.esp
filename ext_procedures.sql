-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE `tx_esp_rootline` (IN tableName VARCHAR(255), IN page INT, IN language INT)
BEGIN                                                                                      

	SET @tableName = tableName;
	SET @page = page;
	SET @language = language;

	SET @query = concat("CREATE TEMPORARY TABLE ", @tableName, "_invers LIKE pages;");
	PREPARE stmt FROM @query;
	EXECUTE stmt;                                                                            
	DEALLOCATE PREPARE stmt; 

	SET @query = concat("INSERT INTO ",@tableName, "_invers (SELECT * FROM pages WHERE uid = ?);");
	PREPARE stmt FROM @query;                                                   
	WHILE @page > 0 DO
		EXECUTE stmt USING @page;
		SELECT pid INTO @page FROM pages WHERE uid = @page;
	END WHILE;
	DEALLOCATE PREPARE stmt;
                                                                                           
	SET @rank = 0;
	SET @query = concat("CREATE TABLE ",@tableName, " (SELECT *, @rank:=@rank + 1 AS rank, pid AS pid_orig FROM ", @tableName, "_invers ORDER BY rank DESC);");
	PREPARE stmt FROM @query;                                                                
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;                                                                 

	SET @query = concat("UPDATE ",@tableName, " SET pid = 0;");
	PREPARE stmt FROM @query;                                                                
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;                                                                 
END$$

DELIMITER ;

