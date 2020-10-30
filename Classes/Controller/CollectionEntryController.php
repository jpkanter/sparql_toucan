<?php
namespace Ubl\SparqlToucan\Controller;

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
 * CollectionEntryController
 */
class CollectionEntryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * collectionEntryRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository
     * @inject
     */
    protected $collectionEntryRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $collectionEntries = $this->collectionEntryRepository->findAll();
        $this->view->assign('collectionEntries', $collectionEntries);
    }

    /**
     * action show
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     * @return void
     */
    public function showAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry)
    {
        $this->view->assign('collectionEntry', $collectionEntry);
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {

    }

    /**
     * action create
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $newCollectionEntry
     * @return void
     */
    public function createAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $newCollectionEntry)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionEntryRepository->add($newCollectionEntry);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     * @ignorevalidation $collectionEntry
     * @return void
     */
    public function editAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry)
    {
        $this->view->assign('collectionEntry', $collectionEntry);
    }

    /**
     * action update
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     * @return void
     */
    public function updateAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionEntryRepository->update($collectionEntry);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry
     * @return void
     */
    public function deleteAction(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $collectionEntry)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionEntryRepository->remove($collectionEntry);
        $this->redirect('list');
    }
}
