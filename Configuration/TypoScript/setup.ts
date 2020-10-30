
plugin.{extension.shortExtensionKey}_{plugin.key} {
    view {
        templateRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_sparqltoucan_sparqlfront.view.templateRootPath}
        partialRootPaths.0 = EXT:sparql_toucan/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_sparqltoucan_sparqlfront.view.partialRootPath}
        layoutRootPaths.0 = EXT:sparql_toucan/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_sparqltoucan_sparqlfront.view.layoutRootPath}
    }
    persistence {
        storagePid = {$plugin.tx_sparqltoucan_sparqlfront.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
}

# these classes are only used in auto-generated templates
plugin.tx_sparqltoucan._CSS_DEFAULT_STYLE (
    textarea.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    input.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    .tx-sparql-toucan table {
        border-collapse:separate;
        border-spacing:10px;
    }

    .tx-sparql-toucan table th {
        font-weight:bold;
    }

    .tx-sparql-toucan table td {
        vertical-align:top;
    }

    .typo3-messages .message-error {
        color:red;
    }

    .typo3-messages .message-ok {
        color:green;
    }
)

# Module configuration
module.tx_sparqltoucan_web_sparqltoucansparqlbackend {
    persistence {
        storagePid = {$module.tx_sparqltoucan_sparqlbackend.persistence.storagePid}
    }
    view {
        templateRootPaths.0 = EXT:{extension.extensionKey}/Resources/Private/Backend/Templates/
        templateRootPaths.1 = {$module.tx_sparqltoucan_sparqlbackend.view.templateRootPath}
        partialRootPaths.0 = EXT:sparql_toucan/Resources/Private/Backend/Partials/
        partialRootPaths.1 = {$module.tx_sparqltoucan_sparqlbackend.view.partialRootPath}
        layoutRootPaths.0 = EXT:sparql_toucan/Resources/Private/Backend/Layouts/
        layoutRootPaths.1 = {$module.tx_sparqltoucan_sparqlbackend.view.layoutRootPath}
    }
}
