Example usecases
================

Breadcrumb/rootline
-------------------

To find the rootline of a given page recursive queries upon the same table have to be executed.
This is not supported by SQL itself. Implementing it by use of PHP results in an exessive ping
pong betwenn the PHP layer and the database at the cost of a lot of resources.  Compiled into a 
stored procedure, it is simple loop, and should be faster. For the example of a breadcrumb a
stored procedure and a TypoScript template is included.

Joined tables
-------------

Many web based search interfaces call SQL queries on a set of tables as a JOIN. Here we can 
gain simplicity and performance with stored procedures.  A joined table query results in a 
filtered cartesian product of the involved tables. Typically we want to display the result 
in a hierarchical way first grouping the rows by the first table, then by the second, optionally 
more levels. A rendering class is included to controll this kind of grouped rendering. 

