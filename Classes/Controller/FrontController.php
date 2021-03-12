<?php
namespace Ubl\SparqlToucan\Controller;

use Ubl\SparqlToucan\Domain\Model\CollectionEntry;
use Ubl\SparqlToucan\Domain\Model\Datapoint;
use Ubl\SparqlToucan\Domain\Repository\CollectionRepository;
use Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository;
use Ubl\SparqlToucan\Domain\Repository\SourceRepository;
use Ubl\SparqlToucan\Domain\Repository\DatapointRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\RequestFactoryInterface;
/***
 *
 * This file is part of the "Sparkle Toucan" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 JP Kanter <kanter@ub.uni-leipzig.de>, Universität Leipzig
 *
 ***/

/**
 * OverviewController
 */
class FrontController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * collectionRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\CollectionRepository
     * @inject
     */
    protected $collectionRepository = null;

    /**
     * collectionEntryRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository
     * @inject
     */
    protected $collectionEntryRepository = null;
    /**
     * datapointRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\DatapointRepository
     * @inject
     */
    protected $datapointRepository = null;
    /**
     * sourceRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\SourceRepository
     * @inject
     */
    protected $sourceRepository = null;
    /**
     * labelcacheRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\LabelcacheRepository
     * @inject
     */
    protected $labelcacheRepository = null;
    /**
     * languagepointRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\LanguagepointRepository
     * @inject
     */
    protected $languagepointRepository = null;

    protected function getLanguage() {
        //this abstracts the language name for the moment but is no permanent solution
        /*Typo V9*/
        /*
                $languageAspect = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
                $sys_language_uid = $languageAspect->getId();
                //or
                $context = GeneralUtility::makeInstance(Context::class);
                // The requested language of the current page as integer (uid)
                $currentLanguageUid = $context->getPropertyFromAspect('language', 'id');
        */
        // $GLOBALS['TSFE']->sys_language_uid
        if (TYPO3_MODE === 'FE') {
            if (isset($GLOBALS['TSFE']->config['config']['language'])) {
                return $GLOBALS['TSFE']->config['config']['language'];
            }
        } elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
            return $GLOBALS['BE_USER']->uc['lang'];
        }
        return 'en'; //default
    }

    public function DisplayAction() {
        $collectionId = $this->settings['choosenCollection'];
        if($collectionId == null) {$collectionId = 2;}
        $collection = $this->collectionRepository->findByIdentifier($collectionId);
        $entries = $this->collectionEntryRepository->fetchCorresponding($collection)->toArray();
        /*
        $points = array();
        foreach( $entries->toArray() as $entry) {
            $temp = $this->datapointRepository->findByIdentifier($entry->getDatapointId());
            if( $temp != Null ) {
                array_push($points, $temp);
            }
        }*/

        $sys_language_name = $this->getLanguage();

        /****DEBUG****/
        $exceptionStack = array();

        $blankEntries = [];
        $cycleCounter = 1;
        $blankLines = 0;

        foreach ($entries as $myKey => $entry) {
            //adding empty elements
            //Style stuff, needed cause Typo V7 Fluid doesnt work properly in the frontend
            switch( $entry->getStyle() ) {
                case 1:
                    $newStyle = "tx_sparqltoucan_ce_bold"; break;
                case 2:
                    $newStyle = "tx_sparqltoucan_ce_italic"; break;
                case 3:
                    $newStyle = "tx_sparqltoucan_ce_bold-italic"; break;
                case 4:
                    $newStyle = "tx_sparqltoucan_ce_thin"; break;
                case 5:
                    $newStyle = "tx_sparqltoucan_ce_thin-italic"; break;
                default:
                    $newStyle = "tx_sparqltoucan_ce_none"; break;
            }
            $entries[$myKey]->setStyle($newStyle);
            //end of ugly style hack
            try {
                if( $entry->getDatapointId() != 0 ) {
                    $value = $this->languagepointRepository->fetchSpecificLanguage($entry->getDatapointId(), $sys_language_name);
                }
                elseif( $entry->getTextpoint() != 0 ) {
                    $value = $this->languagepointRepository->fetchSpecificLanguage($entry->getTextpoint(), $sys_language_name);
                }
                else {
                    $value = ""; //if neither Textpoint nor Datapoint exists its an empty placeholder entry
                }
                $entries[$myKey]->SetTempValue($value);
            } catch (\Exception $e) {
                $error['message'] = $e->getMessage();
                $error['code'] = $e->getCode();
                $exceptionStack[] = $error;
            }
            $cycleCounter++;
        }
        array_push($entries, $blankEntries);

        $this->view->assign("collection", $collection);
        $this->view->assign("exceptions", $exceptionStack);
        $this->view->assign("entries", $entries);
    }

    public function PluginList( array &$config) {
        /* Typo3 V8+ Variant, ConnectionPool Calls dont exist in V7 and below, we have to use Globals DB instead
        $connection = GeneralUtility::makeInstance(ConnectionPool::class) ->getConnectionForTable('tx_sparqltoucan_domain_model_collection');
        $queryBuilder = $connection->createQueryBuilder();
        $query = $queryBuilder
            ->select('*')
            ->from('tx_sparqltoucan_domain_model_collection');
        $collection = $query->execute()->fetchAll();
         */
        $collection = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_sparqltoucan_domain_model_collection', 'deleted = 0');
        $config['items'] = array();
        foreach( $collection as $value ) {
            array_push($config['items'],[$value['name'], $value['uid']]);
        }
    }
}