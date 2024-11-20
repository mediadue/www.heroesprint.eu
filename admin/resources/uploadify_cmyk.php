<?php
session_start();
include ("_docroot.php");
include (SERVER_DOCROOT."logic/class_config.php");
$objConfig = new ConfigTool();
$objDb = new Db;
$objUtility = new Utility;
$conn = $objDb->connection($objConfig);

/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$tmpdir=$objUtility->getFilenameUnique();
  $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/' . $tmpdir . "/";
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	
  $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
  $fileTypes  = str_replace(';','|',$fileTypes);
  $typesArray = split('\|',$fileTypes);
  $fileParts  = pathinfo($_FILES['Filedata']['name']);
  
  if(strtolower($fileParts['extension'])=="php") return;
  
  if (in_array($fileParts['extension'],$typesArray)) {
    // Uncomment the following line if you want to make the directory if it doesn't exist
    @mkdir(str_replace('//','/',$targetPath), 0755, true);
    
    move_uploaded_file($tempFile,$targetFile);
    //echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
    
    /* Attempt to open */
    $im = @imagecreatefromjpeg($targetFile);
    
    $msg='Errore. Ai fini della verifica, il file deve essere di tipo JPEG in formato colore CMYK.';
    
    if($im){
      $retC=0;
      $retM=0;
      $retY=0;
      $retK=0;
      $x=1;
      $y=1;
      
      $info=getimagesize($targetFile);
      $w = $info[0];
      $h = $info[1];
      $channels=$info['channels'];
      
      if($channels!="4") {
        echo $msg;
        return;
      } 
      
      for($x=1;$x<=$w;$x++){
        for($y=1;$y<=$h;$y++){
          $rgbIndex = imagecolorat($im, $x, $y);
          $rgb[0] = ($rgbIndex >> 16) & 0xFF;
          $rgb[1] = ($rgbIndex >> 8) & 0xFF;
          $rgb[2] = $rgbIndex & 0xFF;
          
          $cmyk=RGB_to_CMYK($rgb);
          
          $retC+=$cmyk[0];
          $retM+=$cmyk[1];
          $retY+=$cmyk[2];
          $retK+=$cmyk[3];   
        }
      }
      
      $wh=$w*$h;
      
      $retC=round(($retC/$wh),2)*100;
      $retM=round(($retM/$wh),2)*100;
      $retY=round(($retY/$wh),2)*100;
      $retK=round(($retK/$wh),2)*100;
              
      echo $retC."%*".$retM."%*".$retY."%*".$retK."%*".$tmpdir."/".basename($targetFile); 
      
      //unlink($targetFile);
      //rmdir($targetPath);
      imagedestroy($im);
    }else{
      echo $msg; 
    }
  } else {
    echo $msg;
  }
}
?>