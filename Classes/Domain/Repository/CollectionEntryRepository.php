<?php
namespace Ubl\SparqlToucan\Domain\Repository;

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
     * @return \Ubul\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     */
    public function fetchCorresponding($collectionId) {

        $query = $this->createQuery();
        $query->matching($query->equals('collection_i_d', $collectionId));
        return $query->execute();
    }
}
