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
 * Source
 */
class Textpoint extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * url
     *
     * @var string
     */
    protected $description = '';

    /** @var int */
    protected $crdate;

    /**
     * Returns the crdate
     *
     * @return int
     */
    public function getCrdate()
    {
        return $this->crdate;
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
     * Returns the description
     *
     * @return string $url
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $url
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
