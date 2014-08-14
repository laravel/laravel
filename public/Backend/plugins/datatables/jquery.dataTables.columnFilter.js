/*
* File:        jquery.dataTables.columnFilter.js
* Version:     0.9.0
* Author:      Jovan Popovic 
* 
* Copyright 2011 Jovan Popovic, all rights reserved.
*
* This source file is free software, under either the GPL v2 license or a
* BSD style license, as supplied with this software.
* 
* This source file is distributed in the hope that it will be useful, but 
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
* or FITNESS FOR A PARTICULAR PURPOSE. 
* 
* Parameters:
* @sPlaceHolder                 String      Place where inline filtering function should be place ("tfoot", "thead"). Default is "tfoot"
* @sRangeSeparator              String      Separatot that will be used when range values are sent to the server-side. Default value is "~".
* @iFilteringDelay              int         TODO: Delay that will be set between the filtering requests. Default is 250.
* @sRangeFormat                 string      Default format of the From ... to ... range inputs. Default is From {from} to {to}
* @aoColumns                    Array       Array of the filter settings that will be applied on the columns

http://www.datatables.net/plug-ins/filtering

*/
(function ($) {






    var asInitVals, i, label, th;

    var sTableId = "table";
    var sRangeFormat = "From {from} to {to}";
    //Array of the functions that will override sSearch_ parameters
    var afnSearch_ = new Array();
    var aiCustomSearch_Indexes = new Array();

    var oFunctionTimeout = null;


    function fnCreateInput(regex, smart, bIsNumber) {
        var sCSSClass = "text_filter";
        if (bIsNumber)
            sCSSClass = "number_filter";
        var input = $('<input type="text" class="search_init ' + sCSSClass + '" value="' + label + '"/>');
        th.html(input);
        if (bIsNumber)
            th.wrapInner('<span class="filter_column filter_number" />');
        else
            th.wrapInner('<span class="filter_column filter_text" />');
        asInitVals[i] = label;
        var index = i;

        if (bIsNumber && !oTable.fnSettings().oFeatures.bServerSide) {
            input.keyup(function () {
                /* Filter on the column all numbers that starts with the entered value */
                oTable.fnFilter('^' + this.value, index, true, false);
            });
        } else {
            input.keyup(function () {
                /* Filter on the column (the index) of this element */
                oTable.fnFilter(this.value, index, regex, smart);
            });
        }

        input.focus(function () {
            if ($(this).hasClass("search_init")) {
                $(this).removeClass("search_init");
                this.value = "";
            }
        });
        input.blur(function () {
            if (this.value == "") {
                $(this).addClass("search_init");
                this.value = asInitVals[index];
            }
        });
    }

    function fnCreateRangeInput() {

        th.html(_fnRangeLabelPart(0));
        var sFromId = sTableId + 'range_from_' + i;
        var from = $('<input type="text" class="number_range_filter" id="' + sFromId + '" rel="' + i + '"/>');
        th.append(from);
        th.append(_fnRangeLabelPart(1));
        var sToId = sTableId + 'range_to_' + i;
        var to = $('<input type="text" class="number_range_filter" id="' + sToId + '" rel="' + i + '"/>');
        th.append(to);
        th.append(_fnRangeLabelPart(2));
        th.wrapInner('<span class="filterColumn filter_number_range" />');
        var index = i;
        aiCustomSearch_Indexes.push(i);



        //------------start range filtering function


        /* 	Custom filtering function which will filter data in column four between two values
        *	Author: 	Allan Jardine, Modified by Jovan Popovic
        */
        $.fn.dataTableExt.afnFiltering.push(
	        function (oSettings, aData, iDataIndex) {
	            var iMin = document.getElementById(sFromId).value * 1;
	            var iMax = document.getElementById(sToId).value * 1;
	            var iValue = aData[index] == "-" ? 0 : aData[index] * 1;
	            if (iMin == "" && iMax == "") {
	                return true;
	            }
	            else if (iMin == "" && iValue < iMax) {
	                return true;
	            }
	            else if (iMin < iValue && "" == iMax) {
	                return true;
	            }
	            else if (iMin < iValue && iValue < iMax) {
	                return true;
	            }
	            return false;
	        }
        );
        //------------end range filtering function



        $('#' + sFromId + ',#' + sToId, th).keyup(function () {

            var iMin = document.getElementById(sFromId).value * 1;
            var iMax = document.getElementById(sToId).value * 1;
            if (iMin != 0 && iMax != 0 && iMin > iMax)
                return;

            oTable.fnDraw();

        });


    }


    function fnCreateDateRangeInput() {

        th.html(_fnRangeLabelPart(0));
        var sFromId = sTableId + 'range_from_' + i;
        var from = $('<input type="text" class="date_range_filter" id="' + sFromId + '" rel="' + i + '"/>');
        from.datepicker();
        th.append(from);
        th.append(_fnRangeLabelPart(1));
        var sToId = sTableId + 'range_to_' + i;
        var to = $('<input type="text" class="date_range_filter" id="' + sToId + '" rel="' + i + '"/>');
        th.append(to);
        th.append(_fnRangeLabelPart(2));
        th.wrapInner('<span class="filterColumn filter_date_range" />');
        to.datepicker();
        var index = i;
        aiCustomSearch_Indexes.push(i);


        //------------start date range filtering function

        $.fn.dataTableExt.afnFiltering.push(
	        function (oSettings, aData, iDataIndex) {
	            var dStartDate = from.datepicker("getDate");

	            var dEndDate = to.datepicker("getDate");

	            var dCellDate = $.datepicker.parseDate($.datepicker.regional[""].dateFormat, aData[index]);

	            if (dCellDate == null)
	                return false;

	            if (dStartDate == null && dEndDate == null) {
	                return true;
	            }
	            else if (dStartDate == null && dCellDate < dEndDate) {
	                return true;
	            }
	            else if (dStartDate < dCellDate && dEndDate == null) {
	                return true;
	            }
	            else if (dStartDate < dCellDate && dCellDate < dEndDate) {
	                return true;
	            }
	            return false;
	        }
        );
        //------------end date range filtering function

        $('#' + sFromId + ',#' + sToId, th).change(function () {
            oTable.fnDraw();
        });


    }


    function fnCreateSelect(aData) {
        var index = i;
        var r = '<select class="search_init select_filter"><option value="" class="search_init">' + label + '</option>', j, iLen = aData.length;

        for (j = 0; j < iLen; j++) {
            r += '<option value="' + aData[j] + '">' + aData[j] + '</option>';
        }
        var select = $(r + '</select>');
        th.html(select);
        th.wrapInner('<span class="filterColumn filter_select" />');
        select.change(function () {
            //var val = $(this).val();
            if ($(this).val() != "") {
                $(this).removeClass("search_init");
            } else {
                $(this).addClass("search_init");
            }
            oTable.fnFilter($(this).val(), index);
        });
    }

    function _fnRangeLabelPart(iPlace){
        switch(iPlace){
        case 0:
            return sRangeFormat.substring(0, sRangeFormat.indexOf("{from}"));
        case 1:
            return sRangeFormat.substring(sRangeFormat.indexOf("{from}") + 6, sRangeFormat.indexOf("{to}"));
        default:
            return sRangeFormat.substring(sRangeFormat.indexOf("{to}") + 4);
	}
    }


    $.fn.columnFilter = function (options) {

        oTable = this;

        var defaults = {
            sPlaceHolder: "foot",
            sRangeSeparator: "~",
            iFilteringDelay: 500,
            aoColumns: null,
            sRangeFormat: "From {from} to {to}"

        };

        properties = $.extend(defaults, options);

        return this.each(function () {

            asInitVals = new Array();
            var sFilterRow = "tfoot tr";
            if (properties.sPlaceHolder == "head:after") {
                sFilterRow = "thead tr:last";
            } else if (properties.sPlaceHolder == "head:before") {
                var tr = $("thead tr:last").detach();
                tr.prependTo("thead");
                sFilterRow = "thead tr:first";
            }

            $(sFilterRow + " th", oTable).each(function (index) {
                i = index;
                var aoColumn = { type: "text",
                    bRegex: false,
                    bSmart: true
                };
                if (properties.aoColumns != null) {
                    if (properties.aoColumns.length < i || properties.aoColumns[i] == null)
                        return;
                    aoColumn = properties.aoColumns[i];
                }
                label = $(this).text(); //"Search by " + $(this).text();
                th = $($(this)[0]);
                if (aoColumn != null) {
                    if (aoColumn.sRangeFormat != null)
                        sRangeFormat = aoColumn.sRangeFormat;
                    else
                        sRangeFormat = properties.sRangeFormat
                    switch (aoColumn.type) {
                        case "number":
                            fnCreateInput(true, false, true);
                            break;
                        case "text":
                            bRegex = (aoColumn.bRegex == null ? false : aoColumn.bRegex);
                            bSmart = (aoColumn.bSmart == null ? false : aoColumn.bSmart);
                            fnCreateInput(bRegex, bSmart, false);
                            break;
                        case "select":
                            fnCreateSelect(aoColumn.values);
                            break;
                        case "number-range":
                            fnCreateRangeInput();
                            break;
                        case "date-range":
                            fnCreateDateRangeInput();

                            break;
                        default:
                            break;

                    }
                }
            });

            for (j = 0; j < aiCustomSearch_Indexes.length; j++) {
                var index = aiCustomSearch_Indexes[j];
                var fnSearch_ = function () {
                    return $("#range_from_" + index).val() + properties.sRangeSeparator + $("#range_to_" + index).val()
                }
                afnSearch_.push(fnSearch_);
            }

            if (oTable.fnSettings().oFeatures.bServerSide) {

                var fnServerDataOriginal = oTable.fnSettings().fnServerData;

                oTable.fnSettings().fnServerData = function (sSource, aoData, fnCallback) {

                    for (j = 0; j < aiCustomSearch_Indexes.length; j++) {
                        var index = aiCustomSearch_Indexes[j];

                        for (k = 0; k < aoData.length; k++) {
                            if (aoData[k].name == "sSearch_" + index)
                                aoData[k].value = afnSearch_[j]();
                        }
                    }
                    aoData.push({ "name": "sRangeSeparator", "value": properties.sRangeSeparator });

                    if (fnServerDataOriginal != null) {
                        fnServerDataOriginal(sSource, aoData, fnCallback);
                    }
                    else {
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json)
                        });
                    }

                    /*
                    if (fnServerDataOriginal != null) {
                    if (properties.iDelay != 0) {
                    if (oFunctionTimeout != null)
                    window.clearTimeout(oFunctionTimeout);
                    oFunctionTimeout = window.setTimeout(function () {
                    fnServerDataOriginal(sSource, aoData, fnCallback);
                    }, properties.iDelay);
                    } else {
                    fnServerDataOriginal(sSource, aoData, fnCallback);
                    }
                    }
                    else
                    $.getJSON(sSource, aoData, function (json) {
                    fnCallback(json)
                    });
                    */
                };

            }

        });

    };




})(jQuery);