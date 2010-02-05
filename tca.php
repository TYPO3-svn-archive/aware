<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_aware_channels'] = array (
	'ctrl' => $TCA['tx_aware_channels']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,lifetime'
	),
	'feInterface' => $TCA['tx_aware_channels']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:aware/locallang_db.xml:tx_aware_channels.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'lifetime' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:aware/locallang_db.xml:tx_aware_channels.lifetime',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'int',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, lifetime')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_aware_events'] = array (
	'ctrl' => $TCA['tx_aware_events']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,channel,information'
	),
	'feInterface' => $TCA['tx_aware_events']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'channel' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:aware/locallang_db.xml:tx_aware_events.channel',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_aware_channels',	
				'foreign_table_where' => 'ORDER BY tx_aware_channels.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_aware_channels',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			)
		),
		'information' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:aware/locallang_db.xml:tx_aware_events.information',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, channel, information')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);

?>