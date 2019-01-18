<?php 

class DomDocumentParser {

	private $doc;

	public function __construct($url) {
		$options = array(
			'http'=>array('method'=>"GET", 'header'=>"User-Agent: Smartboy/9.1\n")
		);

		$context = stream_context_create($options);

		$this->doc = new DomDocument();
		@$this->doc-> loadHTML(file_get_contents($url, false, $context));

	}
	public function getLinks(){
		return $this->doc->getElementsByTagName("a");
	}
	public function getTitle(){
		return $this->doc->getElementsByTagName("title");
	}

	public function getMeta(){
		return $this->doc->getElementsByTagName("meta");
	}

	public function getImages(){
		return $this->doc->getElementsByTagName("img");
	}
	
}

 ?>