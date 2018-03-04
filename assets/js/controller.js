var sock = null;
var ellog = null;
window.onload = function() {
	var wsuri;
	ellog = document.getElementById('log');
	if (window.location.protocol === "file:") {
		wsuri = "ws://10.5.16.212:9000";
	} else {
		wsuri = "ws://10.5.16.212:9000";
	}
	if ("WebSocket" in window) {
		sock = new WebSocket(wsuri);
	} else if ("MozWebSocket" in window) {
		sock = new MozWebSocket(wsuri);
	} else {
		log("Browser does not support WebSocket!");
	}
	if (sock) {
		sock.onopen = function() {
			log("Connected to " + wsuri);
		}
		sock.onclose = function(e) {
			log("Connection closed (wasClean = " + e.wasClean + ", code = "
					+ e.code + ", reason = '" + e.reason + "')");
			sock = null;
		}
		sock.onmessage = function(e) {
			log("Got echo: " + e.data);
			// console.info(e.data)
			var array = JSON.parse(e.data);
			console.dir(array);
			var disk = array[2][1];
			var swap = array[1][1][1];
			var memory = array[1][1][0];
			var cpu = array[3][1];
			table(disk);
			tablecpu(cpu);
			highchart(memory, "memory");
			highchart(swap, "swap");
		}
	}
}
function broadcast() {
	var msg = document.getElementById('message').value;
	if (sock) {
		sock.send(msg);
		log("Sent: " + msg);
	} else {
		log("Not connected.");
	}
}
function log(m) {
	ellog.innerHTML += m + '\n';
	ellog.scrollTop = ellog.scrollHeight;
}

function table(value) {
	var html = "";
	for (var i = 0; i < value.length; i++) {
		html += "<tr>";
		$.each(value[i], function(index, value) {
			html += "<td>" + value + "</td>";
//			console.log(index + " : " + value);
		});
		html += "</tr>"
	}
	$('#tablebody').html(html);
}

function tablecpu(value) {
	sortResults('Virt', false);
	
	
	function sortResults(prop, asc) {
	    value = value.sort(function(a, b) {
	        if (asc) {
	            return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
	        } else {
	            return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
	        }
	    });
	    showResults(value);
	}
}
function showResults(value){

	var html = "";
	for (var i = 0; i < value.length; i++) {
		html += "<tr>";
		$.each(value[i], function(index, value) {
			html += "<td>" + value + "</td>";
//			console.log(index + " : " + value);
		});
		html += "</tr>"
	}
	$('#cputablebody').html(html);
}

function highchart(value, nama) {
	var array = new Array();
	$.each(value, function(index, value) {
		array.push([ index, parseInt(value) ]);
	});
	$('#chart' + nama).highcharts({
		chart : {
			plotBackgroundColor : null,
			plotBorderWidth : 0,
			plotShadow : false,
			backgroundColor : 'rgba(255, 255, 255, 0.1)'
		},
		title : {
			text : nama,
			align : 'center',
			verticalAlign : 'middle',
			y : 40
		},
		credits : {
			enabled : false
		},
		tooltip : {
			pointFormat : '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions : {
			pie : {
				dataLabels : {
					enabled : true,
					distance : -50,
					style : {
						fontWeight : 'bold',
						color : 'white'
					}
				},
				startAngle : -90,
				endAngle : 90,
				borderWidth: 0,
				center : [ '50%', '75%' ]
			}
		},
		series : [ {
			type : 'pie',
			name : 'Used',
			innerSize : '50%',
			data : array
		} ]
	});
}
