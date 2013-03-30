<?php

/*********************************************************************
* Extension configuration file for ext "esp".
*
* Generated by ext 30-03-2013 10:21 UTC
*
* https://github.com/t3elmar/Ext
*********************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Stored Procedures for TypoScript',
  'description' => '
Interface to call Mysql Stored Procedures directly from TypoScript. 

This is the implemantation of the ideas form a workshop in TYPO3 CampRheinRhur 2012. 

Input from the workshop:

 * TYPO3 works with mysql_pconnect. The scope of temporary tables is questionable.
 * To get them nonambiguous, use tablenames generated from random numbers.
 * Configure TypoScript of joined tables similiar to menus. (JoH)

The documentation is written in the upcomming sphinx format. 
TER still does not render it, yet. 

 * You find HTML doc under EXT:exp/Manual/
 * TS-online: https://github.com/t3elmar/esp/blob/master/Documentation/TypoScriptReference.rst  

Clone the latest version from github: https://github.com/t3elmar/esp.git
',
  'category' => 'misc',
  'shy' => 0,
  'version' => '1.1.0',
  'dependencies' => '',
  'conflicts' => '',
  'priority' => '',
  'loadOrder' => '',
  'module' => '',
  'state' => 'alpha',
  'uploadfolder' => 0,
  'createDirs' => '',
  'modify_tables' => '',
  'clearcacheonload' => 0,
  'lockType' => '',
  'author' => 'Elmar Hinz',
  'author_email' => 't3elmar@gmail.com',
  'author_company' => '',
  'CGLcompliance' => '',
  'CGLcompliance_note' => '',
  'constraints' => 
  array (
    'depends' => 
    array (
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  '_md5_values_when_last_written' => 'a:166:{s:9:"ChangeLog";s:4:"e756";s:17:"ext_localconf.php";s:4:"106d";s:18:"ext_procedures.sql";s:4:"0a10";s:14:"ext_tables.php";s:4:"eadd";s:10:"index.html";s:4:"b101";s:11:"README.html";s:4:"b101";s:49:"Classes/class.tx_esp_StoredProcedure.php.original";s:4:"1732";s:27:"Classes/StoredProcedure.php";s:4:"afde";s:37:"Documentation/_IncludedDirectives.rst";s:4:"772a";s:37:"Documentation/AdministratorManual.rst";s:4:"6b89";s:27:"Documentation/ChangeLog.rst";s:4:"9490";s:32:"Documentation/Considerations.rst";s:4:"5de7";s:28:"Documentation/HowItWorks.rst";s:4:"671f";s:23:"Documentation/index.rst";s:4:"2a91";s:23:"Documentation/Index.rst";s:4:"2a91";s:34:"Documentation/IntegratorManual.rst";s:4:"ecd6";s:26:"Documentation/Usecases.rst";s:4:"4bb6";s:44:"Documentation/Images/IntroductionPackage.png";s:4:"cdeb";s:30:"Documentation/Images/Typo3.png";s:4:"4fac";s:61:"Documentation/Images/AdministratorManual/ExtensionManager.png";s:4:"14fc";s:47:"Documentation/Images/UserManual/BackendView.png";s:4:"ba6c";s:29:"Documentation/_mymake/conf.py";s:4:"d0fd";s:30:"Documentation/_mymake/make.bat";s:4:"2905";s:30:"Documentation/_mymake/Makefile";s:4:"7dbd";s:65:"Documentation/_mymake/_build/doctrees/_IncludedDirectives.doctree";s:4:"36d9";s:65:"Documentation/_mymake/_build/doctrees/AdministratorManual.doctree";s:4:"3191";s:55:"Documentation/_mymake/_build/doctrees/ChangeLog.doctree";s:4:"f59d";s:60:"Documentation/_mymake/_build/doctrees/Considerations.doctree";s:4:"e15a";s:56:"Documentation/_mymake/_build/doctrees/environment.pickle";s:4:"ab1b";s:56:"Documentation/_mymake/_build/doctrees/HowItWorks.doctree";s:4:"acd2";s:51:"Documentation/_mymake/_build/doctrees/index.doctree";s:4:"7f4d";s:51:"Documentation/_mymake/_build/doctrees/Index.doctree";s:4:"d3c6";s:62:"Documentation/_mymake/_build/doctrees/IntegratorManual.doctree";s:4:"45d5";s:54:"Documentation/_mymake/_build/doctrees/Usecases.doctree";s:4:"5cd5";s:58:"Documentation/_mymake/_build/html/_IncludedDirectives.html";s:4:"66a0";s:58:"Documentation/_mymake/_build/html/AdministratorManual.html";s:4:"013b";s:48:"Documentation/_mymake/_build/html/ChangeLog.html";s:4:"8221";s:53:"Documentation/_mymake/_build/html/Considerations.html";s:4:"2485";s:47:"Documentation/_mymake/_build/html/genindex.html";s:4:"ddff";s:49:"Documentation/_mymake/_build/html/HowItWorks.html";s:4:"bf03";s:44:"Documentation/_mymake/_build/html/Index.html";s:4:"8bad";s:44:"Documentation/_mymake/_build/html/index.html";s:4:"8594";s:55:"Documentation/_mymake/_build/html/IntegratorManual.html";s:4:"8aa5";s:45:"Documentation/_mymake/_build/html/objects.inv";s:4:"4968";s:45:"Documentation/_mymake/_build/html/search.html";s:4:"b1a9";s:48:"Documentation/_mymake/_build/html/searchindex.js";s:4:"9955";s:47:"Documentation/_mymake/_build/html/Usecases.html";s:4:"7b8b";s:51:"Documentation/_mymake/_build/html/_images/Typo3.png";s:4:"4fac";s:66:"Documentation/_mymake/_build/html/_sources/_IncludedDirectives.txt";s:4:"772a";s:66:"Documentation/_mymake/_build/html/_sources/AdministratorManual.txt";s:4:"6b89";s:56:"Documentation/_mymake/_build/html/_sources/ChangeLog.txt";s:4:"9490";s:61:"Documentation/_mymake/_build/html/_sources/Considerations.txt";s:4:"5de7";s:57:"Documentation/_mymake/_build/html/_sources/HowItWorks.txt";s:4:"671f";s:52:"Documentation/_mymake/_build/html/_sources/index.txt";s:4:"2a91";s:52:"Documentation/_mymake/_build/html/_sources/Index.txt";s:4:"2a91";s:63:"Documentation/_mymake/_build/html/_sources/IntegratorManual.txt";s:4:"ecd6";s:55:"Documentation/_mymake/_build/html/_sources/Usecases.txt";s:4:"4bb6";s:57:"Documentation/_mymake/_build/html/_static/ajax-loader.gif";s:4:"ae66";s:51:"Documentation/_mymake/_build/html/_static/basic.css";s:4:"e750";s:60:"Documentation/_mymake/_build/html/_static/comment-bright.png";s:4:"0c85";s:59:"Documentation/_mymake/_build/html/_static/comment-close.png";s:4:"2635";s:53:"Documentation/_mymake/_build/html/_static/comment.png";s:4:"882e";s:54:"Documentation/_mymake/_build/html/_static/contents.png";s:4:"a547";s:53:"Documentation/_mymake/_build/html/_static/doctools.js";s:4:"5ff5";s:58:"Documentation/_mymake/_build/html/_static/down-pressed.png";s:4:"ebe8";s:50:"Documentation/_mymake/_build/html/_static/down.png";s:4:"f6f3";s:50:"Documentation/_mymake/_build/html/_static/file.png";s:4:"6587";s:51:"Documentation/_mymake/_build/html/_static/jquery.js";s:4:"ddb8";s:51:"Documentation/_mymake/_build/html/_static/minus.png";s:4:"8d57";s:50:"Documentation/_mymake/_build/html/_static/plus.png";s:4:"0125";s:54:"Documentation/_mymake/_build/html/_static/pygments.css";s:4:"4276";s:52:"Documentation/_mymake/_build/html/_static/README.txt";s:4:"f3be";s:55:"Documentation/_mymake/_build/html/_static/s-buttons.png";s:4:"e2ee";s:56:"Documentation/_mymake/_build/html/_static/searchtools.js";s:4:"d550";s:59:"Documentation/_mymake/_build/html/_static/shadow-footer.jpg";s:4:"1fa6";s:65:"Documentation/_mymake/_build/html/_static/shadow-page-505x505.png";s:4:"c7bd";s:56:"Documentation/_mymake/_build/html/_static/typo3-logo.png";s:4:"718a";s:56:"Documentation/_mymake/_build/html/_static/typo3basic.css";s:4:"cb1a";s:57:"Documentation/_mymake/_build/html/_static/typo3sphinx.css";s:4:"a0d1";s:55:"Documentation/_mymake/_build/html/_static/underscore.js";s:4:"b538";s:56:"Documentation/_mymake/_build/html/_static/up-pressed.png";s:4:"8ea9";s:48:"Documentation/_mymake/_build/html/_static/up.png";s:4:"ecc3";s:55:"Documentation/_mymake/_build/html/_static/websupport.js";s:4:"9e61";s:70:"Documentation/_mymake/_build/html/_static/fonts/share-bold-webfont.eot";s:4:"8c37";s:70:"Documentation/_mymake/_build/html/_static/fonts/share-bold-webfont.svg";s:4:"d80b";s:70:"Documentation/_mymake/_build/html/_static/fonts/share-bold-webfont.ttf";s:4:"2199";s:71:"Documentation/_mymake/_build/html/_static/fonts/share-bold-webfont.woff";s:4:"ab6f";s:72:"Documentation/_mymake/_build/html/_static/fonts/share-italic-webfont.eot";s:4:"d209";s:72:"Documentation/_mymake/_build/html/_static/fonts/share-italic-webfont.svg";s:4:"b6a5";s:72:"Documentation/_mymake/_build/html/_static/fonts/share-italic-webfont.ttf";s:4:"298c";s:73:"Documentation/_mymake/_build/html/_static/fonts/share-italic-webfont.woff";s:4:"3f06";s:73:"Documentation/_mymake/_build/html/_static/fonts/share-regular-webfont.eot";s:4:"a366";s:73:"Documentation/_mymake/_build/html/_static/fonts/share-regular-webfont.svg";s:4:"4e5d";s:73:"Documentation/_mymake/_build/html/_static/fonts/share-regular-webfont.ttf";s:4:"3a9a";s:74:"Documentation/_mymake/_build/html/_static/fonts/share-regular-webfont.woff";s:4:"b60d";s:57:"Documentation/_mymake/_build/html/_static/icons/howto.png";s:4:"efbe";s:60:"Documentation/_mymake/_build/html/_static/icons/question.png";s:4:"b0c5";s:55:"Documentation/_mymake/_build/html/_static/icons/tip.png";s:4:"1534";s:59:"Documentation/_mymake/_build/html/_static/icons/warning.png";s:4:"4f88";s:57:"Documentation/_mymake/_build/html/_static/icons/world.png";s:4:"009f";s:53:"Documentation/_mymake/_themes/typo3sphinx/layout.html";s:4:"debc";s:53:"Documentation/_mymake/_themes/typo3sphinx/LICENSE.txt";s:4:"12aa";s:52:"Documentation/_mymake/_themes/typo3sphinx/theme.conf";s:4:"807e";s:64:"Documentation/_mymake/_themes/typo3sphinx/static/ajax-loader.gif";s:4:"ae66";s:67:"Documentation/_mymake/_themes/typo3sphinx/static/comment-bright.png";s:4:"0c85";s:66:"Documentation/_mymake/_themes/typo3sphinx/static/comment-close.png";s:4:"2635";s:60:"Documentation/_mymake/_themes/typo3sphinx/static/comment.png";s:4:"882e";s:61:"Documentation/_mymake/_themes/typo3sphinx/static/contents.png";s:4:"a547";s:60:"Documentation/_mymake/_themes/typo3sphinx/static/doctools.js";s:4:"5ff5";s:65:"Documentation/_mymake/_themes/typo3sphinx/static/down-pressed.png";s:4:"ebe8";s:57:"Documentation/_mymake/_themes/typo3sphinx/static/down.png";s:4:"f6f3";s:57:"Documentation/_mymake/_themes/typo3sphinx/static/file.png";s:4:"6587";s:58:"Documentation/_mymake/_themes/typo3sphinx/static/jquery.js";s:4:"ddb8";s:58:"Documentation/_mymake/_themes/typo3sphinx/static/minus.png";s:4:"8d57";s:57:"Documentation/_mymake/_themes/typo3sphinx/static/plus.png";s:4:"0125";s:61:"Documentation/_mymake/_themes/typo3sphinx/static/pygments.css";s:4:"4276";s:59:"Documentation/_mymake/_themes/typo3sphinx/static/README.txt";s:4:"f3be";s:62:"Documentation/_mymake/_themes/typo3sphinx/static/s-buttons.png";s:4:"e2ee";s:66:"Documentation/_mymake/_themes/typo3sphinx/static/shadow-footer.jpg";s:4:"1fa6";s:72:"Documentation/_mymake/_themes/typo3sphinx/static/shadow-page-505x505.png";s:4:"c7bd";s:63:"Documentation/_mymake/_themes/typo3sphinx/static/typo3-logo.png";s:4:"718a";s:63:"Documentation/_mymake/_themes/typo3sphinx/static/typo3basic.css";s:4:"cb1a";s:64:"Documentation/_mymake/_themes/typo3sphinx/static/typo3sphinx.css";s:4:"a0d1";s:62:"Documentation/_mymake/_themes/typo3sphinx/static/underscore.js";s:4:"b538";s:63:"Documentation/_mymake/_themes/typo3sphinx/static/up-pressed.png";s:4:"8ea9";s:55:"Documentation/_mymake/_themes/typo3sphinx/static/up.png";s:4:"ecc3";s:62:"Documentation/_mymake/_themes/typo3sphinx/static/websupport.js";s:4:"9e61";s:77:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-bold-webfont.eot";s:4:"8c37";s:77:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-bold-webfont.svg";s:4:"d80b";s:77:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-bold-webfont.ttf";s:4:"2199";s:78:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-bold-webfont.woff";s:4:"ab6f";s:79:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-italic-webfont.eot";s:4:"d209";s:79:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-italic-webfont.svg";s:4:"b6a5";s:79:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-italic-webfont.ttf";s:4:"298c";s:80:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-italic-webfont.woff";s:4:"3f06";s:80:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-regular-webfont.eot";s:4:"a366";s:80:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-regular-webfont.svg";s:4:"4e5d";s:80:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-regular-webfont.ttf";s:4:"3a9a";s:81:"Documentation/_mymake/_themes/typo3sphinx/static/fonts/share-regular-webfont.woff";s:4:"b60d";s:64:"Documentation/_mymake/_themes/typo3sphinx/static/icons/howto.png";s:4:"efbe";s:67:"Documentation/_mymake/_themes/typo3sphinx/static/icons/question.png";s:4:"b0c5";s:62:"Documentation/_mymake/_themes/typo3sphinx/static/icons/tip.png";s:4:"1534";s:66:"Documentation/_mymake/_themes/typo3sphinx/static/icons/warning.png";s:4:"4f88";s:64:"Documentation/_mymake/_themes/typo3sphinx/static/icons/world.png";s:4:"009f";s:67:"Documentation/_mymake/_themes/typo3sphinx-material/2by9-colors.html";s:4:"3bbd";s:74:"Documentation/_mymake/_themes/typo3sphinx-material/for-stealing-styles.css";s:4:"210b";s:65:"Documentation/_mymake/_themes/typo3sphinx-material/navigation.png";s:4:"879c";s:72:"Documentation/_mymake/_themes/typo3sphinx-material/pygments-ORIGINAL.css";s:4:"d625";s:61:"Documentation/_mymake/_themes/typo3sphinx-material/README.txt";s:4:"5523";s:61:"Documentation/_mymake/_themes/typo3sphinx-material/styles.css";s:4:"1b1e";s:72:"Documentation/_mymake/_themes/typo3sphinx-material/typo3-logo-118x34.png";s:4:"718a";s:84:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/aside-shade.png";s:4:"2cb1";s:77:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/beta.png";s:4:"a09e";s:76:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/f-g.png";s:4:"b89b";s:81:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/featured.png";s:4:"8fd9";s:85:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/h-bar-nav-li.png";s:4:"3dc6";s:80:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/nav-sub.png";s:4:"c9d8";s:82:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/s-buttons.png";s:4:"e2ee";s:86:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/shadow-footer.jpg";s:4:"1fa6";s:84:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/shadow-page.jpg";s:4:"4957";s:84:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/shadow-site.png";s:4:"7390";s:92:"Documentation/_mymake/_themes/typo3sphinx-material/preview-typo3-org/smart-search-arrows.png";s:4:"9d03";s:29:"Tests/StoredProcedureTest.php";s:4:"81c0";s:37:"Tests/TemporaryTableBehaviourTest.php";s:4:"e895";s:31:"static/breadcrumb/constants.txt";s:4:"d41d";s:27:"static/breadcrumb/setup.txt";s:4:"78d3";}',
  'user' => 't3elmar',
  'comment' => 'Back to ALPHA. Handling for non created tables.',
);

?>