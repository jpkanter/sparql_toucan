<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

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

    // wizards
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
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'sparql_toucan-plugin-sparqlfront',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:sparql_toucan/Resources/Public/Icons/user_plugin_sparqlfront.svg']
			);
		
    }
);
