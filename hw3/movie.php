<!DOCTYPE html>
<!-- Name: Ishmeet Singh Malhotra
	 CICS516
	 Student Number: 7785529499 -->





<html>
	<head>
 		<?php    //This is not a part of the assignment but i did it just for experimentation
 				//Adds the name of the movie to the title
			$movie = $_REQUEST["film"];
			$fp = file("{$movie}/info.txt");
			$fp[1] = trim($fp[0]);
		?>
		<title><?php print "$fp[0]"?> - Rancid Tomatoes</title>   
		<link rel="icon" href="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/rotten.gif" type="image/x-icon"/>

		<meta charset="utf-8">
		<link href="movie.css" type="text/css" rel="stylesheet">
	</head>

	<body>
	
	
	
		<div id="banner">
			<img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/banner.png" alt="Rancid Tomatoes">
		</div>
		<?php
			$movie = $_REQUEST["film"];
			$fp = file("{$movie}/info.txt");
			$fp[1] = trim($fp[1]);  //to remove excess spacing
		?>
		
		<h1 id="movName"> <?php print "$fp[0]"; print "({$fp[1]})"; ?> </h1>
		<div id="overallArea">
					

		<div id="leftSection">
		
		<div id="rating" >                
			<span>
				<?php                     // To choose the correct image as per rating
					if($fp[2] <60)
					{
				?>
			<img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/rottenbig.png" alt="Rotten">	
				<?php
					}
					else
					{
				?>
			<img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/freshbig.png" alt="Rotten">	
				<?php
					}
				?>
			
			</span>
			<span>
			<?php   print "{$fp[2]}%"; ?>
			</span>
		</div>
		
		
		<div class="column">
			
			<?php 
				$movieReview = glob($movie . "/review*.txt");
				$countReviews = count($movieReview);
				
				for($a = 0; $a <= ceil(($countReviews/2)-1); $a++){ ?>
				<p class="reviewBox">
            			<?php  // save review info in seperate variables and trim extra spaces
            			list($movieWriteup, $state, $reviewWriter, $source) = file($movieReview[$a]);
            			$movieWriteup = trim($movieWriteup);
            			$state = trim($state);
            			$reviewWriter = trim($reviewWriter);
            			$source = trim($source);
            			
					?>

					<img src = <?php   //Displaying appropriate thumbnail as per the file's info'
						if($state == "FRESH"){
						echo "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/fresh.gif";
					}
					else{echo "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/rotten.gif";
					}
					?>
					alt=<?php
						if($state == "FRESH"){
							echo "FRESH";
						} 
						else{
							echo "ROTTEN";
						}
	            	?>
	            	/>

	            	<q><?php echo $movieWriteup; ?></q>
	            </p>

	            <p class = "personalInfo">
	            	<img src = "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/critic.gif" alt = "critic" />
	            	<?php print $reviewWriter; ?>
	            	<br/>
	            	<span><?php echo $source; ?></span>

	            </p>

	            <?php } ?>
	            
	            </div>
					
			
	
	<div class="column">
			
			<?php 
				$movieReview = glob($movie . "/review*.txt");
				$countReviews = count($movieReview);
				
				for($a = ceil(($countReviews/2)); $a < $countReviews; $a++){ ?>
				<p class="reviewBox">
            			<?php list($movieWriteup, $state, $reviewWriter, $source) = file($movieReview[$a]);
            			$movieWriteup = trim($movieWriteup);
            			$state = trim($state);
            			$reviewWriter = trim($reviewWriter);
            			$source = trim($source);
            			
					?>

					<img src = <?php 
						if($state == "FRESH"){
						echo "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/fresh.gif";
					}
					else{echo "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/rotten.gif";
					}
					?>
					alt=<?php
						if($state == "FRESH"){
							echo "FRESH";
						} 
						else{
							echo "ROTTEN";
						}
	            	?>
	            	/>

	            	<q><?php echo $movieWriteup; ?></q>
	            </p>

	            <p class = "personalInfo">
	            	<img src = "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/critic.gif" alt = "critic" />
	            	<?php print $reviewWriter; ?>
	            	<br/>
	            	<span><?php echo $source; ?></span>

	            </p>

	            <?php } ?>
	            
	            
	        
	        		</div>
	            
	            
	
	
	
		
  		</div>
  		
  		<div id="generalOverview">
			<!--<div>-->
			<?php
			echo "<img src='$movie/overview.png' alt='general overview'>"
			?>
	<!--	</div>-->
	<div id ="overviewText">

		<dl>
		
		<?php // Displaying overview information
			$overview = file("{$movie}/overview.txt");
						
			foreach($overview as $lines)
  			{	
  				
								  				  				
				$token = explode(":", $lines, 2);   // Exploding in 2 parts because links have multiple ':' 
				?>
				<dt><?php print "$token[0]"; ?></dt>
				<dd><?php print "$token[1]"; ?></dd>
			<?php	
				
			}
			?>
		
			
			
		</dl>
		</div>
	 </div>
		
				<p id="pad">
					<?php echo "(1-$countReviews) of $countReviews"; ?>
				</p>
		
		
		</div>
		
			

		<div id="fixing" >
			<a href="https://webster.cs.washington.edu/validate-html.php"><img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/w3c-xhtml.png" alt="Valid html"></a> <br>
			<a href="https://webster.cs.washington.edu/validate-css.php"><img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/w3c-css.png" alt="Valid CSS"></a>
		</div>
	
	

</body>
</html>