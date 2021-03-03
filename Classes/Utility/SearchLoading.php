<?php


namespace Ubl\SparqlToucan\Utility;


class SearchLoading
{
    //TODO: writer a proper select field here
    function customfield($PA, $fobj) {
        return '<input
     name="'.$PA['itemFormElName'].'"
     value="'.htmlspecialchars(($PA['itemFormElValue'] ?
                $PA['itemFormElValue']:'Default value')).'"
     onchange="'.htmlspecialchars(implode('',$PA['fieldChangeFunc'])).'"
     '.$PA['onFocus'].'
     />';
    }
}