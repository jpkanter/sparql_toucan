<?php
defined('TYPO3_MODE') or die();

/**
 * Registers a Plugin to be listed in the Backend.
 */
$plugSig = "sparqltoucan_sparqlfront"; //plugin Signature = plugin key all lowercase, no underscore , then underscore, name of the registered thingy in ext_localconf.php
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$plugSig ] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$plugSig ] = 'pi_flexform,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($plugSig, 'FILE:EXT:' . 'sparql_toucan' . '/Configuration/FlexForms/CollectionConfig.xml');

