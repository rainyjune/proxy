<?php
function generateLocalFile($url)
{
  $html = file_get_html($url);
  $title = $html->find('title', 0)->innertext;
  $images = $html->find('.main img');
  $menuElements = $html->find('#menu_box ul');
  $listElements = $html->find('.main a');
  $listPagesElements = $html->find('.box_page a,.box_page li[class],span.pageinfo');
  $menuArray = getMenus($menuElements);
  $listArray = getList($listElements);
  $listPageArray = getListPages($listPagesElements);
  //var_dump($listPageArray);exit;
  saveFile($url, GBK2UTF8($title), $menuArray, $images, $listArray, $listPageArray);
}
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

function getListPages($listPagesElements)
{
  //var_dump($listPagesElements);exit;
  $result = array('pages'=>array(),'pagesInfo'=>'');
  foreach ($listPagesElements as $key => $element) {
    if ($element->tag =='li') {
      $result['pages'][GBK2UTF8($element->innertext)] = '#';
    } else if($element->tag =='a') {
      $result['pages'][GBK2UTF8($element->innertext)] = $element->href ? $element->href : '#';
    } else if ($element->tag =='span') {
      $result['pagesInfo'] = strip_tags(GBK2UTF8($element->innertext));
    }
  }
  return $result;
}

function getList($listElements)
{
  $listArray = array();
  foreach ($listElements as $link) {
    $listArray[GBK2UTF8($link->innertext)] = $link->href;
  }
  return $listArray;
}

function GBK2UTF8($str)
{
  return iconv('GBK', 'UTF-8', $str);
}

function getFileName($url)
{
  return 'cache/'.md5($url).".html";
}

function saveFile($url,$title, $menus, $images, $listArray, $listPageArray)
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

  // Add List
  addList($doc, $listArray);

  // Add List pages navigation
  addListPages($doc, $listPageArray);

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

function addListPages($doc, $listPageArray)
{
  $container = $doc->getElementById('pageList');
  $pages = $listPageArray['pages'];
  foreach ($pages as $text => $link) {
    $li = $doc->createElement('li');
    $a = $doc->createElement('a');
    $a->nodeValue = $text;
    $a->setAttribute("href", $link);
    $li->appendChild($a);
    $container->appendChild($li);
  }
  $li = $doc->createElement('li');
  $li->nodeValue = $listPageArray['pagesInfo'];
  $container->appendChild($li);
}

function addList($doc, $listArray)
{
  $listContainer = $doc->getElementById('listContainer');
  $ul = $doc->createElement('ul');
  foreach ($listArray as $text => $link) {
    $li = $doc->createElement('li');
    $a = $doc->createElement('a');
    $a->nodeValue = $text;
    $a->setAttribute("href", $link);
    $li->appendChild($a);
    $ul->appendChild($li);
  }
  $listContainer->appendChild($ul);
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