(function ($) {
    $(document).ready(function() {
        // enable or disable input fields for duration dates
        $("#scorm_timeopen_active").add("#scorm_timeclose_active").click(function() {
            var disabled;
            if($(this).attr("checked")) {
                disabled = false;
            } else {
                disabled = true;
            }
            $("select", $(this).parent()).attr("disabled", disabled);
        });
    });
}(jQuery));