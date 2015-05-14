<?php
date_default_timezone_set("Europe/London");

class builder{
  /*CONFIG START*/
  private $destinationFolder = '../htdocs/';
  private $dirPages  = '../pages/';
  private $cssPath   = '../s.css';
  private $css       = '';
  private $justCopy  = array('favicon.ico','files','imgs','js','robots.txt','uploads');
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
    $this->css = file_get_contents($this->cssPath);
    echo "Copying Static Files\n";
    $this->copyStaticFiles();
    echo "Building Blog\n";
    $this->buildBlog();
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
        $page = new page($json['title'],$this->css);
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
  function __construct($title,$css=''){
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
    '<div id="footer-wrp">
        <div id="footer-left">
            <span class="domain">myReadSpeed<span>.com</span></span>
            <p>
            Calculate your reading speed and see how quick and easy it is to read more books at your normal reading pace - you can also schedule free custom installments to read any classic book by email
            <br />
            <font class="who">
            Designed by Chung Nguyen-Le and built by <a href="http://www.adire.co.uk" target="_blank">Adire</a>  
            to encourage more people to read and more people to write - Copyright 2009. All rights reserved.
            </font>
            </p>
        </div>
        <div id="footer-right">
            <ul class="tips">
                <li><a href="/about-us/">About us</a></li>
                <li><a href="/articles/">Learn to speed read</a></li>
                <li><a href="/articles/10-tips/">10 tips to improve your reading speed</a></li>
            </ul>

            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style">
            <a href="http://www.addthis.com/bookmark.php?v=250&pub=cnlifeasitis" class="addthis_button_compact">Share</a>
            <span class="addthis_separator">|</span>
            <a class="addthis_button_facebook"></a>
            <a class="addthis_button_myspace"></a>
            <a class="addthis_button_google"></a>
            <a class="addthis_button_stumbleupon"></a>
            <a class="addthis_button_digg"></a>
            <a class="addthis_button_twitter"></a>
            </div>
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?pub=cnlifeasitis"></script>
            <!-- AddThis Button END -->

        </div>
        <div class="clear"></div>
    </div>
</div>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-8317546-4");
pageTracker._trackPageview();
} catch(err) {}
</script>
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
