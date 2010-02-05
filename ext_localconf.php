<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

/** Initialize vars from extension conf */
$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:
$initVars = array('auto_new','auto_update','edit_lifetime','enable_backend');
foreach($initVars as $var) {
  $TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY][$var] = $_EXTCONF[$var] ? trim($_EXTCONF[$var]) : "";
}

t3lib_div::requireOnce(t3lib_extMgm::extPath('aware').'class.tx_aware.php');

// Registers hooks

$TYPO3_CONF_VARS['BE']['AJAX']['tx_aware_ajax::clientRequest'] = 'EXT:aware/class.tx_aware_ajax.php:tx_aware_ajax->clientRequest';

$TYPO3_CONF_VARS['BE']['AJAX']['tx_aware_module1_ajax::getEvents'] = 'EXT:aware/mod1/ajax.php:tx_aware_module1_ajax->getEvents';

$TYPO3_CONF_VARS['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('aware').'register_js_client.php';

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:aware/class.tx_aware_auto.php:tx_aware_auto';

?>