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
 * Contains labels for Linked Data objects
 */
class LabelcacheRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    //copy pasted function, ignoring PAGEID cause it makes no sense for  this plugin, i sincerly hope this is no
    //kind of noob trap i am getting into
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    public function fetchLabel(\Ubl\SparqlToucan\Domain\Model\Source $sourceId, $uri, $language="en") {
        $fallback_language = "en";
        $query = $this->createQuery();//
        $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('source_id', $sourceId),
                    $query->equals('subject', $uri)
                ]
            )
        );

        $labels = $query->execute()->toArray();
        foreach( $labels as $label) {
            if( $label['language'] == $fallback_language ) {
                $fallback = $label;
            }
            if( $label['language'] == $language) {
                return $label;
            }
        }
        if ( isset($fallback) ) {
            //TODO: note when fallback was used
            return $fallback;
        }

        throw new \Exception("No Entry in the database could be found that matches the search parameters",1);
    }
}
