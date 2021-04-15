<?php
namespace Ubl\SparqlToucan\Domain\Repository;

use Ubl\SparqlToucan\Domain\Model\Collection;

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
 * The repository for Collections
 */
class CollectionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * collectionEntryRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository
     * @inject
     */
    protected $collectionEntryRepository = null;
    /**
     * languagepointRepository
     *
     * @var Ubl\SparqlToucan\Domain\Repository\LanguagepointRepository
     * @inject
     */
    protected $languagepointRepository = null;

    //copy pasted function, ignoring PAGEID cause it makes no sense for  this plugin, i sincerly hope this is no
    //kind of noob trap i am getting into
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Deletes the collection and all corresponding collection entry to make sure there are no orphanend entries
     * @param Collection $collection
     */
    public function saveDelete(Collection $collection) {
        $this->collectionEntryRepository->deleteCorresponding($collection);
        $this->remove($collection);
    }

    /**
     * Takes all corresponding entries of a collection and returns an array of entries (in array form) sorted by
     * grid coordinates (if they actually exists). Returns the entries in the language specified, if that is not found
     * it will be english and if that is not avaible it is any language that is first in the list when queried
     * @param Collection $collection a collection object
     * @param $language language code like 'en', 'fr' or 'se'
     * @return array a sorted list of entries by x1->x999, y1->y999
     */
    public function toEntryMix(Collection $collection, $language) {
        $entries = $this->collectionEntryRepository->fetchCorresponding($collection);
        $entryArray = [];
        $twigs = [];
        foreach($entries as $entry ) {
            if( !$entry->getIsBranch() ) {
                if ($entry->getTextpoint() != null && $entry->getDatapointId() != null) {
                    $entry->setTempValue(
                        $this->languagepointRepository->fetchSpecificLanguage($entry->getTextpoint(), $language)
                        .
                        $this->languagepointRepository->fetchSpecificLanguage($entry->getDatapointId(), $language)
                    );
                } elseif ($entry->getDatapointId() != null && $entry->getTextpoint() == null) {
                    $entry->setTempValue($this->languagepointRepository->fetchSpecificLanguage($entry->getDatapointId(), $language));
                } elseif ($entry->getTextpoint() != null && $entry->getDatapointId() == null) {
                    $entry->setTempValue($this->languagepointRepository->fetchSpecificLanguage($entry->getTextpoint(), $language));
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

        return $entryArray;
    }

}
