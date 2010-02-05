<?php

if(TYPO3_MODE == 'BE') {

	// add client script to typo3 backend 
	$TYPO3backend->addJavascriptFile(t3lib_extMgm::extRelPath('aware').'tx_aware_client.js');

}

?>