..  Editor configuration
	...................................................
	* utf-8 with BOM as encoding
	* tab indent with 4 characters for code snippet.
	* optional: soft carriage return preferred.

.. Includes roles, substitutions, ...
.. include:: _IncludedDirectives.rst

===========================
Extension: |extension_name|
===========================

:Extension name: |extension_name|
:Extension key: |extension_key|
:Version: |version|
:Description: Calling stored procedures from TypoScript
:Language: en
:Author: |author|
:Creation: |creation| 
:Generation: |time|
:Licence: Open Content License available from `www.opencontent.org/opl.shtml <http://www.opencontent.org/opl.shtml>`_

The content of this document is related to TYPO3, a GNU/GPL CMS/Framework available from `www.typo3.org
<http://www.typo3.org/>`_

What does it do?
=================

The extension *Stored Porcedures* is an interface to access *MySql Stored Procedures*
directly from TypoScript for read and write operations on the underlying database. 
Performance can be gained both in coding and in program execution if you choose it for 
the right usecases. For the beginning only MySql will be supported. 

**Table of Contents**


.. toctree::
	:maxdepth: 3

	Considerations
	HowItWorks
	Usecases
	AdministratorManual
        IntegratorManual
	DeveloperManual
	TypoScriptReference
	ChangeLog


