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
 * Languagepoint, the actual data that is supposed to be in a datapoint but sorted by language
 */
class Languagepoint extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * content
     *
     * @var string
     */
    protected $content = '';

    /**
     * language
     *
     * @var string
     */
    protected $language = '';

    /**
     * Id of the source that is used for the datapoint
     *
     * @var \Ubl\SparqlToucan\Domain\Model\Datapoint
     */
    protected $datapointId = 0;

    /**
     * Id of the source that is used for the datapoint
     *
     * @var \Ubl\SparqlToucan\Domain\Model\Textpoint
     */
    protected $textpoint = 0;

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
     * Languagepoint constructor.
     * @param Datapoint|null $datapointId
     * @param string $content - actual label, preferably one lined
     * @param string $language - language of the label, follows std iso abb
     */
    public function __construct(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapointId = NULL ,$content="", $language="en") {
        $this->datapointId = $datapointId;
        $this->content = $content;
        $this->language = $language;
    }
    /**
     * Returns the content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the content
     *
     * @param string $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the language
     *
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the language
     *
     * @param string $language
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
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
     * @param \Ubl\SparqlToucan\Domain\Model\datapoint $datapointId
     * @return void
     */
    public function setSourceId(\Ubl\SparqlToucan\Domain\Model\Datapoint $datapointId)
    {
        $this->datapointId = $datapointId;
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
}
