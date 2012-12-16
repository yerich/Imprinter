(function( $ ) {
	var methods = { 
		init : function(options) {	//Initialize the input
		    var settings = $.extend( {	//Settings
		    	'index'	: 0,
		    	'initial' : [],
		    	'upload_url' : './media_upload.php',
		    	'media_result' : $("#media_result"),
		    	'dropzone' : $("#media_dropzone"),
		    	'files' : [],
		    	'tagslist' : [],
		    	'authorslist' : []
		    }, options);
			
			return this.each(function() {
				data = $(this).data('mediaupload');
				if(!data) {
					$(this).data("mediaupload", {
						'media_result' : settings.media_result,
						'index' : settings.index,
						'initial' : settings.initial,
						'tagslist' : settings.tagslist,
						'authorslist' : settings.authorslist,
					});
				}
				
				self = this;
				$(this).fileupload({	//Initiliza the fileupload plugin
			        dataType: 'json',
			        url: './media_upload.php',
			        sequentialUploads: true,
			        add: function(e, data) {	//Add file event
						//Unique file identifier (used for the session only)
			        	unique = Math.round(Math.random() * Math.random() * 1000000000000000);
			        	data.formData = {unique: unique};
						$.each(data.files, function (index, file) {
							file.unique = unique;
						    $(self).mediaupload("add", file);
						});
			            data.submit();
			        },
			        done: function (e, data) {	//File upload complete event
			            $.each(data.result, function (index, file) {
			                $(self).mediaupload("done", file);
			            });
			        },
			        dropZone : settings.dropzone,
			        formData : [],
			        progress : function(e, data) {	//Advance progress bar event
			        	complete = parseInt(data.loaded / data.total * 100, 10);
			        	$("#media_"+data.files[0].unique+" .media_loading").text("Uploading... "+complete+"%");
			        }
			    });
				
				//Add media from the initial setting
				for(i in settings.initial) {
					$(self).mediaupload("add", settings.initial[i]);
					$(self).mediaupload("done", settings.initial[i]);
					console.log("test");
				}
			    
				//The following three events are to prevent the browaser from executing it's own drag event
			    $(document).bind('dragover', function (e) {
			    	settings.dropzone.show();
				    e.preventDefault();
				});
				
			    $(document).bind('drop', function (e) {
			    	settings.dropzone.hide();
				    e.preventDefault();
				});
				
				$(document).bind('mouseout', function(e) {
					settings.dropzone.hide();
				});
				
				console.log("#"+$(this).attr("id")+" mediaupload initialized");
			});
		},
		
		add : function(file) {	//Add a new form value
			return this.each(function() {
				var data = $("#"+$(this).attr("id")).data('mediaupload');
				//console.log(file);
				
				if(!file.caption) 
					file.caption = "";
				
				//Append new div to the results area
				$(data.media_result).append("<div class=\"media_wrapper media_wrapper_"+data.index+"\" id=\"media_"+file.unique+"\">"+
					"<div class='media_title'><img class='media_delete_img' src='/images/icons/cross.png' alt='Delete' />"+file.name+"</div>"+
					"<div class='media_preview'><div class='media_loading'><span></span>Uploading...</div></div>"+
					'<div class="media_inputs"><table style="width: 100%"><tr>'+
					'<td class="media_table_left"><label for="media_'+data.index+'_caption">Caption</label></td>'+
					'<td><textarea id="media_'+data.index+'_caption" name="media_caption[]" class="media_caption" rows="3" cols="20">'+file.caption+'</textarea></td></tr>'+
					'<tr><td><label for="media_'+data.index+'_author">Credit</label></td>'+
					'<td><div id="media_'+data.index+'_author" class="facebookbox autocomplete media_author"></div></td></tr>'+
					'<tr><td><label for="media_'+data.index+'_tag">Tags</label></td>'+
					'<td><div id="media_'+data.index+'_tags" class="facebookbox autocomplete media_tag"></div></td></tr></table></div>'+
					'<input type="hidden" name="file_unique[]" class="media_file_unique" value="'+file.unique+'"/>'+
					'<input type="hidden" name="file_location[]" class="media_file_location" value="'+file.url+'" /></div>');
				
				$(".media_wrapper_"+data.index+" .media_author").facebookbox({	//Inititialize the Facebookbox plugin for the media authors
					"form_name": "media_author["+file.unique+"]", "suggest" : data.authorslist, "initial" : file.author
				});
				
				$(".media_wrapper_"+data.index+" .media_tag").facebookbox({	//Inititialize the Facebookbox plugin for the media tags
					"form_name": "media_tag["+file.unique+"]", "suggest" : data.tagslist, "initial" : file.tags
				});
				
				$(".media_wrapper_"+data.index+" .media_delete_img").click(function() {
					$(this).parent().parent().remove();
				});
				
				$(this).data("mediaupload", $.extend(data, {
					'index' : data.index + 1
				}));
			});
		},
		
		done : function(file) {	//File has done uploading
			return this.each(function() {
				if(!file.thumb_url)
					file.thumb_url = file.url;
				$("#media_"+file.unique+" .media_preview").html("<img src='"+file.thumb_url+"' />");
				$("#media_"+file.unique+" .media_file_location").val(file.url);
			});
		}
	};
	
	$.fn.mediaupload = function(method){
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		}
		else {
			$.error('Method ' + method + ' does not exist on jQuery.mediaupload');
		}
	}
})( jQuery );