
plugin.tx_sparqltoucan_sparqlfront {
    view {
        # cat=plugin.tx_sparqltoucan_sparqlfront/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:sparql_toucan/Resources/Private/Templates/
        # cat=plugin.tx_sparqltoucan_sparqlfront/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:sparql_toucan/Resources/Private/Partials/
        # cat=plugin.tx_sparqltoucan_sparqlfront/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:sparql_toucan/Resources/Private/Layouts/
        # customsubcategory=css=CSS
        # cat=plugin.tx_sparqltoucan/css; type=string; label=CSS file
        CollectionEntrycssFile = EXT:sparql_toucan/Resources/Public/CSS/CollectionEntry.css
    }
    persistence {
        # cat=plugin.tx_sparqltoucan_sparqlfront//a; type=string; label=Default storage PID
        storagePid =
    }
}

module.tx_sparqltoucan_sparqlbackend {
    view {
        # cat=module.tx_sparqltoucan_sparqlbackend/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:sparql_toucan/Resources/Private/Backend/Templates/
        # cat=module.tx_sparqltoucan_sparqlbackend/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:sparql_toucan/Resources/Private/Backend/Partials/
        # cat=module.tx_sparqltoucan_sparqlbackend/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:sparql_toucan/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.tx_sparqltoucan_sparqlbackend//a; type=string; label=Default storage PID
        storagePid =
    }

    settings {
        useAjaxwithJQuery = 1
        typeNum = 674523
    }
}
