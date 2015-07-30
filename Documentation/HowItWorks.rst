..  Editor configuration
	...................................................
	* utf-8 with BOM as encoding
	* tab indent with 4 characters for code snippet.
	* optional: soft carriage return preferred.

.. include:: Includes.txt


How it Works
============

Using multi results to access the result
----------------------------------------

*Stored procedures* are called with parameters. The parameters can be used as 
bidirectional channels by setting them to *INOUT*. However, they can not pass 
arrays, so they are not usable to return a multiline result. 

To access the multiline result of a SELECT query within the stored procedure 
the multi result feature of mysqli is used. 

The order of queries and results
--------------------------------

The first query to the database sets up the parameters for the procedure if any.

The second query calls the procedure. It returns TRUE on success as the first result. 
The multiline result is accessed by the second result if any. Any further results are 
discarded for now, until the result queue is empty. 

By a third query the outgoing parameters are retrieved if any.

