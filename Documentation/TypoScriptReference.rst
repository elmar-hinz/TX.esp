..  Editor configuration
	...................................................
	* utf-8 with BOM as encoding
	* tab indent with 4 characters for code snippet.
	* optional: soft carriage return preferred.

.. include:: Includes.txt

TypoScript Reference
=====================

tx_esp_MysqlStoredProcedure
---------------------------

Context
.......

::

	your.personal.path = USER_INT
	your.personal.path {
		userFunc = tx_esp_MysqlStoredProcedure->main
		userFunc {
			... properties go here ...
		}
	}

Properties
..........

.. ..................................
.. container:: table-row

	Property
		**storedProcedure**

	Data type
		string

	Description
		The name of the stored procedure in the database. It should be prefixed with *tx_*
		plus the extension key. 

	Example 
		:: 

			storedProcedure = tx_myextension_superquery


.. .................................
.. container:: table-row

	Property
			**parameterOrder**

	Data type
		comma separated list (whitespace is allowed)

	Description
		Declaring parameters for the stored procedure in their order. 

	Example
		::
		
			parameterOrder = page, language, count 
		
		In this example count is an only OUT parameter.


.. .................................
.. container:: table-row

	Property
                **parameters**
	
	Data type
		hashlist of string/stdWrap
	
	Description
		Setting values for the *ingoing* parameters. The order doesn't matter here.
		The keys are the parameter names. The values are strings, stdWrap can be applied. 

	Example
		::
			
                        parameters {
                                page.data = page:uid
                                language = 0
                        }


.. .................................
.. container:: table-row

        Property
                **renderer**
	
	Data type
		content object

	Description
		tx_esp_SimpleRenderer, tx_esp_JoinRenderer or a userdefined renderer.

        The INOUT and OUT parameters of the stored procedures are 
		accesible in the current data by the keys defined by the property 
		*parameterOrder*.
	
	Example
		::

			renderer = USER
			renderer.userFunc = tx_esp_SimpleRenderer
			renderer.userFunc {
				... properties go here ...
			}

			renderer = USER
			renderer.userFunc = tx_esp_JoinRenderer
			renderer.userFunc {
				... properties go here ...
			}

		See the breadcrumb template (static/breadcrumb/setup.txt).


.. .................................
.. container:: table-row

	Property
		**stdWrap**
	
	Data type
		stdWrap

	Description
		Wraps the overall result.

	Example
		::
	
			stdwrap.wrap = <div class="myextension">|</div>

tx_esp_SimpleRenderer
---------------------

We simply display a result row by row.

Context
.......

::

	renderer = USER
	renderer.userFunc = tx_esp_SimpleRenderer
	renderer.userFunc {
		... properties go here ...
	}


Properties
..........

.. .................................
.. container:: table-row

	Property
		**rowRenderer**
	
	Data type
                cObject

	Description
                Renders each row. Like renderObj of CONTENT.

	Example
		::

                      rowRenderer = TEXT
                      rowRenderer {
                        field = myfield
                        wrap = <li>|</li>
                      }
	

.. .................................
.. container:: table-row

	Property
		**stdWrap**
	
	Data type
		stdWrap

	Description
		Wraps the overall result.

	Example
		::
	
			stdwrap.wrap = <ul>|</ul>


tx_esp_JoinRenderer
-------------------

We display the result of a joined table quiery in 
a nested way.

Each level displays one of the joined tables. The 
entries of the second table (level 2) are grouped
below the headlines of the first table (level 1) 
and so on. Each level gets a configuration. 

See Usecases for a practical example. 

Context
.......

::

	renderer = USER
	renderer.userFunc = tx_esp_JoinRenderer
	renderer.userFunc {
		... properties go here ...
	}

Properties
..........

.. .................................
.. container:: table-row

	Property
		**levles**
	
	Data type
		hashlist of levels	

	Description
		Each level gets it's configuration here.  
		The first level key is *1*, the second level key is *2* and so on. 

		Be exact with the numbers! You don't have the freedom of COAs.

	Example
		::
	
			levels {
				1 {
					... level 1 goes here ...
				}
				2 {
					... level 2 goes here ...
				}
				... more levels ...
			}

.. .................................
.. container:: table-row

	Property
		**stdWrap**
	
	Data type
		stdWrap

	Description
		Wraps the overall result.

	Example
		::
	
			stdwrap.wrap = <div class="myrenderer">|</div>

tx_esp_JoinRenderer: level
--------------------------

Context
.......

::

	levels {
		1 {
			... properties go here ...
		}
		2 {
			... properties go here ...
		}
		...
	}

Properties
..........

.. .................................
.. container:: table-row

	Property
		**levelFields**

	Data type
		comma separated list (whitespace is allowed)

	Description
		The fields belonging to the table of this level. They are used to group the level.
	
	Example
		::
		
			levelFields = section_uid, section_header, section_footer
	
		
.. .................................
.. container:: table-row

	Property
		**stdWrap**
	
	Data type
		stdWrap

	Description
		Important stdWrap. It is used for at least three purposes:

			1. to wrap the single entries of the level
			2. to output the single entries of the level
			3. to wrap all entries of sublevels in common (innerWrap)

		The data of the entries are available in the current data.

	Example
		::

			stdWrap {
				wrap = <li class="level1_each">|</li>
				preCObject = TEXT
				preCObject {
					field = section_header  
					wrap = <p class="header">|</p>
				}
				innerWrap = <ul class="level2_all">|</ul>
				postCObject = TEXT
				postCObject {
					field = section_footer 
					wrap = <p class="footer">|</p>
				}
			}

