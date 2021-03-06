<?php
namespace Ubl\SparqlToucan\Tests\Unit\Controller;

/**
 * Test case.
 */
class CollectionControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Controller\CollectionController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Ubl\SparqlToucan\Controller\CollectionController::class)
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
    public function listActionFetchesAllCollectionsFromRepositoryAndAssignsThemToView()
    {

        $allCollections = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collectionRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCollections));
        $this->inject($this->subject, 'collectionRepository', $collectionRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('collections', $allCollections);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCollectionToView()
    {
        $collection = new \Ubl\SparqlToucan\Domain\Model\Collection();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('collection', $collection);

        $this->subject->showAction($collection);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCollectionToCollectionRepository()
    {
        $collection = new \Ubl\SparqlToucan\Domain\Model\Collection();

        $collectionRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $collectionRepository->expects(self::once())->method('add')->with($collection);
        $this->inject($this->subject, 'collectionRepository', $collectionRepository);

        $this->subject->createAction($collection);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenCollectionToView()
    {
        $collection = new \Ubl\SparqlToucan\Domain\Model\Collection();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('collection', $collection);

        $this->subject->editAction($collection);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenCollectionInCollectionRepository()
    {
        $collection = new \Ubl\SparqlToucan\Domain\Model\Collection();

        $collectionRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $collectionRepository->expects(self::once())->method('update')->with($collection);
        $this->inject($this->subject, 'collectionRepository', $collectionRepository);

        $this->subject->updateAction($collection);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenCollectionFromCollectionRepository()
    {
        $collection = new \Ubl\SparqlToucan\Domain\Model\Collection();

        $collectionRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $collectionRepository->expects(self::once())->method('remove')->with($collection);
        $this->inject($this->subject, 'collectionRepository', $collectionRepository);

        $this->subject->deleteAction($collection);
    }
}
