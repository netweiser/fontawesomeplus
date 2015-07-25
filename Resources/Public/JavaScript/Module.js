/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alexander Kontos <info@netweiser.com>
 *  	netweiser - your way to the internet!
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Fontawesomeplus main JavaScript for Backend Module
 */
define('Netweiser/Fontawesomeplus/Module', ['jquery', 'datatables'], function($) {

	var Module = {};

	// Initialize dataTables
	Module.initializeDataTables = function() {
		$('#iconlist').DataTable({
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"bPaginate": true,
			"bLengthChange": true,
			"bFilter": true,
			"bSort": true,
			"bInfo": false,
			"bAutoWidth": true,
			"sDom": '<"row" ' +
						'<"col-sm-4" ' +
							'<"fa fa-lg fa-eye">' +
							'<"fap-inline" l>' +
						'>' +
						'<"col-sm-4 text-center" ' +
							'<p>' +
						'>' +
						'<"col-sm-4 text-right" ' +
							'<"fa fa-lg fa-search">' +
							'<"fap-inline" f>' +
						'>' +
					'>' +
					'<"clear">' +
					't' +
					'<"row" ' +
						'<"col-sm-4" ' +
							'<"fa fa-lg fa-eye">' +
							'<"fap-inline" l>' +
						'>' +
						'<"col-sm-4 text-center" ' +
							'<p>' +
						'>' +
						'<"col-sm-4 text-right" ' +
							'<"fa fa-lg fa-search">' +
							'<"fap-inline" f>' +
						'>' +
					'>',
			"aoColumns": [
				{ "bSortable": false },
				null,
				null
			],
			"aaSorting": [[1,'desc']],
			language: {
				search:         "",
				lengthMenu:    "_MENU_",
				info:           "_START_ - _END_ of _TOTAL_",
				infoEmpty:      "",
				infoFiltered:   "",
				infoPostFix:    "",
				loadingRecords: "",
				zeroRecords:    "",
				emptyTable:     "",
				paginate: {
					first:      "&nbsp;",
					previous:   "|",
					next:       "|",
					last:       "&nbsp;"
				},
				aria: {
					sortAscending:  "",
					sortDescending: ""
				}
			}
		});
		$('#ziplist').DataTable({

			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"bPaginate": true,
			"bLengthChange": true,
			"bFilter": true,
			"bSort": true,
			"bInfo": false,
			"bAutoWidth": true,
			"sDom": '<"row" ' +
						'<"col-sm-4" ' +
							'<"fa fa-lg fa-eye">' +
							'<"fap-inline" l>' +
						'>' +
						'<"col-sm-4 text-center" ' +
							'<p>' +
						'>' +
						'<"col-sm-4 text-right" ' +
							'<"fa fa-lg fa-search">' +
							'<"fap-inline" f>' +
						'>' +
					'>' +
					'<"clear">' +
					't' +
					'<"row" ' +
						'<"col-sm-4" ' +
							'<"fa fa-lg fa-eye">' +
							'<"fap-inline" l>' +
						'>' +
						'<"col-sm-4 text-center" ' +
							'<p>' +
						'>' +
						'<"col-sm-4 text-right" ' +
							'<"fa fa-lg fa-search">' +
							'<"fap-inline" f>' +
						'>' +
					'>',
			"aoColumns": [
				{ "bSortable": false },
				null
			],
			"aaSorting": [[1,'desc']],
			language: {
				search:         "",
				lengthMenu:    "_MENU_",
				info:           "_START_ - _END_ of _TOTAL_",
				infoEmpty:      "",
				infoFiltered:   "",
				infoPostFix:    "",
				loadingRecords: "",
				zeroRecords:    "",
				emptyTable:     "",
				paginate: {
					first:      "&nbsp;",
					previous:   "|",
					next:       "|",
					last:       "&nbsp;"
				},
				aria: {
					sortAscending:  "",
					sortDescending: ""
				}
			}
		});
	};

	$(document).ready(function() {
		// Initialize the view
		Module.initializeDataTables();
	});

});