<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "esp".
 *
 * Auto generated 24-07-2015 21:11
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'esp - Call Stored Procedures from TypoScript',
	'description' => '
Back to speed!	
-- Model: Roaring fast MySQL Stored Procedures, View: Good old TypoScript. -- A fast alternative to Extbase. 
-- Ships the classes SimpleRenderer for single table queries and JoinRenderer for joined table queries. 
-- You can easily add your own renderer classes for special tasks. Just implement the method render() like seen in SimpleRenderer or JoinRenderer.
',
	'category' => 'misc',
	'version' => '6.2.1',
	'state' => 'beta',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'Elmar Hinz',
	'author_email' => 't3elmar@gmail.com',
	'author_company' => 'Elmar Hinz',
	'constraints' => 
	array (
		'depends' => 
		array (
			'php' => '5.3.2-5.999.999',
			'typo3' => '6.0.0-6.2.999',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

