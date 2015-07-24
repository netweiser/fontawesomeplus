<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE === 'BE') {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_fontawesomeplus_evalclass'] = 'EXT:fontawesomeplus/Classes/Eval/class.tx_fontawesomeplus_evalclass.php';
}