<?php
namespace Ubl\SparqlToucan\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Ubl\SparqlToucan\Domain\Model\Datapoint;
use Ubl\SparqlToucan\Domain\Model\Languagepoint;
use Ubl\SparqlToucan\Domain\Model\Textpoint;

/***
 *
 * This file is part of the "Sparql Toucan" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020
 *
 ***/

/**
 * Cache for the actual data that is hold by datapoints sorted by language
 */
class LanguagepointRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    //copy pasted function, ignoring PAGEID cause it makes no sense for  this plugin, i sincerly hope this is no
    //kind of noob trap i am getting into
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    //fetch language point by language and datapoint
    //show available languages

    /**
     * i have choosen to make datapoints and textpoints to be avaible terminations for a languagepoint, therefore i have
     * to do some trickery so it works flawlessly, i am quite unsure if this is the way in overloading the method
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args) {
        if ( $method === 'fetchCorresponding' ) {
            if( count($args) === 1 && $args[0] instanceof Datapoint) {
                return $this->fetchCorrespondingDP($args[0]);
            }
            if( count($args) === 1 && $args[0] instanceof Textpoint) {
                return $this->fetchCorrespondingTP($args[0]);
            }
        }
        if ( $method === 'deleteCorresponding' ) {
            if( count($args) === 1 && $args[0] instanceof Datapoint) {
                $this->deleteCorrespondingDP($args[0]);
            }
            if( count($args) === 1 && $args[0] instanceof Textpoint) {
                $this->deleteCorrespondingTP($args[0]);
            }
        }
        if ( $method === 'fetchSpecificLanguage') {
            if( count($args) >= 2 && count($args) <= 3 && $args[0] instanceof Datapoint) {
                if( isset($args[2]) ) { $arg2 = $args[2]; } else { $arg2 = 'en'; }
                return $this->fetchSpecificLanguageDP($args[0], $args[1], $arg2);
            }
            if( count($args) >= 2 && count($args) <= 3 && $args[0] instanceof Textpoint) {
                if( isset($args[2]) ) { $arg2 = $args[2]; } else { $arg2 = 'en'; }
                return $this->fetchSpecificLanguageTP($args[0], $args[1]);
            }
        }
    }

    public function fetchCorrespondingDP(Datapoint $datapoint) {
        //findBy('datapoint_id', $datapoint)
        $query = $this->createQuery();
        $query->matching($query->equals('datapoint_id', $datapoint));
        //$query->statement("SELECT * from  	tx_sparqltoucan_domain_model_languagepoint  WHERE datapoint_id = ".$datapoint->getUid()." AND deleted = 0 AND hidden = 0");
        return $query->execute();
    }

    public function fetchCorrespondingTP(Textpoint $textpoint) {
        $query = $this->createQuery();
        $query->matching($query->equals('textpoint', $textpoint));
        return $query->execute();
    }

    public function deleteCorrespondingDP(Datapoint $datapoint) {
        if ( $datapoint->getUID() === 0 ) { #weird failsafe, but apparently this happened for reasons my testing
            return false;
        }
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $points = $this->fetchCorresponding($datapoint);
        foreach($points as $point) {
            $this->remove($point);
        }
        $persistenceManager->persistAll();
    }

    public function deleteCorrespondingTP(Textpoint $textpoint) {
        if ( $textpoint->getUID() === 0 ) {
            return false;
        }
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $points = $this->fetchCorresponding($textpoint);
        foreach($points as $point) {
            $this->remove($point);

        }
        $persistenceManager->persistAll();
    }

    /**
     * Searches for the closest languagepoint that can be found, if absolutly none exist the function will
     * throw an exception. Otherwise it will first return the desired language, then the fallback and if only something
     * is found it will return the last language it saw when circling through the found entries
     * NOTE: i wonder whether i should do three separate queries instead of one big one. Need to do benchmarks on that
     * @param Datapoint $datapoint the datapoint the function is looking for, it simply just needs the id
     * @param string $language the language, shortened to two characters naming
     * @param string $defaultLanguage the language that might be used if the desired language cannot be found
     * @return string returns the found label
     * @throws \Exception if no languagepoint can be found at all
     */
    public function fetchSpecificLanguageDP(Datapoint $datapoint, string $language, $defaultLanguage = "en") {
        $allLanguagePoints = $this->fetchCorresponding($datapoint);
        foreach( $allLanguagePoints as $languagePoint) {
            $lan = $languagePoint->getLanguage();
            if($lan  == $language ) {
                return $languagePoint->getContent();
            }
            if( $lan == $defaultLanguage ) {
                $fallback = $languagePoint->getContent();
                continue;
            }
            $anything = $languagePoint->getContent();
        }
        if( isset($fallback) ) {
            return $fallback;
        }
        if( isset($anything) ) {
            return $anything;
        }
        else {
            //what now? Do i throw an exception cause there is an error? Or do i just return nothing cause there is nothing?
            throw new \Exception("No Languagepoint to that Datapoint could be found", 3);
        }
    }

    public function fetchSpecificLanguageTP(Textpoint $textpoint, string $language, $defaultLanguage = "en") {
        $allLanguagePoints = $this->fetchCorresponding($textpoint);
        foreach( $allLanguagePoints as $languagePoint) {
            $lan = $languagePoint->getLanguage();
            if($lan  == $language ) {
                return $languagePoint->getContent();
            }
            if( $lan == $defaultLanguage ) {
                $fallback = $languagePoint->getContent();
                continue;
            }
            $anything = $languagePoint->getContent();
        }
        if( isset($fallback) ) {
            return $fallback;
        }
        if( isset($anything) ) {
            return $anything;
        }
        else {
            //what now? Do i throw an exception cause there is an error? Or do i just return nothing cause there is nothing?
            throw new \Exception("No Languagepoint to that Textpoint could be found", 3);
        }
    }

    public function duplicationCheck(Textpoint $textpoint, $language, Languagepoint $exempt = Null) {
        $query = $this->createQuery();
        if( $exempt == Null ) // typically used when new Languagepoint is set
        {
            $query->matching(
                $query->logicalAnd([
                    $query->equals('textpoint', $textpoint),
                    $query->equals('language', $language)
                ])
            );
        }
        else
        { # in case of edit we want to make sure we dont find the same object we are currently modifying
            $query->matching(
                $query->logicalAnd([
                    $query->equals('textpoint', $textpoint),
                    $query->equals('language', $language),
                    $query->logicalNot($query->equals('uid', $exempt))
                ])
            );
        }
        if( $query->count() > 0 ) return true;
        else return false;
    }
}
