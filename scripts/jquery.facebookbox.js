/**
 * jQuery Facebookbox
 * 
 * A plugin that attempts to mimic the functionality found on Facebook recipient boxes, with autocomplete
 * 
 * Dependencies: jquery.js, jquery.autocomplete.js
 * Author: Richard Ye <http://www.yerich.net/>
 * License: This file may be used for any purpose provided that this line and the line above remain intact.
 */

(function($){
    
    // jQuery autoGrowInput plugin by James Padolsey
    // See related thread: http://stackoverflow.com/questions/931207/is-there-a-jquery-autogrow-plugin-for-text-fields
        
        $.fn.autoGrowInput = function(o) {
            
            o = $.extend({
                maxWidth: 1000,
                minWidth: 0,
                comfortZone: 70
            }, o);
            
            this.filter('input:text').each(function(){
                
                var minWidth = o.minWidth || $(this).width(),
                    val = '',
                    input = $(this),
                    testSubject = $('<tester/>').css({
                        position: 'absolute',
                        top: -9999,
                        left: -9999,
                        width: 'auto',
                        fontSize: input.css('fontSize'),
                        fontFamily: input.css('fontFamily'),
                        fontWeight: input.css('fontWeight'),
                        letterSpacing: input.css('letterSpacing'),
                        whiteSpace: 'nowrap'
                    }),
                    check = function() {
                        
                        if (val === (val = input.val())) {return;}
                        
                        // Enter new content into testSubject
                        var escaped = val.replace(/&/g, '&amp;').replace(/\s/g,'&nbsp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        testSubject.html(escaped);
                        
                        // Calculate new width + whether to change
                        var testerWidth = testSubject.width(),
                            newWidth = (testerWidth + o.comfortZone) >= minWidth ? testerWidth + o.comfortZone : minWidth,
                            currentWidth = input.width(),
                            isValidWidthChange = (newWidth < currentWidth && newWidth >= minWidth)
                                                 || (newWidth > minWidth && newWidth < o.maxWidth);
                        
                        // Animate width
                        if (isValidWidthChange) {
                            input.width(newWidth);
                        }
                        
                    };
                    
                testSubject.insertAfter(input);
                
                $(this).bind('keyup keydown blur update', check);
                
            });
            
            return this;
        
        };
        
    })(jQuery);

(function( $ ) {
	var methods = { 
		init : function(options) {	//Initialize the input
		    var settings = $.extend( {	//Settings
		    	'form_name'	: 'facebookbox',
		    	'initial' : [],
		    	'suggest' : [],
		    }, options);
			
			return this.each(function() {
				var data = $(this).data('facebookbox');
				if(!data) {	//Process parameters supplied to plugin
					$(this).data("facebookbox", {
						'form_name' : settings.form_name,
						'index' : 0,
						'suggest' : settings.suggest
					});
				}
				
				//Append boilerplate code for the inputs and hidden for fields to go in
				$(this).append('<div class="facebookbox_values"></div><div class="facebookbox_hidden"></div><input type="text" class="facebookbox_input" name="'+settings.form_name+'[]" />');
				var orig_input = this;
				//Add auto-suggest for the input
				$(".facebookbox_input", this).autocompleteArray(settings.suggest, 
					{
						onItemSelect : function() {	//What to do when an item is selected manually from the drop down menu
							$(".facebookbox_input", orig_input).focus();
							if ($(".facebookbox_input", orig_input).val() != "") {
								/*
								$(".facebookbox_input", orig_input).parent().facebookbox("add", {	//Add a new value
									value: $(".facebookbox_input", orig_input).val()
								});
								$(".facebookbox_input", orig_input).val("");	//Clear the input
								*/
								$(".facebookbox_input", orig_input).focus();
							}
						},
						autoFill : true,
						selectFirst : true,
						delay : 1,
					});
				
				$(this).click(function () {	//Focus goes to the input if the wrapper is clicked
					$(".facebookbox_input", this).focus();
				});
				
				$(".facebookbox_value", this).click(function (event) {
					event.stopPropagation();	//Stop a click to a value from registering as a click to the wrapper
				});
				
				$(".facebookbox_input", this).keydown(function (event) {	//What happens when a key is typed into the input box?
					if(event.which == 13) {	//Enter key has been pressed
						if ($(this).val() != "") {
							$(this).parent().facebookbox("add", {	//Add a new value
								value: $(this).val()
							});
							$(this).val("");	//Clear the input
						}
					}
					else if(event.which == 8 && event.shiftKey && $(this).val() == "") {	//Shift+Backspace key has been pressed - if the input is empty, delete the last value
						if ($(this).val() == "")
							$(this).parent().facebookbox("removelast");
					}
					else if(event.which == 8 && $(this).val() == "") {	//Select the last tag if backspace is pressed
						if ($(".facebookbox_values .facebookbox_value.selected", $(this).parent()).length > 0) {
							$(this).parent().facebookbox("removelast");
						}
						else {	//Delete the selected tag if backspace is pressed
							$(".facebookbox_values .facebookbox_value:last", $(this).parent()).addClass("selected");
							return;
						}
					}
					$(".facebookbox_values .facebookbox_value:last", $(this).parent()).removeClass("selected");
				});
				
				 $(".facebookbox_input", this).autoGrowInput({	//Make the input adjust to the width of the text
				 	comfortZone : 20,
				 	minWidth : 20,
				 	maxWidth : $(this).parent().width() - 5
				 });
				
				if(settings.initial) {	//If there are values already present, add them
					for(value in settings.initial) {
						$(this).facebookbox("add", {value : settings.initial[value]});
					}
				}
			});
		},
		
		add : function(options) {	//Add a new form value
		    var settings = $.extend( {	//Settings
		    	'form_value'	: options.value	//What is sent to the server
		    }, options);
			
			return this.each(function() {
				var data = $(this).data('facebookbox');
				
				//Append the form input and hidden field for the data
				$(".facebookbox_values", this).append("<div class=\"facebookbox_value facebookbox_value_"+data.index+"\"><span></span>"+settings.value+"</div>");
				$(".facebookbox_hidden", this).append("<input type=\"hidden\" class=\"facebookbox_hidden_"+data.index+"\" value=\""+settings.form_value+"\" name=\""+data.form_name+"[]\" />");
				
				var id = data.index;
				$(".facebookbox_value_"+data.index+" span", this).click(function () {	//Remove event handler
					$(this).parent().parent().parent().facebookbox("remove", id);
				});
				
				$(this).data("facebookbox", $.extend(data, {	//Increment the counter
					'index' : data.index + 1,
					'form_name' : data.form_name
				}));
			});
		},
		
		remove : function(id) {	//Removes a given value
			return this.each(function() {
				$(".facebookbox_value_"+id, this).remove();
				$(".facebookbox_hidden_"+id, this).remove();
			});
		},
		
		removelast : function() {	//Removes the last value
			return this.each(function() {
				$(".facebookbox_values .facebookbox_value:last", this).remove();
				$(".facebookbox_hidden input:last", this).remove();
			});
		}
	};
	
	$.fn.facebookbox = function(method){
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		}
		else {
			$.error('Method ' + method + ' does not exist on jQuery.facebookbox');
		}
	}
})( jQuery );