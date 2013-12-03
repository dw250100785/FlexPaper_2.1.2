@echo off
::close echo
cls
::clean screen
if exist C:\Program Files (x86)\SWFTools\pdf2swf.exe (
  echo "swftools ready!"
  goto run


)else(
  echo "swftools missing"
  pause
)
:run
  system C:\Program Files (x86)\SWFTools\pdf2swf.exe "G:\wamp\www\FlexPaper_2.1.2\php\pdf\G.pdf" -o "G:\wamp\www\FlexPaper_2.1.2\php\docs\G.pdf.swf" -f -T 9 -t -s storeallcharacters -s linknameurl
  pause

