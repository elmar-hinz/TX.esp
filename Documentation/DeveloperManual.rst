================
Developer Manual
================

Stored procedures
=================

Editing stored procedures
-------------------------

To quickly fix a stored procedure you can use phpMyAdmin or adminer. 
A more comfortable IDE for the development of SQL including stored procedures
is MySQL Workbench.

The classical way is, to write stored procedures using your preferred text
editor and to apply them by using the commandline tool **mysql**, similar
to this:: 

	cat your_procedures.sql | mysql -u root -p secret -D typo3

Getting started
---------------

To find examples take a look into **EXT:esp/ext_procedures.sql**.

Guidelines
----------

1.) Prepend the name of your stored procedure with your extension key to avoid
naming conflicts. Best practice::

	tx_extkey_yourprocedure

2.) Recieve the table name as the first parameter of the procedure::

	DELIMITER $$
	CREATE PROCEDURE `tx_extkey_yourprocedure` (IN tableName VARCHAR(255), ... more parameters ....)
	BEGIN                                                                                      
		... your code here
	END$$
	DELIMITER ;

Mind the whitespace after DELIMITER: DELIMITER[space]$$ and DELIMITER[space];

3.) Create the temporary table definition query with tableName by use of concat::

	SET @query = concat("CREATE TEMPORARY TABLE ", @tableName, " ... your table definition ...");
	PREPARE stmt FROM @query;
	EXECUTE stmt;                                                                            
	DEALLOCATE PREPARE stmt; 

Hint:: 

	Even if you create a permanet table, you will not see it in the database, 
	because the table of tableName is cleaned up by the calling PHP class afterwards.

4.) Prevent SQL injections by escaping yout queries where necessary::

	TODO: how to do this

5.) Write your code. This technology is rather recommended for read access than for write access. 
As you circumvent the TCE it's completly up to you to build the right queries. 
You need deep knowledge of the data model of TYPO3, if you want to modify data of existing tabels.
It is recomended to use the TCE for to modify data as long as you don't know exactly what you do.

6.) Document your code. Use speaking names. Because SQL is not a very human readable language 
provide comments what your queries do. You will be thankfull within a few months.


 
    







