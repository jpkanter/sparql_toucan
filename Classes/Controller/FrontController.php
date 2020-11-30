<?php
namespace Ubl\SparqlToucan\Controller;

use Ubl\SparqlToucan\Domain\Repository\CollectionRepository;
use Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository;
use Ubl\SparqlToucan\Domain\Repository\SourceRepository;
use Ubl\SparqlToucan\Domain\Repository\DatapointRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
        $collectionId= $this->settings['choosenCollection'];
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
        $testint = 0;
        foreach( $entries as &$entry) {
            $value = $this->languagepointRepository->fetchSpecificLanguage($entry->getDatapointId(), $sys_language_name);
            $entry->SetTempValue($value);
            $entry->getDatapointId()->setCachedValue($value);
        }
        $this->view->assign("test", $testint);
        $this->view->assign("collection", $collection);
        $this->view->assign("entries", $entries);
    }

    public function PluginList( array &$config) {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class) ->getConnectionForTable('tx_sparqltoucan_domain_model_collection');
        $queryBuilder = $connection->createQueryBuilder();
        $query = $queryBuilder
            ->select('*')
            ->from('tx_sparqltoucan_domain_model_collection');
        $collection = $query->execute()->fetchAll();
        $config['items'] = array();
        foreach( $collection as $value ) {
            array_push($config['items'],[$value['name'], $value['uid']]);
        }
    }
}