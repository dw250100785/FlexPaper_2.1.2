<?php
/*
Plugin Name: FlexPaper
Plugin URI: http://flexpaper.devaldi.com/wordpress.jsp
Description: A plugin for wordpress enabling the document viewer FlexPaper to be used on any Wordpress blog
Version: 1.0.2
Author: Devaldi Ltd
Author URI: http://flexpaper.devaldi.com
License: GPL3
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

include( plugin_dir_path( __FILE__ ) . 'common.php');

$swf_dir		= get_option('flexpaper_workingdirectory');
$pdf_dir		= get_option('flexpaper_pdfdirectory');


// this request handler processes requests for PDF,JSON, PNG and SWF files
function plugin_parse_request($wp) {
    // only process requests with "flexpaper-plugin=view-document"
    if (array_key_exists('flexpaper-plugin', $wp->query_vars) 
            && $wp->query_vars['flexpaper-plugin'] == 'view-document') {

		include( plugin_dir_path( __FILE__ ) . 'pdf2swf_php5.php');
		include( plugin_dir_path( __FILE__ ) . 'pdf2json_php5.php');	
		include( plugin_dir_path( __FILE__ ) . 'splitpdf_php5.php');
		include( plugin_dir_path( __FILE__ ) . 'swfrender_php5.php');		
		
		$swf_dir		= get_option('flexpaper_workingdirectory');
		$pdf_dir		= get_option('flexpaper_pdfdirectory');
		$allowcache		= (get_option('flexpaper_allowcache')!=null)?get_option('flexpaper_allowcache'):"true";
		
		if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	){
			$path_to_pdf2swf 	= 'C:\Program Files\SWFTools\\';
			$path_to_pdf2json 	= 'C:\Program Files\PDF2JSON\\';
			$path_to_pdftk		= 'C:\Program Files\PDF Labs\PDFtk Server\bin\\';
		}else{
			$path_to_pdf2swf 	= '/usr/local/bin/';
			$path_to_pdf2json 	= '/usr/local/bin/';
			$path_to_pdftk		= '/usr/bin/';
		}
		
		if(strlen(get_option('flexpaper_swftools_installdir'))>0){
			$path_to_pdf2swf = get_option('flexpaper_swftools_installdir');
		}
		
		
		if(strlen(get_option('flexpaper_pdf2json_installdir'))>0){
			$path_to_pdf2json = get_option('flexpaper_pdf2json_installdir');
		}
		
		if(strlen(get_option('flexpaper_pdftk_installdir'))>0){
			$path_to_pdftk = get_option('flexpaper_pdftk_installdir');
		}
		
		
		if(strlen($swf_dir)<2){
			$swf_dir = plugin_dir_path( __FILE__ ) . "docs/";	
		}


		if(strlen($pdf_dir)<2){
			$pdf_dir = plugin_dir_path( __FILE__ ) . "pdf/";	
		}

		$doc 			= $_GET["doc"];
		$callback		= "";
	
		if(!endsWith($doc,'.pdf')){$pdfdoc 	= $doc . ".pdf";}else{$pdfdoc 	= $doc;}
		if(isset($_GET["page"])){$page = $_GET["page"];}else{$page = "";}
		if(isset($_GET["format"])){$format=$_GET["format"];}else{$format="swf";}
		$swfdoc 	= $pdfdoc . "_" . $page . ".swf";
		if(isset($_GET["callback"])){$callback = $_GET["callback"];}else{$callback = "";}
		$jsondoc = $pdfdoc . "_" . $page . ".js";
		if(isset($_GET["resolution"])){$resolution=$_GET["resolution"];}else{$resolution=null;}
		if(isset($_GET["subfolder"])){$subfolder=$_GET["subfolder"];}else{$subfolder="";}


		$pngdoc 		= $pdfdoc . "_" . $page . ".png";
		$jpgcachedoc 	= $pdfdoc . "_" . $page . "_res_" . $resolution . ".jpg";


		$messages 		= "";
	
		$swfFilePath 	= $swf_dir . $subfolder . $swfdoc;
		$pdfFilePath 	= $pdf_dir . $subfolder . $pdfdoc;
		$pdfSplitPath	= $swf_dir . $subfolder . $pdfdoc . "_" . $page . ".pdf";
		$pngFilePath 	= $swf_dir . $subfolder . $pngdoc;
		$jpgCachePath 	= $swf_dir . $subfolder . $jpgcachedoc;
		$jsonFilePath 	= $swf_dir . $subfolder . $jsondoc;
		$validatedConfig = true;
	
		if(!is_dir($swf_dir)){
			Echo "Error:Cannot find SWF output directory, please check your configuration file";
			$validatedConfig = false;
		}
	
		if(!is_dir($pdf_dir)){
			echo "Error:Cannot find PDF output directory, please check your configuration file";
			$validatedConfig = false;
		}
		
			if(!$validatedConfig){
				echo "Error:Cannot read directories set up in configuration file, please check your configuration.";
			}else if(	!validPdfParams($pdfFilePath,$pdfdoc,$page) /*|| !validSwfParams($swfFilePath,$swfdoc,$page) */){
				echo "Error:Incorrect file specified, please check your path";
			}else{
				if($format == "swf" || $format == "png" || $format == "pdf" || $format == "jpg" || $format == "jpgpageslice"){
					ob_clean();
																	
					if($format == "swf" || $format == "png" || $format == "jpg" || $format == "jpgpageslice"){
						// converting pdf files to swf format
						if(!file_exists($swfFilePath)){
							$pdfconv=new pdf2swf();
							$pdfconv->swf_dir = $swf_dir;
							$pdfconv->pdf_dir = $pdf_dir;
							
							$messages=$pdfconv->convert($path_to_pdf2swf,$pdfdoc,$page,$subfolder);
						}
					}
					
					// PNG format
					if($format == "png"){
						if(!file_exists($pngFilePath)){
							$pngconv=new swfrender();
							$pngconv->swf_dir = $swf_dir;							
							$pngconv->renderPage($path_to_pdf2swf,$pdfdoc,$swfdoc,$page);							
						}
						
						if($allowcache){
							setCacheHeaders();
						}
	
						if(!$allowcache || ($allowcache && endOrRespond())){
							if($resolution!=null){
								header('Content-Type: image/jpeg');
								echo file_get_contents(generateImage($pngFilePath,$jpgCachePath,$resolution,'png','jpg'));
							}else{
								header('Content-Type: image/png');
								echo file_get_contents($pngFilePath);
							}
						}
						
						
					}
					
					// JPG format
					if($format == "jpg"){
						if(validSwfParams($swfFilePath,$swfdoc,$page)){
							if(!file_exists($pngFilePath)){
								$pngconv=new swfrender();
								$pngconv->swf_dir = $swf_dir;									
								$pngconv->renderPage($path_to_pdf2swf,$pdfdoc,$swfdoc,$page,$subfolder);
							}
							
							if($allowcache){
								setCacheHeaders();
							}
		
							if(!$allowcache || ($allowcache && endOrRespond())){
								header('Content-Type: image/jpeg');
								
								if($resolution==null){
									echo file_get_contents(generateImage($pngFilePath,$jpgCachePath,1200,'png','jpg'));						
								}else{
									echo file_get_contents(generateImage($pngFilePath,$jpgCachePath,$resolution,'png','jpg'));						
								}
							}
						}else{
							if(strlen($messages)==0 || $messages == "[OK]")
								$messages = "[Incorrect file specified, please check your path]";					
						}							
					}
					
					// JPG format, in slices
					if($format == "jpgpageslice"){
						$path = $pngFilePath;

					    //getting extension type (jpg, png, etc)
					    $type = explode(".", $path);
					    $ext = strtolower($type[sizeof($type)-1]);
					    $ext = (!in_array($ext, array("jpeg","png","gif"))) ? "jpeg" : $ext;
		
					    if(isset($_GET["preserveext"]))
							$preserveext = $_GET["preserveext"];
						else
							$preserveext = false;
						
						// get the sector in question
						$sector = $_GET["sector"];
						$highrescache = true;
					
						// set the cache if needed
						$cachedir = $swf_dir;
						$image_filename = basename($path);
						$cachefilename = $cachedir . substr($image_filename,0,strripos($image_filename,".")) . "_" . $sector . ".jpeg";
					
						if(!file_exists($cachefilename) || !$highrescache){
						    //get image size
						    $size = getimagesize($path);
						    $width = $size[0];
						    $height = $size[1];
						
						    //get source image
						    $func = "imagecreatefrom".$ext;
						    $source = $func($path);
						
						    //setting default values
						    $new_width = $width * .25;
						    $new_height = $height * .25;
						    $k_w = 1;
						    $k_h = 1;
						    $src_x =0;
						    $src_y =0;
							$margin_x =0;
							$margin_y =0;
							
							switch($sector){
								// top 50%, left 50%
								case "l1t1":
									$src_x = 0;
									$src_y = 0;
								break;	
								case "l2t1":
									$src_x = $width * .25;
									$src_y = 0;		
								break;
								case "l1t2":
									$src_x = 0;
									$src_y = $height * .25;				
								break;
								case "l2t2":
									$src_x = $width * .25;
									$src_y = $height * .25;
								break;
								
								// top 50%, right 50%
								case "r1t1":
									$src_x = $width * .5;
									$src_y = 0;		
								break;
								case "r2t1":
									$src_x = $width * .75;
									$src_y = 0;				
								break;
								case "r1t2":
									$src_x = $width * .5;
									$src_y = $height * .25;
								break;
								case "r2t2":
									$src_x = $width * .75;
									$src_y = $height * .25;		
								break;
								
								//bottom 50%, left 50%
								case "l1b1":
									$src_x = 0;
									$src_y = $height * .5;
								break;
								case "l2b1":
									$src_x = $width * .25;
									$src_y = $height * .5;
								break;
								case "l1b2":
									$src_x = 0;
									$src_y = $height * .75;
								break;
								case "l2b2":
									$src_x = $width * .25;
									$src_y = $height * .75;
								break;
								
								// bottom 50%, right 50%
								case "r1b1":
									$src_x = $width * .5;
									$src_y = $height * .5;
								break;
								case "r2b1":
									$src_x = $width * .75;
									$src_y = $height * .5;				
								break;
								case "r1b2":
									$src_x = $width * .5;
									$src_y = $height * .75;
								break;
								case "r2b2":
									$src_x = $width * .75;
									$src_y = $height * .75;		
								break;
							}
						
							// adjusting for rounding
							$margin_x = $src_x - floor($src_x)+1;
							$margin_y = $src_y - floor($src_y);
						
						    $output = imagecreatetruecolor( $new_width, $new_height	);
						
						    //to preserve PNG transparency
						    if($ext == "png" && $preserveext)
						    {
						        //saving all full alpha channel information
						        imagesavealpha($output, true);
						        //setting completely transparent color
						        $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
						        //filling created image with transparent color
						        imagefill($output, 0, 0, $transparent);
						    }
						
						    imagecopyresampled( $output, $source,  0, 0, $src_x-(1-$margin_x), $src_y-(1-$margin_y), 
						                        $new_width, $new_height, 
						                        $width * .25+(1-$margin_x)*2, $height * .25+(1-$margin_y)*2);
						    //free resources
						    ImageDestroy($source);
						
						    //output image
						    header('Content-Type: image/'.$ext);
						
							// output the image
							if($preserveext==null){
								if($highrescache){
									imagejpeg($output, $cachefilename);
								}
								
								imagejpeg($output);
							}else{  	
						  		$func = "image".$ext;
								$func($output); 
							}
						
						    //free resources
						    ImageDestroy($output);
						}else{
							header('Content-Type: image/jpeg');
							echo file_get_contents($cachefilename);
						}						
					}
					
					// SWF format
					if(file_exists($swfFilePath)){
						if($format == "swf"){
							
							if($allowcache){
								setCacheHeaders();
							}
							
							if(!$allowcache || ($allowcache && endOrRespond())){
								header('Content-type: application/x-shockwave-flash');
								header('Accept-Ranges: bytes');
								header('Content-Length: ' . filesize($swfFilePath));
								echo file_get_contents($swfFilePath);
							}
						}
					}
				}
				
				if($format == "numpages-query"){
					echo "[{\"pages\":" .getTotalPages($pdf_dir . $pdfdoc) . "}]";
				}
				
				// PDF format
				if($format == "pdf"){
					ob_clean();						
					$pdfsplit = new splitpdf();
					$pdfsplit->swf_dir = $swf_dir;
					$pdfsplit->pdf_dir = $pdf_dir;

					if($pdfsplit->splitPDF($path_to_pdftk,$pdfdoc,$subfolder) == "[OK]"){
						header('Content-type: application/pdf');
						echo file_get_contents($pdfSplitPath);
					}	
				}				

				// JSON & JSONP format
				if($format == "json" || $format == "jsonp"){
					ob_clean();
										
					if(!file_exists($jsonFilePath)){
						$jsonconv = new pdf2json();
						$jsonconv->swf_dir = $swf_dir;
						$jsonconv->pdf_dir = $pdf_dir;
						
						$messages=$jsonconv->convert($path_to_pdf2json,$pdfdoc,$jsondoc,$page,$subfolder);
					}
					
					if(file_exists($jsonFilePath)){
						if($allowcache){
							setCacheHeaders();
						}					
						

						if(!$allowcache || ($allowcache && endOrRespond())){
							header('Content-Type: text/javascript');
		
							if($format == "json"){
								echo file_get_contents($jsonFilePath);
							}
		
							if($format == "jsonp"){
								echo $callback. '('. file_get_contents($jsonFilePath) . ')';
							}
						}
					}else{
						if(strlen($messages)==0)
							$messages = "[Cannot find JSON file. Please check your PHP configuration]";
					}						
				}
				
			}
	
		die();

    }
}
add_action('parse_request', 'plugin_parse_request');


