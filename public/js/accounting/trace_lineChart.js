"use strict";

// CHART TRACE

$(document).ready(function() {

	if(mChart = isChart('dailyTrace')) {

		// mChart.init();

		// // CONFIG

		// mChart.setTitle('Solde relatif');
		// mChart.setScalesEnabled(true);
		// mChart.configureTooltips();
		// mChart.configureEvents();

		// mChart.options.elements = {
		// 	point: {
		// 		radius: 0
		// 	}
		// };

		// // mChart.options.tooltips = {
  // //       	mode: 'index',
		// // 	intersect: false,
  // //       	callbacks: {
  // //       		label: function(t, data) {

  // //       			var dataset = data.datasets[t.datasetIndex];
  // //       			var label = data.labels[t.index];
  // //       			var value = parseFloat(dataset.data[t.index]);

  // //       			var total = dataset.data.reduce(function(accumulateur, current, index, array) {
  // //       				if(current == null) { current = 0; }
  // //       				return parseFloat(accumulateur) + parseFloat(current);
  // //       			});
  // //       			total = Math.round(total*100)/100;

  // //       			var percent = Math.round(value/total*1000)/10;
  // //       			// console.log(percent);

  // //       			return label + ': ' + value.toLocaleString() + ' € (' + percent + '%)';
  // //       		}
  // //       	}
  // //       };

		// mChart.build();


		// PREPARE DATASETS

		// var traceDataset = {
		// 	label: "Suivi",
		// 	data: [],
		// 	backgroundColor: 'rgba(0,0,210,.1)',
		// 	borderColor: 'rgba(0,0,210,.9)',
		// 	borderWidth: 2
		// 	// fill: false,
		// }


		// FORMAT DATA

		// mChart.update();

		// var currDate = new Date(mChart.minDate.getTime());
		// var solde = 0;

		// // 
		// while(currDate <= mChart.maxDate) {

		// 	var currFormattedDate = currDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'numeric', year: 'numeric'});
		// 	var rank = null;
		// 	var data = null;

		// 	$(mChart.data).each(function(i, ref) {

		// 		data = null;
		// 		var refDate = new Date(ref.year, ref.month-1, ref.day);
		// 		var refFormattedDate = refDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'numeric', year: 'numeric'});

		// 		if(refDate > currDate) {
		// 			return false;
		// 		}

		// 		if(refDate.getDate() == currDate.getDate()) {
		// 			rank = i;
		// 			data = ref;
		// 			return false;
		// 		}
		// 	});

		// 	if(data != null) {
		// 		var total = parseFloat(solde) + parseFloat(data.sum);
		// 		solde = Math.round(total *100)/100;
		// 		mChart.data.splice(rank, 1);
		// 	}

		// 	// ADDING DATA TO mCHART
		// 	mChart.chartLabels.push(currFormattedDate);
		// 	traceDataset.data.push(solde);

		// 	// NEXT DAY			
		// 	currDate.setDate(currDate.getDate()+1);
		// }


		// // DISPLAY DATA
		// mChart.chartDatasets.push(traceDataset);
		// mChart.display();
	}






	// ADDING DATA TO CHART DYN

	// var idx = 0;
	// $(lineChart).on('addData', function() {

	// 	traceLabels.push(labels_trace[idx]);
	// 	traceDataset.data.push(data_trace[idx]);
	// 	lineChart.update();

	// 	if(++idx < data_trace.length) {
	// 		setTimeout(function() {
	// 			$(lineChart).trigger('addData');
	// 		}, 1);
	// 	}
	// });

	// setTimeout(function () {
	// 	$(lineChart).trigger('addData');
	// }, 1000);





	// var optionsAnimation = {
	//   //Boolean - If we want to override with a hard coded scale
	//   scaleOverride : true,
	//   //** Required if scaleOverride is true **
	//   //Number - The number of steps in a hard coded scale
	//   scaleSteps : 10,
	//   //Number - The value jump in the hard coded scale
	//   scaleStepWidth : 10,
	//   //Number - The scale starting value
	//   scaleStartValue : 0
	// }

	// setTimeout(function() {
	// 	// lineChart.Line(data, optionsAnimation);
	// 	lineChart.type = 'doughnut';
	// 	lineChart.data = data;
	// 	// lineChart.update();

	// 	console.log(lineChart);
	// }, 3000);





});

