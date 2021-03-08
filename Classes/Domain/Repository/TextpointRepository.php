<?php
namespace Ubl\SparqlToucan\Domain\Repository;

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
 * The repository for textpoints which are super simple cause they are only a link between two places
 */
class TextpointRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

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

    public function updateLanguages(Textpoint $textpoint) {
        $languages = $this->languagepointRepository->fetchCorresponding($textpoint);
        $langArray = [];
        //i am quite sure i could just serialize the toArray() result and be done with it
        foreach($languages as $lang) {
            $langArray[] = $lang->getLanguage();
        }
        $textpoint->setLanguages(serialize($langArray));
        $this->update($textpoint);
    }
}
