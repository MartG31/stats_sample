"use strict";

// CLASS MChart

function MChart() {

	// ATTRIBUTES

	this.manager;
	this.name;

	this.type;
	this.title;

	// ----------------------

	this.options = {
		responsive: true,
		legend: {
			display: false
		},
		animation: {
			animateScale: true,
			animateRotate: true
		},
		title: {
			display: true,
			text: this.title
		}
	};

	this.config = {
		type: this.type,
	    options: this.options
	};
	
	this.ctx;
	this.chart;

	this.chartLabels = [];
	this.chartDatasets = [];

	this.lastUpdateDt;
	this.token;
	this.data;

	// ----------------------

	// Instanciate the chart with its config.
	// Displays the chart without data, waiting the first update...
	this.build = function() {
		this.init();
		this.configureScales();
		this.configureTooltips();
		this.configureEvents();
		this.configureAll();

		this.chart = new Chart(this.ctx, this.config);
		this.prepareDatasets();

		// console.log(this.chart);
	}

	// ----------------------

	this.init = function() {

		this.ctx = document.getElementById(this.name);

		// DEFAULT OPTIONS

		this.options = {
			responsive: true,
			legend: {
				display: false
			},
			animation: {
				animateScale: true,
				animateRotate: true
			},
			title: {
				display: true,
				text: this.title
			}
		};

		this.config = {
			type: this.type,
		    options: this.options
		};
	}	

	this.configureScales = function() {

		switch(this.name) {

			case 'dailyTrace':
			case 'monthlyBalance':
			case 'creditHistory':
			case 'debitHistory':

				this.options.scales = {
					xAxes: [{
					    ticks: {
					    	fontColor: 'rgba(0,0,0,.6)',
					        beginAtZero: true
					    },
					    gridLines: {
					    	color: 'rgba(0,0,0,.3)',
					    	lineWidth: 1,
					    	zeroLineColor: 'rgba(0,0,0,.6)',
					    	zeroLineWidth: 2
					    },
					}],
					yAxes: [{
					    ticks: {
					    	fontColor: 'rgba(0,0,0,.6)',
					        beginAtZero: true
					    },
					    gridLines: {
					    	color: 'rgba(0,0,0,.3)',
					    	lineWidth: 1,
					    	zeroLineColor: 'rgba(0,0,0,.6)',
					    	zeroLineWidth: 2
					    },
					}]
				};

				break;
		}
	}

	this.configureTooltips = function() {

		switch(this.name) {

			case 'creditRepartition':
			case 'debitRepartition':
			case 'creditHistory':
			case 'debitHistory':

			    this.options.tooltips = {
		    		// mode: 'index',
		    		// intersect: false,
					callbacks: {
						label: function(t, data) {

							var dataset = data.datasets[t.datasetIndex];
							var label = data.labels[t.index];
							var value = parseFloat(dataset.data[t.index]);

							var total = dataset.data.reduce(function(accumulateur, current, index, array) {
								if(current == null) { current = 0; }
								return parseFloat(accumulateur) + parseFloat(current);
							});
							total = Math.round(total*100)/100;

							var percent = Math.round(value/total*1000)/10;

							return label + ': ' + value.toLocaleString() + ' € (' + percent + '%)';
						}
					}
		    	};
				break;

			case 'dailyTrace':
			case 'monthlyBalance':
			case 'creditHistory':
			case 'debitHistory':

			    this.options.tooltips = {
		    		mode: 'index',
		    		intersect: false,
		    	};
				break;
		}
	}

	this.configureEvents = function() {

		switch(this.name) {
			case 'creditRepartition':
			case 'debitRepartition':

	    	    this.options.onClick = (e, item) => {

	    	    	if(item.length > 0) {

	    	    		// console.log('===== ITEM CLICKED =====');

	    	    		var datasetIndex = item[0]['_datasetIndex'];
	    	    		var dataIndex = item[0]['_index'];
	    	    		var id = this.data[dataIndex].id;
	    	    		this.redirectWithId(id);	    	    		
	    	    	}
	    	    };
				break;
		}
	}

	this.configureAll = function() {

		switch(this.name) {
			
			case 'dailyTrace':
			case 'creditHistory':
			case 'debitHistory':

				this.options.elements = {
					point: {
						radius: 0
					}
				};
				break;

			case 'creditRepartition':
			case 'debitRepartition':

			    this.options.aspectRatio = 1;
				break;
		}
	}

	// ----------------------

	this.prepareDatasets = function() {

		this.chart.config.data = {
			labels: this.chartLabels,
			datasets: this.chartDatasets
		};

		switch(this.name) {

			// 
			case 'dailyTrace':

				this.chartDatasets.push({
					label: "Suivi",
					data: [],
					backgroundColor: 'rgba(0,0,210,.1)',
					borderColor: 'rgba(0,0,210,.9)',
					borderWidth: 2
					// fill: false,
				});

				break;

			// 
			case 'monthlyBalance':

				this.chartDatasets.push({
					label: "Recettes",
					data: [],
					backgroundColor: 'rgba(0,210,0,1)',
					borderColor: 'rgba(0,128,0,1)',
					borderWidth: 1
				});

				this.chartDatasets.push({
					label: "Dépenses",
					data: [],
					backgroundColor: 'rgba(210,0,0,1)',
					borderColor: 'rgba(128,0,0,1)',
					borderWidth: 1
				});

				this.chartDatasets.push({
					label: "Balance",
					data: [],
					backgroundColor: 'rgba(210,210,0,1)',
					borderColor: 'rgba(128,128,0,1)',
					borderWidth: 1,
				});

				break;

			// 
			case 'creditRepartition':

				this.chartDatasets.push({
					label: "Entrées",
					data: [],
					backgroundColor: [],
					borderColor: 'rgb(255,255,255)',
					borderWidth: 1
				});

				break;

			// 
			case 'debitRepartition':

				this.chartDatasets.push({
					label: "Sorties",
					data: [],
					backgroundColor: [],
					borderColor: 'rgb(255,255,255)',
					borderWidth: 1
				});

				break;

			// 
			case 'creditHistory':

				this.chartDatasets.push({
					label: "Entrées",
					data: [],
					backgroundColor: [],
					borderColor: 'rgba(0,255,0,.6)',
					borderWidth: 2
				});

				break;

			// 
			case 'debitHistory':

				this.chartDatasets.push({
					label: "Sorties",
					data: [],
					backgroundColor: [],
					borderColor: 'rgba(255,0,0,.6)',
					borderWidth: 2
				});
				
				break;
		}

		this.chart.update();
	}

	// ----------------------

	this.update = function() {

		var obj = this;

		// Empty Labels & Datasets before encoding new data 
		this.chartLabels = [];
		$(this.chartDatasets).each(function(i, dataset) {
			dataset.data = [];
		});

		// Formatting methods
		switch(this.name) {

			// 
			case 'dailyTrace':

				var currDate = new Date(this.manager.min_date.getTime());
				var solde = 0;

				// 
				while(currDate <= this.manager.max_date) {

					var currFormattedDate = currDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'numeric', year: 'numeric'});
					var rank = null;
					var data = null;

					$(this.data).each(function(i, ref) {

						data = null;
						var refDate = new Date(ref.year, ref.month-1, ref.day);
						var refFormattedDate = refDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'numeric', year: 'numeric'});

						if(refDate > currDate) {
							return false;
						}

						if(refDate.getDate() == currDate.getDate()) {
							rank = i;
							data = ref;
							return false;
						}
					});

					if(data != null) {
						var total = parseFloat(solde) + parseFloat(data.sum);
						solde = Math.round(total *100)/100;
						this.data.splice(rank, 1);
					}

					// ADDING DATA TO mChart
					this.chartLabels.push(currFormattedDate);
					this.chartDatasets[0].data.push(solde);

					this.chart.update();

					// NEXT DAY			
					currDate.setDate(currDate.getDate()+1);
				}

				break;

			// 
			case 'monthlyBalance':

				var minMonth = new Date(this.manager.min_date.getTime());
				minMonth.setDate(1);
				var maxMonth = new Date(this.manager.max_date.getTime());
				// maxMonth.setMonth(maxMonth.getMonth()+1);
				maxMonth.setDate(1);
				// maxMonth.setDate(maxMonth.getDay()-1);
				var currMonth = new Date(minMonth.getTime());
				currMonth.setDate(1);

				// 
				while(currMonth <= maxMonth) {

					var currMonthFormatted = currMonth.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric'});
					var data = null;
					var rank = null;

					$(this.data).each(function(i, ref) {

						data = null;
						var dataMonth = new Date(ref.year, ref.month-1);
						var dataMonthFormatted = dataMonth.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric'});

						if(currMonthFormatted == dataMonthFormatted) {
							rank = i;
							data = ref;
							return false;
						}
					});

					// ADDING DATA TO mChart
					this.chartLabels.push(currMonthFormatted);

					if(data != null) {
						this.chartDatasets[0].data.push(data.credit);
						this.chartDatasets[1].data.push(data.debit);
						this.chartDatasets[2].data.push(data.balance);

						this.data.splice(rank, 1);
					}
					else {
						this.chartDatasets[0].data.push(0);
						this.chartDatasets[1].data.push(0);
						this.chartDatasets[2].data.push(0);
					}

					// NEXT MONTH	
					currMonth.setMonth(currMonth.getMonth()+1);
				}

				break;

			// 
			case 'creditRepartition':
			case 'debitRepartition':

				this.chartDatasets[0].backgroundColor = [];

				$(this.data).each(function(i, item) {
					if(item.amount != null) {

						// ADDING DATA TO mChart
						if(item.label != null) {
							obj.chartLabels.push(item.label);

							// console.log(obj.manager.mode);

							switch (obj.manager.mode) {

								case 'global':
									obj.chartDatasets[0].backgroundColor.push(item.color);
									break;

								case 'category':
									obj.chartDatasets[0].backgroundColor.push(randomColorAround(
										hexToRgb(item.color).r,
										hexToRgb(item.color).g,
										hexToRgb(item.color).b,
										80
									));
									break;
							}
						}
						else {
							obj.chartLabels.push('Non classé');
							obj.chartDatasets[0].backgroundColor.push('gray');
						}

						obj.chartDatasets[0].data.push(item.amount);
					}
				});

				break;

			// 
			case 'creditHistory':
			case 'debitHistory':

				this.chartDatasets[0].backgroundColor = [];

				var minMonth = new Date(this.manager.min_date.getTime());
				minMonth.setDate(1);
				var maxMonth = new Date(this.manager.max_date.getTime());
				// maxMonth.setMonth(maxMonth.getMonth()+1);
				maxMonth.setDate(1);
				// maxMonth.setDate(maxMonth.getDay()-1);
				var currMonth = new Date(minMonth.getTime());
				currMonth.setDate(1);

				// 
				while(currMonth <= maxMonth) {

					var currMonthFormatted = currMonth.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric'});
					var data = null;
					var rank = null;

					$(this.data).each(function(i, ref) {

						data = null;
						var dataMonth = new Date(ref.year, ref.month-1);
						var dataMonthFormatted = dataMonth.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric'});

						if(currMonthFormatted == dataMonthFormatted) {
							rank = i;
							data = ref;
							return false;
						}
					});

					// ADDING DATA TO mChart
					this.chartLabels.push(currMonthFormatted);

					if(data != null) {
						this.chartDatasets[0].data.push(data.amount);
						this.chartDatasets[0].backgroundColor.push(data.color);
						this.data.splice(rank, 1);
					}
					else {
						this.chartDatasets[0].data.push(0);
						this.chartDatasets[0].backgroundColor.push(null);
					}

					// NEXT MONTH	
					currMonth.setMonth(currMonth.getMonth()+1);
				}

				break;
		}

		this.display();
	}

	this.display = function() {

		var datasets = [];

		for(var dataset in this.chartDatasets) {
			datasets.push(this.chartDatasets[dataset]);
		}

		this.chart.config.data = {
			labels: this.chartLabels,
			datasets: datasets,
		}

		this.chart.update();
	}

	this.redirectWithId = function(id) {

		switch(this.manager.mode) {
			
			case 'global':
				this.manager.updateSession({
					mode: 'category',
					category: id,
					label: null
				}, true);
				break;

			case 'category':
				this.manager.updateSession({
					mode: 'label',
					category: this.manager.category,
					label: id
				}, true);
				break;
		}
	}
}