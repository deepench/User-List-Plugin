jQuery(document).ready(function () {
    
    // For get the userlist information
    function loadTable(page) {
        
        // Get the value form the dropdownlist
        var role = jQuery( '#ulp_role' ).val(); 
        var order = jQuery( '#ulp_order' ).val();
        var userorder = jQuery( '#ulp_userorder' ).val();

        jQuery.ajax({
            url: myAjax.ajaxurl,
            type: "POST",
            data: {
                action: 'load_table',
                page: page,
                ulp_role: role,
                ulp_order: order,
                ulp_userorder: userorder,
                security: myAjax.ulp_script_nonce
            },
            success: function ( data ) {
                jQuery( "#ulp_list_table" ).html( data );
            }
        });
    }
    loadTable(1);

    //Pagination Code
    jQuery(document).on("click", "#pagination a", function (e) {
        e.preventDefault();
        var page_id = jQuery(this).attr("id");
        loadTable(page_id);
    })

    // Submit the filter and ordering
    jQuery(document).on("click", "#submit", function (e) {
        e.preventDefault();
        loadTable(1);
    })


});

