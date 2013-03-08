Example Usecases
================

Breadcrumb/rootline
-------------------

To find the rootline of a given page recursive queries upon the same table have to be executed.
This is not supported by SQL itself. Implementing it by use of PHP results in an exessive ping
pong betwenn PHP and the database. Encapsulated in a stored procedure, it is simple loop, 
maybe faster. 

A breadcrumb example is included. Still a prove of concept. The stored procedure has to be 
improved a lot.


Joined tables for search applications
-------------------------------------

Many web based search interfaces call SQL queries on a set of tables as JOINS. Here we can 
gain simplicity and performance with stored procedures.  A joined table query results in a 
filtered cartesian product of the involved tables. Typically we want to display the result 
in a hierarchical way first grouping the rows by the first table, then by the second, optionally 
more levels. 

A rendering class is planned to be included to controll this kind of grouped rendering. 

