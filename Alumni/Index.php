<?php
include('db_connection.php');
        $name = "";
        $therapie = "";
        $sql = "SELECT * FROM alumni_information";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
        
        // output data of each row
        while($row = $result->fetch_assoc()) {
        //echo "Name: " . $row["naam"]. "<br>";
            $name = $row["naam"];
            $therapie = $row["therapie"];
        }
    } else {
    echo "Error 404";
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Alumni</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
  
<body>
	<div class="container">
		<div class="sidebar">
			<div class="sidebar-top">
				<img class="profile-image" src="http://www.slate.com/content/dam/slate/blogs/xx_factor/2014/susan.jpg.CROP.promo-mediumlarge.jpg" />
				<div class="profile-basic">
					<h1 class="name"><?php echo $name; ?></h1>
				</div>
			</div>
			<div class="profile-info">
				<p class="key">Email:</p>
				<p class="value">oosterhoftherapie@gmail.com</p><br>
                <p class="key">telefoon:</p>
				<p class="value">0612345678</p><br>
                <p class="key">Adres:</p>
				<p class="value" >
					Kerkstraat 42<br/>
					5632, Eindhoven<br/>
				</p><br>
                <p class="key">website:</p>
				<p class="value">www.olgaoosterhof.nl</p><br>
			</div>

		</div>

        
		<div class="content">
			<div class="work-experience">
				<h1 class="heading"><?php echo $therapie; ?></h1>
				<div class="info">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    
                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
                    
                    <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
				</div>
			</div>
		</div>
	</div>
</body>
</html>


