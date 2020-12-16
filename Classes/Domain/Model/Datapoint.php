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
 * Datapoint
 */
class Datapoint extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * cachedValue
     *
     * @var string
     */
    protected $cachedValue = '';

    /**
     * subject
     *
     * @var string
     */
    protected $subject = '';

    /**
     * predicate
     *
     * @var string
     */
    protected $predicate = '';

    /**
     * mode
     *
     * @var int
     */
    protected $mode = 0;

    /**
     * autoUpdate
     *
     * @var bool
     */
    protected $autoUpdate = false;

    /**
     * @var \DateTime
     */
    protected $crdate = null;


    /**
     * Returns the creation date
     *
     * @return \DateTime $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }


    /**
     * Id of the source that is used for the datapoint
     *
     * @var \Ubl\SparqlToucan\Domain\Model\Source
     */
    protected $sourceId = null;

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
     * Returns the cachedValue
     *
     * @return string $cachedValue
     */
    public function getCachedValue()
    {
        return $this->cachedValue;
    }

    /**
     * Sets the cachedValue
     *
     * @param string $cachedValue
     * @return void
     */
    public function setCachedValue($cachedValue)
    {
        $this->cachedValue = $cachedValue;
    }

    /**
     * Returns the subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the subject
     *
     * @param string $subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the predicate
     *
     * @return string $predicate
     */
    public function getPredicate()
    {
        return $this->predicate;
    }

    /**
     * Sets the predicate
     *
     * @param string $predicate
     * @return void
     */
    public function setPredicate($predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * Returns the mode
     *
     * @return int $mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Sets the mode
     *
     * @param int $mode
     * @return void
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Returns the autoUpdate
     *
     * @return bool $autoUpdate
     */
    public function getAutoUpdate()
    {
        return $this->autoUpdate;
    }

    /**
     * Sets the autoUpdate
     *
     * @param bool $autoUpdate
     * @return void
     */
    public function setAutoUpdate($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
    }

    /**
     * Returns the boolean state of autoUpdate
     *
     * @return bool
     */
    public function isAutoUpdate()
    {
        return $this->autoUpdate;
    }

    /**
     * Returns the sourceId
     *
     * @return \Ubl\SparqlToucan\Domain\Model\Source $sourceId
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Sets the sourceId
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Source $sourceId
     * @return void
     */
    public function setSourceId(\Ubl\SparqlToucan\Domain\Model\Source $sourceId)
    {
        $this->sourceId = $sourceId;
    }
}
