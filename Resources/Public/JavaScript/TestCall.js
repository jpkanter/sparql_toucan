var REVISION = 7;
jQuery(document).ready(function(){
    jQuery('#version_number').text("Revision "+REVISION);

    let activateButton = jQuery('#dynamicButton');
    var contentArea = jQuery('#testArea');
    var thisForm = jQuery('#explorerForm');
    let formSelect = jQuery('#sourceSelect');

    activateButton.on('click', function() {
        contentArea.html("<em>Things happened</em>");
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