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
 * This is an ajax adapter between the frontend tx_aware_client and the
 * tx_aware backend.
 *
 * @author Morten Tranberg Hansen <mth at cs dot au dot dk>
 * @date   November 10 2009
 */

class tx_aware_ajax {

	/*
	 * AJAX function polled by the tx_aware_client.
	 *
	 */
	public function clientRequest(array $params = array(), TYPO3AJAX &$ajaxObj) {

		// add events
		$adds = t3lib_div::_GET('adds');
		if (!empty($adds)) {
			foreach($adds as $add) {
				tx_aware::addEvent($add['channel'], $add['data'], true);
			}
		}

		// get events
		$channels = t3lib_div::_GET('channels');
		$events = array();
		if (is_array($channels)) {
			foreach($channels as $channel) {
				$events = array_merge($events, tx_aware::getEvents($channel));
			}
		}

		// return events
		$ajaxObj->setContent($events);
		$ajaxObj->addContent('length', count($events));
		$ajaxObj->setContentFormat('json');
	}

}

if (defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aware/class.tx_aware_ajax.php'])) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aware/class.tx_aware_ajax.php']);
}

?>