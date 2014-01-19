<?php
function getMenus($menuElements)
{
  $menuArray = array();
  foreach($menuElements as $menuElement)
  {
    $subMenu = array();
    foreach ($menuElement->find('li') as $li)
    {
      $aElement = $li->find('a', 0);
      if ($aElement)
      {
        $text = GBK2UTF8($aElement->innertext);
        $subMenu[$text] = $aElement->href;
      }
    }
    $menuArray[] = $subMenu;
  }
  return $menuArray;
}

function GBK2UTF8($str)
{
  return iconv('GBK', 'UTF-8', $str);
}

function getFileName($url)
{
  return 'cache/'.md5($url).".html";
}

function saveFile($url,$title, $menus, $images)
{
  $fileName =getFileName($url);

  $doc = getDomDocument();
  $container = $doc->getElementById('container');

  // Set Title
  setTitle($doc, $title);

  // Add Menus
  addMenus($doc, $menus);

  // Add pictures
  addPictures($doc, $container, $images);

  // Save to a HTML file
  $doc->saveHTMLFile($fileName);
}

function getDomDocument()
{
  $doc = new DOMDocument();
  $doc->encoding = 'UTF-8';
  $doc->loadHTMLFile("templates/detail.html");
  return $doc;
}

function setTitle($doc, $title)
{
  $titleNodeList = $doc->getElementsByTagName('title');
  $titleNode = null;
  if ($titleNodeList->length ==1)
  {
    $titleNode = $titleNodeList->item(0);
  }
  if ($titleNode)
  {
    $titleNode->nodeValue = $title;
  }
}

function addMenus($doc, $menus)
{
  $menuContainer = $doc->getElementById('menu');
  foreach ($menus as $menu) {
    $div = $doc->createElement('div');
    $ul = $doc->createElement('ul');
    foreach ($menu as $text => $link) {
      $li = $doc->createElement('li');
      $a = $doc->createElement('a');
      $a->nodeValue = $text;
      $a->setAttribute("href", $link);
      $li->appendChild($a);
      $ul->appendChild($li);
    }
    $div->appendChild($ul);
    $menuContainer->appendChild($div);
  }
}

function addPictures($doc, $container, $images)
{
  foreach ($images as $element) {
    $div = $doc->createElement('div');
    $img = $doc->createElement('img');
    $img->setAttribute('src', $element->src);
    $img->setAttribute('alt', 'Pic');
    $div->appendChild($img);
    $container->appendChild($div);
  }
}