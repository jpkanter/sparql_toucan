<?php
namespace Ubl\SparqlToucan\Domain\Model;

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
 * A collection of entries that reference datapoints to display the data, singular
 * point of entry for display
 */
class Collection extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * layout
     *
     * @var int
     */
    protected $layout = 0;

    /**
     * styleOverride
     *
     * @var string
     */
    protected $styleOverride = '';

    /**
     * Collection constructor.
     * @param string $name
     * @param int $layout
     * @param string $style_override
     */
    public function __construct($name = "", $layout = 1, $style_override = "") {
        $this->name = $name;
        $this->layout = $layout;
        $this->styleOverride = $style_override;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the layout
     *
     * @return int $layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets the layout
     *
     * @param int $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Returns the styleOverride
     *
     * @return string $styleOverride
     */
    public function getStyleOverride()
    {
        return $this->styleOverride;
    }

    /**
     * Sets the styleOverride
     *
     * @param string $styleOverride
     * @return void
     */
    public function setStyleOverride($styleOverride)
    {
        $this->styleOverride = $styleOverride;
    }
}
