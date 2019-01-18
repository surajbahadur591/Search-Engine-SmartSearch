<?php 
include("classes/DomDocumentParser.php");
include ("config.php");


$alreadyCrawledImage = array();
$alreadycrawled = array();
$crawling = array();


function insertLinks ( $url, $title, $description, $keyword){
	global $con;

	$query = $con->prepare("INSERT INTO sites(url, title, description, keyword) VALUES (:url, :title, :description, :keyword)");
	$query -> bindParam(":url", $url);
	$query -> bindParam(":title", $title);
	$query -> bindParam(":description", $description);
	$query -> bindParam(":keyword", $keyword);
	return $query-> execute();
}

function insertImage ($url, $src, $alt, $title) {
	global $con;

	$query = $con->prepare("INSERT INTO imagess(siteURL, imageURL, alt,  title) VALUES(:siteURL, :imageURL, :alt,  :title)");
	$query -> bindParam(":siteURL", $url);
	$query -> bindParam(":imageURL", $src);
	$query -> bindParam(":alt", $alt);
	$query -> bindParam(":title", $title);
	return $query-> execute();
}

function linkExist ( $url){
	global $con;

	$query = $con->prepare("SELECT * FROM sites where url = :url");
	$query -> bindParam(":url", $url);
	$query-> execute();
	return  $query->rowCount() != 0;
}

function createLink($src, $url){

	$scheme = parse_url($url)["scheme"];
	$host = parse_url($url)["host"];
	if(substr($src, 0, 2 )=="//"){
		$src = $scheme.":". $src;
	}
	else if (substr($src, 0, 1 )=="/"){
		$src = $scheme ."://".$host. $src;
	}
	return $src;
}

function getDetails($url){
	global $alreadyCrawledImage;
	$parser = new DomDocumentParser($url);
	$titleArray = $parser->getTitle();
	$title = $titleArray ->item(0)->nodeValue;
	$title = str_replace("\n", "", $title);

	if($title == "") {
		return;
	}

	$description = "";
	$keyword ="";
	$metaArray = $parser->getMeta();

	foreach($metaArray as $meta) {

		if($meta-> getAttribute("name")=="description"){
			$description = $meta->getAttribute("content");
		}

		if($meta-> getAttribute("name")=="keyword"){
			$keyword = $meta->getAttribute("content");
		}
	}

	$description = str_replace("\n", "", $description);
	$keyword = str_replace("\n", "", $keyword);


	echo "URL: $url <br>Title: $title <br>Description: $description<br>Keywords: $keyword <br><br>";
	if(linkExist($url)){
		echo "$url present";
	}
	else if (insertLinks( $url, $title, $description, $keyword)){
		echo "Success :  $url ";
	}
	else {
		echo " failed:  $url";
	}  

	$imageArray = $parser->getImages();

	foreach( $imageArray as $image) {
		$src = $image->getAttribute("src");
		$title = $image->getAttribute("title");
		$alt = $image->getAttribute("alt");

		if(!$title && !$alt){
			continue;
		}


		$src= createLink($src, $url);
		if (!in_array($src, $alreadyCrawledImage)) {
			$alreadyCrawledImage[] = $src;

			insertImage($url, $src, $alt, $title);
		}

	}

}

function followLinks($url){
	global $alreadycrawled;
	global $crawling;
	$parser = new DomDocumentParser($url);
	$linklist = $parser->getLinks();

	foreach ($linklist as $link){
		$href = $link->getAttribute("href");

		if(strpos($href, '#')!== false){
			continue;
		}
		else if(substr($href, 0, 11) == "javascript:"){
			continue;
		}
		$href = createLink($href, $url);

		if (!in_array($href, $alreadycrawled)){
			$alreadycrawled[]= $href;
			$crawling[]= $href;
			getDetails($href);
		}
		else return;
	}

	array_shift($crawling);

	foreach ($crawling as $site) {
		followLinks($site);
	}
}

$starturl = "https://timesofindia.indiatimes.com";
followlinks( $starturl);

?>