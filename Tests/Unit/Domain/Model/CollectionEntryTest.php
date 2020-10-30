<?php
namespace Ubl\SparqlToucan\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class CollectionEntryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Domain\Model\CollectionEntry
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Ubl\SparqlToucan\Domain\Model\CollectionEntry();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPositionReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getPosition()
        );
    }

    /**
     * @test
     */
    public function setPositionForIntSetsPosition()
    {
        $this->subject->setPosition(12);

        self::assertAttributeEquals(
            12,
            'position',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStyleReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getStyle()
        );
    }

    /**
     * @test
     */
    public function setStyleForIntSetsStyle()
    {
        $this->subject->setStyle(12);

        self::assertAttributeEquals(
            12,
            'style',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStyleNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getStyleName()
        );
    }

    /**
     * @test
     */
    public function setStyleNameForStringSetsStyleName()
    {
        $this->subject->setStyleName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'styleName',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDatapointIdReturnsInitialValueForDatapoint()
    {
        self::assertEquals(
            null,
            $this->subject->getDatapointId()
        );
    }

    /**
     * @test
     */
    public function setDatapointIdForDatapointSetsDatapointId()
    {
        $datapointIdFixture = new \Ubl\SparqlToucan\Domain\Model\Datapoint();
        $this->subject->setDatapointId($datapointIdFixture);

        self::assertAttributeEquals(
            $datapointIdFixture,
            'datapointId',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCollectionIDReturnsInitialValueForCollection()
    {
        self::assertEquals(
            null,
            $this->subject->getCollectionID()
        );
    }

    /**
     * @test
     */
    public function setCollectionIDForCollectionSetsCollectionID()
    {
        $collectionIDFixture = new \Ubl\SparqlToucan\Domain\Model\Collection();
        $this->subject->setCollectionID($collectionIDFixture);

        self::assertAttributeEquals(
            $collectionIDFixture,
            'collectionID',
            $this->subject
        );
    }
}
