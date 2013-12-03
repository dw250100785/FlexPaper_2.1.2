/**
 █▒▓▒░ The FlexPaper Project

 This file is part of FlexPaper.

 FlexPaper is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, version 3 of the License.

 FlexPaper is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with FlexPaper.  If not, see <http://www.gnu.org/licenses/>.

 For more information on FlexPaper please see the FlexPaper project
 home page: http://flexpaper.devaldi.com
 */

/**
*
* FlexPaper helper function for retrieving a active FlexPaper instance 
*
*/ 
window.$FlexPaper = window.getDocViewer = window["$FlexPaper"] = function(id){
	var instance = (id==="undefined")?"":id;

    if (window['ViewerMode'] == 'flash') {
		return window["FlexPaperViewer_Instance"+instance].getApi();
	}else if(window['ViewerMode'] == 'html'){
		return window["FlexPaperViewer_Instance"+instance];
	}
};

/**
 *
 * FlexPaper embedding (name of placeholder, config)
 *
 */
window.FlexPaperViewerEmbedding = window.$f = function(id, args) {
    this.id = id;

    var userAgent = navigator.userAgent.toLowerCase();
    var browser = window["eb.browser"] = {
        version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
        safari: /webkit/.test(userAgent),
        opera: /opera/.test(userAgent),
        msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
        mozilla: /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent),
        chrome: /chrome/.test(userAgent)
    };

    var platform = window["eb.platform"] = {
        win:/win/.test(userAgent),
        mac:/mac/.test(userAgent),
        touchdevice : (function(){try {return 'ontouchstart' in document.documentElement;} catch (e) {return false;} })(),
        android : (userAgent.indexOf("android") > -1),
        ios : ((userAgent.match(/iphone/i)) || (userAgent.match(/ipod/i)) || (userAgent.match(/ipad/i)))
    };

    var config = args.config;
    var _SWFFile,_PDFFile,_IMGFiles,_SVGFiles,_IMGFiles_thumbs="",_IMGFiles_highres="",_JSONFile  = "",_jsDirectory="",_cssDirectory="",_localeDirectory="";_WMode = (config.WMode!=null||config.wmmode!=null?config.wmmode||config.WMode:"direct");
    var _uDoc = ((config.DOC !=null)?unescape(config.DOC):null);
    var instance = "FlexPaperViewer_Instance"+((id==="undefined")?"":id);
    var _JSONDataType = (config.JSONDataType!=null)?config.JSONDataType:"json";

    if (_uDoc != null) {
        _SWFFile 	        = translateUrlByFormat(_uDoc,"swf");
        _PDFFile 	        = translateUrlByFormat(_uDoc,"pdf");
        _JSONFile 	        = translateUrlByFormat(_uDoc,_JSONDataType);
        _IMGFiles 	        = translateUrlByFormat(_uDoc,"jpg");
        _SVGFiles           = translateUrlByFormat(_uDoc,"svg");
        _IMGFiles_thumbs    = translateUrlByFormat(_uDoc,"jpg");
        _IMGFiles_highres   = translateUrlByFormat(_uDoc,"jpgpageslice");
    }

    _SWFFile  			= (config.SwfFile!=null?config.SwfFile:_SWFFile);
    _SWFFile  			= (config.SWFFile!=null?config.SWFFile:_SWFFile);
    _PDFFile 			= (config.PDFFile!=null?config.PDFFile:_PDFFile);
    _IMGFiles 			= (config.IMGFiles!=null?config.IMGFiles:_IMGFiles);
    _IMGFiles 			= (config.PageImagePattern!=null?config.PageImagePattern:_IMGFiles);
    _SVGFiles 			= (config.SVGFiles!=null?config.SVGFiles:_SVGFiles);
    _IMGFiles_thumbs    = (config.ThumbIMGFiles!=null?config.ThumbIMGFiles:_IMGFiles_thumbs);
    _IMGFiles_highres   = (config.HighResIMGFiles!=null?config.HighResIMGFiles:_IMGFiles_highres);
    _JSONFile 			= (config.JSONFile!=null?config.JSONFile:_JSONFile);
    _jsDirectory 		= (config.jsDirectory!=null?config.jsDirectory:detectjsdir());
    _cssDirectory 		= (config.cssDirectory!=null?config.cssDirectory:detectcssdir());
    _localeDirectory 	= (config.localeDirectory!=null?config.localeDirectory:"locale/");
    if(_SWFFile!=null && _SWFFile.indexOf("{" )==0 && _SWFFile.indexOf("[*," ) > 0 && _SWFFile.indexOf("]" ) > 0){_SWFFile = escape(_SWFFile);} // split file fix

    window[instance] = flashembed(id, {
        src						    : _jsDirectory+"../FlexPaperViewer.swf",
        version					    : [11, 3],
        expressInstall			    : "js/expressinstall.swf",
        wmode					    : _WMode
    },{
        ElementId               : id,
        SwfFile  				: _SWFFile,
        PdfFile  				: _PDFFile,
        IMGFiles  				: _IMGFiles,
        SVGFiles  				: _SVGFiles,
        JSONFile 				: _JSONFile,
        ThumbIMGFiles           : _IMGFiles_thumbs,
        HighResIMGFiles         : _IMGFiles_highres,
        useCustomJSONFormat 	: config.useCustomJSONFormat,
        JSONPageDataFormat 		: config.JSONPageDataFormat,
        JSONDataType 			: _JSONDataType,
        Scale 					: (config.Scale!=null)?config.Scale:0.8,
        ZoomTransition 			: (config.ZoomTransition!=null)?config.ZoomTransition:'easeOut',
        ZoomTime 				: (config.ZoomTime!=null)?config.ZoomTime:0.5,
        ZoomInterval 			: (config.ZoomInterval)?config.ZoomInterval:0.1,
        FitPageOnLoad 			: (config.FitPageOnLoad!=null)?config.FitPageOnLoad:false,
        FitWidthOnLoad 			: (config.FitWidthOnLoad!=null)?config.FitWidthOnLoad:false,
        FullScreenAsMaxWindow 	: (config.FullScreenAsMaxWindow!=null)?config.FullScreenAsMaxWindow:false,
        ProgressiveLoading 		: (config.ProgressiveLoading!=null)?config.ProgressiveLoading:false,
        MinZoomSize 			: (config.MinZoomSize!=null)?config.MinZoomSize:0.2,
        MaxZoomSize 			: (config.MaxZoomSize!=null)?config.MaxZoomSize:5,
        SearchMatchAll 			: (config.SearchMatchAll!=null)?config.SearchMatchAll:false,
        SearchServiceUrl 		: config.SearchServiceUrl,
        InitViewMode 			: config.InitViewMode,
        BitmapBasedRendering 	: (config.BitmapBasedRendering!=null)?config.BitmapBasedRendering:false,
        StartAtPage 			: (config.StartAtPage!=null&&config.StartAtPage.toString().length>0&&!isNaN(config.StartAtPage))?config.StartAtPage:1,
        PrintPaperAsBitmap		: (config.PrintPaperAsBitmap!=null)?config.PrintPaperAsBitmap:((browser.safari)?true:false),
        AutoAdjustPrintSize		: (config.AutoAdjustPrintSize!=null)?config.AutoAdjustPrintSize:true,

        EnableCornerDragging 	: ((config.EnableCornerDragging!=null)?config.EnableCornerDragging:true), // FlexPaper Zine parameter
        BackgroundColor 		: config.BackgroundColor, // FlexPaper Zine parameter
        PanelColor 				: config.PanelColor, // FlexPaper Zine parameter
        BackgroundAlpha         : config.BackgroundAlpha, // FlexPaper Zine parameter
        UIConfig                : config.UIConfig,  // FlexPaper Zine parameter

        ViewModeToolsVisible 	: ((config.ViewModeToolsVisible!=null)?config.ViewModeToolsVisible:true),
        ZoomToolsVisible 		: ((config.ZoomToolsVisible!=null)?config.ZoomToolsVisible:true),
        NavToolsVisible 		: ((config.NavToolsVisible!=null)?config.NavToolsVisible:true),
        CursorToolsVisible 		: ((config.SearchToolsVisible!=null)?config.CursorToolsVisible:true),
        SearchToolsVisible 		: ((config.SearchToolsVisible!=null)?config.SearchToolsVisible:true),
        AnnotationToolsVisible  : ((config.AnnotationToolsVisible!=null)?config.AnnotationToolsVisible:true), // Annotations viewer parameter

        StickyTools				: config.StickyTools,
        Toolbar                 : config.Toolbar,
        BottomToolbar           : config.BottomToolbar,
        DocSizeQueryService 	: config.DocSizeQueryService,
	
	PreviewMode			: (config.InitViewMode!="Portrait")?config.PreviewMode:false,
        RenderingOrder 			: config.RenderingOrder,

        localeChain 			: (config.localeChain!=null)?config.localeChain:"en_US",
        jsDirectory 			: _jsDirectory,
        cssDirectory 			: _cssDirectory,
        localeDirectory			: _localeDirectory,
        key 					: config.key
    });
};