function my_plugin_query_vars($vars) {
    $vars[] = 'flexpaper-plugin';
    return $vars;
}
add_filter('query_vars', 'my_plugin_query_vars');


// Decide if admin page or front-end and initialze appropriate things
if(is_admin()){
	//Add admin menu
	add_action( 'admin_menu', 'flexpaper_addmenu' );
	//Register settings
	add_action( 'admin_init', 'flexpaper_admininit' );
}
else{
	//Enqueue needed scripts
	wp_enqueue_script('flexpaper-jquery-extensions', '/wp-content/plugins/flexpaper/js/jquery.extensions.min.js',array('jquery'),'1.0');
	wp_enqueue_script('flexpaper', '/wp-content/plugins/flexpaper/js/flexpaper.js',array('jquery'),'1.0');	
	wp_enqueue_script('flexpaper-wp', '/wp-content/plugins/flexpaper/js/flexpaper-wp.js',array('jquery'),'1.0');
	wp_enqueue_style('flexpaper-style','/wp-content/plugins/flexpaper/css/flexpaper.css',false, '1.0', 'all');

	$flex_opts = array(
		dir=>get_bloginfo('wpurl')."/wp-content/plugins/flexpaper/",
		base_dir=>get_bloginfo('wpurl'),
		width=>(get_option('flexpaper_width')!=null)?get_option('flexpaper_width'):"100%",
		height=>(get_option('flexpaper_height')!=null)?get_option('flexpaper_height'):"100%",
		scale=>(get_option('flexpaper_scale')!=null)?get_option('flexpaper_scale'):0.6,
		fitpageonload=>(get_option('flexpaper_fitpageonload')!=null)?get_option('flexpaper_fitpageonload'):"false",
		fitwidthonload=>(get_option('flexpaper_fitwidthonload')!=null)?get_option('flexpaper_fitwidthonload'):"false",
		progressiveloading=>(get_option('flexpaper_progressive')!=null)?get_option('flexpaper_progressive'):"false",
		initviewmode=>(get_option('flexpaper_initviewmode')!=null)?get_option('flexpaper_initviewmode'):"Portrait",
		viewmodetoolsvisible=>(get_option('flexpaper_viewmodetoolsvisible')!=null)?get_option('flexpaper_viewmodetoolsvisible'):"true",
		zoomtoolsvisible=>(get_option('flexpaper_zoomtoolsvisible')!=null)?get_option('flexpaper_zoomtoolsvisible'):"true",
		navtoolsvisible=>(get_option('flexpaper_navtoolsvisible')!=null)?get_option('flexpaper_navtoolsvisible'):"true",
		cursortoolsvisible=>(get_option('flexpaper_cursortoolsvisible')!=null)?get_option('flexpaper_cursortoolsvisible'):"true",
		searchtoolsvisible=>(get_option('flexpaper_searchtoolsvisible')!=null)?get_option('flexpaper_searchtoolsvisible'):"true",
		renderingorder=>(get_option('flexpaper_renderingorder')!=null)?get_option('flexpaper_renderingorder'):"flash,html",		
		uiconfig=>(get_option('flexpaper_uiconfig')!=null)?get_option('flexpaper_uiconfig'):"",				
		key=>(get_option('flexpaper_key')!=null)?get_option('flexpaper_key'):"",		
		automaticconversion=>(get_option('flexpaper_automaticconversion')!=null)?get_option('flexpaper_automaticconversion'):"false",		
		local=>(get_option('flexpaper_local')!=null)?get_option('flexpaper_local'):"en_US"
		);

		wp_localize_script('flexpaper-wp','flex_opts',$flex_opts);
}

