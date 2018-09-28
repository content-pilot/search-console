var data,chart;
var period = jQuery('select[id=searchconsole-sel-period]').val();

jQuery('select[id=searchconsole-sel-period]').change(function(){
  period= jQuery(this).val();
  getReport();
});

google.charts.load('current', {'packages':['corechart']});

var chartOptions = {
  width: '100%',
  height: 400,
  focusTarget: 'category',
  chartArea: {
    // leave room for y-axis labels
    width: '100%'
  },
  hAxis: {
    showTextEvery: 4
  },
  vAxes: {
      0: {direction: -1, maxValue:1, textPosition: 'none'},
      1: {textPosition: 'none' },
      2: {textPosition: 'none' },
      3: {textPosition: 'none' }
  },
  series: {
      0: { type: "line", targetAxisIndex: 1, tooltip: true },
      1: { type: "line", targetAxisIndex: 2, tooltip: true },
      2: { type: "line", targetAxisIndex: 3, tooltip: true },
      3: { type: "line", targetAxisIndex: 0, tooltip: true },
      4: { type: "line", targetAxisIndex: 0, tooltip: true }
  }
}

function formatData(rows) {
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Keys');
    data.addColumn('number', 'Clicks');
    data.addColumn('number', 'Impressions');
    data.addColumn('number', 'CTR');
    data.addColumn('number', 'Position');

    _.forEach(rows, function(row){
      data.addRow([
        new Date(row.keys[0]),
        row.clicks,
        row.impressions,
        (row.ctr * 100),
        parseFloat(row.position)
      ]);
    })
  return data      
}

function selectHandler(){

  var col = chart.getSelection()[0].column
  if(!col){return}

  var selection = chart.getSelection();       

  // click and data index are one off
  var i = selection[0].column - 1;
  chartOptions.series[i].tooltip = !chartOptions.series[i].tooltip


  // just simple reverse
  if (chartOptions.series[i].lineWidth == 0) {
      chartOptions.series[i].lineWidth = 2;
      chartOptions.series[i].areaOpacity = 0.3;
  }
  else {
      chartOptions.series[i].lineWidth = 0;
      chartOptions.series[i].areaOpacity = 0.0;            
  }

  chart.draw(data, chartOptions);

}

function getReport(){
  gapi.client.webmasters.searchanalytics.query(
          {
              'siteUrl': site,
              'rowLimit': null,
              'searchType': 'web',
              'startDate': moment().subtract(period, 'days').format('YYYY-MM-DD'),
              'endDate': moment().format('YYYY-MM-DD'),
              'dimensions': ['date']
          })
          .then((response) => {
            //var options = chartOptions
            data = formatData(response.result.rows)
            chart = new google.visualization.LineChart(document.getElementById('searchconsole-app'));
            chart.draw(data, chartOptions);
            google.visualization.events.addListener(chart, 'select', selectHandler);


          })
          .then(null, function(err) {
              console.log(err);
          });      
}

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

                getReport();
                $('#showSpinner').toggleClass('hidden');
            
        })  

    }

})( jQuery );