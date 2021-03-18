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
    //the lenghts of my frankenstein constructions scares me a lot
    private const MAXGRIDNUMBER = 500;
    /**
     * datapointRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\DatapointRepository
     * @inject
     */
    protected $datapointRepository = null;
    /**
     * textpointRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\TextpointRepository
     * @inject
     */
    protected $textpointRepository = null;
    /**
     * collectionEntryRepository
     *
     * @var \Ubl\SparqlToucan\Domain\Repository\collectionEntryRepository
     * @inject
     */
    protected $collectionEntryRepository = null;

    /**
     * name
     *
     * @var string
     */

    protected $name = '';

    /**
     * gridArea
     *
     * @var string
     */
    protected $gridArea = '';

    /**
     * position
     *
     * @var int
     */
    protected $position = 1;

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
     * crdate
     *
     * @var int
     */
    protected $crdate = 0;

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
     * the branch entry, this only goes one level, not further
     *
     * @var \Ubl\SparqlToucan\Domain\Model\CollectionEntry
     */
    protected $parentEntry = 0;

    /**
     * isBranch
     *
     * @var boolean
     */
    protected $isBranch = false;

    /**
     * the branch entry, this only goes one level, not further
     *
     * @var \Ubl\SparqlToucan\Domain\Model\Textpoint
     */
    protected $textpoint = null;

    //fake entries without database representation
    /**
     * gridColumn
     *
     * @var int
     */
    protected $gridColumn;
    /**
     * gridRow
     *
     * @var int
     */
    protected $gridRow;
    /**
     * gridColumnEnd
     *
     * @var int
     */
    protected $gridColumnEnd;
    /**
     * gridRowEnd
     *
     * @var int
     */
    protected $gridRowEnd;

    public function getCrdate(): int
    {
        return $this->crdate;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getGridArea(): string
    {
        return $this->gridArea;
    }

    /**
     * @param $cssGridArea
     * @return bool
     */
    public function setGridArea($cssGridArea) : bool
    {
        if( preg_match('/^[0-9]*\s*\/\s*[0-9]*\s*\/\s*[0-9]*\s*\/\s*[0-9]*$/', trim($cssGridArea)) ) {
            //makes sure we get a somewhat clean grid-area format
            $parts = explode("/", $cssGridArea);
            $this->gridArea = trim($parts[0]) ." / ". trim($parts[1]) ." / ". trim($parts[2]) ." / ". trim($parts[3]);
            return true;
        }
        elseif( trim($cssGridArea) == "" ) {
            $this->gridArea = "";
            return true;
        }
        return false;
    }

    public function getGridColumn(): int
    {
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
            return intval($parts[1]);
        }
        else { return 0;}
    }

    public function setGridColumn($column): bool
    {
        $insert = intval($column);
        if( $insert == 0 ) { return false; } //luckily 0 isnt a viable grid position anyway
        if( $insert >= $this->MAXGRIDNUMBER ) { return false; } //magic number sanity check
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
        }
        else {
            $parts = [0 => 1, 1 => 1, 2 =>  2, 3 =>  2]; //default grid
        }
        $this->gridArea = "{$parts[0]} / {$insert} / {$parts[2]} / {$parts[3]}";
        return true;
    }

    public function getGridRow(): int
    {   // '1 / 1 / 2 / 2'
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
            return intval($parts[0]);
        }
        else { return 0;}
    }

    public function setGridRow($row)
    {
        $insert = intval($row);
        if( $insert == 0 ) { return false; } //luckily 0 isnt a viable grid position anyway
        if( $insert >= $this->MAXGRIDNUMBER ) { return false; } //magic number sanity check
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
        }
        else {
            $parts = [0 => 1, 1 => 1, 2 =>  2, 3 =>  2]; //default grid
        }
        $this->gridArea = "{$insert} / {$parts[1]} / {$parts[2]} / {$parts[3]}";
        return true;
    }

    public function getGridRowEnd(): int
    {   // '1 / 1 / 2 / 2'
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
            return intval($parts[2]);
        }
        else { return 0;}
    }

    public function setGridRowEnd($row)
    {
        $insert = intval($row);
        if( $insert == 0 ) { return false; } //luckily 0 isnt a viable grid position anyway
        if( $insert >= $this->MAXGRIDNUMBER ) { return false; } //magic number sanity check
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
        }
        else {
            $parts = [0 => 1, 1 => 1, 2 =>  2, 3 =>  2]; //default grid
        }
        $this->gridArea = "{$parts[0]} / {$parts[1]} / {$insert} / {$parts[3]}";
        return true;
    }

    public function getGridColumnEnd(): int
    {
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
            return intval($parts[3]);
        }
        else { return 0;}
    }

    public function setGridColumnEnd($column)
    {
        $insert = intval($column);
        if( $insert == 0 ) { return false; } //luckily 0 isnt a viable grid position anyway
        if( $insert >= $this->MAXGRIDNUMBER ) { return false; } //magic number sanity check
        if( trim($this->gridArea) != "" ) {
            $parts = explode("/", $this->gridArea);
        }
        else {
            $parts = [0 => 1, 1 => 1, 2 =>  2, 3 =>  2]; //default grid
        }
        $this->gridArea = "{$parts[0]} / {$parts[1]} / {$parts[2]} / {$insert}";
        return true;
    }

    public function getStyle()
    {
        return $this->style;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function getStyleName(): string
    {
        return $this->styleName;
    }

    public function setStyleName($style_name)
    {
        $this->styleName = $style_name;
    }

    public function getTempValue(): string
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
    public function getDatapointId(): ?Datapoint
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

    public function unsetDatapoint() {
        $this->datapointId = 0;
    }

    /**
     * Returns the collectionID
     *
     * @return \Ubl\SparqlToucan\Domain\Model\Collection $collectionID
     */
    public function getCollectionID(): ?Collection
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
     * Returns the branching entry if there is any
     *
     * @return \Ubl\SparqlToucan\Domain\Model\CollectionEntry $parentEntry
     */
    public function getParentEntry()
    {
        return $this->parentEntry;
    }

    /**
     * Sets the parent Entry if its a branch
     *
     * @param \Ubl\SparqlToucan\Domain\Model\CollectionEntry
     * @return boolean
     */
    public function setParentEntry(\Ubl\SparqlToucan\Domain\Model\CollectionEntry $branchEntry): bool
    {
        if( $branchEntry->getIsBranch() === true ) {
            $this->parentEntry = $branchEntry;
            return true;
        } else {
            return false;
        }

    }

    public function unsetParentEntry() {
        $this->parentEntry = 0;
    }

    /**
     * Returns the branching entry if there is any
     *
     * @return \Ubl\SparqlToucan\Domain\Model\Textpoint $textpoint
     */
    public function getTextpoint()
    {
        return $this->textpoint;
    }

    /**
     * Sets the parent Entry if its a branch
     *
     * @param \Ubl\SparqlToucan\Domain\Model\Textpoint
     * @return void
     */
    public function setTextpoint(\Ubl\SparqlToucan\Domain\Model\Textpoint $textpoint): void
    {
       $this->textpoint = $textpoint;
    }

    public function unsetTextpoint() {
        $this->textpoint = 0;
    }


    /**
     * gets the Status of the entry, if its a branch or not
     *
     * @return bool
     */
    public function getIsBranch(): bool
    {
        return $this->isBranch;
    }

    /**
     *
     *
     * @param bool $state
     * @return void
     */
    public function setIsBranch($state)
    {
        $this->isBranch = $state;
    }

    /**
     *
     * @return array
     */
    public function convertToArray() {
        return [
            'CollectionId' => $this->getCollectionID(),
            'DatapointId' => $this->getDatapointId(),
            'StyleName' => $this->getStyleName(),
            'style' => $this->getStyle(),
            'name' => $this->getName(),
            'crdate' => $this->getCrdate(),
            'position' => $this->getPosition(),
            'gridArea' => $this->getGridArea()
        ];
    }

    /**
     * setLink - Wrapper
     *
     * This shortens the use of setDatapoint, setTextpoint and setParentEntry to one function
     * mostly cause it now can be used in a loop. I am, the creator of this, feel really bad about this. To achieve
     * what i wanted i needed to include repositories in the description of this class, which feels quite hacky to
     * be honest. I am not sure sure if this is the way.
     *
     * @param $linkName
     * @param $value
     * @return bool
     */
    public function setLink($linkName, $value) {
        Switch( strtolower($linkName) ) {
            case 'datapoint':
            case 'datapointid':
                if( !$value instanceof Datapoint && is_int($value)) {
                    $value = $this->datapointRepository->findByUid($value);
                }
                $this->setDatapointId($value); break;
            case 'textpoint':
                if( !$value instanceof Textpoint && is_int($value)) {
                    $value = $this->textpointRepository->findByUid($value);
                }
                $this->setTextpoint($value); break;
            case 'parentbranch':
                if( !$value instanceof CollectionEntry && is_int($value)) {
                    $value = $this->collectionEntryRepository->findByUid($value);
                }
                $this->setParentEntry($value); break;
            default:
                return false;
        }
        return true;
    }

    public function unsetLink($linkName) {
        Switch( strtolower($linkName) ) {
            case 'datapoint':
            case 'datapointid':
                $this->unsetDatapoint(); break;
            case 'textpoint':
                $this->unsetTextpoint(); break;
            case 'parentbranch':
                $this->unsetParentEntry(); break;
            default:
                return false;
        }
        return true;
    }
}
