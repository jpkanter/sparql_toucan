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
}
