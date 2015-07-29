<?php

/*********************************************************************
* Extension configuration file for ext "esp".
*
* Generated by ext 28-07-2015 16:31 UTC
*
* https://github.com/t3elmar/Ext
*********************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'esp - Call Stored Procedures from TypoScript',
  'description' => '
Back to speed!	
-- Model: Roaring fast MySQL Stored Procedures, View: Good old TypoScript. -- A fast alternative to Extbase. 
-- Ships the classes SimpleRenderer for single table queries and JoinRenderer for joined table queries. 
-- You can easily add your own renderer classes for special tasks. Just implement the method render() like seen in SimpleRenderer or JoinRenderer.
',
  'category' => 'misc',
  'version' => '6.2.2',
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
      'typo3' => '6.0.0-6.2.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'user' => 't3elmar',
  'comment' => 'Renaming fetchAssociated to fetchAssociative. Keeping old API for backward compatibility but depreciated.',
);

?>