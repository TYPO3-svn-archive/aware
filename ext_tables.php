<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

	if($TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['enable_backend']) {
		t3lib_extMgm::addModulePath('tools_txawareM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		t3lib_extMgm::addModule('tools', 'txawareM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}

}

/*
$TCA['tx_aware_channels'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:aware/locallang_db.xml:tx_aware_channels',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_aware_channels.gif',
	),
);

$TCA['tx_aware_events'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:aware/locallang_db.xml:tx_aware_events',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_aware_events.gif',
	),
);
*/
?>