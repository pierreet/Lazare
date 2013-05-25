<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_file extends WYSIJA_object{
    function WYSIJA_help_file(){
    }
    
    function exists($fileFolder=false){
        $upload_dir = wp_upload_dir();
        $filename=str_replace("/",DS,$upload_dir['basedir']).DS."wysija".DS.$fileFolder;
        if(!file_exists($filename)){
            return array('result'=>false,'file'=>$filename);
        }
        return array('result'=>true,'file'=>$filename);
    }
    
    function get($csvfilename,$folder="temp"){
        $upload_dir = wp_upload_dir();
        $filename=$upload_dir['basedir'].DS."wysija".DS.$folder.DS.$csvfilename;
        if(!file_exists($filename)){
            $filename=$upload_dir['basedir'].DS.$csvfilename;
            if(!file_exists($filename)) $filename=false;
        }
        return $filename;
    }
    
    function makeDir($folder="temp",$mode=0755){
        $upload_dir = wp_upload_dir();
        if(!isset($upload_dir['basedir'])){
            if(isset($upload_dir['error'])) $this->wp_error("<b>WordPress error</b> : ".$upload_dir['error'],1);
            return false;
        }
        if(strpos(str_replace("/",DS,$folder),str_replace("/",DS,$upload_dir['basedir']))!==false){
            $dirname=$folder;
        }else{
            $dirname=$upload_dir['basedir'].DS."wysija".DS.$folder.DS;
        }
        if(!file_exists($dirname)){
            if(!mkdir($dirname, $mode,true)){
                $dirname=false;
            }
            chmod($dirname,$mode);
        }
        return $dirname;
    }
    
    function getUploadDir($folder=false){
        $upload_dir = wp_upload_dir();
        $dirname=$upload_dir['basedir'].DS."wysija".DS;
        if($folder) $dirname.=$folder.DS;
        if(file_exists($dirname))    return $dirname;
        return false;
        
    }
    
    function temp($content,$key="temp",$format=".tmp"){
        $tempDir=$this->makeDir();
        if(!$tempDir)   return false;
        
        $filename=$key."-".time().$format;
        $handle=fopen($tempDir.$filename, "w");
        fwrite($handle, $content);
        fclose($handle);
        return array('path'=>$tempDir.$filename,'name'=>$filename, 'url'=>$this->url($filename,"temp"));
    }
    
    function url($filename,$folder="temp"){
        $upload_dir = wp_upload_dir();
        if(file_exists($upload_dir['basedir'].DS."wysija")){
            $url=$upload_dir['baseurl']."/wysija/".$folder."/".$filename;
        }else{
            $url=$upload_dir['baseurl']."/".$filename;
        }
        return str_replace(DS,"/",$url);
    }
    
    function send($path){
        
        if(file_exists($path)){
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename="export_wysija.csv"');
            readfile($path);
            exit();
        }else $this->error(__('File does not exists.',WYSIJA),true);
    }
    
    function clear(){
        $foldersToclear=array("import","temp");
        $filenameRemoval=array("import-","export-");
        $deleted=array();
        foreach($foldersToclear as $folder){
            $path=$this->getUploadDir($folder);
            
            if(!$path) continue; 
            $files = scandir($path);
            foreach($files as $filename){
                if(!in_array($filename, array('.','..',".DS_Store","Thumbs.db"))){
                    if(preg_match('/('.implode($filenameRemoval,'|').')[0-9]*\.csv/',$filename,$match)){
                       $deleted[]=$path.$filename;
                    }
                }
            }
        }
        foreach($deleted as $filename){
            if(file_exists($filename)){
                $filename=str_replace('/',DS,$filename);
                unlink($filename);
            }
        }
    }
    function rrmdir($dir) {
      if(strpos($dir, '..')!==false){
          $this->error('Path is not safe, cannot contain ..');
          return false;
      }
      if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file){
            if ($file != "." && $file != "..") $this->rrmdir("$dir".DS."$file");
        }
        if(!rmdir($dir)){
            chmod($dir, 0777);
            rmdir($dir);
        }
        
      }
      else if (file_exists($dir)) {
          $dir=str_replace('/',DS,$dir);
          unlink($dir);
      }
    }
    function rcopy($src, $dst) {
      if(strpos($src, '..')!==false || strpos($dir, '..')!==false){
          $this->error('src : '.$src);
          $this->error('dst : '.$dst);
          $this->error('Path is not safe, cannot contain ..');
          return false;
      }else{
          if (file_exists($dst)) $this->rrmdir($dst);
      }
      if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file){
            if ($file != "." && $file != "..") $this->rcopy("$src/$file", "$dst/$file");
        }
      }
      else if (file_exists($src)) {
          copy(str_replace('/',DS,$src), str_replace('/',DS,$dst));
      }
    }
    
    function chmodr($path, $filemode=0644, $dirmode=0755) {
        if (is_dir($path) ) {
            if (!chmod($path, $dirmode)) {
                $dirmode_str=decoct($dirmode);
                print "Failed applying filemode '$dirmode_str' on directory '$path'\n";
                print "  `-> the directory '$path' will be skipped from recursive chmod\n";
                return;
            }
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if($file != '.' && $file != '..') {  // skip self and parent pointing directories
                    $fullpath = $path.DS.$file;
                    $this->chmodr($fullpath, $filemode,$dirmode);
                }
            }
            closedir($dh);
        } else {
            if (is_link($path)) {
                print "link '$path' is skipped\n";
                return;
            }
            if (!chmod($path, $filemode)) {
                $filemode_str=decoct($filemode);
                print "Failed applying filemode '$filemode_str' on file '$path'\n";
                return;
            }
        }
    } 
}
