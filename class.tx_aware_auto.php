<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Morten Tranberg Hansen (mth at cs dot au dot dk)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This class uses backend hooks to automatically create events
 * whenever database records are created or updated.
 *
 * @author Morten Tranberg Hansen <mth at cs dot au dot dk>
 * @date   November 10 2009
 */

class tx_aware_auto {

	public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$reference) {

		$new = t3lib_div::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['auto_new'], true);
		$update = t3lib_div::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['auto_update'], true);
		
    if ($status === 'update' && t3lib_div::inArray($update, $table)) {          //record updated

			tx_aware::addEvent('update'.'#'.$table.':'.$id, array('username'=>$GLOBALS['BE_USER']->user['username'], 'fields'=>$fieldArray), true);

    } else if ($status === 'new' && t3lib_div::inArray($new, $table)) {      // new record

			tx_aware::addEvent('new'.'#'.$table, array('username'=>$GLOBALS['BE_USER']->user['username'], 'id'=>$reference->substNEWwithIDs[$id]), true);

    } 

	}

}

if (defined("TYPO3_MODE") && isset($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/aware/class.tx_aware_auto.php"]))	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/aware/class.tx_aware_auto.php"]);
}

?>