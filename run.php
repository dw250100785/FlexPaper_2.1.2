<?php  
 //获取文件所在目录  
 $dir=dirname(__FILE__) ;  
 //删除测试文件  
 //@unlink( $dir."\\test.swf" );  
 //使用pdf2swf转换命令  
 $command= "C:\Program Files (x86)\SWFTools\pdf2swf.exe  -t \"".$dir."\\test.pdf\" -o  \"".$dir."\\test.swf\" -s flashversion=9 ";  
 //创建shell对象  
 $WshShell   = new COM("WScript.Shell");  
 //执行cmd命令  
 $oExec      = $WshShell->Run("cmd /C ". $command, 0, true);  
?>  