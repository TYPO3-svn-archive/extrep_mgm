<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPlugin(Array('LLL:EXT:extrep_mgm/pi1/locallang.php:pi_title', $_EXTKEY.'_pi1'),'list_type');

$tempColumns = Array (
	'tx_extrepmgm_function' => Array (		
		'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function',
		'config' => Array (
			'type' => 'select',
			'items' => array(
				array('LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function.I.0',0),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function.I.1',1),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function.I.2',2),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function.I.3',3),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function.I.4',4),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tt_content.tx_extrepmgm_function.I.5',5),
			)
		)
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='tx_extrepmgm_function;;;;1-1-1';




$tempColumns = Array (
    'tx_extrepmgm_group' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_group',        
        'config' => Array (
            'type' => 'select',    
            'foreign_table' => 'tx_extrepmgm_extgroup',    
            'foreign_table_where' => 'AND tx_extrepmgm_extgroup.pid=###STORAGE_PID### ORDER BY tx_extrepmgm_extgroup.title',    
            'size' => 10,    
            'minitems' => 0,
            'maxitems' => 100,    
            'MM' => 'tx_extrep_keytable_tx_extrepmgm_group_mm',
        )
    ),
    'tx_extrepmgm_appr_flag' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_appr_scope',
        'config' => Array (
            'type' => 'check',
        )
    ),
    'tx_extrepmgm_nodoc_flag' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_nodoc_flag',
        'config' => Array (
            'type' => 'check',
			'items' => array(
				array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_nodoc_flag.I.0',0),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_nodoc_flag.I.1',1),
			)
        )
    ),
    'tx_extrepmgm_flags' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_flags',
        'config' => Array (
            'type' => 'check',
			'items' => array(
				array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_flags.I.0',0),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_flags.I.1',1),
				array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_flags.I.2',2),
			)
        )
    ),
    'tx_extrepmgm_homepage' => Array (
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_homepage',
        'config' => Array (
            'type' => 'text',
            'cols' => 48,
			'rows' => 10
        )
    ),
	'tx_extrepmgm_rev' => Array (
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_keytable.tx_extrepmgm_rev',
        'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'sxw',	
			'max_size' => 1000,	
			'uploadfolder' => 'uploads/tx_extrepmgm',
			'show_thumbs' => 1,	
			'size' => 3,
			'minitems' => 0,
			'maxitems' => 1,		
        )
	),
);
t3lib_div::loadTCA('tx_extrep_keytable');
t3lib_extMgm::addTCAcolumns('tx_extrep_keytable',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_extrep_keytable','tx_extrepmgm_group;;;;1-1-1,tx_extrepmgm_appr_flag,tx_extrepmgm_nodoc_flag,tx_extrepmgm_flags,tx_extrepmgm_homepage,tx_extrepmgm_rev');



$tempColumns = Array (
    'tx_extrepmgm_isreviewer' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_isreviewer',
        'config' => Array (
            'type' => 'check',
        )
    ),
    'tx_extrepmgm_isdocreviewer' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_isdocreviewer',
        'config' => Array (
            'type' => 'check',
        )
    ),
    'tx_extrepmgm_maxbytes' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_maxbytes',
        'config' => Array (
            'type' => 'input',
            'size' => '15',
			'eval' => 'int',
			'checkbox' => 0,
        )
    ),
    'tx_extrepmgm_jobs' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_jobs',
        'config' => Array (
            'type' => 'text',
            'cols' => 48,
			'rows' => 5
        )
    ),
    'tx_extrepmgm_addpoints' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_addpoints',
        'config' => Array (
            'type' => 'input',
            'size' => '15',
			'eval' => 'int',
			'checkbox' => 0,
        )
    ),
	'tx_extrepmgm_images' => Array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_images',		
		'config' => Array (
			'type' => 'group',
			'internal_type' => 'file',
			'allowed' => 'gif,png,jpeg,jpg',	
			'max_size' => 50,	
			'uploadfolder' => 'uploads/tx_extrepmgm',
			'show_thumbs' => 1,	
			'size' => 3,
			'minitems' => 0,
			'maxitems' => 3,
		)
	),
    'tx_extrepmgm_contribute' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_contribute',
        'config' => Array (
            'type' => 'input',
            'size' => '48',
			'max' => 256
        )
    ),
    'tx_extrepmgm_personallife' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_personallife',
        'config' => Array (
            'type' => 'input',
            'size' => '48',
			'max' => 256
        )
    ),
    'tx_extrepmgm_typo3experiences' => Array (        
        'exclude' => 1,
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:fe_users.tx_extrepmgm_typo3experiences',
        'config' => Array (
            'type' => 'input',
            'size' => '48',
			'max' => 256
        )
    ),
	
	
);
t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_extrepmgm_isreviewer;;;;1-1-1, tx_extrepmgm_isdocreviewer, tx_extrepmgm_maxbytes, tx_extrepmgm_jobs,tx_extrepmgm_addpoints,tx_extrepmgm_images,  tx_extrepmgm_contribute,tx_extrepmgm_personallife,tx_extrepmgm_typo3experiences');




