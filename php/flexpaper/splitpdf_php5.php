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

class splitpdf
{
	/**
	* Constructor
	*/
	function __construct()
	{
		if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	){
			$this->command = "pdftk.exe \"{path.pdf}{pdffile}\" burst output \"{path.swf}{pdffile}_%1d.pdf\" compress";
		}else{
			$this->command = "pdftk \"{path.pdf}{pdffile}\" burst output \"{path.swf}{pdffile}_%1d.pdf\" compress";
		}
	}

	/**
	* Destructor
	*/
	function __destruct() {

    }

	/**
	* Method:splitPDF
	*/
	public function splitPDF($path_to_pdftk,$pdfdoc,$subfolder)
	{
		$output=array();
        $command = $path_to_pdftk . $this->command;
        $command = str_replace("{path.pdf}",$this->pdf_dir . $subfolder,$command);
        $command = str_replace("{path.swf}",$this->swf_dir . $subfolder,$command);
        $command = str_replace("{pdffile}",$pdfdoc,$command);

		try {
    		$return_var=0;
            exec($command,$output,$return_var);
            if($return_var==1 || $return_var==0 || (strstr(PHP_OS, "WIN") && $return_var==1)){
                return "[OK]";
            }else{
                return "[Error converting splitting PDF, please check your configuration]";
            }
		} catch (Exception $ex) {
			return $ex;
		}
	}
}
?>