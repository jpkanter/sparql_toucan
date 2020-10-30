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
 * SourceController
 */
class SourceController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * sourceRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\SourceRepository
     * @inject
     */
    protected $sourceRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $sources = $this->sourceRepository->findAll();
        $this->view->assign('sources', $sources);
    }

    /**
     * action show
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return void
     */
    public function showAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->view->assign('source', $source);
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
     * @param \Ubl\SparqlToucan\Domain\Model\Source $newSource
     * @return void
     */
    public function createAction(\Ubl\SparqlToucan\Domain\Model\Source $newSource)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->sourceRepository->add($newSource);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @ignorevalidation $source
     * @return void
     */
    public function editAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->view->assign('source', $source);
    }

    /**
     * action update
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return void
     */
    public function updateAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->sourceRepository->update($source);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $source
     * @return void
     */
    public function deleteAction(\Ubl\SparqlToucan\Domain\Model\Source $source)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->sourceRepository->remove($source);
        $this->redirect('list');
    }
}
