<?php 
class SiteResultProvider {
	private $con;
	public function __construct($con){

$this->con = $con;
	}

	public function getNumResult($key){
		$query = $this->con->prepare("SELECT COUNT(*) as total FROM sites WHERE 
			title LIKE :key OR
			url LIKE :key OR
			description LIKE :key OR
			keyword LIKE :key");
		$searchkey = "%" . $key . "%";
		$query->bindParam(":key", $searchkey);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row["total"];
	}
	public function getPages ($page, $pagesize, $key ){

			$query = $this->con->prepare("SELECT * FROM sites WHERE 
			title LIKE :key OR
			url LIKE :key OR
			description LIKE :key OR
			keyword LIKE :key 
			ORDER BY clicks DESC");


		$searchkey = "%" . $key . "%";
		$query->bindParam(":key", $searchkey);
		$query->execute();

		$resulthtml = "<div class='siteresults'>";

		while($row = $query->fetch(PDO::FETCH_ASSOC)){
			$id = $row["id"];
			$url = $row["url"];
			$title = $row["title"];
			$description = $row["description"];

			$resulthtml .= "<div class='resultscontainer'> 

				<h3 class='title'>
					<a class='result' href='$url'>$title</a>
				</h3>
				<span class='url'>$url</span>
				<span class='description'>$description</span>


			<div>";
		}

		$resulthtml .= "</div>";
		return $resulthtml;

	}

}
 ?>
