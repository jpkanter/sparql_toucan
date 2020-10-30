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
 * an entry that can only exist in one collection
 */
class CollectionEntry extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * position
     *
     * @var int
     */
    protected $position = 0;

    /**
     * style
     *
     * @var int
     */
    protected $style = 0;

    /**
     * styleName
     *
     * @var string
     */
    protected $styleName = '';

    /**
     * the referenced datapoint
     *
     * @var \Ubl\SparqlToucan\Domain\Model\Datapoint
     */
    protected $datapointId = null;

    /**
     * the collection this entry belongs to
     *
     * @var \Ubl\SparqlToucan\Domain\Model\Collection
     */
    protected $collectionID = null;

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
     * Returns the position
     *
     * @return int $position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position
     *
     * @param int $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Returns the style
     *
     * @return int $style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Sets the style
     *
     * @param int $style
     * @return void
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * Returns the styleName
     *
     * @return string $styleName
     */
    public function getStyleName()
    {
        return $this->styleName;
    }

    /**
     * Sets the styleName
     *
     * @param string $styleName
     * @return void
     */
    public function setStyleName($styleName)
    {
        $this->styleName = $styleName;
    }

    /**
     * Returns the datapointId
     *
     * @return \Ubl\SparqlToucan\Domain\Model\Datapoint $datapointId
     */
    public function getDatapointId()
    {
        return $this->datapointId;
    }

    /**
     * Sets the datapointId
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Datapoint $datapointId
     * @return void
     */
    public function setDatapointId(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapointId)
    {
        $this->datapointId = $datapointId;
    }

    /**
     * Returns the collectionID
     *
     * @return \Ubl\SparqlToucan\Domain\Model\Collection $collectionID
     */
    public function getCollectionID()
    {
        return $this->collectionID;
    }

    /**
     * Sets the collectionID
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Collection $collectionID
     * @return void
     */
    public function setCollectionID(\Ubl\SparqlToucan\Domain\Model\Collection $collectionID)
    {
        $this->collectionID = $collectionID;
    }
}
