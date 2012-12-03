function getTimestamp(date) {
	var datestr = ((date.getMonth()+1) + "/" + date.getDate() + "/" + date.getFullYear())
	
	var hours = date.getHours()
	var minutes = date.getMinutes()
	if (minutes < 10){
		minutes = "0" + minutes
	}
	if(hours > 12)
		datestr += " "+((hours-12) + ":" + minutes + " PM");
	else if(hours > 11)
		datestr += " "+(hours + ":" + minutes + " PM");
	else if(hours == 0)
		datestr += " "+("12:" + minutes + " AM");
	else
		datestr += " "+(hours + ":" + minutes + " AM");
	
	return datestr;
}

(function( $ ) {
	var methods = { 
		init : function(options) {	//Initialize the input
		    var settings = $.extend( {	//Settings
		    	'form_name'	: 'frontpagefilter',
		    	'initial' : {},
		    	'suggest_tags' : [],
		    	'suggest_authors' : [],
		    	'suggest_series' : [],
		    	'suggest_section' : [],
		    }, options);
		    
		    return this.each(function() {
				var data = $(this).data('frontpagefilter');
				if(!data) {	//Process parameters supplied to plugin
					$(this).data("frontpagefilter", {
						'form_name' : settings.form_name,
						'index' : 0,
						'suggest_tags' : settings.suggest_tags,
						'suggest_authors' : settings.suggest_authors,
						'suggest_series' : settings.suggest_series,
						'suggest_section' : settings.suggest_section
					});
				}
				
				var id = $(this).attr('id');
				var filterset_id = id.substr(id.lastIndexOf("_") + 1);
				$(this).append("<table style='width: 100%' id='editor_filters_"+filterset_id+"' class='editor_filter'></table>");
		    	
		    	if(settings.initial[filterset_id]) {
		    		for(i in settings.initial[filterset_id]) {
		    			$("#"+id).frontpageFilter('addFilter', 
		    				{
		    				initial : settings.initial[filterset_id][i]["value"],
		    				type : settings.initial[filterset_id][i]["type"],
		    				})
		    		}
		    	}
		    });
		},
		
		findMaxId : function(options) {
			var max = 1;
			var elements = $(".editor_filter_row", this).each(function() {
				var id = $(this).attr('id');
				var filterset_id = id.substr(id.lastIndexOf("_") + 1);
				if(Number(filterset_id) > max)
					max = Number(filterset_id);
			});
			return Number(max);
		},
		
		addFilter : function(options) {
			var data = $(this).data('frontpagefilter');
		    var settings = $.extend( {	//Settings
		    	'intial' : "",
		    	'type' : false
		    }, options);
		    
			return(this.each(function() {
				var id = $(this).attr('id');
				var filterset_id = id.substr(id.lastIndexOf("_") + 1);
				
				var unique = Number($(".editor_filter").frontpageFilter('findMaxId')) + 1;
				unique = String(unique);
				filtertext = "<tr id='editor_filter_"+unique+"' class='editor_filter_row'>"+
					"<td style='width: 220px;'>Filter By: <select id='filter_type_select_"+unique+"' name='filter["+filterset_id+"]["+unique+"][type]'>"+
					"<option value=''>Choose one...</option>"+
					"<option value='view'>Content Type</option>"+
					"<option value='photos'>Number of Photos</option>"+
					"<option value='date'>Date</option>"+
					"<option value='age'>Age</option>"+
					"<option value=''>----</option>"+
					"<option value='title'>Title</option>"+
					"<option value='author'>Author</option>"+
					"<option value='series'>Series</option>"+
					"<option value='section'>Section</option>"+
					"<option value='tag'>Tag</option></select></td>"+
					"<td id='filter_input_"+unique+"' class='filter_input'></td><td id='filter_options_"+unique+"' style='width: 200px;'></td>"+
					"<td style='width: 20px; text-align: right;'><a href='javascript:void(0)' onclick=\"$('#"+id+"').frontpageFilter('removeFilter', 'editor_filter_"+unique+"')\">"+
					"<img src='/images/icons/cross.png' alt='Delete' title='Delete' /></a></td></tr>"
				
				$(".editor_filter", this).append(filtertext);
				
				$("#filter_type_select_"+unique).change(function() {
					$("#filter_input_"+unique).html("");
					var form_id = "form_input_"+unique;
					if($(this).val() == "view" || $(this).val() == "type") {
						var articletypes = ["content", "article", "frontpage"];
						$.each(articletypes, function(v) {
							var value = articletypes[v];
							var checkedstate = "";
							if(settings.initial && settings.initial[articletypes[v]] == "true")
								checkedstate = " checked='checked'"
							$("#filter_input_"+unique).append("<input type='checkbox'"+checkedstate+" value='true' name='filter["+filterset_id+"]["+unique+"][value]["+value+"]' id='"+form_id+"_"+value+"' /> " + value + " &nbsp;&nbsp;&nbsp;");
						});
					}
					else if($(this).val() == "tag") {
						$("#filter_input_"+unique).html("<div id='"+form_id+"' class='facebookbox autocomplete'></div>");
						$("#"+form_id).facebookbox({
							"form_name": "filter["+filterset_id+"]["+unique+"][value]", "suggest" : data.suggest_tags, "initial" : settings.initial
						});
					}
					else if($(this).val() == "author") {
						$("#filter_input_"+unique).html("<div id='"+form_id+"' class='facebookbox autocomplete'></div>");
						$("#"+form_id).facebookbox({
							"form_name": "filter["+filterset_id+"]["+unique+"][value]", "suggest" : data.suggest_authors, "initial" : settings.initial
						});
					}
					else if($(this).val() == "series") {
						$("#filter_input_"+unique).html("<div id='"+form_id+"' class='facebookbox autocomplete'></div>");
						$("#"+form_id).facebookbox({
							"form_name": "filter["+filterset_id+"]["+unique+"][value]", "suggest" : data.suggest_series, "initial" : settings.initial
						});
					}
					else if($(this).val() == "section") {
						$("#filter_input_"+unique).html("<div id='"+form_id+"' class='facebookbox autocomplete'></div>");
						$("#"+form_id).facebookbox({
							"form_name": "filter["+filterset_id+"]["+unique+"][value]", "suggest" : data.suggest_section, "initial" : settings.initial
						});
					}
					else if($(this).val() == "title") {
						if(!settings.initial) settings.initial = "";
						$("#filter_input_"+unique).html("<input type='text' value='"+settings.initial+"' name='filter["+filterset_id+"]["+unique+"][value]' id='"+form_id+"' style='width: 100%' />");
					}
					else if($(this).val() == "photos") {
						if(!settings.initial) settings.initial = {};
						if(!settings.initial.num) settings.initial.num = 1;
						var selecthtml = "<select name='filter["+filterset_id+"]["+unique+"][value][cond]' id='"+form_id+"_cond'>";
						var conds = ["less than", "equal to or less than", "equal to", "equal to or greater than", "greater than"];
						for(i in conds) {
							if(settings.initial['cond'] == conds[i])
								selecthtml += "<option value='"+conds[i]+"' selected='selected'>"+conds[i]+"</option>";
							else
								selecthtml += "<option value='"+conds[i]+"'>"+conds[i]+"</option>";
						}
						selecthtml += "</select>";
						
						selecthtml += " <input type='text' value='"+settings.initial['num']+"' name='filter["+filterset_id+"]["+unique+"][value][num]' id='"+form_id+"' style='width: 40px' />";
						
						$("#filter_input_"+unique).html(selecthtml);
					}
					else if($(this).val() == "date") {
						if(!settings.initial) settings.initial = {};
						if(!settings.initial.date) settings.initial.date = "";
						else {
							var date = new Date(settings.initial.date*1000);
							if(date.getMonth())
								settings.initial.date = getTimestamp(date);
						}
						var selecthtml = "<select name='filter["+filterset_id+"]["+unique+"][value][cond]' id='"+form_id+"_cond'>";
						var conds = ["created after", "created before"];
						for(i in conds) {
							if(settings.initial['cond'] == conds[i])
								selecthtml += "<option value='"+conds[i]+"' selected='selected'>"+conds[i]+"</option>";
							else
								selecthtml += "<option value='"+conds[i]+"'>"+conds[i]+"</option>";
						}
						selecthtml += "</select>";
						
						selecthtml += " <input type='text' value='"+settings.initial['date']+"' id='"+form_id+"' style='width: 200px' />";
						selecthtml += "<br /><span style='font-size: 10px'>Enter any date format, i.e. \"2010-04-16\", \"12pm Aug 2, 2013\", \"last Tuesday\", etc. <span class='filter_date_display'></span></span> "
						selecthtml += "<input type='hidden' name='filter["+filterset_id+"]["+unique+"][value][date]' value='' id='"+form_id+"_value' class='filter_date_value' />";
						$("#filter_input_"+unique).html(selecthtml);
						
						$("#"+form_id).keyup(function(v) {
							if(!$(this).val())
								return;
							var tmp = $("#filter_input_"+unique);
							$.get("ajax.php", {action : 'strtotime', str : $(this).val()}, function(r) {
								if(!r) {
									$(".filter_date_display", tmp).html("<br /><strong style='color: red'>Warning: Invalid Date</strong>");
									$(".filter_date_value", tmp).val("");
								}
								else {
									$(".filter_date_display", tmp).html("<br />Interpreted as <strong>"+getTimestamp(new Date(r * 1000))+"</strong>");
									$(".filter_date_value", tmp).val(r);
								}
							});
						});
						
						$("#"+form_id).keyup();
					}
					else if($(this).val() == "age") {
						if(!settings.initial) settings.initial = {};
						if(!settings.initial.minutes) settings.initial.minutes = "0";
						if(!settings.initial.hours) settings.initial.hours = "0";
						if(!settings.initial.days) settings.initial.days = "0";
						if(!settings.initial.weeks) settings.initial.weeks = "0";
						
						var selecthtml = "<select name='filter["+filterset_id+"]["+unique+"][value][cond]' id='"+form_id+"_cond'>";
						var conds = ["created later than", "created earlier than"];
						for(i in conds) {
							if(settings.initial['cond'] == conds[i])
								selecthtml += "<option value='"+conds[i]+"' selected='selected'>"+conds[i]+"</option>";
							else
								selecthtml += "<option value='"+conds[i]+"'>"+conds[i]+"</option>";
						}
						selecthtml += "</select>";
						
						selecthtml += " <input type='text' value='"+settings.initial.weeks+"' name='filter["+filterset_id+"]["+unique+"][value][weeks]' id='"+form_id+"' style='width: 30px' />";
						selecthtml += " weeks, ";
						selecthtml += "<input type='text' value='"+settings.initial.days+"' name='filter["+filterset_id+"]["+unique+"][value][days]' id='"+form_id+"' style='width: 30px' />";
						selecthtml += " days, ";
						selecthtml += "<input type='text' value='"+settings.initial.hours+"' name='filter["+filterset_id+"]["+unique+"][value][hours]' id='"+form_id+"' style='width: 30px' />";
						selecthtml += " hours, ";
						selecthtml += "<input type='text' value='"+settings.initial.hours+"' name='filter["+filterset_id+"]["+unique+"][value][minutes]' id='"+form_id+"' style='width: 30px' />";
						selecthtml += " minutes ago";
						
						$("#filter_input_"+unique).html(selecthtml);
					}
					
					settings.initial = false;
				});
				
				if(settings.type != false) {
					$("#filter_type_select_"+unique).val(settings.type);
					$("#filter_type_select_"+unique).change();
				}
			}));
		},
		
		removeFilter : function(id) {
			$("#"+id).remove();
		}
	};
	
	$.fn.frontpageFilter = function(method){
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		}
		else {
			$.error('Method ' + method + ' does not exist on jQuery.frontpageFilter');
		}
	}
})( jQuery );