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
 * This file is part of the "Sparql Toucan" Extension for TYPO3 CMS.
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
class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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

    pUblic function OverviewAction() {
        $collections = $this->collectionRepository->findAll();
        $this->view->assign("collections", $collections);
        $sources = $this->sourceRepository->findAll();
        $this->view->assign("sources", $sources);
        $datapoints = $this->datapointRepository->findAll();
        $this->view->assign("datapoints", $datapoints);
    }

    pUblic function showCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->view->assign('collection', $collection);
    }

    /**
     * action updateCollection
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    pUblic function updateCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->collectionRepository->update($collection);
        $this->redirect('overview');
    }
    /**
     * action editCollection
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @ignorevalidation $collection
     * @return void
     */
    pUblic function editCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->view->assign('collection', $collection);
        $entries = $this->collectionEntryRepository->fetchCorresponding($collection);
        $this->view->assign('collectionEntry', $entries);
        $datapoints = $this->datapointRepository->findAll();
        $this->view->assign('datapoints', $datapoints);
    }
    /**
     * action newCollection
     *
     * @return void
     */
    public function newCollectionAction()
    {

    }
    /**
     * action newDatapointAction
     *
     * @return void
     */
    public function newDatapointAction()
    {
        $sources= $this->sourceRepository->findAll();
        $this->view->assign("sources", $sources);
    }
    /**
     * action createDatapoint
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $newCollection
     * @return void
     */
    public function createDatapointAction(\Ubl\SparqlToucan\Domain\Model\Datapoint $newDatapoint)
    {
        // update content
        $value = $this->recursiveSparqlQuery(
            $newDatapoint->getSourceId()->getUrl(),
            "<".$newDatapoint->getSubject().">",
            "<".$newDatapoint->getPredicate().">");
        $newDatapoint->setCachedValue($value);
        $this->datapointRepository->add($newDatapoint);
        $this->redirect('overview');
    }
    /**
     * action createCollection
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $newCollection
     * @return void
     */
    public function createCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $newCollection)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is pUblicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionRepository->add($newCollection);
        $this->redirect('Overview');
    }
    /**
     * action createCollectionEntry
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $newCollectionEntry
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    public function createCollectionEntryAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $newCollectionEntry)
    {
        $collection = $this->collectionRepository->findByUid($newCollectionEntry->getCollectionID());
        $this->collectionEntryRepository->fetchCorresponding($collection);
        $this->collectionEntryRepository->add($newCollectionEntry);

        $this->redirect('editCollection', Null, Null, array('collection' => $collection));
    }
    /**
     * action deleteCollectionEntry
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     * @return void
     */
    public function deleteCollectionEntryAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $collection = $collectionEntry->getCollectionID();
        $this->collectionEntryRepository->remove($collectionEntry);
        $this->redirect('editCollection', Null, Null, array('collection'=>$collection));
    }

    /**
     * action deleteSource
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return void
     */
    public function deleteSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $datapoints = $this->datapointRepository->findSourcesUsage($source);
        $this->view->assign("datapoints", $datapoints);
        // $this->addFlashMessage('The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        //check for corresponding entries
        #$this->SourceRepository->remove($Source);
        $this->redirect('overview');
    }

    public function remoteUpdateDatapointsAction() {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        //request test
        $debug = array();
        $datapoints = $this->datapointRepository->findAll()->toArray();
        foreach( $datapoints as $dataentry ) {
            $url = $dataentry->getSourceId()->getUrl();
            $sub = '<'.$dataentry->getSubject().'>';
            $pred = '<'.$dataentry->getPredicate().'>';
            $value = $this->recursiveSparqlQuery($url, $sub, $pred);
            array_push($debug, $this->simpleQuery($url, $sub, $pred));
            $content = "";
            foreach( $value as $line) {
                if( strlen($content) > 0) { $content.= "<br>";}
                $content.= $line;
            }
            $dataentry->setCachedValue($content);
            echo( $content);
            $this->datapointRepository->update($dataentry);
            $persistenceManager->persistAll();
        }
        $this->view->assign("debug", $debug);
        $this->view->assign("datapoints", $this->datapointRepository->findAll());

    }

    private function recursiveSparqlQuery($url, $subject, $predicate) {
        $languageFilter = "de"; # TODO
        $requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'form_params' => [
                'query' => 'SELECT * WHERE {'.$subject.' '.$predicate.' ?obj}',
                'format' => 'json'
            ]
        ];
        $response = $requestFactory->request($url, 'POST', $additionalOptions);

        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
            $jsoned = json_decode($content, True);
            $answer = $jsoned['results']['bindings'];
            $result = array();
            foreach( $answer as $entry) {
                if( $entry['obj']['type'] == 'literal') {
                    if( $entry['obj']['xml:lang'] == $languageFilter) {
                        array_push($result, $entry['obj']['value']);
                    }
                }
                elseif( $entry['obj']['type'] == 'typed-literal') {
                    array_push($result, $entry['obj']['value']);
                }
                elseif( $entry['obj']['type'] == 'uri') {
                    array_push($result, $entry['obj']['value']);
                    # array_push($result, $this->recursiveSparqlQuery($url, '<'.$entry['obj']['value'].'>', "?sub"));
                }
            }
            return $result;
        }
        else {
            return Null;
        }
    }

    private function simpleQuery($url, $subject, $predicate) {
        $requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'form_params' => [
                'query' => 'SELECT * WHERE {'.$subject.' '.$predicate.' ?obj}',
                'format' => 'json'
            ]
        ];
        $response = $requestFactory->request($url, 'POST', $additionalOptions);
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
            $jsoned = json_decode($content, True);
            return $jsoned['results']['bindings'];
        }
    }
}