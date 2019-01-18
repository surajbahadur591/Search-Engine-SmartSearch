<?php 
include ("config.php");
include ("classes/SiteResultProvider.php");
if(isset($_GET["key"])){
$key = $_GET["key"];

}
else {
	exit("Enter your search");
}

$type = isset($_GET["type"])? $_GET["type"] : "sites";

?>

<!DOCTYPE html>
<html>
<head>

  <title>Smart Search</title>

  <meta name="description" content="Search Smartly">
  <meta name="keywords" content="SmartSearch, smartsearch, smart, search, smartboy">
  <meta name="author" content="Suraj Bahadur">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image" href="resource/SSL.png">
	<link rel="stylesheet" type="text/css" href="css/style1.css">
</head>
<body>

	<div class="wrapper">

		<div class="header">
			<div class="header_content">

				<div class="LOGO_Container">
					<a href="index.php"><img src="resource/Smart Search LOGO.png" width="40%;"></a>
				</div>
				<div class="Search_Container">
					
					<form action="search.php" method="GET">

						<div class="SearchBarContainer">
							<input type="text" class="SearchBox" name="key" value="<?php echo $key ?>">
							<button class="SearchButton"><img src="resource/SSL.png"></button>

						</div>
						
					</form>

				</div>
				
			</div>
			

			<div class="tabsContainer">
				<ul class="tabslists">
					<li class=" <?php   echo $type =='sites'? 'active' : '' ?>"><a href='<?php echo"search.php?key=$key&type=sites"; ?>'>Sites</a></li>
					<li class=" <?php   echo $type =='images'? 'active' : '' ?>"><a href='<?php echo"search.php?key=$key&type=images"; ?>'>Images</a></li>

				</ul>
				
			</div>

		</div>
		<div class="resultSection">
			<?php 
			$resultprovider = new SiteResultProvider($con);
			$numbercount = $resultprovider->getNumResult($key);
			echo " <p class='resultscount'> $numbercount results found </p>";


			echo $resultprovider->getPages(1, 20, $key);
			 ?>
			




		</div>


	</div>
</body>
</html>
