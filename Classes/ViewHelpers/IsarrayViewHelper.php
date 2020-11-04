<?php
namespace Ubl\SparqlToucan\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

//copy my world from stack overflow https://stackoverflow.com/questions/38435066/check-if-variable-is-of-type-array-in-fluid
//i confess here and now that at this state i have little to no knowledge about viewhelpers
class IsarrayViewHelper extends AbstractViewHelper
{
    /**
    * Arguments Initialization
    */
    public function initializeArguments()
    {
        $this->registerArgument('var', 'string', 'test', TRUE);
    }

    /**
    * @return integer test
    */
    public function render()
    {

        $arg      = $this->arguments['var'];

        $argType  = gettype($arg);
        if( is_array($this->arguments['var']) == True ) {
            return 1;    //match
        } else {
            return 0;    //No match
        }
    }
}