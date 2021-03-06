<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_labelcache',
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
        'searchFields' => 'content, subject, language, status, source_id',
        'iconfile' => 'EXT:sparql_toucan/Resources/Public/Icons/tx_sparqltoucan_domain_model_labelcache.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, content, subject, language, status, source_id',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, content, subject, language, status, source_id, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_sparqltoucan_domain_model_labelcache',
                'foreign_table_where' => 'AND tx_sparqltoucan_domain_model_labelcache.pid=###CURRENT_PID### AND tx_sparqltoucan_domain_model_labelcache.sys_language_uid IN (-1,0)',
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
        'crdata' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'content' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_labelcache.content',
            'config' => [
                'type' => 'input',
                'size' => 254,
                'eval' => 'trim'
            ],
        ],
        'subject' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_labelcache.subject',
            'config' => [
                'type' => 'input',
                'size' => 254,
                'eval' => 'trim'
            ],
        ],
        'language' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_labelcache.language',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'trim'
            ],
        ],
        'status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_labelcache.status',
            'config' => [
                'type' => 'input',
                'size' => 6,
                'eval' => 'trim'
            ],
        ],
        'source_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparqltoucan_domain_model_labelcache.source_id',
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
