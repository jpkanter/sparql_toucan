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
 * DatapointController
 */
class DatapointController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * datapointRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\DatapointRepository
     * @inject
     */
    protected $datapointRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $datapoints = $this->datapointRepository->findAll();
        $this->view->assign('datapoints', $datapoints);
    }

    /**
     * action show
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint
     * @return void
     */
    public function showAction(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint)
    {
        $this->view->assign('datapoint', $datapoint);
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
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $newDatapoint
     * @return void
     */
    public function createAction(\Ubl\SparqlToucan\Domain\Model\Datapoint $newDatapoint)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->datapointRepository->add($newDatapoint);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint
     * @ignorevalidation $datapoint
     * @return void
     */
    public function editAction(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint)
    {
        $this->view->assign('datapoint', $datapoint);
    }

    /**
     * action update
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint
     * @return void
     */
    public function updateAction(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->datapointRepository->update($datapoint);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint
     * @return void
     */
    public function deleteAction(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapoint)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->datapointRepository->remove($datapoint);
        $this->redirect('list');
    }
}
