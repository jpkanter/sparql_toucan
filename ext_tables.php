<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Ubl.SparqlToucan',
            'Sparqlfront',
            'Sparql Toucan Addin'
        );

        if (TYPO3_MODE === 'BE') {

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Ubl.SparqlToucan',
                'web', // Make module a submodule of 'web'
                'sparqlbackend', // Submodule key
                '', // Position
                [
                    'Backend' => 'overview, 
                                    addCollection, 
                                    newCollection,
                                    editCollection, 
                                    createCollection, 
                                    updateCollection, 
                                    
                                    createCollectionEntry, 
                                    deleteCollectionEntry, 
                                    
                                    newDatapoint,
                                    editDatapoint,
                                    updateDatapoint,
                                    showDatapoint, 
                                    createDatapoint,
                                    
                                    addSource,
                                    newSource,
                                    editSource,
                                    createSource,
                                    updateSource,
                                    showSource,
                                    deleteSource,
                                    
                                    labelcacheOverview,
                                    languagepointOverview,
                                    
                                    explore,
                                    testSomething,
                                    remoteUpdateDatapoints',
                    'Collection' => 'list, show, new, create, edit, update, delete','CollectionEntry.css' => 'list, show, new, create, edit, update, delete',
                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:sparql_toucan/Resources/Public/Icons/user_mod_sparqlbackend.svg',
                    'labels' => 'LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_sparqlbackend.xlf',
                ]
            );

        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('sparql_toucan', 'Configuration/TypoScript', 'Sparql Toucan');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sparqltoucan_domain_model_source', 'EXT:sparql_toucan/Resources/Private/Language/locallang_csh_tx_sparqltoucan_domain_model_source.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sparqltoucan_domain_model_source');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sparqltoucan_domain_model_datapoint', 'EXT:sparql_toucan/Resources/Private/Language/locallang_csh_tx_sparqltoucan_domain_model_datapoint.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sparqltoucan_domain_model_datapoint');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sparqltoucan_domain_model_collection', 'EXT:sparql_toucan/Resources/Private/Language/locallang_csh_tx_sparqltoucan_domain_model_collection.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sparqltoucan_domain_model_collection');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sparqltoucan_domain_model_collectionentry', 'EXT:sparql_toucan/Resources/Private/Language/locallang_csh_tx_sparqltoucan_domain_model_collectionentry.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sparqltoucan_domain_model_collectionentry');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sparqltoucan_domain_model_labelcache');

    }
);
