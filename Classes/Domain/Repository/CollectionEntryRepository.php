<?php
namespace Ubl\SparqlToucan\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
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
 * The repository for CollectionEntries
 */
class CollectionEntryRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    //copy pasted function, ignoring PAGEID cause it makes no sense for  this plugin, i sincerly hope this is no
    //kind of noob trap i am getting into

    public function ignorePID()
    {
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns all entries to a corresponding collection
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     */
    public function fetchCorresponding(Collection $collection) {

        $query = $this->createQuery();
        $query->matching($query->equals('collection_i_d', $collection));
        return $query->execute();
    }

    public function deleteCorresponding(Collection $collection) {
        $persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $entries = $this->fetchCorresponding($collection);
        foreach($entries as $entry) {
            $this->remove($entry);

        }
        $persistenceManager->persistAll();
    }
}
