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
    protected $collectionRepository = null;

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
            //$collection = $this->collectionRepository->findByUid($flexformData['settings']['choosenCollection']);
            $bla = [$this->collectionRepository];

            //$headerContent = '<strong>Sparql Toucan - ' . htmlspecialchars($collection->getName()) . '</strong><br />';

            // Festlegen der Template-Datei
            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $fluidTemplate */
            $fluidTemplateFilePath = GeneralUtility::getFileAbsFileName('EXT:' . $extKey . '/Resources/Private/Templates/SparqlFrontPreview.html');
            $fluidTemplate = GeneralUtility::makeInstance(StandaloneView::class);
            $fluidTemplate->setTemplatePathAndFilename($fluidTemplateFilePath);
            //$fluidTmpl->assign('flex', $flexform);
            $fluidTemplate->assign('debug', $bla);

            // Rendern
            $itemContent = $parentObject->linkEditContent($fluidTemplate->render(), $row);
        }
    }
}