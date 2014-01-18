<?php
include 'simple_html_dom.php';
include 'functions.php';
$url = (isset($_GET['url']) && !empty($_GET['url']) && trim($_GET['url'])) ? urldecode($_GET['url']) : '';
if ($url)
{
  $md5FileName = getFileName($url);

  // If cache exists, read from cache.
  if (file_exists($md5FileName))
  {
    echo file_get_contents($md5FileName);
    exit;
  }

  // Get file content from remote server.
  $html = file_get_html($url);
  $title = $html->find('title', 0)->innertext;
  $images = $html->find('.main img');
  $menuElements = $html->find('#menu_box ul');
  $menuArray = getMenus($menuElements);
  saveFile($url, GBK2UTF8($title), $menuArray, $images);
}