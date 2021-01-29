console.log("Big Script Block here");
jQuery(document).ready( function () {
    console.log("First Stage");
    let form = jQuery('#ajaxselectlist-form');
    let selectForm = jQuery('.ajaxFormOption');
    let resultContainer = jQuery('#ajaxCallResult');
    let service = {
        ajaxCallTest: function (data) {
            jQuery.ajax({
                url: 'index.php',
                cache: false,
                data: data.serialize(),
                success: function (result) {
                    resultContainer.html(result).fadeIn('fast');
                },
                error: function (jqXHR, textStatus, errorThrow) {
                    resultContainer.html('Ajax request - ' + textStatus + ': ' + errorThrow).fadeIn('fast');
                }
            });
        }
    };
    form.submit(function (ev) {
        console.log(jQuery(this));
        ev.preventDefault();
        service.ajaxCallTest(jQuery(this));
    });
    selectForm.on('change', function () {
        resultContainer.fadeOut('fast');

        console.log("test");
        form.submit();
    });
    selectForm.trigger('change');
});