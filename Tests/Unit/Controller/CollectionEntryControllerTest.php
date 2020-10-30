<?php
namespace Ubl\SparqlToucan\Tests\Unit\Controller;

/**
 * Test case.
 */
class CollectionEntryControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Controller\CollectionEntryController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Ubl\SparqlToucan\Controller\CollectionEntryController::class)
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
    public function listActionFetchesAllCollectionEntriesFromRepositoryAndAssignsThemToView()
    {

        $allCollectionEntries = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collectionEntryRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionEntryRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCollectionEntries));
        $this->inject($this->subject, 'collectionEntryRepository', $collectionEntryRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('collectionEntries', $allCollectionEntries);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCollectionEntryToView()
    {
        $collectionEntry = new \Ubl\SparqlToucan\Domain\Model\CollectionEntry();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('collectionEntry', $collectionEntry);

        $this->subject->showAction($collectionEntry);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCollectionEntryToCollectionEntryRepository()
    {
        $collectionEntry = new \Ubl\SparqlToucan\Domain\Model\CollectionEntry();

        $collectionEntryRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $collectionEntryRepository->expects(self::once())->method('add')->with($collectionEntry);
        $this->inject($this->subject, 'collectionEntryRepository', $collectionEntryRepository);

        $this->subject->createAction($collectionEntry);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenCollectionEntryToView()
    {
        $collectionEntry = new \Ubl\SparqlToucan\Domain\Model\CollectionEntry();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('collectionEntry', $collectionEntry);

        $this->subject->editAction($collectionEntry);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenCollectionEntryInCollectionEntryRepository()
    {
        $collectionEntry = new \Ubl\SparqlToucan\Domain\Model\CollectionEntry();

        $collectionEntryRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $collectionEntryRepository->expects(self::once())->method('update')->with($collectionEntry);
        $this->inject($this->subject, 'collectionEntryRepository', $collectionEntryRepository);

        $this->subject->updateAction($collectionEntry);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenCollectionEntryFromCollectionEntryRepository()
    {
        $collectionEntry = new \Ubl\SparqlToucan\Domain\Model\CollectionEntry();

        $collectionEntryRepository = $this->getMockBuilder(\Ubl\SparqlToucan\Domain\Repository\CollectionEntryRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $collectionEntryRepository->expects(self::once())->method('remove')->with($collectionEntry);
        $this->inject($this->subject, 'collectionEntryRepository', $collectionEntryRepository);

        $this->subject->deleteAction($collectionEntry);
    }
}
