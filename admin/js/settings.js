var ajaxUrl = jQuery('main').attr('data-ajax');
var nonce = jQuery('main').attr('data-nonce');

function drop_all_tables() {
    var action = 'bubo_insights_drop_all_tables';
    jQuery.ajax( ajaxUrl, {
        method : "POST",
        dataType : "json",
        data : {action: action, nonce: nonce },
        success: function(response) {
            jQuery("#purge").html("PURGED!");
            alert(response);
			console.log(response);
        },
        error: function(response) {
            alert("Problems encountered, database has not been purged correctly");
			console.log(response);
        }
    });
}

jQuery(document).ready(function() {
    jQuery("#purge").on("click", function(e) {
        if(confirm("Did you backup?")) {
            if(confirm("Are you sure?\nThis can't be undone!")) {
                drop_all_tables();
            }
        }
    });
});