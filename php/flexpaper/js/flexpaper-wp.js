(function($) {
	$(document).ready(function() {
		$('div.flexpaper_link').each(function(){
		$(this).css({width: (flex_opts.width.indexOf('%')>0||flex_opts.width.indexOf('px')>0)?flex_opts.width:flex_opts.width+'px', height: (flex_opts.height.indexOf('%')>0||flex_opts.width.indexOf('px')>0)?flex_opts.height:flex_opts.height+'px'});
		$(this).parent().css({width: (flex_opts.width.indexOf('%')>0||flex_opts.width.indexOf('px')>0)?flex_opts.width:flex_opts.width+'px', height: (flex_opts.height.indexOf('%')>0||flex_opts.width.indexOf('px')>0)?flex_opts.height:flex_opts.height+'px'});
		$(this).attr('id',$(this).parent().attr('id')+'_viewer');


		var pdfDocName = ($(this).data('document').indexOf('.swf')>0)?$(this).data('document').substr(0,$(this).data('document').length-4) + '.pdf':$(this).data('document');
		if(pdfDocName.lastIndexOf(".pdf") != pdfDocName.length - 4){pdfDocName = pdfDocName + ".pdf"}

		if(flex_opts.automaticconversion && flex_opts.automaticconversion != "false"){
			var _this = this;
			
			jQuery.ajax({
				url: flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=numpages-query",
				dataType : 'json', 
				success: function(wordData) {
					$(_this).FlexPaperViewer(
		               { config : {
		
		                 SWFFile : "{" + flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=swf&page=[*,0],"+wordData[0].pages+"}",
						 IMGFiles : flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=jpg&page={page}",
						 JSONFile : flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=json&page={page}",						 
						 IMGFiles : flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=jpg&page={page}",
						 ThumbIMGFiles : flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=jpg&page={page}&resolution=300",
						 PDFFile : flex_opts.base_dir + "/?flexpaper-plugin=view-document&doc="+pdfDocName+"&format=pdf&page=[*,1]",
						 						  
		                 Scale : 0.6,
		                 ZoomTransition : 'easeOut',
		                 ZoomTime : 0.5,
		                 ZoomInterval : 0.1,
		 	   	 		 FitPageOnLoad : flex_opts.fitpageonload,
		 		 		 FitWidthOnLoad : flex_opts.fitwidthonload,
				 		 InitViewMode : flex_opts.initviewmode,
		                 FullScreenAsMaxWindow : false,
		                 ProgressiveLoading : false,
		                 MinZoomSize : 0.2,
		                 MaxZoomSize : 5,
		                 SearchMatchAll : false,
		                 RenderingOrder : flex_opts.renderingorder,
		                 StartAtPage : '',
		
						 PreviewMode : true,
				
				 		 ViewModeToolsVisible : flex_opts.viewmodetoolsvisible,
				 		 ZoomToolsVisible : flex_opts.zoomtoolsvisible,
				 		 NavToolsVisible : flex_opts.navtoolsvisible,
				 		 CursorToolsVisible : flex_opts.cursortoolsvisible,
				 		 SearchToolsVisible : flex_opts.searchtoolsvisible,
				
						 jsDirectory : flex_opts.dir+"js/",
						 localeDirectory : flex_opts.dir+"locale/",
						 UIConfig : flex_opts.uiconfig,
						 key : flex_opts.key,
		                 WMode : 'window',
		                 localeChain: 'en_US'
		               }}
		        );
				},
				error: function(data, ajaxOptions, thrownError){
					console.log("error loading numpages info");
				}
			});
		}else{
			$(this).FlexPaperViewer(
	               { config : {
	
	                 SWFFile : ($(this).data('document').indexOf('.swf')>0)?$(this).data('document').substr(0,$(this).data('document').length-4) + '.swf':$(this).data('document') + '.swf',
	                 IMGFiles : ($(this).data('document').indexOf('.swf')>0)?$(this).data('document').substr(0,$(this).data('document').length-4) + '_{page}.png':$(this).data('document') + '_{page}.png',
	                 JSONFile : ($(this).data('document').indexOf('.swf')>0)?$(this).data('document').substr(0,$(this).data('document').length-4) + '.js':$(this).data('document') + '.js',
	                 PDFFile : ($(this).data('document').indexOf('.swf')>0)?$(this).data('document').substr(0,$(this).data('document').length-4) + '.pdf':$(this).data('document') + '.pdf',
	
	                 Scale : 0.6,
	                 ZoomTransition : 'easeOut',
	                 ZoomTime : 0.5,
	                 ZoomInterval : 0.1,
	 	   	 		 FitPageOnLoad : flex_opts.fitpageonload,
	 		 		 FitWidthOnLoad : flex_opts.fitwidthonload,
			 		 InitViewMode : flex_opts.initviewmode,
	                 FullScreenAsMaxWindow : false,
	                 ProgressiveLoading : false,
	                 MinZoomSize : 0.2,
	                 MaxZoomSize : 5,
	                 SearchMatchAll : false,
	                 RenderingOrder : flex_opts.renderingorder,
	                 StartAtPage : '',
	
					 PreviewMode : true,
			
			 		 ViewModeToolsVisible : flex_opts.viewmodetoolsvisible,
			 		 ZoomToolsVisible : flex_opts.zoomtoolsvisible,
			 		 NavToolsVisible : flex_opts.navtoolsvisible,
			 		 CursorToolsVisible : flex_opts.cursortoolsvisible,
			 		 SearchToolsVisible : flex_opts.searchtoolsvisible,
			
					 jsDirectory : flex_opts.dir+"js/",
					 localeDirectory : flex_opts.dir+"locale/",
					 UIConfig : flex_opts.uiconfig,
					 key : flex_opts.key,
	                 WMode : 'window',
	                 localeChain: 'en_US'
	               }}
	        );
		}
        
        });		
	});
})(jQuery);