function detectjsdir(){
    if(jQuery('script[src$="flexpaper.js"]').length>0){
        return jQuery('script[src$="flexpaper.js"]').attr('src').replace('flexpaper.js','');
    }else{
        return "js/"
    }
}

function detectcssdir(){
    if(jQuery('script[src$="flexpaper.css"]').length>0){
        return jQuery('link[href="flexpaper.css"]').href('src').replace('flexpaper.css','');
    }else{
        return "css/"
    }
}

function translateUrlByDocument(url,document){
	return (url!=null && url.indexOf('{doc}') > 0 ? url.replace("{doc}", document):null);	
}

function translateUrlByFormat(url,format){
	if(url.indexOf("{") == 0 && format != "swf"){ // loading in split file mode
		url = url.substring(1,url.lastIndexOf(","));

        if(format!="pdf"){
            url = url.replace("[*,0]","{page}")
        }
	}

    if(format =="jpgpageslice"){
        url = url + "&sector={sector}";
    }

    return (url!=null && url.indexOf('{format}') > 0 ? url.replace("{format}", format):null);
}

/** 
 * 
 * FlexPaper embedding functionality. Based on FlashEmbed
 *
 */

(function() {

	var  IE = document.all,
		 URL = 'http://www.adobe.com/go/getflashplayer',
		 JQUERY = typeof jQuery == 'function', 
		 RE = /(\d+)[^\d]+(\d+)[^\d]*(\d*)/,
         MOBILE = (function(){try {return 'ontouchstart' in document.documentElement;} catch (e) {return false;} })(),
		 GLOBAL_OPTS = { 
			// very common opts
			width: '100%',
			height: '100%',		
			id: "_" + ("" + Math.random()).slice(9),
			
			// flashembed defaults
			allowfullscreen: true,
			allowscriptaccess: 'always',
			quality: 'high',
            allowFullScreenInteractive : true,
			
			// flashembed specific options
			version: [10, 0],
			onFail: null,
			expressInstall: null, 
			w3c: false,
			cachebusting: false  		 		 
	};

    window.isTouchScreen = MOBILE;

	if (window.attachEvent) {
		window.attachEvent("onbeforeunload", function() {
			__flash_unloadHandler = function() {};
			__flash_savedUnloadHandler = function() {};
		});
	}
	
	// simple extend
	function extend(to, from) {
		if (from) {
			for (var key in from) {
				if (from.hasOwnProperty(key)) {
					to[key] = from[key];
				}
			}
		} 
		return to;
	}

    // used by Flash to dispatch a event properly
    window.dispatchJQueryEvent = function (elementId,eventName,args){
        jQuery('#'+elementId).trigger(eventName,args);
    }

	// used by asString method	
	function map(arr, func) {
		var newArr = []; 
		for (var i in arr) {
			if (arr.hasOwnProperty(i)) {
				newArr[i] = func(arr[i]);
			}
		}
		return newArr;
	}

	window.flashembed = function(root, opts, conf) {
		// root must be found / loaded	
		if (typeof root == 'string') {
			root = document.getElementById(root.replace("#", ""));
		}
		
		// not found
		if (!root) { return; }
		
		root.onclick = function(){return false;}
		
		if (typeof opts == 'string') {
			opts = {src: opts};	
		}

		return new Flash(root, extend(extend({}, GLOBAL_OPTS), opts), conf); 
	};	
	
	// flashembed "static" API
	var f = extend(window.flashembed, {
		
		conf: GLOBAL_OPTS,
	
		getVersion: function()  {
			var fo, ver;
			
			try {
				ver = navigator.plugins["Shockwave Flash"].description.slice(16); 
			} catch(e) {
				
				try  {
					fo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
					ver = fo && fo.GetVariable("$version");
					
				} catch(err) {
                try  {
                    fo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                    ver = fo && fo.GetVariable("$version");  
                } catch(err2) { } 						
				} 
			}
			
			ver = RE.exec(ver);
			return ver ? [ver[1], ver[3]] : [0, 0];
		},
		
		asString: function(obj) { 

			if (obj === null || obj === undefined) { return null; }
			var type = typeof obj;
			if (type == 'object' && obj.push) { type = 'array'; }
			
			switch (type){  
				
				case 'string':
					obj = obj.replace(new RegExp('(["\\\\])', 'g'), '\\$1');
					
					// flash does not handle %- characters well. transforms "50%" to "50pct" (a dirty hack, I admit)
					obj = obj.replace(/^\s?(\d+\.?\d+)%/, "$1pct");
					return '"' +obj+ '"';
					
				case 'array':
					return '['+ map(obj, function(el) {
						return f.asString(el);
					}).join(',') +']'; 
					
				case 'function':
					return '"function()"';
					
				case 'object':
					var str = [];
					for (var prop in obj) {
						if (obj.hasOwnProperty(prop)) {
							str.push('"'+prop+'":'+ f.asString(obj[prop]));
						}
					}
					return '{'+str.join(',')+'}';
			}
			
			// replace ' --> "  and remove spaces
			return String(obj).replace(/\s/g, " ").replace(/\'/g, "\"");
		},
		
		getHTML: function(opts, conf) {

			opts = extend({}, opts);
			opts.id = opts.id + (" " + Math.random()).slice(9);
			
			/******* OBJECT tag and it's attributes *******/
			var html = '<object width="' + opts.width + 
				'" height="' + opts.height + 
				'" id="' + opts.id + 
				'" name="' + opts.id + '"';
			
			if (opts.cachebusting) {
				opts.src += ((opts.src.indexOf("?") != -1 ? "&" : "?") + Math.random());		
			}			
			
			if (opts.w3c || !IE) {
				html += ' data="' +opts.src+ '" type="application/x-shockwave-flash"';		
			} else {
				html += ' classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';	
			}
			
			html += '>'; 
			
			/******* nested PARAM tags *******/
			if (opts.w3c || IE) {
				html += '<param name="movie" value="' +opts.src+ '" />'; 	
			} 
			
			// not allowed params
			opts.width = opts.height = opts.id = opts.w3c = opts.src = null;
			opts.onFail = opts.version = opts.expressInstall = null;
			
			for (var key in opts) {
				if (opts[key]) {
					html += '<param name="'+ key +'" value="'+ opts[key] +'" />';
				}
			}	
		
			/******* FLASHVARS *******/
			var vars = "";
			
			if (conf) {
				for (var k in conf) {
                    if ((typeof conf[k] != "undefined") && (typeof conf[k] != "unknown") && k!='Toolbar' && k!='BottomToolbar') {
						var val = conf[k];
                        if(k=="JSONFile"){val = escape(val);}
						vars += k +'='+ (/function|object/.test(typeof val) ? f.asString(val) : val) + '&';
					}
				}
				vars = vars.slice(0, -1);
				html += '<param name="flashvars" value=\'' + vars + '\' />';
			}
			
			html += "</object>";	
			
			return html;				
		},
		
		isSupported: function(ver) {
			return VERSION[0] > ver[0] || VERSION[0] == ver[0] && VERSION[1] >= ver[1];			
		}		
		
	});
	
	var VERSION = f.getVersion(); 
	
	function Flash(root, opts, conf) {
        var browser = window["eb.browser"];
        var platform = window["eb.platform"];

        var supportsHTML4   = (browser.mozilla && browser.version.split(".")[0] >= 3) ||
            (browser.chrome) ||
            (browser.msie && browser.version.split(".")[0] >= 8) ||
            (browser.safari) ||
            (browser.opera);

        // Default to a rendering mode if its not set
        if(!conf.RenderingOrder && conf.SwfFile !=  null){conf.RenderingOrder = "flash";}
        if(!conf.RenderingOrder && conf.JSONFile !=  null && conf.JSONFile){conf.RenderingOrder = "html";}
        if(!conf.RenderingOrder && conf.PdfFile !=  null){conf.RenderingOrder = "html5";}

        // version is ok
		if ((conf.RenderingOrder.indexOf('flash') == 0 || (conf.RenderingOrder.indexOf('flash')>0 &&!supportsHTML4)) && f.isSupported(opts.version)) {
            if(conf.Toolbar){
                var wrapper = jQuery(root).wrap("<div style='"+jQuery(root).attr('style')+"'></div>").parent();
                jQuery(root).css({
                    left : '0px',
                    top: '0px',
                    position : 'relative',
                    width : '100%',
                    height : '100%'
                }).addClass('flexpaper_viewer');

                wrapper.prepend(jQuery(conf.Toolbar));
            }

			window['ViewerMode'] = 'flash';
			root.innerHTML = f.getHTML(opts, conf);

            if(conf.BottomToolbar && conf.AnnotationToolsVisible!=false){
                jQuery.get(conf.BottomToolbar,function(toolbarData){
                    wrapper.append(toolbarData);
                });
            }

            if(conf.Toolbar){
                // initialize event handlers for flash
                jQuery.getScript(conf.jsDirectory+'flexpaper_flashhandlers_htmlui.js', function() {
                    FLEXPAPER.bindFlashEventHandlers(root);
                });
            }
			
		// express install
		} else if ((conf.RenderingOrder.indexOf('flash') == 0) && opts.expressInstall && f.isSupported([6, 65])) {
			window['ViewerMode'] = 'flash';

            if(conf.Toolbar){
                var wrapper = jQuery(root).wrap("<div style='"+jQuery(root).attr('style')+"'></div>").parent();
                jQuery(root).css({
                    left : '0px',
                    top: '0px',
                    position : 'relative',
                    width : '100%',
                    height : '100%'
                }).addClass('flexpaper_viewer');

                wrapper.prepend(jQuery(conf.Toolbar));
            }

			root.innerHTML = f.getHTML(extend(opts, {src: opts.expressInstall}), {
				MMredirectURL: location.href,
				MMplayerType: 'PlugIn',
				MMdoctitle: document.title
			});

            if(conf.BottomToolbar && conf.AnnotationToolsVisible!=false){
                jQuery.get(conf.BottomToolbar,function(toolbarData){
                    wrapper.append(toolbarData);
                });
            }

            if(conf.Toolbar){
                // initialize event handlers for flash
                jQuery.getScript(conf.jsDirectory+'flexpaper_flashhandlers_htmlui.js', function() {
                    FLEXPAPER.bindFlashEventHandlers(root);
                });
            }

		} else { //use html viewer or die
			window['ViewerMode'] = 'html';
			//jQuery.noConflict();
			if(true){
				jQuery(document).ready(function() {
                    if(conf.Toolbar){jQuery.fn.showFullScreen = function(){$FlexPaper(jQuery(this).attr('id')).openFullScreen();}}

					jQuery.getScript(conf.jsDirectory+'FlexPaperViewer.js', function() {
						var viewerId = jQuery(root).attr('id');
						var supportsPDFJS 	= 	(browser.mozilla && browser.version.split(".")[0] >= 4) ||
												(browser.chrome && browser.version.split(".") >= 535) ||
												(browser.msie && browser.version.split(".")[0] >= 10) ||
												(browser.safari && browser.version.split(".")[0] >= 534 /*&& !platform.ios*/);

                        // If rendering order isnt set but the formats are supplied then assume the rendering order.
                        if(!conf.RenderingOrder){
                            conf.RenderingOrder = "";
                            if(conf.PdfFile!=null){conf.RenderingOrder = "html5";}
                            if(conf.SwfFile!=null){conf.RenderingOrder += (conf.RenderingOrder.length>0?",":"")+"flash"}
                        }

                        // add fallback for html if not specified
                        if(conf.JSONFile!=null){
                            if(platform.ios || platform.android){ // ios should use html as preferred rendering mode if available.
                                conf.RenderingOrder = "html" + (conf.RenderingOrder.length>0?",":"") + conf.RenderingOrder;
                            }else{
                                conf.RenderingOrder += (conf.RenderingOrder.length>0?",":"")+"html";
                            }
                        }

						var oRenderingList 	= conf.RenderingOrder.split(",");
						var pageRenderer 	= null;

                        // if PDFJS isn't supported and the html formats are supplied, then use these as primary format
                        if(oRenderingList && oRenderingList.length==1 && conf.JSONFile!=null && conf.JSONFile.length>0 && conf.IMGFiles!=null && conf.IMGFiles.length>0 && !supportsPDFJS){
                            oRenderingList[1] = conf.RenderingOrder[0];
                            oRenderingList[0] = 'html';
                        }

                        if(conf.PdfFile!=null && conf.PdfFile.length>0 && conf.RenderingOrder.split(",").length>=1 && supportsPDFJS && (oRenderingList[0] == 'html5' || (oRenderingList.length > 1 && oRenderingList[0] == 'flash' && oRenderingList[1] == 'html5')) && !(platform.touchdevice && oRenderingList.length > 1 && oRenderingList[1] == 'html')){
							pageRenderer = new CanvasPageRenderer(viewerId,conf.PdfFile,conf.jsDirectory,
                                {
                                    jsonfile : conf.JSONFile,
                                    pageImagePattern : conf.IMGFiles,
                                    pageThumbImagePattern : conf.ThumbIMGFiles,
                                    compressedJSONFormat : !conf.useCustomJSONFormat,
                                    JSONPageDataFormat : conf.JSONPageDataFormat,
                                    JSONDataType : conf.JSONDataType
                                });
						}else{
							pageRenderer = new ImagePageRenderer(
							viewerId,
							{
				   			jsonfile : conf.JSONFile,
							pageImagePattern : conf.IMGFiles,
                            pageThumbImagePattern : conf.ThumbIMGFiles,
                            pageHighResImagePattern : conf.HighResIMGFiles,
                            pageSVGImagePattern : conf.SVGFiles,
							compressedJSONFormat : !conf.useCustomJSONFormat,
							JSONPageDataFormat : conf.JSONPageDataFormat,
							JSONDataType : conf.JSONDataType,
                            SVGMode : conf.RenderingOrder.toLowerCase().indexOf('svg')>=0
							},
                            conf.jsDirectory);
						}					

						var instance = "FlexPaperViewer_Instance"+((viewerId==="undefined")?"":viewerId);
						window[instance] = new FlexPaperViewer_HTML({
							rootid 		    : viewerId,
                            Toolbar 	    : ((conf.Toolbar!=null)?conf.Toolbar:null),
                            BottomToolbar   : ((conf.BottomToolbar!=null)?conf.BottomToolbar:null),
				            instanceid 	: instance,
				            document: {
				                SWFFile 				: conf.SwfFile,
				                IMGFiles 				: conf.IMGFiles,
                                ThumbIMGFiles           : conf.ThumbIMGFiles,
				                JSONFile 				: conf.JSONFile,
				                PDFFile 				: conf.PdfFile,
								Scale 					: conf.Scale,
								FitPageOnLoad 			: conf.FitPageOnLoad,
								FitWidthOnLoad 			: conf.FitWidthOnLoad,
								MinZoomSize 			: conf.MinZoomSize,
								MaxZoomSize 			: conf.MaxZoomSize,
								SearchMatchAll 			: conf.SearchMatchAll,
								InitViewMode 			: conf.InitViewMode,
								PreviewMode			: conf.PreviewMode,
								StartAtPage 			: conf.StartAtPage,
								RenderingOrder 			: conf.RenderingOrder,
								useCustomJSONFormat 	: conf.useCustomJSONFormat,
								JSONPageDataFormat 		: conf.JSONPageDataFormat,
								JSONDataType 			: conf.JSONDataType,
								ZoomInterval 			: conf.ZoomInterval,
								ViewModeToolsVisible 	: conf.ViewModeToolsVisible,
								ZoomToolsVisible 		: conf.ZoomToolsVisible,
								NavToolsVisible 		: conf.NavToolsVisible,
								CursorToolsVisible 		: conf.CursorToolsVisible,
								SearchToolsVisible 		: conf.SearchToolsVisible,
                                AnnotationToolsVisible  : conf.AnnotationToolsVisible,
								StickyTools 			: conf.StickyTools,
								PrintPaperAsBitmap 		: conf.PrintPaperAsBitmap,
								AutoAdjustPrintSize 	: conf.AutoAdjustPrintSize,
								EnableCornerDragging	: conf.EnableCornerDragging,
                                UIConfig                : conf.UIConfig,
                                BackgroundColor			: conf.BackgroundColor, // FlexPaper Zine parameter
                                PanelColor				: conf.PanelColor, // FlexPaper Zine parameter

								localeChain 			: conf.localeChain
				            }, 
							renderer 			: pageRenderer,
							key 				: conf.key,
							jsDirectory 		: conf.jsDirectory,
							localeDirectory 	: conf.localeDirectory,
							cssDirectory 		: conf.cssDirectory,
							docSizeQueryService : conf.DocSizeQueryService
				        });

						window[instance].initialize();
						window[instance].bindEvents();
						
						window[instance]['load'] = window[instance].loadFromUrl;
						window[instance]['loadDoc'] = window[instance].loadDoc;
						window[instance]['fitWidth'] = window[instance].fitwidth;
						window[instance]['fitHeight'] = window[instance].fitheight;
						window[instance]['gotoPage'] = window[instance].gotoPage;
						window[instance]['getCurrPage'] = window[instance].getCurrPage;
						window[instance]['getTotalPages'] = window[instance].getTotalPages;
						window[instance]['nextPage'] = window[instance].next;
						window[instance]['prevPage'] = window[instance].previous;
						window[instance]['setZoom'] = window[instance].Zoom;
						window[instance]['Zoom'] = window[instance].Zoom;
						window[instance]['openFullScreen'] = window[instance].openFullScreen;
						window[instance]['sliderChange'] = window[instance].sliderChange;
						window[instance]['searchText'] = window[instance].searchText;
                        window[instance]['resize'] = window[instance].resize;
                        window[instance]['rotate'] = window[instance].rotate;
						//window[instance]['nextSearchMatch'] = window[instance].nextSearchMatch; //TBD
						//window[instance]['prevSearchMatch'] = window[instance].nextSearchMatch; //TBD
						window[instance]['switchMode'] = window[instance].switchMode;
						window[instance]['printPaper'] = window[instance].printPaper;
						//window[instance]['highlight'] = window[instance].highlight; //TBD
						//window[instance]['postSnapshot'] = window[instance].postSnapshot; //TBD
						window[instance]['setCurrentCursor'] = window[instance].setCurrentCursor;
                        window[instance]['showFullScreen'] = window[instance].openFullScreen;

		   			    pageRenderer.initialize(function(){
		   			    	 //console.profile('FlexPaper init-postinit');
							  window[instance].document.numPages = pageRenderer.getNumPages();
							  window[instance].document.dimensions = pageRenderer.getDimensions();
							  window[instance].show();

                              jQuery(root).trigger('onDocumentLoaded',pageRenderer.getNumPages());
						});					
					});
				});
			}else{
				// fail #2.1 custom content inside container
				if (!root.innerHTML.replace(/\s/g, '')) {
					var pageHost = ((document.location.protocol == "https:") ? "https://" :	"http://");
					
					root.innerHTML = 
						"<h2>Your browser is not compatible with FlexPaper</h2>" + 
						"<h3>Upgrade to a newer browser or download Adobe Flash Player 10 or higher.</h3>" + 
						"<p>Click on the icon below to download the latest version of Adobe Flash" + 
						"<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
											+ pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>";
																							
					if (root.tagName == 'A') {	
						root.onclick = function() {
							location.href = URL;
						};
					}				
				}
				
				// onFail
				if (opts.onFail) {
					var ret = opts.onFail.call(this);
					if (typeof ret == 'string') { root.innerHTML = ret; }	
				}		
			}	
		}
		
		// http://flowplayer.org/forum/8/18186#post-18593
		if (IE) {
			window[opts.id] = document.getElementById(opts.id);
		} 
		
		// API methods for callback
		extend(this, {
				
			getRoot: function() {
				return root;	
			},
			
			getOptions: function() {
				return opts;	
			},

			
			getConf: function() {
				return conf;	
			}, 
			
			getApi: function() {
				return root.firstChild;	
			}
			
		}); 
	}

	// setup jquery support
	if (JQUERY) {
		jQuery.fn.flashembed = function(opts, conf) {
			return this.each(function() { 
				jQuery(this).data("flashembed", flashembed(this, opts, conf));
			});
		};

        jQuery.fn.FlexPaperViewer = function(args){
            var embed = new FlexPaperViewerEmbedding(this.attr('id'),args);
            this.element = jQuery('#'+embed.id);
            return this.element;
        };
	}else{
        throw new Error("jQuery missing!");
    }
})();

function getIEversion()
// Returns the version of Internet Explorer or a -1
// (indicating the use of another browser).
{
  var rv = -1; // Return value assumes failure.
  if (navigator.appName == 'Microsoft Internet Explorer')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  return rv;
}


window.unsupportedPDFJSieversion = getIEversion()>0 && getIEversion()<9;
// Checking if the typed arrays are supported
(function checkTypedArrayCompatibility() {
  if (typeof Uint8Array !== 'undefined') {
    // some mobile versions do not support subarray (e.g. safari 5 / iOS)
    if (typeof Uint8Array.prototype.subarray === 'undefined') {
        Uint8Array.prototype.subarray = function subarray(start, end) {
          return new Uint8Array(this.slice(start, end));
        };
        Float32Array.prototype.subarray = function subarray(start, end) {
          return new Float32Array(this.slice(start, end));
        };
    }

    // some mobile version might not support Float64Array
    if (typeof Float64Array === 'undefined')
      window.Float64Array = Float32Array;

    return;
  }

  function subarray(start, end) {
    return new TypedArray(this.slice(start, end));
  }

  function setArrayOffset(array, offset) {
    if (arguments.length < 2)
      offset = 0;
    for (var i = 0, n = array.length; i < n; ++i, ++offset)
      this[offset] = array[i] & 0xFF;
  }

  function TypedArray(arg1) {
    var result;
    if (typeof arg1 === 'number') {
      result = [];
      for (var i = 0; i < arg1; ++i)
        result[i] = 0;
    } else if ('slice' in arg1) {
      result = arg1.slice(0);
    } else {
      result = [];
      for (var i = 0, n = arg1.length; i < n; ++i) {
        result[i] = arg1[i];
      }
    }

    result.subarray = subarray;
    result.buffer = result;
    result.byteLength = result.length;
    result.set = setArrayOffset;

    if (typeof arg1 === 'object' && arg1.buffer)
      result.buffer = arg1.buffer;

    return result;
  }

  window.Uint8Array = TypedArray;

  // we don't need support for set, byteLength for 32-bit array
  // so we can use the TypedArray as well
  window.Uint32Array = TypedArray;
  window.Int32Array = TypedArray;
  window.Uint16Array = TypedArray;
  window.Float32Array = TypedArray;
  window.Float64Array = TypedArray;
})();

// Object.create() ?
(function checkObjectCreateCompatibility() {
  if (typeof Object.create !== 'undefined')
    return;

  Object.create = function objectCreate(proto) {
    function Constructor() {}
    Constructor.prototype = proto;
    return new Constructor();
  };
})();

// Object.defineProperty() ?
(function checkObjectDefinePropertyCompatibility() {
  if (typeof Object.defineProperty !== 'undefined') {
    var definePropertyPossible = true;
    try {
      // some browsers (e.g. safari) cannot use defineProperty() on DOM objects
      // and thus the native version is not sufficient
      Object.defineProperty(new Image(), 'id', { value: 'test' });
      // ... another test for android gb browser for non-DOM objects
      eval("var Test = function Test() {};Test.prototype = { get id() { } };Object.defineProperty(new Test(), 'id',{ value: '', configurable: true, enumerable: true, writable: false });");
    } catch (e) {
      definePropertyPossible = false;
    }
    if (definePropertyPossible) return;
  }

  Object.defineProperty = function objectDefineProperty(obj, name, def) {
  if(window.unsupportedPDFJSieversion){return;}
    delete obj[name];
    if ('get' in def)
      obj.__defineGetter__(name, def['get']);
    if ('set' in def)
      obj.__defineSetter__(name, def['set']);
    if ('value' in def) {
      obj.__defineSetter__(name, function objectDefinePropertySetter(value) {
        this.__defineGetter__(name, function objectDefinePropertyGetter() {
          return value;
        });
        return value;
      });
      obj[name] = def.value;
    }
  };
})();

// Object.keys() ?
(function checkObjectKeysCompatibility() {
  if (typeof Object.keys !== 'undefined')
    return;

  Object.keys = function objectKeys(obj) {
    var result = [];
    for (var i in obj) {
      if (obj.hasOwnProperty(i))
        result.push(i);
    }
    return result;
  };
})();

// No readAsArrayBuffer ?
(function checkFileReaderReadAsArrayBuffer() {
  if (typeof FileReader === 'undefined')
    return; // FileReader is not implemented
  var frPrototype = FileReader.prototype;
  // Older versions of Firefox might not have readAsArrayBuffer
  if ('readAsArrayBuffer' in frPrototype)
    return; // readAsArrayBuffer is implemented
  Object.defineProperty(frPrototype, 'readAsArrayBuffer', {
    value: function fileReaderReadAsArrayBuffer(blob) {
      var fileReader = new FileReader();
      var originalReader = this;
      fileReader.onload = function fileReaderOnload(evt) {
        var data = evt.target.result;
        var buffer = new ArrayBuffer(data.length);
        var uint8Array = new Uint8Array(buffer);

        for (var i = 0, ii = data.length; i < ii; i++)
          uint8Array[i] = data.charCodeAt(i);

        Object.defineProperty(originalReader, 'result', {
          value: buffer,
          enumerable: true,
          writable: false,
          configurable: true
        });

        var event = document.createEvent('HTMLEvents');
        event.initEvent('load', false, false);
        originalReader.dispatchEvent(event);
      };
      fileReader.readAsBinaryString(blob);
    }
  });
})();

// No XMLHttpRequest.response ?
(function checkXMLHttpRequestResponseCompatibility() {
  if(window.unsupportedPDFJSieversion){return;}

  var xhrPrototype = XMLHttpRequest.prototype;
  if (!('overrideMimeType' in xhrPrototype)) {
    // IE10 might have response, but not overrideMimeType
    Object.defineProperty(xhrPrototype, 'overrideMimeType', {
      value: function xmlHttpRequestOverrideMimeType(mimeType) {}
    });
  }
  if ('response' in xhrPrototype ||
      'mozResponseArrayBuffer' in xhrPrototype ||
      'mozResponse' in xhrPrototype ||
      'responseArrayBuffer' in xhrPrototype)
    return;
  // IE9 ?
  if (typeof VBArray !== 'undefined') {
    Object.defineProperty(xhrPrototype, 'response', {
      get: function xmlHttpRequestResponseGet() {
        return new Uint8Array(new VBArray(this.responseBody).toArray());
      }
    });
    return;
  }

  // other browsers
  function responseTypeSetter() {
    // will be only called to set "arraybuffer"
    this.overrideMimeType('text/plain; charset=x-user-defined');
  }
  if (typeof xhrPrototype.overrideMimeType === 'function') {
    Object.defineProperty(xhrPrototype, 'responseType',
                          { set: responseTypeSetter });
  }
  function responseGetter() {
    var text = this.responseText;
    var i, n = text.length;
    var result = new Uint8Array(n);
    for (i = 0; i < n; ++i)
      result[i] = text.charCodeAt(i) & 0xFF;
    return result;
  }
  Object.defineProperty(xhrPrototype, 'response', { get: responseGetter });
})();

// window.btoa (base64 encode function) ?
(function checkWindowBtoaCompatibility() {
  if ('btoa' in window)
    return;

  var digits =
    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

  window.btoa = function windowBtoa(chars) {
    var buffer = '';
    var i, n;
    for (i = 0, n = chars.length; i < n; i += 3) {
      var b1 = chars.charCodeAt(i) & 0xFF;
      var b2 = chars.charCodeAt(i + 1) & 0xFF;
      var b3 = chars.charCodeAt(i + 2) & 0xFF;
      var d1 = b1 >> 2, d2 = ((b1 & 3) << 4) | (b2 >> 4);
      var d3 = i + 1 < n ? ((b2 & 0xF) << 2) | (b3 >> 6) : 64;
      var d4 = i + 2 < n ? (b3 & 0x3F) : 64;
      buffer += (digits.charAt(d1) + digits.charAt(d2) +
                 digits.charAt(d3) + digits.charAt(d4));
    }
    return buffer;
  };
})();

// Function.prototype.bind ?
(function checkFunctionPrototypeBindCompatibility() {
  if (typeof Function.prototype.bind !== 'undefined')
    return;

  Function.prototype.bind = function functionPrototypeBind(obj) {
    var fn = this, headArgs = Array.prototype.slice.call(arguments, 1);
    var bound = function functionPrototypeBindBound() {
      var args = Array.prototype.concat.apply(headArgs, arguments);
      return fn.apply(obj, args);
    };
    return bound;
  };
})();

// IE9/10 text/html data URI
(function checkDataURICompatibility() {
  if (!('documentMode' in document) ||
      document.documentMode !== 9 && document.documentMode !== 10)
    return;
  // overriding the src property
  var originalSrcDescriptor = Object.getOwnPropertyDescriptor(
    HTMLIFrameElement.prototype, 'src');
  Object.defineProperty(HTMLIFrameElement.prototype, 'src', {
    get: function htmlIFrameElementPrototypeSrcGet() { return this.$src; },
    set: function htmlIFrameElementPrototypeSrcSet(src) {
      this.$src = src;
      if (src.substr(0, 14) != 'data:text/html') {
        originalSrcDescriptor.set.call(this, src);
        return;
      }
      // for text/html, using blank document and then
      // document's open, write, and close operations
      originalSrcDescriptor.set.call(this, 'about:blank');
      setTimeout((function htmlIFrameElementPrototypeSrcOpenWriteClose() {
        var doc = this.contentDocument;
        doc.open('text/html');
        doc.write(src.substr(src.indexOf(',') + 1));
        doc.close();
      }).bind(this), 0);
    },
    enumerable: true
  });
})();

// HTMLElement dataset property
(function checkDatasetProperty() {
if(window.unsupportedPDFJSieversion){return;}
  var div = document.createElement('div');
  if ('dataset' in div)
    return; // dataset property exists

  Object.defineProperty(HTMLElement.prototype, 'dataset', {
    get: function() {
      if (this._dataset)
        return this._dataset;

      var dataset = {};
      for (var j = 0, jj = this.attributes.length; j < jj; j++) {
        var attribute = this.attributes[j];
        if (attribute.name.substring(0, 5) != 'data-')
          continue;
        var key = attribute.name.substring(5).replace(/\-([a-z])/g,
          function(all, ch) { return ch.toUpperCase(); });
        dataset[key] = attribute.value;
      }

      Object.defineProperty(this, '_dataset', {
        value: dataset,
        writable: false,
        enumerable: false
      });
      return dataset;
    },
    enumerable: true
  });
})();

// HTMLElement classList property
(function checkClassListProperty() {
if(window.unsupportedPDFJSieversion){return;}
  var div = document.createElement('div');
  if ('classList' in div)
    return; // classList property exists

  function changeList(element, itemName, add, remove) {
    var s = element.className || '';
    var list = s.split(/\s+/g);
    if (list[0] === '') list.shift();
    var index = list.indexOf(itemName);
    if (index < 0 && add)
      list.push(itemName);
    if (index >= 0 && remove)
      list.splice(index, 1);
    element.className = list.join(' ');
  }

  var classListPrototype = {
    add: function(name) {
      changeList(this.element, name, true, false);
    },
    remove: function(name) {
      changeList(this.element, name, false, true);
    },
    toggle: function(name) {
      changeList(this.element, name, true, true);
    }
  };

  Object.defineProperty(HTMLElement.prototype, 'classList', {
    get: function() {
      if (this._classList)
        return this._classList;

      var classList = Object.create(classListPrototype, {
        element: {
          value: this,
          writable: false,
          enumerable: true
        }
      });
      Object.defineProperty(this, '_classList', {
        value: classList,
        writable: false,
        enumerable: false
      });
      return classList;
    },
    enumerable: true
  });
})();

// Check console compatability
(function checkConsoleCompatibility() {
  if (!('console' in window)) {
    window.console = {
      log: function() {},
      error: function() {}
    };
  } else if (!('bind' in console.log)) {
    // native functions in IE9 might not have bind
    console.log = (function(fn) {
      return function(msg) { return fn(msg); };
    })(console.log);
    console.error = (function(fn) {
      return function(msg) { return fn(msg); };
    })(console.error);
  }
})();

// Check onclick compatibility in Opera
(function checkOnClickCompatibility() {
  // workaround for reported Opera bug DSK-354448:
  // onclick fires on disabled buttons with opaque content
  function ignoreIfTargetDisabled(event) {
    if (isDisabled(event.target)) {
      event.stopPropagation();
    }
  }
  function isDisabled(node) {
    return node.disabled || (node.parentNode && isDisabled(node.parentNode));
  }
  if (navigator.userAgent.indexOf('Opera') != -1) {
    // use browser detection since we cannot feature-check this bug
    document.addEventListener('click', ignoreIfTargetDisabled, true);
  }
})();

// Checks if navigator.language is supported
(function checkNavigatorLanguage() {
if(window.unsupportedPDFJSieversion){return;}
  if ('language' in navigator)
    return;
  Object.defineProperty(navigator, 'language', {
    get: function navigatorLanguage() {
      var language = navigator.userLanguage || 'en-US';
      return language.substring(0, 2).toLowerCase() +
        language.substring(2).toUpperCase();
    },
    enumerable: true
  });
})();
