<?php  
 //��ȡ�ļ�����Ŀ¼  
 $dir=dirname(__FILE__) ;  
 //ɾ�������ļ�  
 //@unlink( $dir."\\test.swf" );  
 //ʹ��pdf2swfת������  
 $command= "C:\Program Files (x86)\SWFTools\pdf2swf.exe  -t \"".$dir."\\test.pdf\" -o  \"".$dir."\\test.swf\" -s flashversion=9 ";  
 //����shell����  
 $WshShell   = new COM("WScript.Shell");  
 //ִ��cmd����  
 $oExec      = $WshShell->Run("cmd /C ". $command, 0, true);  
?>  