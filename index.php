
<script type="text/javascript" src="js/swfobject/swfobject.js"></script>  
<script type="text/javascript">  
         var swfVersionStr = "10.0.0";  
         var xiSwfUrlStr = "playerProductInstall.swf";  
         var flashvars = {  
                 SwfFile : escape("test.swf"),  
				 cale : 0.6,  
				 ZoomTransition : "easeOut",  
				 ZoomTime : 0.5,  
				 ZoomInterval : 0.1,  
				 FitPageOnLoad : false,  
				 FitWidthOnLoad : true,  
				 PrintEnabled : true,  
				 FullScreenAsMaxWindow : false,  
				 ProgressiveLoading : true,  
				 PrintToolsVisible : true,  
				 ViewModeToolsVisible : true,  
				 ZoomToolsVisible : true,  
				 FullScreenVisible : true,  
				 NavToolsVisible : true,  
				 CursorToolsVisible : true,  
				 SearchToolsVisible : true,  
				 localeChain: "zh_CN"  
         };
  
         var params = {};
         params.quality = "high";  
         params.bgcolor = "#ffffff";  
         params.allowscriptaccess = "sameDomain";  
         params.allowfullscreen = "true";  
         var attributes = {};  
         attributes.id = "FlexPaperViewer";  
         attributes.name = "FlexPaperViewer";  
         swfobject.embedSWF(  
             "FlexPaperViewer.swf", "flashContent",  
             "650", "500",  
             swfVersionStr, xiSwfUrlStr,  
             flashvars, params, attributes);  
         swfobject.createCSS("#flashContent", "display:block;text-align:left;");  
     </script>  
  
 <body>  
  <div style="position:absolute;left:10px;top:10px;">  
      <div id="flashContent">  
      </div>  
     </div>  
</body>  