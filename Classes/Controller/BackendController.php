<?php
namespace Ubl\SparqlToucan\Controller;

use Ubl\SparqlToucan\Domain\Model\Datapoint;
use Ubl\SparqlToucan\Domain\Model\Languagepoint;
use Ubl\SparqlToucan\Domain\Repository\CollectionRepository;
use Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository;
use Ubl\SparqlToucan\Domain\Repository\SourceRepository;
use Ubl\SparqlToucan\Domain\Repository\DatapointRepository;
use Ubl\SparqlToucan\Domain\Repository\LanguagepointRepository;
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

    public function OverviewAction() {
        $collections = $this->collectionRepository->findAll();
        $this->view->assign("collections", $collections);
        $sources = $this->sourceRepository->findAll();
        $this->view->assign("sources", $sources);
    }

    public function showCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->view->assign('collection', $collection);
    }

    /**
     * action updateCollection
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    public function updateCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
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
    public function editCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
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
     * @param Datapoint|null $newDatapoint
     * @return void
     */
    public function newDatapointAction(Datapoint $newDatapoint = Null)
    {
        $sources= $this->sourceRepository->findAll();
        $this->view->assign("sources", $sources);
        $this->view->assign("newDatapoint", $newDatapoint);
    }
    /**
     * action createDatapoint
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $newDatapoint
     * @return void
     */
    public function createDatapointAction(Datapoint $newDatapoint)
    {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        // update content
        $this->datapointRepository->add($newDatapoint);
        $persistenceManager->persistAll();
        $this->updateDatapointLanguagepoints($newDatapoint);
        $this->redirect('overview');
    }

    public function updateDatapointLanguagepoints(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint)
    {
        $datapoint->getUID();
        try {
            $subject = $datapoint->getSubject();
            if( preg_match("^<.*>$^", $subject) == 0) {
                $subject = "<" . $subject . ">";
            }
            $predicate = $datapoint->getPredicate();
            if( preg_match("^<.*>$^", $predicate) == 0) {
                $predicate = "<" . $predicate . ">";
            }
            $jsoned = $this->simpleQuery($datapoint->getSourceId()->getUrl(), $subject, $predicate);
            /* This might get really interesting if we assume that people modeled their linked data incorrectly. If every
            thing is in order, there should be either a language identifier or some other thing that tells us informations
            but its entirely possible that at least the following things happen:
            - there is a language part AND literal URLS
            - there is more than one literal string
             * */
            $languagePoints = array();
            $counter_nonLanguage = 0;
            $counter_Language = 0;
            foreach( $jsoned as $row ) {
                $node = $row['obj'];
                //check for language
                if( isset($node['xml:lang']) ) {
                    $l_point = new Languagepoint($datapoint, $node['value'], $node['xml:lang']);
                    $languagePoints[] = clone $l_point;
                    $counter_Language+= 1;
                }
                else {
                    $l_point = new Languagepoint($datapoint, $node['value']); // default language
                    $languagePoints[] = clone $l_point; //things get dicey if there is more than one
                    $counter_nonLanguage+= 1;
                }
            }
            if( $counter_Language > 0 and $counter_nonLanguage > 0 ) {
                //language AND literals, something has to be done
                throw new \Exception("Language and literal entries at the same time", 6);
            }
            if( $counter_nonLanguage > 1 ) {
                //more than one literal
                throw new \Exception("More than one non-language entry in node.", 5);
            }
            if( $counter_nonLanguage == 0 and $counter_Language == 0) {
                //no entry at all
                throw new \Exception("Nothing found in the remote Source", 4);
            }
            //everything is okay
            foreach( $languagePoints as $l_point ) {
                $this->languagepointRepository->add($l_point);
            }
            return $languagePoints;
        }
        catch( \Exception $e ) {
            return $e->getMessage();
        }

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
     * action showCollectionEntry
     * Shows the collection edit site but with some additional data to put the point into context
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     * @return void
     */
    public function showCollectionEntryAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry)
    {
        $this->view->assign("ThisEntry", $collectionEntry);
        $this->view->assign("OtherEntries", $this->collectionEntryRepository->fetchCorresponding($collectionEntry->getCollectionID()));
        $this->view->assign("Datapoints", $this->datapointRepository->findAll());
        $this->view->assign("LanguagePoints", $this->languagepointRepository->fetchCorresponding($collectionEntry->getDatapointId()));
    }

    public function updateCollectionEntryAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry) {
        $this->addFlashMessage('[The Entry has been updated, Cache clearing required to see effects immediately]', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->collectionEntryRepository->update($collectionEntry);
        $this->redirect('showCollectionEntry', Null, Null, array('collectionEntry'=>$collectionEntry));
    }


    /**
     * action createSource
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $newSource
     * @return void
     */
    public function createSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $newSource)
    {
        $this->sourceRepository->add($newSource);
        $this->redirect('overview');
    }

    /**
     * action editSource
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @ignorevalidation $source
     * @return void
     */
    public function editSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->view->assign('source', $source);
    }

    /**
     * action showSource
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @ignorevalidation $source
     * @return void
     */
    public function showSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->view->assign('source', $source);
    }
    /**
     * action updateSource
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return void
     */
    public function updateSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->sourceRepository->update($source);
        $this->redirect('overview');
    }
    /**
     * action newSource
     *
     * @return void
     */
    public function newSourceAction()
    {

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
        $datapoints = $this->datapointRepository->findAll();
        $totallist = array();
        foreach( $datapoints as $point ) {
            try {
                $pointList = $this->updateDatapointLanguagepoints($point);
                $totallist = array_merge($totallist, $pointList);
            }
            catch( \Exception $e ) {
                echo $e->getMessage()."<br>";
            }

        }
        $this->view->assign("languagepoints", $totallist);

    }
    /**
     * action explore
     * viewtool to explore sparql endpoints
     *
     * @param array
     * @return void
     */
    public function exploreAction() {
        $sources = $this -> sourceRepository->findAll();
        $this->view->assign("sources", $sources);
        $formdata = $this->request->getArguments();
        $this->view->assign("debug", $formdata);
        if( isset($formdata['postcontent'])) {
            $url = $this->sourceRepository->findByUid($formdata['postcontent']['sourceId'])->getUrl();
            // mirror content of form back to form
            $this->view->assign("form", $formdata['postcontent']);
            // start request to sparql endpoint

            $subject = trim($formdata['postcontent']['subject']);
            if( preg_match("^<.*>$^", $subject) == 0) {
                $subject = "<" . $subject . ">";
            }
            // ^<.*>$

            $predicate = trim($formdata['postcontent']['predicate']);
            if( $predicate == "") { $predicate = "?pre"; }

            $this->view->assign("form", $formdata['postcontent']);

            if( trim($formdata['postcontent']['query']) != "") { //direct query
                $content = $this->directQuery($url, $formdata['postcontent']['query']);
                $this->view->assign("debug", $content);
                return void;
            }

            $content = $this->simpleQuery($url, $subject, $predicate);
            $this->view->assign("explorer", $this->parseSparqlJson($content, $formdata['postcontent']['sourceId']));
            $this->view->assign("debug", $content);
        }

    }

    /**
     * action labelcache
     * shows all the labels
     *
     * @param array
     * @return void
     */
    public function labelcacheOverviewAction() {
        $labels = $this->labelcacheRepository->findAll();
        $this->view->assign("labelcache", $labels);
    }

    /**
     * action languagepoint
     * shows all languagepoint without regard for order
     *
     * @param void
     * @return void
     */
    public function languagepointOverviewAction() {
        $languagepoints = $this->languagepointRepository->findAll();
        $this->view->assign("languagepoints", $languagepoints);
    }

    public function datapointOverviewAction() {
        $datapoints = $this->datapointRepository->findAll();
        $supplement = array();
        foreach( $datapoints as $key => $value ) {
            $lang = $this->languagepointRepository->fetchCorresponding($value);
            $supplement[$key] = $lang;
        }
        $this->view->assign("datapoints", $datapoints);
        $this->view->assign("supplement", $supplement);
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

    /**
     * @param $url URL of the sparql endpoint
     * @param $subject subject of the standard sparql query involved
     * @param $predicate predicate of the standard sparql query
     * @return array containing the jsoned result of the endpoint
     * @throws \Exception Code 10 - other than status Code 200
     */
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
            try {
                $jsoned = json_decode($content, True);
                return $jsoned['results']['bindings'];
            }
            catch(\Exception $e) {
                throw $e;
            }

        }
        else {
            throw new \Exception("Couldnt interpret returned server message", 10);
        }
    }
    //overload
    private function directQuery($url, $query) {
        $requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'form_params' => [
                'query' => $query,
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

    private function parseSparqlJson($query, $SourceID = null, $predicate_name = 'pre', $object_name = 'obj') {
        $a_pre = "<a href=\"";
        $a_mid = "\">";
        $a_end = "</a>";
        $export = array();
        foreach( $query as $entry ) {
            $line = array();
            $pre = $entry[$predicate_name];
            $obj = $entry[$object_name] ;
            if( $pre['type'] == "uri") {
                if( $SourceID != null ) {
                    $label = $this->findLabels($SourceID, $pre['value']);
                }
                else {
                    $label = $pre['value'];
                }
                $line['pre'] = array();
                $line['pre']['display'] = $a_pre . $pre['value'] . $a_mid . $label . $a_end;
                $line['pre']['value'] = $pre['value'];
            }
            else {
                $line['pre'] = $pre['type']. " - ". $pre['value'];
            }
            if( $obj['type'] == "literal" or $obj['type'] == "typed-literal") {
                $line['obj']['value'] = $obj['value'];
                $line['obj']['label'] = $obj['value'];

            }
            elseif( $obj['type'] == "uri") {
                $line['obj']['value'] = $obj['value'];
                $line['obj']['label'] = $label = $this->findLabels($SourceID, $obj['value']);
                $line['form'] = "uri";
            }
            foreach( $obj as $property => $value ) {
                if( $property != "type" and $property != "value" ) {
                    switch( $property ) {
                        case 'xml:lang':
                            $property = "Language";
                            break;
                    }
                    $newValue = $property . " - " . $value;
                    if( !isset($line['extra']) ) {
                        $line['extra'] = $newValue;
                    } // overly complicated, i am sure
                    else {
                        if( is_array($line['extra'])) {
                            $line['extra'][] = $newValue;
                        }
                        else {
                            $line['extra'] = [$line['extra'], $newValue];
                        }
                    }
                }
            }
            $export[] = $line;
        }
        return $export;
    }

    private function findLabels($SourceID, $uri, $language="en") {
        // add database lookup here first
        $source = $this->sourceRepository->findByUid($SourceID);
        try {
            $label = $this->labelcacheRepository->fetchLabel($source, $uri, $language);
        }
        catch(\Exception $e) {
            if( $e->getCode() == 1) //this doesnt look good
            {
                $label = $this->getLabel($SourceID, $uri, $language);
            }
            else {
                throw $e;
            }

        }
        return $label;

    }

    private function getLabel($SourceID, $uri, $language) {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $fallback_language = "en";
        $source = $this->sourceRepository->findByUid($SourceID);
        $url = $this->sourceRepository->findByUid($SourceID)->getUrl();
        $maybeData = $this->simpleQuery($url, "<".$uri.">", "<http://www.w3.org/2000/01/rdf-schema#label>");
        if( $maybeData != null ) {
            foreach( $maybeData as $entry ){
                if( isset($entry['obj']['xml:lang']) and trim($entry['obj']['value']) != "") {
                    $onelabel = new \Ubl\SparqlToucan\Domain\Model\Labelcache();
                    $onelabel->setSubject($uri);
                    $onelabel->setSourceId($source);
                    $onelabel->setLanguage($entry['obj']['xml:lang']);
                    $onelabel->setContent($entry['obj']['value']);

                    $this->labelcacheRepository->add($onelabel);
                    $persistenceManager->persistAll();

                    if( $entry['obj']['xml:lang'] == $language) {
                        $label = $entry['obj']['value'];
                    }
                    if( !isset($label) and $entry['obj']['xml:lang'] == $fallback_language) {
                        $label = $entry['obj']['value'];
                    }
                }
            }
        }
        //if nothing is found $default wont be set, in this case we create a new dummy label for the time beeing
        if( !isset($label) ) {
            $onelabel = new \Ubl\SparqlToucan\Domain\Model\Labelcache();
            $onelabel->setSubject($uri);
            $onelabel->setSourceId($source);
            $onelabel->setLanguage($fallback_language);
            $onelabel->setContent($uri);
            $onelabel->setStatus(1);
            $this->labelcacheRepository->add($onelabel);
            $label = $uri;
        }
        return $label;

    }

    public function testSomethingAction() {
        $datapoint = $this->datapointRepository->findByIdentifier(11);
        $this->view->assign("debug9", $this->languagepointRepository->fetchCorresponding($datapoint));
        $this->view->assign("debug7", $datapoint);
        //$this->languagepointRepository->deleteCorresponding($datapoint);
        //$this->view->assign("debug1", $this->updateDatapointLanguagepoints($datapoint));
        //$datapoint = $this->datapointRepository->findByIdentifier(9);
        //$this->view->assign("debug2", $this->updateDatapointLanguagepoints($datapoint));
    }
}