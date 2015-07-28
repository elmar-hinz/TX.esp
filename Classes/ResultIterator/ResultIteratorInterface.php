<?php

namespace ElmarHinz\Esp\ResultIterator;

/***************************************************************
*  Copyright notice
*
*  (c) 2012 - 2015 Elmar Hinz <elmar.hinz@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

interface ResultIteratorInterface {

	/*
	* Traverse the query result in an associated way
	*
	* Returns the result of a db query as rows.
	* Each result row is an array of fieldname/value pairs. 
	* Once the all results are traversed it returns FALSE.
	* There is no reset option.
	*
	* As example compare mysqli 
	*
	* @see http://php.net/manual/de/mysqli-result.fetch-assoc.php
	* @return mixed the next result row or FALSE
	*/
	public function fetchAssociative();

	/*
	 * Alias to fetchAssociative.
	 *
	 * DEPRECIATED. Will be removed with 7.x.
	 */
	public function fetchAssociated();

}


?>
