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
 * The repository for Datapoints
 */
class DatapointRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    //copy pasted function, ignoring PAGEID cause it makes no sense for  this plugin, i sincerly hope this is no
    //kind of noob trap i am getting into
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns all entries that use this source
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return \Ubul\SparqlToucan\Domain\Model\Datapoint $datapoint
     */
    public function findSourceUsage($source) {

        $query = $this->createQuery();
        $query->matching($query->equals('source_id', $source));
        return $query->execute();
    }
}
