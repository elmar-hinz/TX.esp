..  Editor configuration
	...................................................
	* utf-8 with BOM as encoding
	* tab indent with 4 characters for code snippet.
	* optional: soft carriage return preferred.

.. include:: Includes.txt

==============
Considerations
==============

Why stored procedures?
----------------------

There are three mayor motivations to develop and use this extension:

	1. Performance of execution
	2. Speed of development
	3. Simplicity of development 

However, it depends on, when we can reach this goals and when not.

What are they for?
-------------------

The original field of stored procedures is to perform update operations on complex databases, 
with regard to the referential integrity, by encapsulating business logic into the datase 
and separating it from client programming on top.

In contrast in the field of TYPO3 the referential integrity of update operations is controlled 
by the TCE. Here stored procedures focus on select operations avoiding heavy PHP layers
like extbase for read operations, especially for non-cached ones as USER_INT.  

Performance of execution
------------------------

It is not that *Mysql Stored Procedures* are generally faster by nature than calls from PHP. 
Both can be cached by the database to some extend. It is a complex field influenced by multiple
factors.  In the context of TYPO3 there are usecases, where you can gain performance using
*Mysql Stored Procedures* in comparism to other solutions.

Reducing the amount of calls to the database
............................................

A direct merit for performance is to replace multiple calls to the database by a single
one to a stored procedure. Querying the rootline or a menu tree are typical situations when 
recursive calls to the same table are done.

Avoiding heavy PHP layers
.........................

An indirect merit for performance is, to avoid heavy, object intensive PHP layers.

Speed and simplicity of development
-----------------------------------

Extbase as an *Object Relational Mapper* is focused on modelling the domain in form of PHP
objects. In theory you would not bother with the persistance layer at all. In practice you
addintionally have to define the persistance layer in form of *TCA* and *SQL definitions*. Overall
you deal with 3 layers, the database, PHP and TypoScript.

On contrarst |extension_name| is focused on the relational model of the database. 
*SQL joins* combine the data from muliple tables instead of using a domain model. 
You go with two layers, *stored procedures* for the model and *TypoScript* for the 
presentation. There is no such thing like a domain model in between.

You could say, that thinking in form of a relational data model is rather oldschool. 
However, it's not a question of fashion. It depends on the field of the application, 
if a relational or an object orientated data model is the better solution for it.

If your data is already organized in a relational model for historical reasons, it is kind
of overhead in planning, in development and in performance to put an additional object 
orientated layer on top of it.

After all it depends on the siklls of the development team, if it prefers to focus on PHP 
or on TypoScript and SQL.

Why not Mysql Views?
--------------------

Good interjection. In theory you would use *SQL views* to do read operations on the database, 
while stored procedures are rather targeted for write operations. One issue with views in MySql 
is performance. Exactly where views start to become ambitious, they are said to lose their 
ability to access table indexes an slow down matters in MySql. 

The second important point is, that stored procedures provide a real programming language,
a basic prerequisite to do model programming.

Having said this, there are many usecases, where views are a good choice as long as they 
can make use of the *merge algorithem*, just to address the most important detail. 


