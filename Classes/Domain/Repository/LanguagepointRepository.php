<?php
namespace Ubl\SparqlToucan\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Ubl\SparqlToucan\Domain\Model\Datapoint;

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

    public function fetchCorresponding(Datapoint $datapoint) {
        $query = $this->createQuery();
        $query->matching($query->equals('datapoint_id', $datapoint));
        return $query->execute();
    }

    public function deleteCorresponding(Datapoint $datapoint) {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $points = $this->fetchCorresponding($datapoint);
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
    public function fetchSpecificLanguage(Datapoint $datapoint, string $language, $defaultLanguage = "en") {
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
}
