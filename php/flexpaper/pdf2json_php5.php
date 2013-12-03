<?php
/**
* █▒▓▒░ The FlexPaper Project 
* 
* Copyright (c) 2009 - 2011 Devaldi Ltd
*
* When purchasing a commercial license, its terms substitute this license.
* Please see http://flexpaper.devaldi.com/ for further details.
* 
*/

class pdf2json
{
	private $pdftoolsPath;
	
	/**
	* Constructor
	*/
	function __construct()
	{
		if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	){
			$this->command = "pdf2json.exe \"{path.pdf}{pdffile}\" -enc UTF-8 -compress -split 10 \"{path.swf}{pdffile}_%.js\"";
		}else{
			$this->command = "pdf2json \"{path.pdf}{pdffile}\" -enc UTF-8 -compress -split 10 \"{path.swf}{pdffile}_%.js\"";
		}
	}

	/**
	* Destructor
	*/
	function __destruct() {
        //echo "swfextract destructed\n";
    }
	
	/**
	* Method:render page as image
	*/
	public function convert($path_to_pdf2json,$pdfdoc,$jsondoc,$page,$subfolder)
	{
		$output=array();
	
		try {
			$command = $path_to_pdf2json . $this->command;
			$command = str_replace("{path.pdf}",$this->pdf_dir . $subfolder,$command);
			$command = str_replace("{path.swf}",$this->swf_dir . $subfolder,$command);
			$command = str_replace("{pdffile}",$pdfdoc,$command);
			$command = str_replace("{jsonfile}",$jsondoc,$command);
			
			$return_var=0;

			exec($command,$output,$return_var);

			if($return_var==0){
				return "[OK]";
			}else{
				return "[Error converting PDF to JSON, please check your directory permissions and configuration]";
			}
		} catch (Exception $ex) {
			return "[" . $ex . "]";
		}
	}
}
?>