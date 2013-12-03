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

class pdf2swf
{
	/**
	* Constructor
	*/
	function __construct()
	{
		if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	){
			$this->command = "pdf2swf.exe \"{path.pdf}{pdffile}\" -o \"{path.swf}{pdffile}_%.swf\" -f -T 9 -t -s storeallcharacters -s linknameurl";
		}else{
			$this->command = "pdf2swf \"{path.pdf}{pdffile}\" -o \"{path.swf}{pdffile}_%.swf\" -f -T 9 -t -s storeallcharacters -s linknameurl";	
		}
	}

	/**
	* Destructor
	*/
	function __destruct() {
        //echo "pdf2swf destructed\n";
    }

	/**
	* Method:convert
	*/
	public function convert($path_to_pdf2swf,$doc,$page,$subfolder)
	{
		$output=array();
		$pdfFilePath = $this->pdf_dir . $subfolder . $doc;
		$swfFilePath = $this->swf_dir . $subfolder . $doc  . $page. ".swf";		
		$command = $path_to_pdf2swf . $this->command;
			
		$command = str_replace("{path.pdf}",$this->pdf_dir . $subfolder,$command);
		$command = str_replace("{path.swf}",$this->swf_dir . $subfolder,$command);
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
		
		$pagecmd = str_replace("%",$page,$command);
		$pagecmd = $pagecmd . " -p " . $page;

		exec($pagecmd,$output,$return_var);
		$hash = getStringHashCode($command);
           if(!isset($_SESSION['CONVERSION_' . $hash])){
               exec(getForkCommandStart() . $command . getForkCommandEnd());
               $_SESSION['CONVERSION_' . $hash] = true;
           }	
			
		if($return_var==0 || strstr(strtolower($return_var),"notice")){
			$s="[Converted]";
		}else{
            $errmsgs = arrayToString($output);

           	if(strpos($errmsgs,"FATAL")>0){
           		if(strpos($errmsgs,"\n",strpos($errmsgs,"FATAL"))>0){
           			$s=" " . substr($errmsgs,strpos($errmsgs,"FATAL")+8,strpos($errmsgs,"\n",strpos($errmsgs,"FATAL"))-strpos($errmsgs,"FATAL"));	
           		}else{
           			$s=" " . substr($errmsgs,strpos($errmsgs,"FATAL")+8,strpos($errmsgs,"\n",strpos($errmsgs,"FATAL"))-strpos($errmsgs,"FATAL"));	
           		}
           		
           		$s = str_replace("Internal error","PDF conversion error",$s);
           	}else{
                $s=" Error converting document, make sure the conversion tool is installed and that correct user permissions are applied to the SWF Path directory";
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