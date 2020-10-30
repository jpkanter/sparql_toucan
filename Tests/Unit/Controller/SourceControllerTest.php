<?php
namespace Ubl\SparqlToucan\Tests\Unit\Controller;

/**
 * Test case.
 */
class SourceControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Controller\SourceController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Ubl\SparqlToucan\Controller\SourceController::class)
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
    public function listActionFetchesAllSourcesFromRepositoryAndAssignsThemToView()
    {

        $allSources = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sourceRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\SourceRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $sourceRepository->expects(self::once())->method('findAll')->will(self::returnValue($allSources));
        $this->inject($this->subject, 'sourceRepository', $sourceRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('sources', $allSources);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenSourceToView()
    {
        $source = new \Ubl\SparqlToucan\Domain\Model\Source();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('source', $source);

        $this->subject->showAction($source);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenSourceToSourceRepository()
    {
        $source = new \Ubl\SparqlToucan\Domain\Model\Source();

        $sourceRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\SourceRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $sourceRepository->expects(self::once())->method('add')->with($source);
        $this->inject($this->subject, 'sourceRepository', $sourceRepository);

        $this->subject->createAction($source);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenSourceToView()
    {
        $source = new \Ubl\SparqlToucan\Domain\Model\Source();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('source', $source);

        $this->subject->editAction($source);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenSourceInSourceRepository()
    {
        $source = new \Ubl\SparqlToucan\Domain\Model\Source();

        $sourceRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\SourceRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $sourceRepository->expects(self::once())->method('update')->with($source);
        $this->inject($this->subject, 'sourceRepository', $sourceRepository);

        $this->subject->updateAction($source);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenSourceFromSourceRepository()
    {
        $source = new \Ubl\SparqlToucan\Domain\Model\Source();

        $sourceRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\SourceRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $sourceRepository->expects(self::once())->method('remove')->with($source);
        $this->inject($this->subject, 'sourceRepository', $sourceRepository);

        $this->subject->deleteAction($source);
    }
}
