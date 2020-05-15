"use strict";

// CHART BALANCE

$(document).ready(function() {

	if(mChart = isChart('creditRepartition')) {
		
		// mChart.init();

		// // CONFIG

		// mChart.options.aspectRatio = 1;
		// mChart.setTitle('Entrées');
		// mChart.configureTooltips();
		// mChart.configureEvents();
		// mChart.build();


		// // PREPARE DATASETS

		// var creditDataset = {
		// 	label: "Entrées",
		// 	data: [],
		// 	backgroundColor: [],
		// 	borderColor: 'rgba(0,0,0,.3)',
		// 	borderWidth: 1
		// };


		// // FORMAT DATA

		// mChart.update();

		// $(mChart.data).each(function(i, item) {
		// 	if(item.credit != null) {

		// 		// ADDING DATA TO mCHART
		// 		if(item.label == null) {
		// 			mChart.chartLabels.push('Non classé');
		// 			creditDataset.backgroundColor.push('gray');
		// 		} else {
		// 			mChart.chartLabels.push(item.label);
		// 			creditDataset.backgroundColor.push(randomColorAround(25,220,25,150));
		// 		}
		// 		creditDataset.data.push(item.credit);
		// 	}
		// });


		// // DISPLAY DATA
		// mChart.chartDatasets.push(creditDataset);
		// mChart.display();
	}
});




// "use strict";

// // CHART CREDIT DOUGHNUT

// $(document).ready(function() {

// 	// FORMATTING DATA

// 	console.log('============================================================================');
// 	console.log('-----------------------------------');
// 	console.log('--- creditRepartition Initial Data ---');
// 	console.log(creditRepartition);
// 	console.log('-----------------------------------');

// 	var labels_credit = [];
// 	var data_credit = [];
// 	var colors_credit = [];

// 	$(creditRepartition).each(function(i, d) {
// 		if(d.credit != null) {
// 			if(d.label == null) {
// 				labels_credit.push('Non classé');
// 				colors_credit.push('gray');
// 			} else {
// 				labels_credit.push(d.label);
// 				colors_credit.push(randomColorAround(25,220,25,150));
// 			}
// 			data_credit.push(d.credit);
// 		}
// 	});

// 	console.log('--- creditRepartition Final Labels ---');
// 	console.log(labels_credit);	
// 	console.log('--- creditRepartition Final Data ---');
// 	console.log(data_credit);

// 	// BUILDING CHART

//     var doughnutChartCredit = new Chart($("#doughnut-chart-credit"), {
//     	type: 'doughnut',
//     	data: {
//     		labels: labels_credit,
//     		datasets: [
//     			{
//     				label: "Recettes",
//     				data: data_credit,
//     				backgroundColor: colors_credit,
//     				borderColor: 'rgba(0,0,0,.3)',
//     				borderWidth: 1
//     			}
//     		]
//     	},
//     	options: {
// 	        aspectRatio: 1,
// 			title: {
// 	    		display: true,
// 	    		text: 'Recettes'
// 			},
// 	    	legend: {
// 	    		display: true,
// 	    		labels: {
// 	    			// fontColor: 'white'
// 	    		}
// 	    	},
// 	    	tooltips: {
// 	    		callbacks: {
// 	    			label: function(t, data) {

// 	    				var dataset = data.datasets[t.datasetIndex];
// 	    				var label = data.labels[t.index];
// 	    				var value = parseFloat(dataset.data[t.index]);

// 	    				var total = dataset.data.reduce(function(accumulateur, current, index, array) {
// 	    					if(current == null) { current = 0; }
// 	    					return parseFloat(accumulateur) + parseFloat(current);
// 	    				});
// 	    				total = Math.round(total*100)/100;

// 	    				var percent = Math.round(value/total*1000)/10;
// 	    				console.log(percent);

// 	    				return label + ': ' + value.toLocaleString() + ' € (' + percent + '%)';
// 	    			}
// 	    		}
// 	    	}
// 	    }
//     });

//     $('#switch-display').click(function() {

//     	if (doughnutChartCredit.options.circumference === Math.PI) {
//     		doughnutChartCredit.options.circumference = 2 * Math.PI;
//     		doughnutChartCredit.options.rotation = -Math.PI / 2;
//     	} else {
//     		doughnutChartCredit.options.circumference = Math.PI;
//     		doughnutChartCredit.options.rotation = -Math.PI;
//     	}
//     	doughnutChartCredit.update();
//     });

