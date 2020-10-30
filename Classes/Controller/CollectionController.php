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
 * CollectionController
 */
class CollectionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * collectionRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\CollectionRepository
     * @inject
     */
    protected $collectionRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $collections = $this->collectionRepository->findAll();
        $this->view->assign('collections', $collections);
    }

    /**
     * action show
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    public function showAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->view->assign('collection', $collection);
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
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $newCollection
     * @return void
     */
    public function createAction(\Ubl\SparqlToucan\Domain\Model\Collection $newCollection)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionRepository->add($newCollection);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @ignorevalidation $collection
     * @return void
     */
    public function editAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->view->assign('collection', $collection);
    }

    /**
     * action update
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    public function updateAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionRepository->update($collection);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collection
     * @return void
     */
    public function deleteAction(\Ubl\SparqlToucan\Domain\Model\Collection $collection)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->collectionRepository->remove($collection);
        $this->redirect('list');
    }
}
