<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_extrepmgm_pi1 = < plugin.tx_extrepmgm_pi1.CSS_editor
',43);

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_extrepmgm_pi1.php','_pi1','list_type',1);
t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	plugin.tx_extrepmgm_pi1 >
	plugin.tx_extrepmgm_pi1 = CASE
	plugin.tx_extrepmgm_pi1.key.field = tx_extrepmgm_function
	plugin.tx_extrepmgm_pi1.2 = USER
	plugin.tx_extrepmgm_pi1.2 {
		userFunc = tx_extrepmgm_pi1->main2
	}
	plugin.tx_extrepmgm_pi1.4 < plugin.tx_extrepmgm_pi1.2
	plugin.tx_extrepmgm_pi1.default = USER_INT
	plugin.tx_extrepmgm_pi1.default {
		includeLibs = '.$TYPO3_LOADED_EXT[$_EXTKEY]['siteRelPath'].'pi1/class.tx_extrepmgm_pi1.php
		userFunc = tx_extrepmgm_pi1->main
	}
');

t3lib_extMgm::addUserTSConfig('
    options.saveDocNew.tx_extrepmgm_extgroup=1
    options.saveDocNew.tx_extrepmgm_langadmin=1
    options.saveDocNew.tx_extrepmgm_team=1
    options.saveDocNew.tx_extrepmgm_project=1
');

?>