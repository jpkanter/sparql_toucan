<?php
defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Ubl.SparqlToucan',
    'Sparqlfront',
    [
        'Front' => 'display'
    ],
    // non-cacheable actions
    [

    ]
);


// wizards - the thing thats add it in the "add content element" menu
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
    wizards.newContentElement.wizardItems.plugins {
        elements {
            sparqlfront {
                iconIdentifier = sparql_toucan-plugin-sparqlfront
                title = LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparql_toucan_sparqlfront.name
                description = LLL:EXT:sparql_toucan/Resources/Private/Language/locallang_db.xlf:tx_sparql_toucan_sparqlfront.description
                tt_content_defValues {
                    CType = list
                    list_type = sparqltoucan_sparqlfront
                }
            }
        }
        show = *
    }
}'
);

if (version_compare(TYPO3_branch, '7.0', '>')) {
    if (TYPO3_MODE === 'BE') {
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'sparql_toucan-plugin-sparqlfront',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:sparql_toucan/Resources/Public/Icons/user_plugin_sparqlfront.svg']);
    }
}

//Page module hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']
['sparqltoucan_sparqlfront'] = \Ubl\SparqlToucan\Hooks\PageLayoutViewDrawItemHook::class;
//future versions : $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] = t3lib_extMgm::extPath($_EXTKEY).'classes/class.page_layoutView.php:tx_gallery_tt_content_drawItem';