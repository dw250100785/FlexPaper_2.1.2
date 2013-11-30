<?php
/**
* █▒▓▒░ The FlexPaper Project 
* 
* Copyright (c) 2009 - 2011 Devaldi Ltd
*
* Commercial licenses are available. The commercial player version
* does not require any FlexPaper notices or texts and also provides
* some additional features.
* When purchasing a commercial license, its terms substitute this license.
* Please see http://flexpaper.devaldi.com/ for further details.
* 
*/

require_once("config.php");
require_once("common.php");

class pdf2swf
{
	private $configManager = null;

	/**
	* Constructor
	*/
	function __construct()
	{
		$this->configManager = new Config();
	}

	/**
	* Destructor
	*/
	function __destruct() {
        //echo "pdf2swf destructed\n";
    	}

	/**
	* Method:convert
     *
     * 在线调用win组件  pdf转换成swf
	*/
	public function convert($doc,$page)
	{
		$output=array();
        /*
         * $pdfFilePath   没有转换过的pdf需要在线转换
         * $swfFilePath   已经转换过的直接调用显示
         * */
		$pdfFilePath = $this->configManager->getConfig('path.pdf') . $doc;
		$swfFilePath = $this->configManager->getConfig('path.swf') . $doc  . $page. ".swf";
		
		if($this->configManager->getConfig('splitmode')=='true')
			$command = $this->configManager->getConfig('cmd.conversion.splitpages');
		else
			$command = $this->configManager->getConfig('cmd.conversion.singledoc');
			
		$command = str_replace("{path.pdf}",$this->configManager->getConfig('path.pdf'),$command);
		$command = str_replace("{path.swf}",$this->configManager->getConfig('path.swf'),$command);
		$command = str_replace("{pdffile}",$doc,$command);
		
		try {
			if (!$this->isNotConverted($pdfFilePath,$swfFilePath)) {
				array_push ($output, utf8_encode("[Converted]"));
				return arrayToString($output);
			}
		} catch (Exception $ex) {
			array_push ($output, "Error," . utf8_encode($ex->getMessage()));
			return arrayToString($output);
		}

		$return_var=0;
		
		if($this->configManager->getConfig('splitmode')=='true'){
			$pagecmd = str_replace("%",$page,$command);
			$pagecmd = $pagecmd . " -p " . $page;

			exec($pagecmd,$output,$return_var);
			$hash = getStringHashCode($command);
            if(!isset($_SESSION['CONVERSION_' . $hash])){
                exec(getForkCommandStart() . $command . getForkCommandEnd());
                $_SESSION['CONVERSION_' . $hash] = true;
            }
		}else
			exec($command,$output,$return_var);
        /*
         * 开始转换  pdf2swftools
         *
         * */
			
		if($return_var==0 || strstr(strtolower($return_var),"notice")){
			$s="[Converted]";
		}else{
            $errmsgs = arrayToString($output);
            if(strrpos($errmsgs,"too complex")>0 && !$this->configManager->getConfig('splitmode')=='true'){
                $s=" This document is too complex to render in simple mode. Please use split mode when rendering documents like these.";
            }else{
            	if(strpos($errmsgs,"FATAL")>0){
            		if(strpos($errmsgs,"\n",strpos($errmsgs,"FATAL"))>0){
            			$s=" " . substr($errmsgs,strpos($errmsgs,"FATAL")+8,strpos($errmsgs,"\n",strpos($errmsgs,"FATAL"))-strpos($errmsgs,"FATAL"));	
            		}else{
            			$s=" " . substr($errmsgs,strpos($errmsgs,"FATAL")+8,strpos($errmsgs,"\n",strpos($errmsgs,"FATAL"))-strpos($errmsgs,"FATAL"));	
            		}
            		
            		$s = str_replace("Internal error","PDF conversion error",$s);
            	}else{
	                $s=" Error converting document, make sure the conversion tool is installed and that correct user permissions are applied to the SWF Path directory" . $this->configManager->getDocUrl();
            	}
            }
		}
		return $s;
	}

	/**
	* Method:isConverted
	*/
	public function isNotConverted($pdfFilePath,$swfFilePath)
	{
		if (!file_exists($pdfFilePath)) {
			throw new Exception("Document does not exist");
		}
		if ($swfFilePath==null) {
			throw new Exception("Document output file name not set");
		} else {
			if (!file_exists($swfFilePath)) {
				return true;
			} else {
				if (filemtime($pdfFilePath)>filemtime($swfFilePath)) return true;
			}
		}
		return false;
	}
}
?>