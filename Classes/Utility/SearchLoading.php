<?php


namespace Ubl\SparqlToucan\Utility;


class SearchLoading
{
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