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
 * Labelcache
 */
class Labelcache extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * content
     *
     * @var string
     */
    protected $content = '';

    /**
     * subject
     *
     * @var string
     */
    protected $subject = '';


    /**
     * language
     *
     * @var string
     */
    protected $language = '';

    /**
     * status
     * 0 = normal entry
     * 1 = temporary entry created cause nothing could be found
     *
     * @var integer
     */
    protected $status = 0;

    /**
     * Returns the content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->Content;
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
        return $this->Language;
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
     * Returns the status
     *
     * @return string $subject
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
