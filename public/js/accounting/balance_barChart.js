"use strict";

// CHART BALANCE

$(document).ready(function() {

	if(mChart = isChart('monthlyBalance')) {

		// mChart.init();

		// // CONFIG

		// mChart.setTitle('Balance financière');
		// mChart.setScalesEnabled(true);
		// mChart.configureTooltips();
		// mChart.configureEvents();

	 //    mChart.options.tooltips = {
  //   		mode: 'index',
  //   		intersect: false,
  //   	};

		// mChart.build();


		// PREPARE DATASETS

		// var creditDataset = {
		// 	label: "Recettes",
		// 	data: [],
		// 	backgroundColor: 'rgba(0,210,0,1)',
		// 	borderColor: 'rgba(0,128,0,1)',
		// 	borderWidth: 1
		// };

		// var debitDataset = {
		// 	label: "Dépenses",
		// 	data: [],
		// 	backgroundColor: 'rgba(210,0,0,1)',
		// 	borderColor: 'rgba(128,0,0,1)',
		// 	borderWidth: 1
		// };

		// var balanceDataset = {
		// 	label: "Balance",
		// 	data: [],
		// 	backgroundColor: 'rgba(210,210,0,1)',
		// 	borderColor: 'rgba(128,128,0,1)',
		// 	borderWidth: 1,
		// }; 


		// // FORMAT DATA

		// mChart.update();

		// var minMonth = new Date(minDate.getTime());
		// minMonth.setDate(1);
		// var maxMonth = new Date(maxDate.getTime());
		// // maxMonth.setMonth(maxMonth.getMonth()+1);
		// maxMonth.setDate(1);
		// // maxMonth.setDate(maxMonth.getDay()-1);
		// var currMonth = new Date(minMonth.getTime());
		// currMonth.setDate(1);

		// // 
		// while(currMonth <= maxMonth) {

		// 	var currMonthFormatted = currMonth.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric'});
		// 	var data = null;
		// 	var rank = null;

		// 	$(mChart.data).each(function(i, ref) {

		// 		data = null;
		// 		var dataMonth = new Date(ref.year, ref.month-1);
		// 		var dataMonthFormatted = dataMonth.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric'});

		// 		if(currMonthFormatted == dataMonthFormatted) {
		// 			rank = i;
		// 			data = ref;
		// 			return false;
		// 		}
		// 	});

		// 	// ADDING DATA TO mCHART
		// 	mChart.chartLabels.push(currMonthFormatted);

		// 	if(data != null) {
		// 		creditDataset.data.push(data.credit);
		// 		debitDataset.data.push(data.debit);
		// 		balanceDataset.data.push(data.balance);

		// 		mChart.data.splice(rank, 1);
		// 	}
		// 	else {
		// 		creditDataset.data.push(0);
		// 		debitDataset.data.push(0);
		// 		balanceDataset.data.push(0);
		// 	}

		// 	// NEXT MONTH	
		// 	currMonth.setMonth(currMonth.getMonth()+1);
		// }


		// // DISPLAY DATA
		// mChart.chartDatasets.push(creditDataset);
		// mChart.chartDatasets.push(debitDataset);
		// mChart.chartDatasets.push(balanceDataset);
		// mChart.display();
	}
});