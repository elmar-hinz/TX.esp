=================
Integrator Manual
=================

Breadcrumb
==========

Install the stored procedure
----------------------------

See Administrator Manual. 

You can i.e. use your own implementation of the stored procedure, 
by setting your procedure name in TypoScript::

	lib.tx_esp.breadcrumb.userFunc.storedProcedure = tx_myext_myprocedure

Include the static template
---------------------------

Include the static template *esp breadcrumb example* in your TypoScript template.

Setup Typoscript
---------------- 

Use the breadcrumb lib where you like::	
	
	page = PAGE
	page.20 =< lib.tx_esp.breadcrumb

Adjust it to your needs::

	lib.tx_esp.breadcrumb.userFunc.stdWrap.wrap2 = <nav id="my_id">|</nav>





