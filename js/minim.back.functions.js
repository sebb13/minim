/*
 *	minim - PHP framework
    Copyright (C) 2019  Sébastien Boulard

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; see the file COPYING. If not, write to the
    Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
function getErrorLogsGraph(chartTarget, chartWidth){
	if(typeof chartTarget === 'undefined') {
		var chartTarget = '';
		var chartWidth = 0;
		if($('#chart_div').is(':visible')) {
			chartTarget = '#chart_div';
			chartWidth = $('#chart_div').parent().parent().parent().width()-20;
		} 
		if($('#chart_div_home').is(':visible')) {
			chartTarget = '#chart_div_home';
			chartWidth = $('#chart_div_home').parent().parent().width()-20;
		}
	}
	
	if(chartWidth !== 0) {
		var promise = genericRequest({
							app_token: getToken(), 
							content: getCurrentPage(),
							dataType: 'json',
							exw_action: 'Logs::getJsonForCharts'
						});
		promise.success(function(JSONdata) {
			var chart = c3.generate({
								bindto: chartTarget,
								data: {
									x: 'x',
									columns: JSON.parse(JSONdata),
									colors: {
										nbErrors:  '#c63531'
									}
								},
								axis: {
									x: {
										type: 'timeseries',
										tick: {
											format: '%d-%m-%y'
										}
									},
									nbErrors: {
										min: 0
									}
								},
								size: {
									width: chartWidth
								}
			});
		});
		promise.error(function() {
			alert('error');
		});
	}
return true;
}