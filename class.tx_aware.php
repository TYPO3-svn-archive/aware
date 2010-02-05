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
 * This is the main backend component for aware.  It provides basic
 * functions for creating and accessing events, that can be used by any
 * backend or frontend extension.
 *
 * @author Morten Tranberg Hansen <mth at cs dot au dot dk>
 * @date   November 10 2009
 */

class tx_aware implements t3lib_Singleton {

	/*
	 * Set the lifetime of events that belong to a channel.
	 * 
	 * @param	string the channel
	 * @param	integer the lifetime
	 */
	public static function setChannelLifetime($channel, $lifetime) {

		$c = tx_aware::getChannel($channel);
		if (!empty($c)) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_aware_channels', 'uid='.$c['uid'], array('lifetime'=>intval($lifetime)));
		}

	}

	/*
	 * Created a channel.
	 * 
	 * @param	string the channel to create
	 */
	public static function createChannel($channel) {
		$result = tx_aware::getChannel($channel);
		if (empty($result)) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_aware_channels', array('tstamp'=>$GLOBALS['EXEC_TIME'], 'crdate'=>$GLOBALS['EXEC_TIME'], 'cruser_id'=>$GLOBALS['BE_USER']->user['uid'], 'name'=>$channel));

			//set the default edit channel lifetime
			/*if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['edit_lifetime']) && strtok($channel,'#')=='edit') {
				tx_aware::setChannelLifetime($channel, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['edit_lifetime']);
				}*/

			if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['newchannel']))	{
				$hooks = &$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['newchannel'];
				if (is_array($hooks)) {
					foreach ($hooks as $hook)	{
						// TODO: very ugly to pass unusable instance of tx_aware class (all methods are now static)
						t3lib_div::callUserFunction($hook, $channel, t3lib_div::makeInstance('tx_aware'), false, true);
					}
				}
			}

			$result = tx_aware::getChannel($channel);
		}

		return $result;
	}


	/*
	 * Add an event to the event database.
	 * 
	 * @param	string the channel to add an event to
	 * @param	array the event data
	 * @param boolean set whether or not non-existing channels should be automatically created.
	 * 
	 */
	public static function addEvent($channel, array $data=array(), $createChannel=false) {
		$time = $GLOBALS['EXEC_TIME'];//time();

		if ($channel=='') {
			debug('Empty channel!', 'addEvent');
			return;
		}

		if ($createChannel) {
			$c = tx_aware::createChannel($channel);
		} else {
			$c = tx_aware::getChannel($channel);
		}
		
		if (empty($c)) {
			debug('Channel \''.$channel.'\'does not exists!', 'addEvent');
			return;
		}

		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_aware_events', array('tstamp'=>$time, 'crdate'=>$time, 'cruser_id'=>$GLOBALS['BE_USER']->user['uid'], 'channel'=>$c['uid'], 'information'=>serialize($data)));
		
		$event = array('channel'=>$channel, 'data'=>$data, 'timestamp'=>$time);

		if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['listeners']) && isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['listeners'][$channel]))	{
			$listeners = &$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['aware']['listeners'][$channel];
			if (is_array($listeners)) {
				foreach ($listeners as $listener)	{
					// TODO: very ugly to pass unusable instance of tx_aware class (all methods are now static)
					t3lib_div::callUserFunction($listener, $event, t3lib_div::makeInstance('tx_aware'), false, true);
				}
			}
		}

	}

	/*
	 * Get aware events related to a specific channel.  By default only non-seen events are
	 * returned. This can be changed through the $showSeenEvents and $explicitLastEvent
	 * arguments.  Seen events are configured per user.
	 * 
	 * @param	string the channel to get events from
	 * @param	boolean set of already seen events should be shown
	 * @param integer set to explicit last seen event ID instead of the one stored in be_user record
	 * 
	 * @return array Each entry in the array is an event (which itself is an array).
	 */
	public static function getEvents($channel, $showSeenEvents=false, $explicitLastEvent=0) {

		$c = tx_aware::getChannel($channel);
		if (empty($c)) {
			return array();
		}

		$lifetime_clause = '';
		if (intval($c['lifetime'])>0) {
			$lifetime_clause = ' AND crdate>'.(time()-intval($c['lifetime']));
		}

		$user_clause = '';		
		if (!$showSeenEvents) {

			if ($explicitLastEvent) {
				$lastEvent = $explicitLastEvent;
			} else {
				$lastEvent = $GLOBALS['BE_USER']->getModuleData('tx_aware:lastEvent('.$channel.')');
			}

			if (!empty($lastEvent)) {
				$user_clause = ' AND uid>'.$lastEvent;
			}

		}

		$rows =  $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_aware_events', 'channel='.$c['uid'].$lifetime_clause.$user_clause, '', 'uid');

		$result = array();
		foreach($rows as $row) {
			$result[] = tx_aware::dbToEvent($row, $channel);
		}

		if (!empty($result)) {
			$GLOBALS['BE_USER']->pushModuleData('tx_aware:lastEvent('.$channel.')', $rows[count($rows)-1]['uid']);
		}

		return $result;
	}


	private static function getChannel($channel) {

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_aware_channels', 'name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($channel, 'tx_aware_channels'));
		return $rows[0];

	}


	private static function dbToEvent(array $row, $channel) {
		return array('channel'=>$channel, 'data'=>unserialize($row['information']), 'timestamp'=>$row['crdate']);
	}


}

if (defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aware/class.tx_aware.php'])) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aware/class.tx_aware.php']);
}

?>