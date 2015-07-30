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

:: 

    typo3.org   >   TYPO3 CMS   >   Overview    >   Requirements

A breadcrumb example is included. Still a prove of concept. 

In this case the stored procedure is challenging while the TypoScript is simple,
calling the class **SimpleRenderer**.

Nested results of search applications
--------------------------------------

As an example we imagine a database of CDs. Each song belongs to a CD. 
Each CD belongs to an artist or group. The database would contain three
tables *artists*, *cds*, *songs*. A query would **join** this tables.
A search interface could order the results in three nested levels, first artist, 
second CD, third song. In the HTML output this could be nested lists or headlines.

::

    Search form: 
    
        [ love ] [ => submit  =< ]

    Result list:

        Artist A 
            CD 1
                1. song: 
                               ... I love you, yeah ... 
                2. song: 
                               ... love, love, love ... 
                3. song: 
                               ... xxx glove xxx ... 
            CD 2
                1. song
                               ... love, love me do ... 
                2. song
                               ... I love you, I love you, I love you ... 
                3. song
                               ... never loved me ... 
                4. song
                               ... I love you ... 

            Artist info: ... (The level fields are still accessible after the nested list.)
            
        Artist B 
            CD 1
                1. song
                               ... love me tender, love me sweet ... 
                2. song
                               ... I love you ... 

            Artist info: ... (The level fields are still accessible after the nested list.)

        Artist C 
            CD 1
                1. song
                               ... baby, love never felt ...

            Artist info: ... (The level fields are still accessible after the nested list.)

The rendering class **JoinRenderer** controls this kind of nested rendering 
of joined table queries. 

You can also query M:N relations.Here *artists* and *cds* could be M:N related, 
if you would like to work with persons instead of group names. 
Table model and stored procedure would differ, while the TypoScript view would be the same.