//Admin menu
function flexpaper_addmenu(){
	add_menu_page('Flexpaper Plugin Settings', 'Flexpaper Settings', 'administrator', __FILE__, 'flexpaper_settings');
}

//Register settings
function flexpaper_admininit(){
	register_setting( 'flexpaper-settings', 'flexpaper_width' );
	register_setting( 'flexpaper-settings', 'flexpaper_height' );
	register_setting( 'flexpaper-settings', 'flexpaper_scale' );
	register_setting( 'flexpaper-settings', 'flexpaper_local' );
	register_setting( 'flexpaper-settings', 'flexpaper_fitpageonload' );
	register_setting( 'flexpaper-settings', 'flexpaper_fitwidthonload' );
	register_setting( 'flexpaper-settings', 'flexpaper_progressive' );
	register_setting( 'flexpaper-settings', 'flexpaper_initviewmode' );
	register_setting( 'flexpaper-settings', 'flexpaper_viewmodetoolsvisible' );
	register_setting( 'flexpaper-settings', 'flexpaper_zoomtoolsvisible' );
	register_setting( 'flexpaper-settings', 'flexpaper_navtoolsvisible' );
	register_setting( 'flexpaper-settings', 'flexpaper_cursortoolsvisible' );
	register_setting( 'flexpaper-settings', 'flexpaper_searchtoolsvisible' );
	register_setting( 'flexpaper-settings', 'flexpaper_renderingorder' );	
	register_setting( 'flexpaper-settings', 'flexpaper_uiconfig' );			
	register_setting( 'flexpaper-settings', 'flexpaper_key' );
	register_setting( 'flexpaper-settings', 'flexpaper_automaticconversion' );			
	register_setting( 'flexpaper-settings', 'flexpaper_swftools_installdir' );
	register_setting( 'flexpaper-settings', 'flexpaper_pdf2json_installdir' );
	register_setting( 'flexpaper-settings', 'flexpaper_pdftk_installdir' );				
	register_setting( 'flexpaper-settings', 'flexpaper_swftools_enabled' );
	register_setting( 'flexpaper-settings', 'flexpaper_pdf2json_enabled' );
	register_setting( 'flexpaper-settings', 'flexpaper_pdftk_enabled' );
	register_setting( 'flexpaper-settings', 'flexpaper_pdfdirectory' );	
	register_setting( 'flexpaper-settings', 'flexpaper_workingdirectory' );		
	register_setting( 'flexpaper-settings', 'flexpaper_allowcache' );			
}

