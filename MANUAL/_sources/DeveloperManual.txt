================
Developer Manual
================

Stored procedures
=================

Editing stored procedures
-------------------------

To quickly fix a stored procedure you can use phpMyAdmin or adminer. 
A more comfortable IDE for the development of SQL including stored procedures
is `MySQL Workbench`.

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

2.) Create the stored procedure with its parameters::

	DELIMITER $$
	CREATE PROCEDURE `tx_extkey_yourprocedure` (INOUT firstParameter VARCHAR(255), ... more parameters ....)
	BEGIN                                                                                      
		... your code here
	END$$
	DELIMITER ;

Mind the whitespace after DELIMITER: DELIMITER[space]$$ and DELIMITER[space];

3.) Fill the procedure:

Optionally set values of the return parameters::

  SET firstParameter = ... something ...

Optionally return  a SELECT query::

  SELECT * FROM ....

As you circumvent the TCE it's completly up to you to build the right queries. 
You need deep knowledge of the data model of TYPO3, if you want to modify data 
of existing tabels. It is recomended to use the TCE for to modify data as long as you 
don't know exactly what you do.

4.) Prevent SQL injections by escaping your queries where necessary.

Specially keep this in mind, when you are using `CONCAT()` to create the query.

5.) Document your code. Use speaking names. Because SQL is not a very human readable language 
provide comments what your queries do. You will be thankfull within a few months.