$tempColumns = Array (
    'tx_extrepmgm_appr_status' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_status',        
        'config' => Array (
            'type' => 'select',
            'items' => Array (
                Array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_status.I.0', '0'),
                Array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_status.I.1', '5'),
                Array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_status.I.2', '10'),
                Array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_status.I.3', '15'),
                Array('LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_status.I.4', '20'),
            ),
        )
    ),
    'tx_extrepmgm_appr_comment' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_comment',        
        'config' => Array (
            'type' => 'text',
            'cols' => '30',    
            'rows' => '5',
        )
    ),
    'tx_extrepmgm_appr_fe_user' => Array (        
        'exclude' => 1,        
        'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrep_repository.tx_extrepmgm_appr_fe_user',        
        'config' => Array (
            'type' => 'group',    
            'internal_type' => 'db',    
            'allowed' => 'fe_users',    
            'size' => 1,    
            'minitems' => 0,
            'maxitems' => 1,
        )
    ),
);
t3lib_div::loadTCA('tx_extrep_repository');
t3lib_extMgm::addTCAcolumns('tx_extrep_repository',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_extrep_repository','tx_extrepmgm_appr_status;;;;1-1-1, tx_extrepmgm_appr_comment, tx_extrepmgm_appr_fe_user');




$TCA['tx_extrepmgm_extgroup'] = Array (
    'ctrl' => Array (
        'title' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_extgroup',        
        'label' => 'title',    
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY title',    
        'delete' => 'deleted',    
        'enablecolumns' => Array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_extrepmgm_extgroup.gif',
    ),
    'feInterface' => Array (
        'fe_admin_fieldList' => 'hidden, title, descr',
    )
);


$TCA['tx_extrepmgm_langadmin'] = Array (
    'ctrl' => Array (
        'title' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin',        
        'label' => 'title',    
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate',    
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_extrepmgm_langadmin.gif',
    ),
    'feInterface' => Array (
        'fe_admin_fieldList' => 'title, langkey, auth_translator, sub_translators',
    )
);



$TCA['tx_extrepmgm_team'] = Array (
    'ctrl' => Array (
        'title' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_team',        
        'label' => 'title',    
        'tstamp' => 'tstamp',
        'sortby' => 'sorting',    
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
    ),
);
$TCA['tx_extrepmgm_project'] = Array (
    'ctrl' => Array (
        'title' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_project',        
        'label' => 'title',    
        'tstamp' => 'tstamp',
        'default_sortby' => 'ORDER BY title',    
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
    ),
);



if (TYPO3_MODE=='BE')    {
    t3lib_extMgm::addModule('web','txextrepmgmM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}

?>