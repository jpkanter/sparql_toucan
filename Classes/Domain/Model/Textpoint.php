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
    /**
     * languages
     *
     * @var string
     */
    protected $languages = '';
    /**
     * templang
     * imaginary data in the structure that has no database representation
     *
     * @var array
     */
    protected $templang = [];

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
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Sets the languages
     * WORD OF WARNING, this is a statistic method to allow the front end to contain some more content for clarity
     * what it does is updating the languages field everytime a corresponding languagepoint is created, this checks if
     * the string is actually serialized and the correct kind, BUT unserialize bears some risk if ever exposed to the
     * user, so that should never happen
     *
     * @param string $languages
     * @return bool
     */
    public function setLanguages(string $languages):bool
    {
        if( !unserialize($languages)) { return false;}
        $this->languages = $languages;
        return true;
    }

    /**
     * adds a single language to the serizalized string, returns true if it actually happened
     *
     * @param string $language
     * @return bool
     */
    public function addLanguage(string $language):bool {
        $langs = unserialize($this->languages);
        foreach( $langs as $lan ) {
            if( $lan == trim($language) ) { return false;}
        }
        $langs[] = trim($language);
        $this->languages = serialize($langs);
        return true;
    }
    /**
     * Returns the description
     *
     * @return string $url
     */
    public function getTemplang()
    {
        return $this->templang;
    }

    /**
     * Sets the description
     *
     * @param string $url
     * @return void
     */
    public function setTemplang($templang)
    {
        $this->templang= $templang;
    }
}
