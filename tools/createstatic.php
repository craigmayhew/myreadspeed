<?php
date_default_timezone_set("Europe/London");

class builder{
  /*CONFIG START*/
  private $destinationFolder = '../htdocs/';
  private $dirPages  = '../pages/';
  private $justCopy  = array('favicon.ico','images','c.css','j.js','robots.txt');
  private $sideNav   = '';
  /*CONFIG END*/

  private function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                echo 'copied '.$dst.'/'.$file."\n";
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
  }

  public function build(){
    echo "Copying Static Files\n";
    $this->copyStaticFiles();
    echo "Building Pages\n";
    $this->buildPages($this->dirPages);
  }

  private function copyStaticFiles(){
    //copy static files
    foreach($this->justCopy as $fileOrFolder){
      if(is_file('../'.$fileOrFolder)){
        echo 'copied '.$this->destinationFolder.$fileOrFolder."\n";
        copy('../'.$fileOrFolder,$this->destinationFolder.$fileOrFolder);
      }else{
        $this->recurse_copy('../'.$fileOrFolder,$this->destinationFolder.$fileOrFolder);
      }
    }
  }
  
  //build pages
  private function buildPages($dir){
    if($handle = opendir($dir)){
      while(false !== ($entry = readdir($handle))){
        if(is_dir($dir.$entry) && $entry != '.' && $entry != '..'){
          $this->buildPages($dir.$entry.'/');
        }
        if(substr($entry,-5) != '.json'){continue;}
        $json = json_decode(file_get_contents($dir.$entry),true);
        $page = new page($json['title']);
        $page->setContent(file_get_contents(substr($dir.$entry,0,-5).'.html'));
        $page->setSideNav($this->sideNav);
        $content = $page->build();
        $this->generateFile($this->destinationFolder.$json['url'].'/index.html',$content);
      }
    }
  }

  private function generateFile($name,$content){
    $dir = dirname($name);
    if(!is_dir($dir)){
      mkdir($dir,0777,true);
    }
    file_put_contents($name,$content);
    echo 'Generated '.$name."\n";
  }
}

class page{
  private $content  = '';
  private $navTop   = '';
  public  $navRight = '';
  private $title    = '';
  function __construct($title){
    $this->title = $title;

    $this->header =
    '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>'.$this->title.'</title>
    <link rel="stylesheet" type="text/css" href="/c.css" />
    <script type="text/javascript" src="/j.js"></script>
    <link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
    <div id="container">
        <!-- header -->
        <div id="header">
            <div id="header-content">
                <div id="header-content-left">
                    <a href="/"><img src="/images/header.gif" width="525" border="0" alt="logo-myreadspeed.com" /></a>
                </div>
                <div id="header-content-right">
                    <p class="quote"></p>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div id="content">
            <div id="content-wrap">';
  }
  private function buildFooter(){
    $this->footer =
                '<div id="fix-footer"></div>'.
            '</div>'.
            '<div class="clear"></div>'.
        '</div>'.

      '<div id="footer">
      <div id="footer-wrp">
        <div id="footer-left">
            <span class="domain">myReadSpeed<span>.com</span></span>
            <p>
            Calculate your reading speed and see how quick and easy it is to read more books at your normal reading pace - you can also schedule free custom installments to read any classic book by email
            </p>
        </div>
        <div id="footer-right">
            <ul class="tips">
                <li><a href="/about-us/">About us</a></li>
                <li><a href="/articles/">Learn to speed read</a></li>
                <li><a href="/articles/10-tips/">10 tips to improve your reading speed</a></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>
</body>';
  }
  public function setContent($content){
    $this->content = $content;
  }
  public function setSideNav($nav){
    $this->navRight = $nav;
  }
  public function build(){
    $this->buildFooter();
    return $this->header.$this->content.$this->footer;
  }
}

//go build stuff!
$builder = new builder();
$builder->build();