//Admin page display code
function flexpaper_settings(){
?>
<div class="wrap">
<h2>FlexPaper Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'flexpaper-settings' ); ?>
    <table class="form-table">
    <tr>
    	<td valign="top">
			<table class="form-table">
		        <tr valign="top">
		        <th scope="row">Width</th>
		        <td><input type="text" name="flexpaper_width" value="<?php echo get_option('flexpaper_width'); ?>" /></td>
		        </tr>
		         
		        <tr valign="top">
		        <th scope="row">Height</th>
		        <td><input type="text" name="flexpaper_height" value="<?php echo get_option('flexpaper_height'); ?>" /></td>
		        </tr>
		
		        <tr valign="top">
		        <th scope="row">Initial Scale</th>
		        <td><input type="text" name="flexpaper_scale" value="<?php echo get_option('flexpaper_scale'); ?>" /></td>
		        </tr>
		
				<tr valign="top">
		        <th scope="row">Fit Page on Load</th>
		        <td>
				<?php $flex_fitpageonload = get_option('flexpaper_fitpageonload');?>
		                <select name="flexpaper_fitpageonload">
		                                <option<?php if($flex_fitpageonload=="false"){ echo ' selected="selected"'; }?> value="false">no</option>
		                                <option<?php if($flex_fitpageonload=="true"){ echo ' selected="selected"'; }?> value="true">yes</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">Fit Width on Load</th>
		        <td>
				<?php $flex_fitwidthonload = get_option('flexpaper_fitwidthonload');?>
		                <select name="flexpaper_fitwidthonload">
		                                <option<?php if($flex_fitwidthonload=="false"){ echo ' selected="selected"'; }?> value="false">no</option>
		                                <option<?php if($flex_fitwidthonload=="true"){ echo ' selected="selected"'; }?> value="true">yes</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">Progressive Loading</th>
		        <td>
				<?php $flex_progressiveloading = get_option('flexpaper_progressive');?>
		                <select name="flexpaper_progressive">
		                                <option<?php if($flex_progressiveloading=="false"){ echo ' selected="selected"'; }?> value="false">no</option>
		                                <option<?php if($flex_progressiveloading=="true"){ echo ' selected="selected"'; }?> value="true">yes</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">View Mode</th>
		        <td>
				<?php $flex_initviewmode = get_option('flexpaper_initviewmode');?>
		                <select name="flexpaper_initviewmode">
		                                <option<?php if($flex_initviewmode=="Portrait"){ echo ' selected="selected"'; }?>>Portrait</option>
		                                <option<?php if($flex_initviewmode=="TwoPage"){ echo ' selected="selected"'; }?>>TwoPage</option>
				</select>
				</td>
		        </tr>	
		
				<tr valign="top">
		        <th scope="row">Rendering Order</th>
		        <td>
				<?php $flex_ro = get_option('flexpaper_renderingorder');?>
		                <select name="flexpaper_renderingorder">
		                                <option<?php if($flex_ro=="flash,html"){ echo ' selected="selected"'; }?>>flash,html</option>
				                        <option<?php if($flex_ro=="flash,html5"){ echo ' selected="selected"'; }?>>flash,html5</option>
		                                <option<?php if($flex_ro=="html,flash"){ echo ' selected="selected"'; }?>>html,flash</option>
				                        <option<?php if($flex_ro=="html,html5"){ echo ' selected="selected"'; }?>>html,html5</option>
				                        <option<?php if($flex_ro=="html5,flash"){ echo ' selected="selected"'; }?>>html5,flash</option>
				                        <option<?php if($flex_ro=="html5,html"){ echo ' selected="selected"'; }?>>html5,html</option>				
				</select>
				</td>
		        </tr>				
		
				<?php 
					$flex_automaticconversion = get_option('flexpaper_automaticconversion');					
				?>
		        <tr valign="top">
			        <th scope="row">On the Fly Publishing</th>
			        <td>
			                <select name="flexpaper_automaticconversion" id="flexpaper_automaticconversion" onChange="checkConversion()">
			                                <option<?php if($flex_automaticconversion =="false"){ echo ' selected="selected"'; }?> value="false">no</option>
			                                <option<?php if($flex_automaticconversion =="true"){ echo ' selected="selected"'; }?> value="true">yes</option>
					</select>
					</td>
			    </tr>		        
		    </table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tr valign="top">
		        <th scope="row">Locale</th>
		        <td>
					<?php $flex_local = get_option('flexpaper_local');?>
					<select name="flexpaper_local">
						<option<?php if($flex_local=="en_US"){ echo ' selected="selected"'; }?>>en_US</option>
						<option<?php if($flex_local=="fr_FR"){ echo ' selected="selected"'; }?>>fr_FR</option>
						<option<?php if($flex_local=="zh_CN"){ echo ' selected="selected"'; }?>>zh_CN</option>
						<option<?php if($flex_local=="es_ES"){ echo ' selected="selected"'; }?>>es_ES</option>
						<option<?php if($flex_local=="pt_BR"){ echo ' selected="selected"'; }?>>pt_BR</option>
						<option<?php if($flex_local=="ru_RU"){ echo ' selected="selected"'; }?>>ru_RU</option>
						<option<?php if($flex_local=="fi_FN"){ echo ' selected="selected"'; }?>>fi_FN</option>
						<option<?php if($flex_local=="de_DE"){ echo ' selected="selected"'; }?>>de_DE</option>
						<option<?php if($flex_local=="nl_NL"){ echo ' selected="selected"'; }?>>nl_NL</option>
						<option<?php if($flex_local=="tr_TR"){ echo ' selected="selected"'; }?>>tr_TR</option>
						<option<?php if($flex_local=="se_SE"){ echo ' selected="selected"'; }?>>se_SE</option>
						<option<?php if($flex_local=="pt_PT"){ echo ' selected="selected"'; }?>>pt_PT</option>
						<option<?php if($flex_local=="el_EL"){ echo ' selected="selected"'; }?>>el_EL</option>
						<option<?php if($flex_local=="da_DN"){ echo ' selected="selected"'; }?>>da_DN</option>
						<option<?php if($flex_local=="cz_CS"){ echo ' selected="selected"'; }?>>cz_CS</option>
						<option<?php if($flex_local=="it_IT"){ echo ' selected="selected"'; }?>>it_IT</option>
						<option<?php if($flex_local=="pl_PL"){ echo ' selected="selected"'; }?>>pl_PL</option>
						<option<?php if($flex_local=="pv_FN"){ echo ' selected="selected"'; }?>>pv_FN</option>
						<option<?php if($flex_local=="hu_HU"){ echo ' selected="selected"'; }?>>hu_HU</option>
					</select>
				</td>
		        </tr>
		        
				<tr valign="top">
		        <th scope="row">Show View Mode Tools</th>
		        <td>
				<?php $flex_viewmodetoolsvisible = get_option('flexpaper_viewmodetoolsvisible');?>
		                <select name="flexpaper_viewmodetoolsvisible">
		                                <option value="true" <?php if($flex_viewmodetoolsvisible=="true"){ echo ' selected="selected"'; }?>>yes</option>
										<option value="false" <?php if($flex_viewmodetoolsvisible=="false"){ echo ' selected="selected"'; }?>>no</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">Show Zoom Tools</th>
		        <td>
				<?php $flex_zoomtoolsvisible = get_option('flexpaper_zoomtoolsvisible');?>
		                <select name="flexpaper_zoomtoolsvisible">
		                                <option value="true" <?php if($flex_zoomtoolsvisible=="true"){ echo ' selected="selected"'; }?>>yes</option>
										<option value="false" <?php if($flex_zoomtoolsvisible=="false"){ echo ' selected="selected"'; }?>>no</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">Show Nav Tools</th>
		        <td>
				<?php $flex_navtoolsvisible = get_option('flexpaper_navtoolsvisible');?>
		                <select name="flexpaper_navtoolsvisible">
		                                <option value="true" <?php if($flex_navtoolsvisible=="true"){ echo ' selected="selected"'; }?>>yes</option>
										<option value="false" <?php if($flex_navtoolsvisible=="false"){ echo ' selected="selected"'; }?>>no</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">Show Cursor Tools</th>
		        <td>
				<?php $flex_cursortoolsvisible = get_option('flexpaper_cursortoolsvisible');?>
		                <select name="flexpaper_cursortoolsvisible">
		                                <option value="true" <?php if($flex_cursortoolsvisible=="true"){ echo ' selected="selected"'; }?>>yes</option>
										<option value="false" <?php if($flex_cursortoolsvisible=="false"){ echo ' selected="selected"'; }?>>no</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">Show Search Tools Tools</th>
		        <td>
				<?php $flex_searchtoolsvisible = get_option('flexpaper_searchtoolsvisible');?>
		                <select name="flexpaper_searchtoolsvisible">
		                                <option value="true" <?php if($flex_searchtoolsvisible=="true"){ echo ' selected="selected"'; }?>>yes</option>
										<option value="false" <?php if($flex_searchtoolsvisible=="false"){ echo ' selected="selected"'; }?>>no</option>
				</select>
				</td>
		        </tr>
				
				<tr valign="top">
		        <th scope="row">UIConfig file</th>
		        <td><input type="text" name="flexpaper_uiconfig" value="<?php echo get_option('flexpaper_uiconfig'); ?>" /></td>
				</tr>
		
				<tr valign="top">
		        <th scope="row">License Key</th>
		        <td><input type="text" name="flexpaper_key" value="<?php echo get_option('flexpaper_key'); ?>" /></td>
				</tr>
			</table>
		</td>	
    </tr>
	</table>

	<div style="display:block;clear:both;height:50px;">&nbsp;</div>
	
    <table class="form-table">
    <tr>
    	<td valign="top">
		<b class="conversionParameter">On the Fly Publishing - Conversion Settings</b>:<br/>
		<table class="form-table" style="background-color:#EEEEEE">
		<?php
					$path_to_pdf2swf 	= '';
					$path_to_pdf2json 	= '';
					$pdf2swf_exec		= '';
					$pdf2json_exec		= '';
					$path_to_pdftk		= '';
					$pdftk_exec			= '';
					
					if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	){
						$path_to_pdf2swf 	= 'C:\Program Files\SWFTools\\';
						$path_to_pdf2json 	= 'C:\Program Files\PDF2JSON\\';
						$path_to_pdftk		= 'C:\Program Files\PDF Labs\PDFtk Server\bin\\';
						$pdf2swf_exec 		= 'pdf2swf.exe';
						$pdf2json_exec 		= 'pdf2json.exe';
						$pdftk_exec			= 'pdftk.exe';
					}else{
						$path_to_pdf2swf 	= '/usr/local/bin/';
						$path_to_pdf2json 	= '/usr/local/bin/';
						$path_to_pdftk		= '/usr/bin/';
						$pdf2swf_exec 		= 'pdf2swf';
						$pdf2json_exec 		= 'pdf2json';
						$pdftk_exec			= 'pdftk';
					}
					
					if(strlen(get_option('flexpaper_swftools_installdir'))>0){
						$path_to_pdf2swf = get_option('flexpaper_swftools_installdir');
					}
					
					
					if(strlen(get_option('flexpaper_pdf2json_installdir'))>0){
						$path_to_pdf2json = get_option('flexpaper_pdf2json_installdir');
					}
					
					if(strlen(get_option('flexpaper_pdftk_installdir'))>0){
						$path_to_pdftk = get_option('flexpaper_pdftk_installdir');
					}
	
					$pdf2swf_installed = pdf2swfEnabled($path_to_pdf2swf . $pdf2swf_exec);
					$pdf2json_installed = pdf2jsonEnabled($path_to_pdf2json . $pdf2json_exec);
					$pdftk_installed = pdftkEnabled($path_to_pdftk . $pdftk_exec);
					
					if(strlen($swf_dir)<2){
						$swf_dir = plugin_dir_path( __FILE__ ) . "docs/";	
					}
	
	
					if(strlen($pdf_dir)<2){
						$pdf_dir = plugin_dir_path( __FILE__ ) . "pdf/";	
					}
	
					?>
	
			        <?php 
						if(is_dir($pdf_dir)){
							$pdfdir_exists = true;
						}else{
							$pdfdir_exists = false;
						}
			        ?>
			        
			        <tr valign="top">
				        <th scope="row" class="conversionParameter">PDF directory</th>
				        <td><input style="width:400px;" class="conversionParameter" type="text" name="flexpaper_pdfdirectory" value="<?php echo $pdf_dir ?>" /> <?php if($pdfdir_exists){ ?><img class="conversionParameter" src="../wp-content/plugins/flexpaper/tick.png"><?php }else{ ?><img class="conversionParameter" src="../wp-content/plugins/flexpaper/icon_no.png"> This directory does not exist. Please specify a new directory path. <?php } ?></td>
			        </tr>
			        

			        <?php 
						if(is_dir($swf_dir) && is_writable($swf_dir)){
							$swfdir_writable = true;
						}else{
							$swfdir_writable = false;
						}
			        ?>
			        
	
			        <tr valign="top">
				        <th scope="row" class="conversionParameter">Working directory</th>
				        <td><input style="width:400px;" class="conversionParameter" type="text" name="flexpaper_workingdirectory" value="<?php echo $swf_dir ?>" /> <?php if($swfdir_writable){ ?><img class="conversionParameter" src="../wp-content/plugins/flexpaper/tick.png"><?php }else{ ?><img class="conversionParameter" src="../wp-content/plugins/flexpaper/icon_no.png"> This directory does not exist or is not writable. Please check your configuration. <?php } ?></td>
			        </tr>
			        
	
			        <tr valign="top">
				        <th scope="row" class="conversionParameter"><a href="https://code.google.com/p/flexpaper-desktop-publisher/downloads/list" target="_new">SWFTools</a> installed?</th>
				        <td><input class="conversionParameter" type="text" name="flexpaper_swftools_installdir" value="<?php echo $path_to_pdf2swf ?>" /> <?php if(!$pdf2swf_installed) { ?><input type="hidden" name="flexpaper_swftools_enabled" value="false" /><img class="conversionParameter" src="../wp-content/plugins/flexpaper/icon_no.png"><?php }else{ ?><input type="hidden" name="flexpaper_swftools_enabled" value="true" /><img class="conversionParameter" src="../wp-content/plugins/flexpaper/tick.png"><?php } ?></td>
			        </tr>
			        
			        <tr valign="top">
				        <th scope="row" class="conversionParameter"><a href="https://code.google.com/p/flexpaper-desktop-publisher/downloads/list" target="_new">PDF2JSON</a> installed?</th>
				        <td><input class="conversionParameter" type="text" name="flexpaper_pdf2json_installdir" value="<?php echo $path_to_pdf2json ?>" /> <?php if(!$pdf2json_installed) { ?><input type="hidden" name="flexpaper_pdf2json_enabled" value="false" /><img class="conversionParameter" src="../wp-content/plugins/flexpaper/icon_no.png"><?php }else{ ?><input type="hidden" name="flexpaper_pdf2json_enabled" value="true" /><img class="conversionParameter" src="../wp-content/plugins/flexpaper/tick.png"><?php } ?></td>
			        </tr>		        
			        
			        <tr valign="top">
				        <th scope="row" class="conversionParameter"><a href="http://www.pdflabs.com/tools/pdftk-server/" target="_new">PDFTK</a> installed?</th>
				        <td><input class="conversionParameter" type="text" name="flexpaper_pdftk_installdir" value="<?php echo $path_to_pdftk ?>" /> <?php if(!$pdftk_installed) { ?><input type="hidden" name="flexpaper_pdftk_enabled" value="false" /><img class="conversionParameter" src="../wp-content/plugins/flexpaper/icon_no.png"><?php }else{ ?><input type="hidden" name="flexpaper_pdftk_enabled" value="true" /><img class="conversionParameter" src="../wp-content/plugins/flexpaper/tick.png"><?php } ?></td>
			        </tr>
			        
					<tr valign="top">
			        <th scope="row">Allow Browser Cache</th>
			        <td>
					<?php $flex_allowcache = get_option('flexpaper_allowcache');?>
			                <select name="flexpaper_allowcache">
                                <option<?php if($flex_allowcache =="true"){ echo ' selected="selected"'; }?> value="true">yes</option>			                
                                <option<?php if($flex_allowcache =="false"){ echo ' selected="selected"'; }?> value="false">no</option>
							</select>
					</td>
			        </tr>
			        
			        	
			        <script language="javascript">		
						function checkConversion(){
							if(jQuery('#flexpaper_automaticconversion').val() == "true"){
								jQuery('.conversionParameter').animate({opacity : 1},150).prop('disabled', false);
					
							}else{
								jQuery('.conversionParameter').animate({opacity : 0.5},150).prop('disabled', true);		
							
							}
						}
						
						checkConversion();
					</script>
		</table>
		</td>	
    </tr>
	</table>
	
	
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php 
}


function flexpaper_shortcode( $atts, $content = null ){
	STATIC $flexid = 1;
	$flexdata = "<div id=\"flexpaper".$flexid."\"><div data-document=\"".$content."\" class=\"flexpaper_link\">".$content."</div></div>";
	$flexid++;
	return $flexdata;
}
add_shortcode('flexpaper', 'flexpaper_shortcode');

?>
