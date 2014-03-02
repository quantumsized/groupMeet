<?php
$xml=("http://apps.leg.wa.gov/billinfo/SummaryRss.aspx?bill=1193&year=2013");

$xmlDoc = new DOMDocument();
$xmlDoc->load($xml);

//get elements from "<channel>"
$channel=$xmlDoc->getElementsByTagName('channel')->item(0);
$channel_title = $channel->getElementsByTagName('title')
->item(0)->childNodes->item(0)->nodeValue;
$channel_link = $channel->getElementsByTagName('link')
->item(0)->childNodes->item(0)->nodeValue;
$channel_desc = $channel->getElementsByTagName('description')
->item(0)->childNodes->item(0)->nodeValue;

//$rss_feed = "<div style=\"border:1px solid #aaa;padding:3px;\">";
//output elements from "<channel>"
$channel = "<p style=\"padding:0px;margin:0px;font-size:16pt;\"><a href=\"$channel_link\">$channel_title\n<br /><span style=\"font-size:9pt;\">$channel_desc</span></a></p>\n";

$feed = "";
//get and output "<item>" elements
$x=$xmlDoc->getElementsByTagName('item');
//echo count($x);
for ($i=0; $i<1; $i++) {
  $item_title=$x->item($i)->getElementsByTagName('title')
  ->item(0)->childNodes->item(0)->nodeValue;
  $item_link=$x->item($i)->getElementsByTagName('link')
  ->item(0)->childNodes->item(0)->nodeValue;
  //$item_desc=$x->item($i)->getElementsByTagName('description')
  //->item(0)->childNodes->item(0)->nodeValue;

  $feed .= "<p><a href=\"$item_link\">$item_title</a></p>\n";
}
//$rss_feed .= "</div>";
?>
