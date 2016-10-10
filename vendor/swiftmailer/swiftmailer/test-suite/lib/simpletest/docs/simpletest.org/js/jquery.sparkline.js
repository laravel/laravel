/**
 * Javascript Sparklines Library
 * Based on a work by John Resig
 * http://ejohn.org/projects/jspark/
 * 
 * This work is licensed under a Creative Commons Attribution 2.5 License
 * More Info: http://creativecommons.org/licenses/by/2.5/
 * 
 * To use, place your data points within your HTML, like so:
 * <span class="sparkline">10,8,20,5...</span>
 *
 * in your CSS you might want to have the rule:
 * .sparkline { display: none }
 * so that non-compatible browsers don't see a huge pile of numbers.
 *
 */

function sparklinequery(o) {
	var p = o.text().split(',');
	o.empty();

	var nw = "auto";
	var nh = "auto";

	var f = 2;
	var w = ( nw == "auto" || nw == 0 ? p.length * f : nw - 0 );
	var h = ( nh == "auto" || nh == 0 ? "1em" : nh );

	var co = document.createElement("canvas");

	if ( co.getContext ) {
		o.css({ display: "inline" });
	} else {
		return false;
	}
	

	co.style.height = h;
	co.style.width = w;
	co.width = w;
	o.append( co );

	var h = co.offsetHeight;
	co.height = h;

	var min = 9999;
	var max = -1;

	for ( var i = 0; i < p.length; i++ ) {
		p[i] = p[i] - 0;
		if ( p[i] < min ) min = p[i];
		if ( p[i] > max ) max = p[i];
	}

	if ( co.getContext ) {
		var c = co.getContext("2d");
		c.strokeStyle = o.css("color");
		c.lineWidth = 1.0;
		c.beginPath();

		for ( var i = 0; i < p.length; i++ ) {
			x = (w / p.length) * i;
			if (max != min) {
				y = h - (((p[i] - min) / (max - min)) * h) ;
			} else {
				y = 0;
			}
			y = 3 * i;
			c.lineTo(x, y);
		}

		c.stroke();
alert(c);
		o.css({ display:"inline" });
	}
}