<?php
if(session_status()!==PHP_SESSION_ACTIVE)
    session_start();

?>
<!DOCTYPE HTML>
<html lang="it">
<head>
		<meta charset="UTF-8">
		<meta name="author" content="Angelica Marrone">
		<link type="text/css" rel="stylesheet" href="style.css">
		<title>Restituzione</title>
		
</head>
<body>
			<div colspan=2 class="header">
			<a href="home.php"><img src="logo1.png" alt="TO.Biblio: La tua biblioteca online a Torino"></a>
			</div>
			<div class="user"><h3>User:</h3>
				<?php
				if(isset($_SESSION["username"]))
					echo '<p>'.$_SESSION["username"].'</p>';
				else 
					echo '<p>Anonimo</p>';
				?>
			<h3>Libri sul tuo scaffale:</h3>
				<?php
				if(isset($_SESSION["prest"])){
					$con = mysqli_connect("localhost","uReadOnly","posso_solo_leggere","biblioteca");
				if ( mysqli_connect_errno() ){
                        printf("<p>Errore nel collegamento al DB : %s</p>\n", mysqli_connect_error());
                    }else{
					$totbook_u=0;
					$query="SELECT COUNT(*) FROM books WHERE prestito = '" . $_SESSION["username"] . "';";
					$stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt, $totbook_u);				
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
					$scaffale=$totbook_u-1;
				echo '<p>'.$scaffale.'</p>';
				}
				}
				else
					echo '<p>0</p>';
					?>
			</div>
			<div class=navigator><h2>men&ugrave;</h2>
			<nav>
				<a href="home.php">Home</a>
				<?php
                    if(!isset($_SESSION["dentro"])){
                       echo ("<p><a href='login.php'>Login</a></p>");
                       echo ("<p class='inattivo'>Logout</p>");
                    } else {
                        echo("<p><a href='logout.php'>Logout</a></p>");
                        echo("<p class='inattivo'>Login</p>");
                    }
                    ?>
				<p><a href="new.php">New</a></p>
				<p><a href="libri.php">Libri</a></p>
			</nav>
			</div>
			<div class="content"> <h2>La restituzione &egrave avvenuta con successo!</h2>
					<?php
					$con = mysqli_connect("localhost","uReadOnly","posso_solo_leggere","biblioteca");
				if ( mysqli_connect_errno() ){
							printf("<p>Errore nel collegamento al DB : %s</p>\n", mysqli_connect_error());
						}else{
							$select= $_REQUEST["ciao"];
							$query = "SELECT * FROM books";
							$resultlibri = mysqli_query($con, $query);
							if (mysqli_num_rows($resultlibri) > 0){
								while ($row = mysqli_fetch_assoc($resultlibri)) {
									if ($row["id"] == $select){
										$durataprestito = round((time() - strtotime($row["data"])) / 60 / 60 / 24);
										$con1 = mysqli_connect("localhost","uReadWrite","SuperPippo!!!","biblioteca");
										$query2= "UPDATE books SET prestito='', data=".date('Y-m-d')." ,giorni=0 WHERE id= ".$row['id'].";";
										mysqli_query($con1,$query2);
										
									
									}
									
									
								}
							}
						}
						
				?>
				Restituzione avvenuta dopo <?php echo $durataprestito ?> giorni.
				<p><a href="libri.php">Torna a Libri</a></p>
			</div>
   	
		
			<div colspan=2 class="footer">
            <?php echo '<p class="foot">Ti trovi in: '.basename($_SERVER['PHP_SELF']).'</p>'; ?>
            <footer class="footer" id="foot"></footer>
            <script type="text/JavaScript">
                let author = document.getElementsByTagName("meta")[1].content;
                document.getElementById("foot").innerHTML = "<p> Autore: " + author + "<\/p>";
            </script>
			</div>

	</body>
</html>