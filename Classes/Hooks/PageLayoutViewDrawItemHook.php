<?php

namespace Ubl\SparqlToucan\Hooks;

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Ubl\SparqlToucan\Domain\Model\Collection;
use Ubl\SparqlToucan\Domain\Repository\CollectionRepository;

/**
 * Hook to render preview widget of custom content elements in page module
 * @see \TYPO3\CMS\Backend\View\PageLayoutView::tt_content_drawItem()
 */
class PageLayoutViewDrawItemHook implements PageLayoutViewDrawItemHookInterface {

    /**
     * collectionRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\CollectionRepository $collectionRepository
     * @inject
     */
    //protected $collectionRepository = null;

    function __construct() {
        /** @var $extbaseObjectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
        $extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->collectionRepository = $extbaseObjectManager->get('Ubl\SparqlToucan\Domain\Repository\CollectionRepository');
        $this->collectionEntryRepository = $extbaseObjectManager->get('Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository');
        $this->languagepointRepository = $extbaseObjectManager->get('Ubl\SparqlToucan\Domain\Repository\LanguagepointRepository');
    }

    /**
     * Rendering for custom content elements
     *
     * @param PageLayoutView $parentObject
     * @param bool $drawItem
     * @param string $headerContent
     * @param string $itemContent
     * @param array $row
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
        $extKey = "sparql_toucan";
        if($row['CType'] == 'list' && $row['list_type'] == 'sparqltoucan_sparqlfront' ) {

            $drawItem = false;

            if (!empty($row['pi_flexform'])) {
                /** @var FlexFormService $flexFormService */
                $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
            }
            $flexformData = $flexFormService->convertFlexFormContentToArray($row['pi_flexform']);
            $collection = $this->collectionRepository->findByUid($flexformData['settings']['choosenCollection']);
            //$bla = [$this->collectionRepository];

            $headerContent = false;//'<strong>Sparql Toucan - ' . htmlspecialchars($collection->getName()) . '</strong><br />';
            $lines = [
                "name" => $collection->getName(),
                "layout" => $collection->getLayout(),
                "entries" => count($this->collectionEntryRepository->fetchCorresponding($collection)),
                "preview" => ""
            ];

            $entries = $this->collectionEntryRepository->fetchCorresponding($collection);
            $entryArray = [];
            $twigs = [];
            foreach($entries as $entry ) {
                if( !$entry->getIsBranch() ) {
                    if ($entry->getTextpoint() != null && $entry->getDatapointId() != null) {
                        $entry->setTempValue(
                            $this->languagepointRepository->fetchSpecificLanguage($entry->getDatapointId(), "en")
                            .
                            $this->languagepointRepository->fetchSpecificLanguage($entry->getTextpoint(), "en")
                        );
                    } elseif ($entry->getDatapointId() != null && $entry->getTextpoint() == null) {
                        $entry->setTempValue($this->languagepointRepository->fetchSpecificLanguage($entry->getDatapointId(), "en"));
                    } elseif ($entry->getTextpoint() != null && $entry->getDatapointId() == null) {
                        $entry->setTempValue($this->languagepointRepository->fetchSpecificLanguage($entry->getTextpoint(), "en"));
                    } else {
                        $entry->setTempValue(""); //this should definitely not happen
                    }
                    if( $entry->getParentEntry() == 0 ) {
                        $entryArray[] = $entry->convertToArray();
                    }
                    else {
                        if( !isset($twigs[$entry->getParentEntry()->getUid()]) ) { $twigs[$entry->getParentEntry()->getUid()] = [];}
                        $twigs[$entry->getParentEntry()->getUid()][] = $entry->getTempValue();
                    }

                }
            }

            foreach( $twigs as $key => $entry) {
                usort($twigs[$key], function($a, $b) {
                   return $a['position'] <=> $b['position'];
                });
            }

            foreach($entries as $entry ) {
                if( $entry->getIsBranch() ) {
                    $compound = "";
                    foreach ($twigs[$entry->getUid()] as $subentry) {
                        $compound.= $subentry . " ";
                    }
                    $entry->setTempValue(trim($compound));
                    $entryArray[] = $entry->convertToArray();
                }
            }

            usort($entryArray, function($a, $b) {
                return $a['gridColumn'] <=> $b['gridColumn'];
            });

            usort($entryArray, function($a, $b) {
                return $a['gridRow'] <=> $b['gridRow'];
            });

            foreach( $entryArray as $entry ) {
                $lines['preview'].= $entry['tempValue'] . "\n";
            }

            // Festlegen der Template-Datei
            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $fluidTemplate */
            $fluidTemplateFilePath = GeneralUtility::getFileAbsFileName('EXT:' . $extKey . '/Resources/Private/Templates/SparqlFrontPreview.html');
            $fluidTemplate = GeneralUtility::makeInstance(StandaloneView::class);
            $fluidTemplate->setTemplatePathAndFilename($fluidTemplateFilePath);
            //$fluidTmpl->assign('flex', $flexform);
            $fluidTemplate->assign('stats', $lines);

            // Rendern
            $itemContent = $parentObject->linkEditContent($fluidTemplate->render(), $row);
        }
    }
}