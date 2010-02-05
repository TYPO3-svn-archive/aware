<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Morten Tranberg Hansen <mth@cs.au.dk>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * AJAX functions for 'aware' module1.
 *
 * @author	Morten Tranberg Hansen <mth@cs.au.dk>
 * @package	TYPO3
 * @subpackage	tx_aware
 */
class  tx_aware_module1_ajax {

	
	public function getEvents(array $params = array(), TYPO3AJAX &$ajaxObj) {
		global $BE_USER, $TYPO3_DB;
		
		if ($BE_USER->user['admin'])	{
			$start = intval(t3lib_div::_GP('start'));
			$limit = intval(t3lib_div::_GP('limit'));
			$sort = t3lib_div::_GP('sort');
			$dir = t3lib_div::_GP('dir');

			if (!empty($sort) && !empty($dir)) {
				$sort = $sort.' '.$dir;
			} else {
				$sort = 'uid DESC';
			}

			$channel_rows =  $TYPO3_DB->exec_SELECTgetRows('*', 'tx_aware_channels');
			$channels = array();
			foreach($channel_rows as $row) {
				$channels[$row['uid']] = $row['name'] . ' ('.$row['lifetime'].')';
			}

			$num_rows = $TYPO3_DB->exec_SELECTcountRows('*', 'tx_aware_events');

			$rows =  $TYPO3_DB->exec_SELECTgetRows('*', 'tx_aware_events', '', '', $sort, $limit.' OFFSET '.$start);

			$data = array();
			foreach($rows as $row) {
				$data[] = array('uid'=>$row['uid'], 'channel'=>$channels[$row['channel']], 'data'=>unserialize($row['information']), 'timestamp'=>$row['crdate']);
			}

			$ajaxObj->addContent('total', $num_rows);
			$ajaxObj->addContent('data', $data);
		} else {
			$ajaxObj->addContent('success', false);
		}

		$ajaxObj->setContentFormat('json');

	}
	
}



if (defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aware/mod1/ajax.php']))	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aware/mod1/ajax.php']);
}

?>