"use strict";

// CHART BALANCE

$(document).ready(function() {

	if(mChart = isChart('debitRepartition')) {
		
		// mChart.init();

		// // CONFIG

		// mChart.setTitle('Sorties');
		// mChart.options.aspectRatio = 1;
		// mChart.configureTooltips();
		// mChart.configureEvents();
		// mChart.build();


		// // PREPARE DATASETS

		// var debitDataset = {
		// 	label: "Sorties",
		// 	data: [],
		// 	backgroundColor: [],
		// 	borderColor: 'rgba(0,0,0,.3)',
		// 	borderWidth: 1
		// };


		// // FORMAT DATA

		// mChart.update();

		// $(mChart.data).each(function(i, item) {
		// 	if(item.debit != null) {

		// 		// ADDING DATA TO mCHART
		// 		if(item.label == null) {
		// 			mChart.chartLabels.push('Non classé');
		// 			debitDataset.backgroundColor.push('gray');
		// 		} else {
		// 			mChart.chartLabels.push(item.label);
		// 			debitDataset.backgroundColor.push(randomColorAround(220,25,25,150));
		// 		}
		// 		debitDataset.data.push(item.debit);
		// 	}
		// });


		// // DISPLAY DATA
		// mChart.chartDatasets.push(debitDataset);
		// mChart.display();
	}
});


// "use strict";

// // CHART DEBIT DOUGHNUT

// $(document).ready(function() {

// 	// FORMATTING DATA

// 	console.log('============================================================================');
// 	console.log('-------------------------------------');
// 	console.log('--- debitRepartition Initial Data ---');
// 	console.log(debitRepartition);
// 	console.log('-------------------------------------');

// 	var labels_debit = [];
// 	var data_debit= [];
// 	var colors_debit = [];

// 	$(debitRepartition).each(function(i, d) {
// 		if(d.debit != null) {
// 			if(d.label == null) {
// 				labels_debit.push('Non classé');
// 				colors_debit.push('gray');
// 			} else {
// 				labels_debit.push(d.label);
// 				colors_debit.push(randomColorAround(220,25,25,150));
// 			}
// 			data_debit.push(d.debit);
// 		}
// 	});

// 	console.log('--- debitRepartition Final Labels ---');
// 	console.log(labels_debit);
// 	console.log('--- debitRepartition Final Data ---');
// 	console.log(data_debit);

// 	// BUILDING CHART

// 	var doughnutChartDebit = new Chart($("#doughnut-chart-debit"), {
// 		type: 'doughnut',
// 		data: {
// 			labels: labels_debit,
// 			datasets: [
// 				{
// 					label: "Dépenses",
// 					data: data_debit,
// 					backgroundColor: colors_debit,
// 					borderColor: 'rgba(0,0,0,.3)',
// 					borderWidth: 1
// 				}
// 			]
// 		},
// 		options: {
// 			defaultFontColor: 'red',
// 			defaultFontSize: 24,
// 	        aspectRatio: 1,
// 			title: {
// 	    		display: true,
// 	    		text: 'Dépenses'	
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
// 	});
// 	console.log('test chart <<<<<<<<<<<<<<');
// 	console.log(doughnutChartDebit.options);

// 	// doughnutChartDebit.options.defaultFontColor = 'red';
// 	// doughnutChartDebit.options.defaultFontSize = 12;

// 	// console.log('test chart <<<<<<<<<<<<<<');
// 	// console.log(doughnutChartDebit.options);


// 	doughnutChartDebit.update();

// 	$('#switch-display').click(function() {

// 		if (doughnutChartDebit.options.circumference === Math.PI) {
// 			doughnutChartDebit.options.circumference = 2 * Math.PI;
// 			doughnutChartDebit.options.rotation = -Math.PI / 2;
// 		} else {
// 			doughnutChartDebit.options.circumference = Math.PI;
// 			doughnutChartDebit.options.rotation = -Math.PI;
// 		}
// 		doughnutChartDebit.update();
// 	});

// });