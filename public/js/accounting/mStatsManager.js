"use strict";

// CLASS MChart

function MStatsManager(params) {

	this.path_data = params.path_data;
	this.path_session = params.path_session;

	this.charts = params.charts;

	// this.filters = params.filters;
	this.mode = params.filters.mode;
	this.category = params.filters.category;
	this.label = params.filters.label;
	this.account = params.filters.account;

	// ------------

	this.min_date = new Date(params.filters.min_date + ' 00:00:00');
	this.max_date = new Date(params.filters.max_date + ' 00:00:00');
	this.min_month;
	this.max_month;

	// ------------

	this.mCharts = [];

	this.refreshLastDt = new Date();
	this.refreshToken;

	// ------------

	this.init = function() {

		// this.refreshDate();
		// console.log(this);
	}

	this.refreshDate = function() {

		this.min_month = this.min_date.toLocaleDateString('fr-FR', { month: 'long' , year: 'numeric'});
		this.max_month = this.max_date.toLocaleDateString('fr-FR', { month: 'long' , year: 'numeric'});

		// console.log('min_date: ' + this.min_date.toLocaleString() + ' === ( ' +this.min_month+ ' )'); 
		// console.log('max_date: ' + this.max_date.toLocaleString() + ' === ( ' +this.max_month+ ' )');
		
		$('#period').text(this.min_month + ' - ' + this.max_month);	
	}

	// ------------

	this.createCharts = function() {

		var obj = this;

		$(this.charts).each(function(i, chart) {

			var mChart = new MChart();
			mChart.manager = obj;
			mChart.name = chart;

			switch (chart) {

				case 'dailyTrace':
					mChart.type = 'line';
					mChart.title = 'Solde relatif';
					break;

				case 'monthlyBalance':
					mChart.type = 'bar';
					mChart.title = 'Balance financière';
					break;

				case 'creditRepartition':
					mChart.type = 'doughnut';
					mChart.title = 'Entrées';
					break;

				case 'debitRepartition':
					mChart.type = 'doughnut';
					mChart.title = 'Sorties';
					break;

				case 'creditHistory':
					mChart.type = 'bar';
					mChart.title = 'Historique des entrées';
					break;

				case 'debitHistory':
					mChart.type = 'bar';
					mChart.title = 'Historique des sorties';
					break;
			}

			obj.mCharts.push(mChart);
		});
		// console.log(this.mCharts);

		$(this.mCharts).each(function(i, mChart) {
			mChart.build();
		});

	}

	this.configureNavigationEvents = function() {

		var obj = this;

		$('#return-to-index').click(function() {

			obj.updateSession({
				mode: 'global',
				category: null,
				label: null
			}, true);
		});

		$('#return-to-cat').click(function() {
			
			obj.updateSession({
				mode: 'category',
				category: obj.category,
				label: null
			}, true);
		});
	}

	this.configureTimelineEvents = function() {

		var obj = this;

		$('#go-back').click(function() {
			prevMonthFirst(obj.min_date);
			prevMonthLast(obj.max_date);
			obj.refreshStats();
		});

		$('#go-forward').click(function() {
			nextMonthFirst(obj.min_date);
			nextMonthLast(obj.max_date);
			obj.refreshStats();
		});

		$('#extend-back').click(function() {
			prevMonthFirst(obj.min_date);
			obj.refreshStats();
		});

		$('#extend-forward').click(function() {
			nextMonthLast(obj.max_date);
			obj.refreshStats();
		});

		$('#curtail-back').click(function() {
			nextMonthFirst(obj.min_date);
			obj.refreshStats();
		});

		$('#curtail-forward').click(function() {
			prevMonthLast(obj.max_date);
			obj.refreshStats();
		});
	}

	// ------------

	this.refreshStats = function() {

		this.refreshDate();

		var token = Math.random().toString(36);
		this.refreshToken = token;

		var obj = this;

		// MULTI-CLICK FILTER

		var refreshDt = new Date();
		var diff = refreshDt - this.refreshLastDt;
		// console.log('diff: '+diff);

		this.refreshLastDt = refreshDt;

		if(diff > 1000) {
			// console.log('==> NEW CLICK');
			this.updateCharts(token);
		}
		else {
			// console.log('==> BLOCKED');
			setTimeout(async function() {
				// console.log('==> DEFERED');
				if(refreshDt == obj.refreshLastDt) {
					// console.log('==> LAST CLICK');
					obj.updateCharts(token);
				}
			}, 200);
		}
	}

	this.updateCharts = function(token) {

		var obj = this;

		var data = {
			token: token,
			charts: obj.charts,
			mode: obj.mode,
			category: obj.category,
			label: obj.label,
			account: obj.account,
			min_date: toEnglishDateString(obj.min_date),
			max_date: toEnglishDateString(obj.max_date)
		}

		// console.log(data);

		$.ajax({
			method: "post",
			url: obj.path_data,
			data: data,
			dataType: 'json',

			success: function(json, status, response) {

				// console.log(json);

				// SI LE TOKEN N'A PAS ETE REGENERE ENTRE TEMPS
				if(json.token == obj.refreshToken) {

					console.log(json.infos);


					// UPDATE CHARTS
					for(var chart in json.data) {
						
						$(obj.mCharts).each(function(i, mChart) {

							if(mChart.name == chart) {
								mChart.data = json.data[chart];
								mChart.update();
							}
						});
					}


					// GESTION AFFICHAGE Suivi / Revenus / Dépenses

					if(json.infos.credit == null && json.infos.debit == null) {
						$('.mg-card-suivi').hide(800);
					}
					else {
						$('.mg-card-suivi').show(800);
					}
					if(json.infos.credit == null) {
						$('.mg-card-credit').hide(800);
					}
					else {
						$('.mg-card-credit').show(800);
					}
					if(json.infos.debit == null) {
						$('.mg-card-debit').hide(800);
					}
					else {
						$('.mg-card-debit').show(800);
					}

					// UPDATE SESSION
					obj.updateSession(data);
				}
			},

			error: function(response, status, error) {
				// console.log('=== '+obj.name+' ajax error ===');
				// console.log('-> error :');
				// console.log(error);
				// console.log('============');
			},

			complete: function(response, status) {

			}
		});
	}

	this.updateSession = function(data, reloadPage = false) {

		var obj = this;

		$.ajax({
			method: "post",
			url: obj.path_session,
			data: data,
			dataType: 'json',

			success: function(json, status, response) {
				// console.log('=== updateSession ajax success ===');
				// console.log(json);

				if(reloadPage) {
					// console.log('=== reloadPage ===');
					document.location.reload(true);
				}
			},

			error: function(response, status, error) {
				// console.log('=== updateSession ajax error ===');
				// console.log('-> error :');
				// console.log(error);
				// console.log('============');
			},

			complete: function(response, status) {

			}
		});
	}
}