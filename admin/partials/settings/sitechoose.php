
<legend class="screen-reader-text"><span><?php _e('Choose your preferred site', $this->plugin_name); ?></span></legend>
<i class="hidden dashicons dashicons-update spin" id="showSpinner"></i>
<select class="regular-text" id="<?php echo $this->plugin_name; ?>-general-site" name="<?php echo $this->plugin_name; ?>-general[site]">
    <option value="">Select a site</option>
</select>

<script>

var site = "<?php echo($options['site']) ?>";
var access_token = "<?php echo($token['access_token']) ?>";

(function( $ ) {
    'use strict';

    if(access_token){
        gapi.load('client', start);
    }

    function start(){
        $('#showSpinner').toggleClass('hidden');

        gapi.client.load('webmasters', 'v3')
            .then(function(){


                gapi.auth.setToken({access_token:access_token})

                gapi.client.webmasters.sites.list()
                    .then(function(response) {

                        response.result.siteEntry.sort(function(o1, o2) { return o1.siteUrl > o2.siteUrl ? 1 : o1.siteUrl < o2.siteUrl ? -1 : 0; });

                        $.each(response.result.siteEntry, function (i, item) {
                            $('#search-console-general-site').append($('<option>', { 
                                value: item.siteUrl,
                                text : item.siteUrl 
                            }));
                        });

                        $('#search-console-general-site option[value="' + site + '"]').attr('selected', 'selected');

                        $("#search-console-general-site").selectize({
                            placeholder: "Select a site",
                            create: true
                        });


                    })
                    .then(null, function(err) {
                      console.log(err);
                    });                
                $('#showSpinner').toggleClass('hidden');
            
        })  

    }

})( jQuery );
</script>