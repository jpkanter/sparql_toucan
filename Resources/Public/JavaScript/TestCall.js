var REVISION = 9;
jQuery(document).ready(function(){
    jQuery('#version_number').text("Revision "+REVISION);

    let activateButton = jQuery('#dynamicButton');
    var contentArea = jQuery('#testArea');
    var thisForm = jQuery('#explorerForm');
    let formSelect = jQuery('#sourceSelect');

    activateButton.on('click', function() {
        SparqlQuery(contentArea,
            jQuery('#sourceUri option:selected').text(),
            jQuery('#form_subject').val(),
            jQuery('#form_predicate').val());
    });

    formSelect.on('change', function() {
        let text = jQuery('#randoText');
        let currentValue = jQuery('#sourceSelect option:selected').val();
        jQuery('#sourceUri option[value='+currentValue+']').attr('selected', 'selected');
        text.text(jQuery('#sourceUri option:selected').text());
        //this solution is as low tech as it gets
    })

})

function EventHandler1(someText = "default") {
    console.log(someText);
}

function SparqlQuery(drawTarget, endpointURL, subject, predicate = "") {
    if( predicate === "" ) {
        predicate = "?pre";
    }
    else {
        predicate = "<" + predicate + ">";
    }
    myQuery = {
        'format': 'json',
        'query': 'SELECT * WHERE { <'+subject+'> <'+predicate+'> ?obj }'
    }
    jQuery.ajax({
        url: endpointURL,
        cache: false,
        data: myQuery,
        success: function(result) {
            drawTarget.html("<pre>"+result+"</pre>")
        },
        error: function( jqXHR, textStatus, errorThrow) {
            console.log('Ajax request - ' + textStatus + ': ' + errorThrow);
            // throw some exceptions por favor
        }
    });
}