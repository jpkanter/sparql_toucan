<?php
namespace Ubl\SparqlToucan\Controller;

use http\Exception;
use MongoDB\Driver\Query;
use Ubl\SparqlToucan\Domain\Model\Collection;
use Ubl\SparqlToucan\Domain\Model\CollectionEntry;
use Ubl\SparqlToucan\Domain\Model\Datapoint;
use Ubl\SparqlToucan\Domain\Model\Languagepoint;
use Ubl\SparqlToucan\Domain\Model\Textpoint;
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
    /**
     * textpointRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\TextpointRepository
     * @inject
     */
    protected $textpointRepository = null;

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
        //TODO: Fix this to the same method used in DynamicLayout
        foreach($datapoints as $thisKey => $onePoint) {
            if( trim($onePoint->getName()) == "") {
                $datapoints[$thisKey]->setName(">> ".substr($onePoint->getsubject(), -20));
            }
        }
        $this->view->assign('datapoints', $datapoints);
        $textpoints = $this->textpointRepository->findAll();
        $this->view->assign('textpoints', $textpoints);
    }

    /**
     * action editCollectionDynamicLayout
     * View to get the layouting for a collection right
     *
     * @param Collection $collection
     */
    public function editCollectionDynamicLayoutAction(\Ubl\SparqlToucan\Domain\Model\Collection  $collection)
    {
        $this->view->assign('collection', $collection);
        $entries = $this->collectionEntryRepository->fetchCorresponding($collection);
        $sys_language_name = $this->getLanguage();
        $freeEntries = [];
        $chainedEntries = [];

        foreach($entries as $thisKey => $onePoint) {
            if( $onePoint->getDatapointId() != 0 && trim($onePoint->getDatapointId()->getName()) == "") {
                $entries[$thisKey]->getDatapointId()->setName(">> ".substr($onePoint->getDatapointId()->getpredicate(), -20));
            }
            try {
                if( $onePoint->getDatapointId() != 0 ) {
                    $value = $this->languagepointRepository->fetchSpecificLanguage($onePoint->getDatapointId(), $sys_language_name);
                }
                elseif( $onePoint->getTextpoint() != 0 ) {
                    $value = $this->languagepointRepository->fetchSpecificLanguage($onePoint->getTextpoint(), $sys_language_name);
                }
                else {
                    $value = ""; //if neither Textpoint nor Datapoint exists its an empty placeholder entry
                }
            } catch (\Exception $e) {
                $value = "<[NO LP]>";
            }
            $entries[$thisKey]->SetTempValue($value);
            if( trim($onePoint->getGridArea()) == "" && $onePoint->getParentEntry == 0 ) {
                $freeEntries[] = $onePoint;
            }
            if( trim($onePoint->getGridArea()) != "" && $onePoint->getParentEntry == 0) {
                $chainedEntries[] = $onePoint;
            }
        }
        $this->view->assign('collectionEntry', $entries);
        $this->view->assign('freeEntries', $freeEntries);
        $this->view->assign('chainedEntries', $chainedEntries);
    }

    public function editCollectionRearrangeAction() {
        //expects formData regardless
        $formdata = $this->request->getArguments();
        $thisData = $formdata['editCollectionRearrange'];
        $thisCollection = $this->collectionRepository->findByUid($thisData['collection']);
        foreach( $thisData as $key => $entry) {
            if($key != "collection") {
                $cEntry = $this->collectionEntryRepository->findByUid($key);
                $cEntry->setGridArea($entry);
                $this->collectionEntryRepository->Update($cEntry);
            }
        }
        $this->redirect("editCollectionDynamicLayout", null, null, array('collection' => $thisCollection));
        //redirect to editCollectionDynamic
    }

    /**
     * action newCollection
     *
     * @return void
     */
    public function newCollectionAction()
    {
        //just displays the form, not db interaction required
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

    public function updateDatapointLanguagepoints(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint, $selfCatch = true)
    {
        $mode = $datapoint->getMode();
        if( $mode === 0 ) { // First Label Mode
            return $this->updateLanguagepointsMode0($datapoint, $selfCatch);
        }

        if( $mode === 2 ) { // Special Algorithm Way
            return $this->updateLanguagepointsMode2($datapoint, $selfCatch);
        }


    }

    private function updateLanguagepointsMode0($jsoned, Datapoint $datapoint, $selfCatch = true) {
        try {
            $subject = $datapoint->getSubject();
            $this->arrowBrackets($subject);
            $predicate = $datapoint->getPredicate();
            $this->arrowBrackets($predicate);
            $jsoned = $this->simpleQuery($datapoint->getSourceId()->getUrl(), $subject, $predicate);
        }
        catch ( \Exception $e ) { //theoretically ->simpleQuery should not throw exceptions
            if( $selfCatch ) { return $e->getMessage(); }
            else { throw $e; }
        }

        try {
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
            $this->languagepointRepository->deleteCorresponding($datapoint);
            foreach( $languagePoints as $l_point ) {
                $this->languagepointRepository->add($l_point);
            }
            return $languagePoints;
        }
        catch( \Exception $e ) {
            if( $selfCatch ) {
                return $e->getMessage();
            }
            else {
                throw $e;
            }
        }
    }

    private function updateLanguagepointsMode2(Datapoint $datapoint, $selfCatch = true) {
        $weekDays = ["http://schema.org/Monday" => 0, "http://schema.org/Tuesday" => 1, "http://schema.org/Wednesday" => 2,
                     "http://schema.org/Thursday" => 3, "http://schema.org/Friday" => 4, "http://schema.org/Saturday" => 5,
                     "http://schema.org/Sunday" => 6];
        $predicate = $datapoint->getPredicate();
        if( strtolower($predicate) == "http://schema.org/openinghoursspecification"
        ||  strtolower($predicate) == "https://schema.org/openinghoursspecification" ) { //the desperate try to future proof this thing
            $subject = $datapoint->getSubject();
            $specialQuery = "SELECT ?day, ?open, ?close 
                WHERE { <{$subject}> <{$predicate}> ?entry .
                    {
                         SELECT ?entry, ?day, ?open, ?close WHERE { 
                            ?entry <http://schema.org/dayOfWeek> ?day;
                            <http://schema.org/closes> ?close;
                            <http://schema.org/opens> ?open. 
                         } 
                    }
                }"; //query is quite simple, its basically foreach entry[type=openingHours) get day, closing time & opening time
            $jsoned = $this->genericSparqlQuery($datapoint->getSourceId()->getUrl(), $specialQuery);
            foreach( $jsoned as $entry ) {
                //group opening hours together
            }
            return $jsoned;
        }
        else {
            throw new \Exception("Dont know how to handle Predicate '{$predicate}' with Mode 2", 7 );
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

    public function deleteCollectionAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection) {
        $this->collectionRepository->saveDelete($collection);
        $this->redirect("overview");
    }

    public function createCollectionAction2(\Ubl\SparqlToucan\Domain\Model\Collection $newCollection)
    {
        $this->collectionRepository->add($newCollection);
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
        $dps = $this->datapointRepository->findAll()->toArray();
        array_unshift($dps, ['uid' => 0, 'name' => "noentry"]); //TODO: i18n
        $this->view->assign("Datapoints", $dps);
        $tps = $this->textpointRepository->findAll()->toArray();
        array_unshift($tps, ['uid' => 0, 'name' => "noentry"]);
        $this->view->assign("Textpoints", $tps);
        $this->view->assign("LanguagePoints", $this->languagepointRepository->fetchCorresponding($collectionEntry->getDatapointId()));
    }

    public function updateCollectionEntryAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry) {
        $this->addFlashMessage('[The Entry has been updated, Cache clearing required to see effects immediately]', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->collectionEntryRepository->update($collectionEntry);
        $this->redirect('showCollectionEntry', Null, Null, array('collectionEntry'=>$collectionEntry));
    }

    public function updateCollectionEntry2Action() {
        //this feels quite fragile
        $this->view->assign("debug", $this->request->getArguments());
        $form = $this->request->getArguments();
        $pureCE = $this->collectionEntryRepository->findByUid(intval($form['collectionEntry']['__identity']));
        $pureCE->setName($form['name']);
        $pureCE->setGridRow(intval($form['gridRow']));
        $pureCE->setGridColumn(intval($form['gridColumn']));
        $pureCE->setGridRowEnd(intval($form['gridRowEnd']));
        $pureCE->setGridColumnEnd(intval($form['gridColumnEnd']));
        $pureCE->setPosition(intval($form['position']));
        $pureCE->setStyle(intval($form['style']));
        $pureCE->setStyleName($form['styleName']);
        $links = ['datapointId', 'textpoint'];
        foreach( $links as $link) {
            $value = -1;
            if ( isset($form[$link]['__identity']) && is_string($form[$link]['__identity'])) {
                $value = intval($form[$link]['__identity']);
            }
            elseif( isset($form[$link]) && is_string($form[$link]) ) {
                $value = intval($form[$link]);
            }
            if( $value == 0 ) {
                $pureCE->unsetLink($link);
            }
            else {
                $pureCE->setLink($link, $value);
            }
        }
        $this->view->assign('test', $pureCE);
        $this->collectionEntryRepository->update($pureCE);
        /*Okay, i dont understand this, call me a moron, so be it. I need to update right here right now so it has any
        effect, then it works flawlessly. My original plan was to preserve the general structure and just redirect
        to the actual update function in case i want to expand there. But despite giving the modified "pureCE" object
        nothing happens, most likely cause it fetches a fresh version of it, why ever it should do that. I dont
        understand that part, but if i persistent my frankensteined Collection Entry it works. Most likely the real
        update collection Entry doesnt do anything anymore. Maybe it has something to do with the double redirect, i am
        not entirely sure when the persistance manager triggers, maybe only for the first redirect, anyway, this works
        i will just leave it there, temporarily of course till i have something better, but we all know how this works,
        it will stay here for ever except if it breaks somehow very spectacularly*/
        $this->redirect('updateCollectionEntry', Null, Null, array('collectionEntry'=>$pureCE));
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
     * action , doesnt delete anything but displays the view with the usage of that source and gives a bunch of options
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return void
     */
    public function deleteSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->view->assign("source", $source); //mirror mirror
        $statistics = [
            'overall' => 0,
            'affectedDatapoints' => 0,
            'affectedLanguagepoints' => 0,
            'affectedCollections' => 0,
            'notAffectedCollections' => 0]
        ;
        //affected datapoints, uses cachedValue as avenue for more data
        $datapoints = $this->datapointRepository->findUsedSource($source);
        foreach($datapoints as $thisKey => $onePoint) {
            if( trim($onePoint->getName()) == "") {
                $datapoints[$thisKey]->setName(">> ".substr($onePoint->getsubject(), -20));
            }
            $statistics['overall']+=1; $statistics['affectedDatapoints']+=1;
            $localLanguagePoints = $this->languagepointRepository->fetchCorresponding($onePoint)->toArray();
            if( !empty($localLanguagePoints)) {
                $datapoints[$thisKey]->setCachedValue($localLanguagePoints);
                foreach( $localLanguagePoints as $nothing) {
                    $statistics['affectedLanguagepoints']+=1;$statistics['overall']+=1;
                }
            }
        }
        $this->view->assign("datapoints", $datapoints);
        // affected collections
        $allCollections = $this->collectionRepository->findAll();
        $phantomCollection = [];
        foreach($allCollections as $singleCollection ) {
            $tempEntries = $this->collectionEntryRepository->fetchCorresponding($singleCollection);
            $stat['del'] = 0;
            $stat['all'] = 0;
            foreach( $tempEntries as $entry ) {
                $stat['all']+= 1;
                if( $entry->getDatapointId()->getSourceId() == $source ) {
                    $stat['del']+= 1;
                }
            }
            if( $stat['del'] > 0 ) {
                $airArray['name'] = $singleCollection->getName();
                $airArray['entries'] = $stat['all'];
                $airArray['deleteEntries'] = $stat['del'];
                $airArray['percentage'] = strval(round($stat['del'] / $stat['all'], 3)*100);
                $phantomCollection[] = $airArray;
                $statistics['affectedCollections']+= 1; $statistics['overall']+= 1;
            }
            else {
                $statistics['notAffectedCollections']+= 1;
            }
        }
        $this->view->assign("collections", $phantomCollection);
        $this->view->assign("statistics", $statistics);

        // $this->addFlashMessage('The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        //check for corresponding entries
    }

    public function finalDeleteSourceAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->addFlashMessage('The choosen source with name '.$source->getName().' was deleted without regard of any data consistency.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->sourceRepository->remove($source);
        $this->redirect('overview');
    }

    public function remoteUpdateDatapointsAction() {
        //request test
        $datapoints = $this->datapointRepository->findAll();
        $totallist = array();
        foreach( $datapoints as $point ) {
            try {
                $randomPoint = $this->updateDatapointLanguagepoints($point, false);
                $totallist[] = $randomPoint[0]; //TODO: this is quite weird, fix it
            }
            catch( \Exception $e ) {
                echo $e->getMessage()."<br>";
            }

        }
        $this->view->assign("languagepoints", $totallist);
    }

    public function timemachineAction() {
        $formdata = $this->request->getArguments();
        $time_delta = 3600;
        if( isset($formdata['timeDelta']) ) {
            if ( ($time_delta = strtotime($formdata['timeDelta'])) !== false ) {
                $this->view->assign("time_delta", $formdata['timeDelta']);
                $time_delta = $time_delta - strtotime("00:00");
            }
        }
        $deletionTime = time() - $time_delta;

        $travelers = array();

        $tables = [
            "datapoint" => $this->datapointRepository,
            "languagepoint" => $this->languagepointRepository,
            "collectionentry" => $this->collectionEntryRepository,
            "collection" => $this->collectionRepository,
            "labelcache" => $this->labelcacheRepository,
            "source" => $this->sourceRepository
        ];
        //according to the docs i shall not do this, making queries outside of the repo
        foreach( $tables as $key => $repository ) { //this feels like a little dirty trick that actually works
            $query = $repository -> createQuery();
            $query->setOrderings(['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);
            $query->matching($query->greaterThanOrEqual('crdate', $deletionTime));
            $travelers[$key] = $query->execute()->toArray();
        }

        $this->view->assign("tables", $travelers);

        $this->view->assign("deletionTime", $deletionTime);

        # $deletionTime = now - 3600;
    }

    public function deleteTimemachineAction() {
        $machine_reach = 24 * 3600; //seconds, we are staying in metaphor here, our machine is weak and cannot go further than 24 hours
        $formdata = $this->request->getArguments();
        $this->view->assign("form", $formdata);

        //failsafes
        if( !isset($formdata['timemachineLever']['deletionTime'])) {
            $this->addFlashMessage("The machine was activated without providing the parameters needed.", '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
            $this->redirect("timemachine");
        }
        $deletionTime = intval($formdata['timemachineLever']['deletionTime']);
        if( $deletionTime == 0 or $deletionTime == 1 ) { //as we deal in minutes this should never fail right?
            $this->addFlashMessage("The machine encountered parameters of the wrong kind.", '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
            $this->redirect("timemachine");
        }
        if( time() - $deletionTime > $machine_reach ) {
            $this->addFlashMessage("The machine was used for a scope beyond its capability", '', \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING);
            $this->redirect("timemachine");
        }

        $tables = [
            "datapoint" => $this->datapointRepository,
            "languagepoint" => $this->languagepointRepository,
            "collectionentry" => $this->collectionEntryRepository,
            "collection" => $this->collectionRepository,
            "labelcache" => $this->labelcacheRepository,
            "source" => $this->sourceRepository
        ];
        foreach( $tables as $key => $repository ) {
            $query = $repository -> createQuery();
            $query->setOrderings(['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);
            $query->matching($query->greaterThanOrEqual('crdate', $deletionTime));
            $result = $query->execute()->toArray();
            foreach($result as $entry ) {
                $repository->remove($entry);
            }
        }
        $this->addFlashMessage("The machine did it work and erased the past", '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->redirect("timemachine");
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

            //$predicate = trim($formdata['postcontent']['predicate']);
            //if( $predicate == "") { $predicate = "?pre"; }

            $this->view->assign("form", $formdata['postcontent']);

            if( trim($formdata['postcontent']['query']) != "") { //direct query
                $content = $this->directQuery($url, $formdata['postcontent']['query']);
                $this->view->assign("debug", $content);
                return void;
            }

            $explorative_result = $this->explorativeQuery($url, $subject);
            /** Pre backing lines for Type V7
             Otherwise stuff like this would work in Fluid: <f:if condition="{labels.{lineItem}}">
             Fluid in Typo7 doesnt support this kind of behaviour, therefore i need to do it in php, annoying
             */

            $explore_table = array();
            foreach( $explorative_result[1] as $key => $predicate ) {
                if( isset($explorative_result[0][$key]) ) {
                    $label = $explorative_result[0][$key];
                }
                else {
                    $label = $key;
                }
                $objects = array();
                foreach( $predicate as $object) {
                    $label_key = $object['value'];
                    if( isset($explorative_result[0][$label_key]) ) { //never works if type!=uri
                        $object_label = $explorative_result[0][$label_key];
                    }
                    else {
                        $object_label = $object['value'];
                    }
                    $adda = "";
                    foreach( $object as $objkey => $objvalue) {
                        if( $objkey != "value" && $objkey != "type" ) {
                            $adda.= $objkey.": ".$objvalue."; ";
                        }
                    }
                    if( empty($adda)) { $adda = "N/A";} //TODO: i18n here
                    $objects[] = ["value" => $label_key, "label" => $object_label, "type" => $object['type'], "adda" => $adda];
                }
                $explore_table[] = ["value" => $key, "label" => $label, "rowspan" => count($predicate), "objects" => $objects];

            }

            /**End of PreBacking Processing*/
            $this->view->assign("trueExplore", $explore_table);
        }

    }

    public function textpointOverviewAction() {
        $textpoints = $this->textpointRepository->findAll();
        //$textpoints = $textpoints->toArray();
        foreach($textpoints as $key => $item) {
            if( unserialize($item->getLanguages()) ) {
                $languages = unserialize($item->getLanguages());
            }else { $languages = [];}
            $textpoints[$key]->setTemplang($languages);
        }
        $this->view->assign("textpoints", $textpoints);
    }

    public function createTextpointAction(Textpoint $textpoint) {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $this->textpointRepository->add($textpoint);
        $persistenceManager->persistAll();

        $formdata = $this->request->getArguments();
        if( array_key_exists('btn_createedit', $formdata) ) {
            $this->redirect('editTextpoint', Null, Null, array('textpoint' => $textpoint));
        } else {
            $this->redirect('textpointOverview');
        }
    }

    public function editTextpointAction(Textpoint $textpoint) {
        $this->view->assign("textpoint", $textpoint);
        $this->view->assign("languagepoints", $this->languagepointRepository->fetchCorresponding($textpoint));
    }

    public function updateTextpointAction(Textpoint $textpoint) {
        $this->addFlashMessage('The object was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $formdata = $this->request->getArguments();
        $this->textpointRepository->update($textpoint);
        if( array_key_exists('btn_save', $formdata) ) {
            $this->redirect('editTextpoint', Null, Null, array('textpoint' => $textpoint));
        } else {
            $this->redirect('textpointOverview');
        }
    }

    public function updateTextpointLangAction(Textpoint $textpoint) {
        $this->addFlashMessage('Languageinfo was manually updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->textpointRepository->updateLanguages($textpoint);
        $this->redirect('textpointOverview');
    }

    public function deleteTextpointAction(Textpoint $textpoint) {
        $langPoints = $this->languagepointRepository->fetchCorresponding($textpoint);
        if( $langPoints->count() > 0 ) {
            $this->view->assign("languagepoints", $langPoints);
            $this->view->assign("textpoint", $textpoint);
        }
        else {
            $this->textpointRepository->remove($textpoint);
            $this->redirect('textpointOverview');
        }
    }

    public function forceDeleteTextpointAction(Textpoint $textpoint) {
        $this->addFlashMessage('Deleted Textpoint '.$textpoint->getName()." and all corresponding Languagepoints.", '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->languagepointRepository->deleteCorresponding($textpoint);
        $this->textpointRepository->remove($textpoint);
        $this->redirect('textpointOverview');
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

    public function createLanguagepointAction(Languagepoint $newLanguagepoint) {
            $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $this->addFlashMessage('The object was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->languagepointRepository->add($newLanguagepoint);
            $persistenceManager->persistAll();
        //failsafe, direct languagepoint edits should only be avaible in textpoints but anyway
        if( $newLanguagepoint->getTextpoint() !== 0 ) {
            $this->textpointRepository->updateLanguages($newLanguagepoint->getTextpoint());
            $this->redirect('editTextpoint', null, null, array('textpoint' => $newLanguagepoint->getTextpoint()));
        }
        else {
            $this->redirect('overview');
        }

    }

    public function deleteLanguagepointAction(Languagepoint $oldLanguagepoint) {
            $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $this->addFlashMessage('The object was removed.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->languagepointRepository->remove($oldLanguagepoint);
            $persistenceManager->persistAll();
        if( $oldLanguagepoint->getTextpoint() !== 0 ) {
            $this->textpointRepository->updateLanguages($oldLanguagepoint->getTextpoint());
            $this->redirect('editTextpoint', null, null, array('textpoint' => $oldLanguagepoint->getTextpoint()));
        }
        else {
            $this->redirect('overview');
        }

    }

    public function updateLanguagepointAction(Languagepoint $languagepoint) {
            $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $this->addFlashMessage('The object was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->languagepointRepository->update($languagepoint);
            $persistenceManager->persistAll();
        if( $languagepoint->getTextpoint() !== 0 ) {
            $this->textpointRepository->updateLanguages($languagepoint->getTextpoint());
            $this->redirect('editTextpoint', null, null, array('textpoint' => $languagepoint->getTextpoint()));
        }
        else {
            $this->redirect('overview');
        }
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

    public function newComplexCollectionAction(Datapoint $seedDatapoint) {
        //request all object stuff
        $this->view->assign("seedDatapoint", $seedDatapoint);

        $url = $seedDatapoint->getSourceId()->getUrl();
        $subject = trim($seedDatapoint->getSubject());
        if( preg_match("^<.*>$^", $subject) == 0) {
            $subject = "<" . $subject . ">";
        }
        $content = $this->simpleQuery($url, $subject, "?pre");
        $this->view->assign("explorer", $content);

        try {
            $this->view->assign("newName", $this->labelcacheRepository->fetchLabel($seedDatapoint->getSourceId(), $seedDatapoint->getSubject()));
        }
        catch( \Exception $e ) {
            if( $e->getCode() == 1 ) {
                $this->view->assign("newName", "");
            }
        }

        $addressCondition = [
            "http://www.w3.org/2000/01/rdf-schema#label",
            "http://schema.org/email",
            "http://schema.org/telephone",
            "http://schema.org/address"
        ];
        $deletePoints = [];
        foreach( $content as $line) {
            foreach( $addressCondition as $key => $subj ) {
                if( $subj == $line['pre']['value'] ) {
                    $deletePoints[] = $key;
                }
            }
        }
        foreach( $deletePoints as $key) {
            unset($addressCondition[$key]);
        }
        if( count($addressCondition) <= 0 ) {
            $this->view->assign("place", True);
            $this->view->assign("sources", $this->sourceRepository->findAll());
        }


    }

    public function createComplexCollectionAction() {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $formdata = $this->request->getArguments();
        $this->view->assign("debug", $formdata);
        //Input Sanitation
            //i am quite sure i can leverage extbase here instead of do it myself
        if( trim($formdata['CollectionAndMore']['layout']) == "" ) {
            $formdata['CollectionAndMore']['layout'] = 1;
        }

        $newCollection = new Collection(
            $formdata['CollectionAndMore']['name'],
            $formdata['CollectionAndMore']['layout'],
            $formdata['CollectionAndMore']['style_override']
        );
        $this->collectionRepository->add($newCollection);

        $thisSource = $this->sourceRepository->findByIdentifier($formdata['CollectionAndMore']['sourceId']);
        $url = $thisSource->getUrl();
        $subject = trim($formdata['CollectionAndMore']['subject']);
        if( preg_match("^<.*>$^", $subject) == 0) {
            $subject = "<" . $subject . ">";
        }
        $content = $this->simpleQuery($url, $subject, "?pre");
        //in real case we need to request the type of template here
        $addressCondition = [
            "http://www.w3.org/2000/01/rdf-schema#label" => 1,
            "http://schema.org/email" => 5,
            "http://schema.org/telephone" => 6,
            "http://schema.org/address" => -1
        ];
        $subPointList = [
          "http://schema.org/address" => [
              "http://schema.org/streetAddress" => 2,
              "http://schema.org/postalCode" => 3,
              "http://schema.org/addressLocality" => 4,
          ]
        ];
        $reusedDatapoints = array();
        $remainingConditions = array();
        //check direct links of existing datapoints
        foreach( $addressCondition as $singleCondition => $position) {
            if( $position != -1 ) {
                $result = $this->datapointRepository->findCopies($thisSource, $formdata['CollectionAndMore']['subject'], $singleCondition);
                if( count($result) <= 0 )  {
                    $remainingConditions[$singleCondition]  = $position;
                }
                else {
                    $reusedDatapoints[] = $result->toArray()[0];
                }
            }
            else {
                $parts = 0;
                foreach( $subPointList[$singleCondition] as $key => $discard ) {
                    foreach( $content as $line ) { //this can be solved better
                        if($line['pre']['value'] == $singleCondition) {
                            $tempSubject = $line['obj']['value'];
                            break;
                        }
                    }
                    $result = $this->datapointRepository->findCopies($thisSource, $tempSubject, $key);
                    if( count($result) <= 0 )  {
                        $parts++; //counts upwards to all sub fields we need
                    }
                    else {
                        $reusedDatapoints[] = $result->toArray()[0];
                    }
                }
                if( $parts == count($subPointList[$singleCondition])) {
                    $remainingConditions[$singleCondition] = $position; //$position shold always be -1
                }
            }

        }
        $this->view->assign("oldDatapoints", $reusedDatapoints);

        $newDatapoints = array();
        $this->view->assign("remain", $remainingConditions);
        foreach( $remainingConditions as $predicate => $position ) {
            if( $position != -1 ) {
                $tempDP = new Datapoint();
                $tempDP->setSourceId($thisSource);
                $tempDP->setSubject($formdata['CollectionAndMore']['subject']);
                $tempDP->setPredicate($predicate);
                $tempDP->setAutoUpdate(true);
                $newDatapoints[] = clone $tempDP;
            }
            else {
                //finding subject
                foreach( $content as $line ) {
                    if($line['pre']['value'] == $predicate) {
                        $tempSubject = $line['obj']['value'];
                        break;
                    }
                }
                foreach( $subPointList[$predicate] as $anotherPredicate => $discard ) {
                    //ERROR CONDITION IF NO RESULT SHOWN
                    $tempDP = new Datapoint();
                    $tempDP->setSourceId($thisSource);
                    $tempDP->setSubject($tempSubject);
                    $tempDP->setPredicate($anotherPredicate);
                    $tempDP->setAutoUpdate(true);
                    $newDatapoints[] = clone $tempDP;
                }
            }

        }
        $this->view->assign("newDatapoints", $newDatapoints);
        //Persists Datapoints here
        foreach( $newDatapoints as $DP ) {
            $this->datapointRepository->add($DP);
        }
        $persistenceManager->persistAll();

        $newCollectionEntries = array();
        foreach( $newDatapoints as $DP ) {
            foreach($addressCondition as $key => $position) {
                if( $position != -1 ) {
                    if( $DP->getPredicate() == $key ) {
                        $tempEntry = new CollectionEntry();
                        $tempEntry->setName($key);
                        $tempEntry->setGridRow($position);
                        $tempEntry->setStyle(0);
                        $tempEntry->setStyleName("");
                        $tempEntry->setCollectionID($newCollection);
                        $tempEntry->setDatapointId($DP);
                        $newCollectionEntries[] = clone $tempEntry;
                        break;
                    }
                }
                else {
                    foreach( $subPointList[$key] as $predicate => $subPosition ) {
                        if( $DP->getPredicate() == $predicate ) {
                            $tempEntry = new CollectionEntry();
                            $tempEntry->setName($predicate);
                            $tempEntry->setGridRow($subPosition);
                            $tempEntry->setStyle(0);
                            $tempEntry->setStyleName("");
                            $tempEntry->setCollectionID($newCollection);
                            $tempEntry->setDatapointId($DP);
                            $newCollectionEntries[] = clone $tempEntry;
                            break;
                        }
                    }
                }
            }
        }
        foreach( $reusedDatapoints as $DP) {
            //i dont need to foreach this right? - 03.12.2020
            foreach($addressCondition as $key => $position) {
                if( $position != -1 ) {
                    if( $DP->getPredicate() == $key ) {
                        $tempEntry = new CollectionEntry();
                        $tempEntry->setName($key);
                        $tempEntry->setGridRow($position);
                        $tempEntry->setStyle(0);
                        $tempEntry->setStyleName("");
                        $tempEntry->setCollectionID($newCollection);
                        $tempEntry->setDatapointId($DP);
                        $newCollectionEntries[] = clone $tempEntry;
                        break;
                    }
                }
                else {
                    foreach( $subPointList[$key] as $key2 => $position ) {
                        if( $DP->getPredicate() == $key2 ) {
                            $tempEntry = new CollectionEntry();
                            $tempEntry->setName($key2);
                            $tempEntry->setGridRow($position);
                            $tempEntry->setStyle(0);
                            $tempEntry->setStyleName("");
                            $tempEntry->setCollectionID($newCollection);
                            $tempEntry->setDatapointId($DP);
                            $newCollectionEntries[] = clone $tempEntry;
                            break;
                        }
                    }
                }

            }
        }
        foreach( $newCollectionEntries as $CE ) {
            $this->collectionEntryRepository->add($CE);
        }
        $persistenceManager->persistAll();

        $this->view->assign("newCollectionEntries", $newCollectionEntries);

        $this->view->assign("newCollection", $newCollection);
    }

    /**
     * action updateCollection
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    public function updateCollectionLanguagepointsAction(Collection $collection) {
        $entryQueryResults = $this->collectionEntryRepository->fetchCorresponding($collection);
        if( count($entryQueryResults) <= 0) {
            $this->addFlashmessage("This collection has no entries at all", "", \TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE);
        }
        $thecount = 0;
        foreach($entryQueryResults as $entry) {
            $this->UpdateDatapointLanguagepoints($entry->getDatapointId());
            $thecount++;
        }
        $this->addFlashmessage($thecount." Entries were updated.", "", \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->redirect("overview");
    }

    private function recursiveSparqlQuery($url, $subject, $predicate) {
        $languageFilter = "de"; # TODO
        //$requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class); #V8.7
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'form_params' => [
                'query' => 'SELECT * WHERE {'.$subject.' '.$predicate.' ?obj}',
                'format' => 'json'
            ]
        ];

        $request = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Core\\Http\\HttpRequest',
            $url,
            'HEAD',
            $additionalOptions);
        $request->setMethod('POST');
        $response = $request->send();
        // $response = $requestFactory->request($url, 'POST', $additionalOptions); #V8.7

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
     * @param string $url URL of the sparql endpoint api
     * @param string $query a valid sparql query
     * @param string $return_format kind of returned data (eg. json, xml...)
     * @param string[] $sub_array list of strings that describe the tree where the returned data lays in list format
     * @param string $query_interface parameter/appended part to the url to perform the query
     * @return array as list of arrays
     * @throws \Exception
     */
    private function genericSparqlQuery($url, $query, $return_format = "json", $sub_array=['results', 'bindings'], $query_interface="query") {
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'form_params' => [
                $query_interface => $query,
                'format' => $return_format
            ]
        ];
        $request = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Core\\Http\\HttpRequest',
            $url);
        $request->setMethod(\TYPO3\CMS\Core\Http\HttpRequest::METHOD_POST)
            ->addPostParameter($additionalOptions['form_params']);
        $response = $request->send();

        if ($response->getStatus() === 200) { #->getStatusCode() for V8.7+
            $content = $response->getBody(); #getBody()->getContents() for V8.7+
            try {
                $jsoned = json_decode($content, True);
                $returnedArray = $jsoned;
                try { //scales down the array structure to the part that is the result
                    foreach( $sub_array as $key) {
                        $returnedArray = $returnedArray[$key];
                    }
                    return $returnedArray;
                }
                catch(OutOfBoundsException  $e) {
                    throw new \Exception("Given Array Structure doesnt match results retrieved", 9);
                }
            }
            catch(\Exception $e) {
                throw $e;
            }

        }
        else { //TODO: extend this to proper status handling
            throw new \Exception("Couldnt interpret returned server message
            \n Status Code: ".$response->getStatus()."
            \n Query: ".$query, 10);
        }
    }

    /**
     * Wrapper for query, used to be something else
     * @param $url URL of the sparql endpoint
     * @param $subject subject of the standard sparql query involved
     * @param $predicate predicate of the standard sparql query
     * @return array
     * @throws \Exception
     */
    private function simpleQuery($url, $subject, $predicate) {
        return $this->genericSparqlQuery($url, "SELECT * WHERE {".$subject." ".$predicate." ?obj}");
    }

    private function explorativeQuery($url, $subject, $filter=True) {
        $timeSet = [];
        $timeSet[] = ['time' => microtime(), 'msg' => 'start'];
        $defaultLanguage = "en";

        if( preg_match("^<.*>$^", $subject) == 0) {
            $subject = "<" . $subject . ">";
        }

        $query = "SELECT * 
                  WHERE { ".$subject." ?pre ?obj 
                    OPTIONAL { ?obj <http://www.w3.org/2000/01/rdf-schema#label> ?objLabel }
                    OPTIONAL { ?obj <http://www.w3.org/2000/01/rdf-schema#comment> ?objComment }
                    OPTIONAL { ?pre <http://www.w3.org/2000/01/rdf-schema#label> ?preLabel }
                    OPTIONAL { ?pre <http://www.w3.org/2000/01/rdf-schema#comment> ?preComment }
                  }";
        $labelNames = ["obj" => "objLabel", "pre" => "preLabel"];

        $resultList = $this->genericSparqlQuery($url, $query); //other stuff stays default for now
        $timeSet[] = ['time' => microtime(), 'msg' => 'query'];
        $allLabels = array();
        $elements = array();

        foreach( $resultList as $singularEntry ) {
            $pre = $singularEntry['pre']['value'];

            //label maker
            foreach( $labelNames as $key => $fieldName) {
                if( $singularEntry[$key]['type'] == "uri") {

                    if( !isset($singularEntry[$fieldName]) ) {
                        continue; //in case there is no label anyway
                    }
                    $keyValue = $singularEntry[$key]['value'];
                    if( !isset($allLabels[$keyValue]) ) {
                        $allLabels[$keyValue] = array();
                    }
                    if( isset($singularEntry[$fieldName]['xml:lang']) ) {
                        if( !isset($allLabels[$keyValue][$singularEntry[$fieldName]['xml:lang']]) ) {
                            $allLabels[$keyValue][$singularEntry[$fieldName]['xml:lang']] = $singularEntry[$fieldName]['value'];
                        }
                    }
                    else {
                        if( !isset($allLabels[$keyValue][$defaultLanguage]) ) {
                            $allLabels[$keyValue][$defaultLanguage] = $singularEntry[$fieldName]['value'];
                        }
                    }
                }
            }

            if( !isset($elements[$pre]) ) {
                $elements[$pre] = array();
                $elements[$pre][] = $singularEntry['obj'];
                continue;
            }
            else {
                $goAhead = True;
                foreach( $elements[$pre] as $thisPre ) {
                    if( $singularEntry['obj']['value'] == $thisPre['value']) {
                        $goAhead = False; //crutch
                        continue 2; //jumps ahead in main loop and ignores the rest of the stuff here
                    }
                }
                //if we actually get here
                if( $goAhead) $elements[$pre][] = $singularEntry['obj'];
            }
        }
        $timeSet[] = ['time' => microtime(), 'msg' => 'main loop'];
        //at this point we have all unfiltered information, to many for normal use cases
        if( $filter ) {
            if (TYPO3_MODE === 'FE') {
                if (isset($GLOBALS['TSFE']->config['config']['language'])) {
                    $desiredLanguage = $GLOBALS['TSFE']->config['config']['language'];
                }
            } elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
                $desiredLanguage =  $GLOBALS['BE_USER']->uc['lang'];
            }
            else {
                $desiredLanguage = $defaultLanguage; //default
            }
            $oneLanguageLabel = array();
            foreach( $allLabels as $key => $entry) {
                if( isset($entry[$desiredLanguage]) ) {
                    $oneLanguageLabel[$key] = $entry[$desiredLanguage];
                }
                elseif( isset($entry[$defaultLanguage])) {
                    $oneLanguageLabel[$key] = $entry[$defaultLanguage];
                }
                else {//any language that might be left, i just take the first entry i get, we could integrate priority here
                    foreach( $entry as $anyLanguage) {
                        $oneLanguageLabel[$key] = $anyLanguage;
                        continue 2;
                    }
                }
            }

            $timeSet[] = ['time' => microtime(), 'msg' => 'language loop'];
            return [$oneLanguageLabel, $elements, $timeSet];
        }


        return [$allLabels, $elements, $timeSet];
    }

    //overload

    /** TODO replace this
     * @param $url
     * @param $query
     * @return array
     * @throws \Exception
     */
    private function directQuery($url, $query) {
        //$requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class); #V8.7
        return $this->genericSparqlQuery($url, $query);
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
                $line[$predicate_name] = array();
                $line[$predicate_name]['display'] = $a_pre . $pre['value'] . $a_mid . $label . $a_end;
                $line[$predicate_name]['value'] = $pre['value'];
            }
            else {
                $line[$predicate_name] = $pre['type']. " - ". $pre['value'];
            }
            if( $obj['type'] == "literal" or $obj['type'] == "typed-literal") {
                $line[$object_name]['value'] = $obj['value'];
                $line[$object_name]['label'] = $obj['value'];

            }
            elseif( $obj['type'] == "uri") {
                $line[$object_name]['value'] = $obj['value'];
                $line[$object_name]['label'] = $label = $this->findLabels($SourceID, $obj['value']);
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

    public function dynamicAction() {
        $sources = $this->sourceRepository->findAll();
        $this->view->assign("sources", $sources);
    }

    public function testSomethingAction() {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        //$datapoint = $this->datapointRepository->findByIdentifier(11);
        //$this->view->assign("debug9", $this->languagepointRepository->fetchCorresponding($datapoint));
        //$this->view->assign("debug7", $datapoint);
        //$this->languagepointRepository->deleteCorresponding($datapoint);
        //$this->view->assign("debug1", $this->updateDatapointLanguagepoints($datapoint));
        //$datapoint = $this->datapointRepository->findByIdentifier(9);
        //$this->view->assign("debug2", $this->updateDatapointLanguagepoints($datapoint));
        //$this->view->assign("majorDebug", $this->explorativeQuery("https://data.finc.info/sparql", "https://data.finc.info/resource/organisation/DE-15"));
        $this->view->assign("collection", $this->collectionRepository->findAll());
        //apparently language is finicky
        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9004000) {
            $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
            $this->view->assign("sysLanguageUid", $languageAspect->getId());
        } else {
            $this->view->assign("sysLanguageUid", $GLOBALS['TSFE']->sys_language_uid);
        }

        $ce = $this->collectionEntryRepository->findByUid(13);
        $stuff = ["collection" => $ce, "Col" => $ce->getGridColumn(), "row" => $ce->getGridColumn()];
        $this->view->assign("ce", $stuff);
        $entry = $this->collectionEntryRepository->findByUid(15);
        echo("Test 1 - wrong Area:".intval($entry->setGridArea("23 / 23/ 23 / kl"))."<br>");
        echo("Test 2 - right Area but weird: ".intval($entry->setGridArea("1   / 211/2 /   2")));
        $this->view->assign("entry", $entry);
        $word = "https://LOD.source.de/DNBV-1248";
        $this->view->assign("test-12", $word);
        $this->arrowBrackets($word);
        $this->view->assign("test-13", $word);
        //update Mode 2
        $dp = $this->datapointRepository->findByUid(31);
        $this->view->assign("queryTest", $this->updateLanguagepointsMode2($dp));

    }

    public function ajaxCallTestAction(Collection $collection) {
        $this->view->assign("collection", $collection);
    }

    /**
     * Puts the word in arrow brackets if it isnt already, works by reference
     *
     * @param &$word
     * @return string
     */
    private function arrowBrackets(&$word) {
        if( preg_match("^<.*>$^", $word) === 0) {
            $word = "<" . $word . ">";
        }
        return $word; //totally unnecessary but doesnt hurt
    }

    #TODO: delete this backport
    public function backportAction()
    {
        try {
            $ce_old = $resultSet = $this->collectionEntryRepository->findAll();
            $ce = [];
            foreach($ce_old as $entry) {
                $myArray = [
                    'CollectionId' => $entry->getCollectionID(),
                    'DatapointId' => $entry->getDatapointId(),
                    'Style_name' => $entry->getStyleName(),
                    'style' => $entry->getStyle(),
                    'name' => $entry->getName(),
                    'crdate' => $entry->getCrDate(),
                    'position' => $entry->getPosition(),
                    'gridArea' => $entry->getGridArea()
                ];
                $ce[] = $entry->convertToArray();
            }
        }
        catch(\Exception $e) {
            $error = array();
            $error['message'] = $e->getMessage();
            $error['code'] = $e->getCode();
            $this->view->assign("trycatch", $error);
        }
        $this->view->assign("thisVar", $ce);
        $this->view->assign("other", $this->collectionEntryRepository->fetchCorresponding($this->collectionRepository->findByIdentifier(2)));
        //$this->redirect("display", "Front", Null, array("choosenCollection" => 2));
    }
}