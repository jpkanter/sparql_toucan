<?php
namespace Ubl\SparqlToucan\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class CollectionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Ubl\SparqlToucan\Domain\Model\Collection
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Ubl\SparqlToucan\Domain\Model\Collection();
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
    public function getLayoutReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getLayout()
        );
    }

    /**
     * @test
     */
    public function setLayoutForIntSetsLayout()
    {
        $this->subject->setLayout(12);

        self::assertAttributeEquals(
            12,
            'layout',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStyleOverrideReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getStyleOverride()
        );
    }

    /**
     * @test
     */
    public function setStyleOverrideForStringSetsStyleOverride()
    {
        $this->subject->setStyleOverride('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'styleOverride',
            $this->subject
        );
    }
}
