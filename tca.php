<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$TCA['tx_extrepmgm_extgroup'] = Array (
    'ctrl' => $TCA['tx_extrepmgm_extgroup']['ctrl'],
    'interface' => Array (
        'showRecordFieldList' => 'hidden,title,descr'
    ),
    'feInterface' => $TCA['tx_extrepmgm_extgroup']['feInterface'],
    'columns' => Array (
        'hidden' => Array (        
            'exclude' => 1,    
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
            'config' => Array (
                'type' => 'check',
                'default' => '0'
            )
        ),
        'title' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_extgroup.title',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'max' => '30',    
                'eval' => 'required,trim',
            )
        ),
        'descr' => Array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_extgroup.descr',        
            'config' => Array (
                'type' => 'input',    
                'size' => '48',    
                'max' => '255',
            )
        ),
    ),
    'types' => Array (
        '0' => Array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, descr;;;;3-3-3')
    ),
    'palettes' => Array (
        '1' => Array('showitem' => '')
    )
);


$TCA['tx_extrepmgm_langadmin'] = Array (
    'ctrl' => $TCA['tx_extrepmgm_langadmin']['ctrl'],
    'interface' => Array (
        'showRecordFieldList' => 'title,langkey,auth_translator,sub_translators'
    ),
    'feInterface' => $TCA['tx_extrepmgm_langadmin']['feInterface'],
    'columns' => Array (
        'title' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.title',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'max' => '50',    
                'eval' => 'required,trim',
            )
        ),
        'langkey' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.langkey',        
            'config' => Array (
                'type' => 'input',    
                'size' => '10',    
                'max' => '10',    
                'eval' => 'required,trim,uniqueInPid',
            )
        ),
        'auth_translator' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.auth_translator',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'sub_translators' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.sub_translators',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 10,    
                'minitems' => 0,
                'maxitems' => 100,    
                'MM' => 'tx_extrepmgm_langadmin_sub_translators_mm',
            )
        ),
        'credits' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.credits',
            'config' => Array (
                'type' => 'text',
                'cols' => '48',
                'rows' => '5',
            )
        ),
        'charset' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.charset',        
            'config' => Array (
                'type' => 'input',    
                'size' => '10',    
                'max' => '20',    
                'eval' => 'trim',
            )
        ),
        'sponsor_company' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.sponsor_company',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
            )
        ),
        'sponsor_url' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.sponsor_url',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
            )
        ),
        'ref_lang_keys' => Array (        
            'label' => 'LLL:EXT:extrep_mgm/locallang_db.php:tx_extrepmgm_langadmin.ref_lang_keys',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'trim',
            )
        ),
    ),
    'types' => Array (
        '0' => Array('showitem' => 'title;;;;2-2-2, langkey;;;;3-3-3, auth_translator, sub_translators,credits,charset,sponsor_company,sponsor_url,ref_lang_keys')
    ),
    'palettes' => Array (
        '1' => Array('showitem' => '')
    )
);





$TCA['tx_extrepmgm_team'] = Array (
    'ctrl' => $TCA['tx_extrepmgm_team']['ctrl'],
    'columns' => Array (
        'title' => Array (        
            'label' => 'Title:',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'max' => '50',    
                'eval' => 'required,trim',
            )
        ),
        'purpose' => Array (        
            'label' => 'Purpose:',
            'config' => Array (
                'type' => 'text',
            )
        ),		
        'supervisor' => Array (        
            'label' => 'Supervisor:',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'leader' => Array (        
            'label' => 'Leader:',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),				
        'team' => Array (        
            'label' => 'Team members:',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 10,    
                'minitems' => 0,
                'maxitems' => 100,    
                'MM' => 'tx_extrepmgm_team_members_mm',
            )
        ),
    ),
    'types' => Array (
        '0' => Array('showitem' => 'title, purpose, supervisor, leader, team, notepad')
    ),
);
$TCA['tx_extrepmgm_project'] = Array (
    'ctrl' => $TCA['tx_extrepmgm_project']['ctrl'],
    'columns' => Array (
        'title' => Array (        
            'label' => 'Title:',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'max' => '50',    
                'eval' => 'required,trim',
            )
        ),
        'description' => Array (        
            'label' => 'Description:',
            'config' => Array (
                'type' => 'text',
            )
        ),		
        'status' => Array (        
            'label' => 'Status:',
            'config' => Array (
                'type' => 'text',
            )
        ),	
        'notepad' => Array (        
            'label' => 'Notepad:',
            'config' => Array (
                'type' => 'text',
            )
        ),	        	
        'skills' => Array (        
            'label' => 'Skills/How to proceed:',
            'config' => Array (
                'type' => 'text',
            )
        ),
		'priority' => Array (
			'label' => 'Priority',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					array('',0),
					array('High',5),
					array('Mid',3),
					array('Low',1),
				)
			)
		),
        'team_id' => Array (
            'label' => 'Related to team:',
            'config' => Array (
                'type' => 'select',    
				'items' => Array (
					array('',0),
				),
                'foreign_table' => 'tx_extrepmgm_team',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'leader' => Array (        
            'label' => 'Leader:',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),				
        'team' => Array (        
            'label' => 'Team members:',        
            'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 10,    
                'minitems' => 0,
                'maxitems' => 100,    
                'MM' => 'tx_extrepmgm_project_members_mm',
            )
        ),
    ),
    'types' => Array (
        '0' => Array('showitem' => 'title, description, status, skills,notepad, priority, team_id, leader, team')
    ),
);

?>