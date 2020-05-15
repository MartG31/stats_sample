"use strict";

// 
// 
// ============================================ //
//   Date Functions                             //
// ============================================ //

function toEnglishDateString(date) {

	var y = date.toLocaleDateString('fr-FR', { year: 'numeric'} );
	var m = date.toLocaleDateString('fr-FR', { month: '2-digit'} );
	var d = date.toLocaleDateString('fr-FR', { day: '2-digit'} );

	return y+'-'+m+'-'+d;
}

function prevMonthFirst(date) {
	date.setDate(1);
	date.setMonth(date.getMonth()-1);
}

function prevMonthLast(date) {
	date.setDate(1);
	date.setDate(date.getDate()-1);
	
}

function nextMonthFirst(date) {
	date.setDate(1);
	date.setMonth(date.getMonth()+1);
}

function nextMonthLast(date) {
	date.setDate(1);
	date.setMonth(date.getMonth()+2);
	date.setDate(date.getDate()-1);					
}

// 
// 
// ============================================ //
//   Color Functions                            //
// ============================================ //

function componentToHex(c) {
  var hex = c.toString(16);
  return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function hexToRgb(hex) {
	
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	
	var colorTab = result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : false;

	return colorTab ? colorTab : null;
}

function randomColorAround(r, g, b, range = 100) {

	// console.log('=================================================');
	// console.log('initial ----- R: '+r+' / G: '+g+' / B: '+b);

	var red, green, blue;
	var maxOffset = Math.floor(range/2);

	var rOffset = maxOffset - Math.floor(Math.random() * (range+1) );
	var gOffset = maxOffset - Math.floor(Math.random() * (range+1) );
	var bOffset = maxOffset - Math.floor(Math.random() * (range+1) );

	// console.log('offsets ----- R: '+rOffset+' / G: '+gOffset+' / B: '+bOffset);

	red = Math.floor(r + rOffset);
	green = Math.floor(g + gOffset);
	blue = Math.floor(b + bOffset);

	// console.log('colors ----- R: '+red+' / G: '+green+' / B: '+blue);

	if(red > 255) { red = 255; }
	if(green > 255) { green = 255; }
	if(blue > 255) { blue = 255; }

	if(red < 0) { red = 0; }
	if(green < 0) { green = 0; }
	if(blue < 0) { blue = 0; }
	
	// console.log('final ----- R: '+red+' / G: '+green+' / B: '+blue);

	return 'rgb('+red+','+green+','+blue+')';
}

// 
// 
// ============================================ //
//   Other Functions                            //
// ============================================ //

function sleep(milliseconds) {
  const date = Date.now();
  let currentDate = null;
  do {
    currentDate = Date.now();
  } while (currentDate - date < milliseconds);
}
