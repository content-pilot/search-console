<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.francescopepe.com
 * @since      1.0.0
 *
 * @package    DashyLite 
 * @subpackage DashyLite/admin/partials
 */

//Grab all options
$options = $this->getOptionsAndRefreshToken();

// Cleanup
$site = $options['site'];

if(empty($options['site'])){
  echo('<h1>Go to settings to choose your site from Search Console</h1>');
  echo('<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '-settings">' . __('Settings', $this->plugin_name) . '</a>');
}
?>

<div id="search-console-widget">
  <div><i class="hidden dashicons dashicons-update spin" id="showSpinner"></i></div>
  <select id="searchconsole-sel-period">
    <option value="14" selected="selected">Last 14 days</option>
    <option value="30">Last 30 days</option>
    <option value="60">Last 60 days</option>
  </select>

  <div id="gsc-chart"></div>
  <div>
    report generated by <a href="https://www.francescopepe.com">TropicalSeo</a>
  </div>

</div>

<?php
if(!empty($options['site'])){
?>

<script type="text/javascript">

// global variables
var access_token = "<?php echo($options['token']['access_token']) ?>";
var site = "<?php echo($site) ?>";
var data,chart;

var period = jQuery('select[id=searchconsole-sel-period]').val();

jQuery('select[id=searchconsole-sel-period]').change(function(){
  period= jQuery(this).val();
  changePeriod()
  getReport();
});

var chartQuery = {
              'siteUrl': site,
              'rowLimit': null,
              'searchType': 'web',
              'startDate': moment().subtract(period, 'days').format('YYYY-MM-DD'),
              'endDate': moment().format('YYYY-MM-DD'),
              'dimensions': ['date']
          }

function changePeriod(){
  chartQuery.startDate = moment().subtract(period, 'days').format('YYYY-MM-DD');
}

;(function( $ ) {
    'use strict';

    if(access_token){
        gapi.load('client', start);
    }

    function start(){

        $('#showSpinner').toggleClass('hidden');

        gapi.client.load('webmasters', 'v3')
            .then(function(){

                gapi.auth.setToken({access_token:access_token})

                getReport();
                $('#showSpinner').toggleClass('hidden');
            
        })  

    }

})( jQuery );
</script>
<style type="text/css">
.dashicons.spin {
   animation: dashicons-spin 1s infinite;
   animation-timing-function: linear;
}

@keyframes dashicons-spin {
   0% {
      transform: rotate( 0deg );
   }
   100% {
      transform: rotate( 360deg );
   }
}   
</style>

<?php
}
?>