..  Editor configuration
	...................................................
	* utf-8 with BOM as encoding
	* tab indent with 4 characters for code snippet.
	* optional: soft carriage return preferred.

.. include:: Includes.txt

Example Usecases
================

Breadcrumb/rootline
-------------------

To find the rootline of a given page recursive queries upon the same table have to be 
executed. This is not supported by SQL itself. Implementing it by use of PHP results in 
an exessive ping pong betwenn PHP and the database. Encapsulated in a stored procedure, 
it is simple loop executed inside the database itself. 

A breadcrumb example is included. Still a prove of concept. 

Nested results of search applications
--------------------------------------

As an example we imagine a database of CDs. Each song belongs to a CD. 
Each CD belongs to an artist or group. The database would contain three
tables *artist*, *cd*, *song*. A query would **join** this tables.
A search interface could order the results in three nested levels, first artist, 
second CD, third song. In the HTML output this could be nested lists or sections.

The rendering class **JoinRenderer** controls this kind of nested rendering 
of joined table queries. 

