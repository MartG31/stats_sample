"use strict";

// console.log("");
// console.log(">>> nav toggle events ready");

$(document).ready(function() {
	
	$('#nav-collapse-btn').on({
		
		click : function(e) {
				e.stopPropagation();
				console.log(">>> nav toggle");
				$('#nav-overlay').toggleClass('active');
				$('#side-nav').toggleClass('active');
			}
	});	

	$('#nav-overlay').on({
		
		click : function(e) {
				e.stopPropagation();
				console.log(">>> nav quit");
				$('#side-nav').removeClass('active');
				$('#nav-overlay').removeClass('active');
				$('#side-nav').removeClass('active');
			}
	});
});