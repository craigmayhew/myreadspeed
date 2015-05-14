<?php

require '../vendor/autoload.php';
use Aws\Common\Aws;

$dir = '/home/craig/gitrepos/mine/myreadspeed.com/htdocs';
$bucket = 'www.myreadspeed.com';
$keyPrefix = '';
$options = array(
  'params'      => array('ACL' => 'public-read'),
  'concurrency' => 20,
  'debug'       => true
);

$config = array(
  'profile' => 'default',
  'region' => 'eu-west-1'
);

$aws = Aws::factory($config);
$client = $aws->get('s3');
$client->uploadDirectory($dir, $bucket, $keyPrefix, $options);
