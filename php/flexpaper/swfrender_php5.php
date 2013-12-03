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

class swfrender
{	
	/**
	* Constructor
	*/
	function __construct()
	{
		if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	){
			$this->command = "swfrender.exe \"{path.swf}{swffile}\" -o \"{path.swf}{pdffile}_{page}.png\" -X 2048 -s keepaspectratio";
		}else{
			$this->command = "swfrender \"{path.swf}{swffile}\" -o \"{path.swf}{pdffile}_{page}.png\" -X 2048 -s keepaspectratio";
		}
	}

	/**
	* Destructor
	*/
	function __destruct() {

    }
	
	/**
	* Method:render page as image
	*/
	public function renderPage($path_to_pdf2swf,$pdfdoc,$swfdoc,$page,$subfolder)
	{
		$output=array();

		try {
			$command = $path_to_pdf2swf . $this->command;
			$command = str_replace("{path.swf}",$this->swf_dir . $subfolder,$command);
			$command = str_replace("{swffile}",$swfdoc,$command);
			$command = str_replace("{pdffile}",$pdfdoc,$command);
			$command = str_replace("{page}",$page,$command);

			$return_var=0;
			exec($command,$output,$return_var);
			if($return_var==0){
				return "[OK]";
			}else{
				return "[Error converting PDF to PNG, please check your configuration]";
			}
		} catch (Exception $ex) {
			return $ex;
		}
	}
}
?>