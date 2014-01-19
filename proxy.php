<?php
include 'phplibs/simple_html_dom.php';
include 'functions.php';
$url = (isset($_GET['url']) && !empty($_GET['url']) && trim($_GET['url'])) ? urldecode($_GET['url']) : '';
if ($url)
{
  $urlComponents = parse_url($url);
  $md5FileName = getFileName($url);

  // If cache does not exists, fetch from remote server and then write into cache.
  if (!file_exists($md5FileName))
  {
    generateLocalFile($url);
  }
  echo file_get_contents($md5FileName);
}