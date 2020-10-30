<?php
namespace Ubl\SparqlToucan\Tests\Unit\Controller;

/**
 * Test case.
 */
class DatapointControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Controller\DatapointController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Ubl\SparqlToucan\Controller\DatapointController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllDatapointsFromRepositoryAndAssignsThemToView()
    {

        $allDatapoints = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $datapointRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\DatapointRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $datapointRepository->expects(self::once())->method('findAll')->will(self::returnValue($allDatapoints));
        $this->inject($this->subject, 'datapointRepository', $datapointRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('datapoints', $allDatapoints);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenDatapointToView()
    {
        $datapoint = new \Ubl\SparqlToucan\Domain\Model\Datapoint();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('datapoint', $datapoint);

        $this->subject->showAction($datapoint);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenDatapointToDatapointRepository()
    {
        $datapoint = new \Ubl\SparqlToucan\Domain\Model\Datapoint();

        $datapointRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\DatapointRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $datapointRepository->expects(self::once())->method('add')->with($datapoint);
        $this->inject($this->subject, 'datapointRepository', $datapointRepository);

        $this->subject->createAction($datapoint);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenDatapointToView()
    {
        $datapoint = new \Ubl\SparqlToucan\Domain\Model\Datapoint();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('datapoint', $datapoint);

        $this->subject->editAction($datapoint);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenDatapointInDatapointRepository()
    {
        $datapoint = new \Ubl\SparqlToucan\Domain\Model\Datapoint();

        $datapointRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\DatapointRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $datapointRepository->expects(self::once())->method('update')->with($datapoint);
        $this->inject($this->subject, 'datapointRepository', $datapointRepository);

        $this->subject->updateAction($datapoint);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenDatapointFromDatapointRepository()
    {
        $datapoint = new \Ubl\SparqlToucan\Domain\Model\Datapoint();

        $datapointRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\DatapointRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $datapointRepository->expects(self::once())->method('remove')->with($datapoint);
        $this->inject($this->subject, 'datapointRepository', $datapointRepository);

        $this->subject->deleteAction($datapoint);
    }
}
