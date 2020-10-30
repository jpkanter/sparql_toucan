<?php
namespace Ubl\SparqlToucan\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class DatapointTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Domain\Model\Datapoint
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Ubl\SparqlToucan\Domain\Model\Datapoint();
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
    public function getCachedValueReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCachedValue()
        );
    }

    /**
     * @test
     */
    public function setCachedValueForStringSetsCachedValue()
    {
        $this->subject->setCachedValue('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'cachedValue',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSubjectReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSubject()
        );
    }

    /**
     * @test
     */
    public function setSubjectForStringSetsSubject()
    {
        $this->subject->setSubject('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'subject',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPredicateReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPredicate()
        );
    }

    /**
     * @test
     */
    public function setPredicateForStringSetsPredicate()
    {
        $this->subject->setPredicate('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'predicate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getModeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getMode()
        );
    }

    /**
     * @test
     */
    public function setModeForIntSetsMode()
    {
        $this->subject->setMode(12);

        self::assertAttributeEquals(
            12,
            'mode',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAutoUpdateReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getAutoUpdate()
        );
    }

    /**
     * @test
     */
    public function setAutoUpdateForBoolSetsAutoUpdate()
    {
        $this->subject->setAutoUpdate(true);

        self::assertAttributeEquals(
            true,
            'autoUpdate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSourceIdReturnsInitialValueForSource()
    {
        self::assertEquals(
            null,
            $this->subject->getSourceId()
        );
    }

    /**
     * @test
     */
    public function setSourceIdForSourceSetsSourceId()
    {
        $sourceIdFixture = new \Ubl\SparqlToucan\Domain\Model\Source();
        $this->subject->setSourceId($sourceIdFixture);

        self::assertAttributeEquals(
            $sourceIdFixture,
            'sourceId',
            $this->subject
        );
    }
}
