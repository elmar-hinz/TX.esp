-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE `tx_esp_rootline`(page INT, language INT)
BEGIN

	DROP TEMPORARY TABLE IF EXISTS tx_esp_rootline_invers;
	CREATE TEMPORARY TABLE tx_esp_rootline_invers LIKE pages;
	TRUNCATE TABLE tx_esp_rootline_invers;

	WHILE page > 0 DO
		INSERT INTO tx_esp_rootline_invers (SELECT * FROM pages WHERE uid = page);
		SELECT pid INTO page FROM pages WHERE uid = page;
	END WHILE;

	SET @rank = 0;
	SELECT *, @rank:=@rank + 1 AS rank FROM tx_esp_rootline_invers ORDER BY rank DESC;
	DROP TEMPORARY TABLE IF EXISTS tx_esp_rootline_invers;

END

