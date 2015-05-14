<?php
if(!isset($this->store['cm_title'])){
  $this->store['title'] = 'myReadSpeed.com';
}

$hdr =
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>'.$this->store['title'].'</title>
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
