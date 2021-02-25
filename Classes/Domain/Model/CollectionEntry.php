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
     * grid_column
     *
     * @var int
     */
    protected $gridColumn = 1; //CSS Grids start at 1

    /**
     * grid_row
     *
     * @var int
     */
    protected $gridRow = 1; //CSS Grids start at 1
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
     * tempValue
     *
     * @var string
     */
    protected $tempValue = '';

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
     * Returns the gridColumn
     *
     * @return int $grid_column
     */
    public function getGridColumn()
    {
        return $this->gridColumn;
    }

    /**
     * Sets the gridColumn
     *
     * @param int $column
     * @return void
     */
    public function setGridColumn($column)
    {
        $this->gridColumn = $column;
    }

    /**
     * Returns the gridRow
     *
     * @return int $grid_row
     */
    public function getGridRow()
    {
        return $this->gridRow;
    }

    /**
     * Sets the gridRow
     *
     * @param int $row
     * @return void
     */
    public function setGridRow($row)
    {
        $this->grid_row = $row;
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
     * Returns the style_name
     *
     * @return string $styleName
     */
    public function getStyleName()
    {
        return $this->styleName;
    }

    /**
     * Sets the style_name
     *
     * @param string $style_name
     * @return void
     */
    public function setStyleName($style_name)
    {
        $this->styleName = $style_name;
    }

    /**
     * Returns the tempValue
     *
     * @return string $tempValue
     */
    public function getTempValue()
    {
        return $this->tempValue;
    }

    /**
     * Sets the tempValue, a variable that doesnt exists in  the database and is here for display reasons
     * it also might be a quite ugly hack
     *
     * @param string $tempValue
     * @return void
     */
    public function setTempValue($tempValue)
    {
        $this->tempValue = $tempValue;
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

    /**
     *
     * @return array
     */
    public function convertToArray() {
        $myArray = [
            'CollectionId' => $this->getCollectionID(),
            'DatapointId' => $this->getDatapointId(),
            'Style_name' => $this->getStyle_name(),
            'style' => $this->getStyle(),
            'name' => $this->getName(),
            'crdate' => $this->getCrdate(),
            'position' => $this->getPosition()
        ];
        return $myArray;
    }
}
