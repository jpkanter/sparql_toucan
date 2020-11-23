<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,cached_value,subject,predicate,mode,auto_update,source_id',
        'iconfile' => 'EXT:sparql_toucan/Resources/Public/Icons/tx_sparqltoucan_domain_model_datapoint.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, cached_value, subject, predicate, mode, auto_update, source_id',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, cached_value, subject, predicate, mode, auto_update, source_id, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_sparqltoucan_domain_model_datapoint',
                'foreign_table_where' => 'AND tx_sparqltoucan_domain_model_datapoint.pid=###CURRENT_PID### AND tx_sparqltoucan_domain_model_datapoint.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],

        'name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.name',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'eval' => 'trim'
            ],
        ],
        'cached_value' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.cached_value',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'subject' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'predicate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.predicate',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'mode' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.mode',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'auto_update' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.auto_update',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
                'default' => 0,
            ]
            
        ],
        'source_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_datapoint.source_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_sparqltoucan_domain_model_source',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    
    ],
];
