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
		<title>Fallito</title>
		
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
				echo '<p>'.$totbook_u.'</p>';
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
			<div class="content"> <h2>ERRORE: non &egrave stato possibile effettuare la registrazione </h2>
			
			<p><a href="new.php">Riprova</a></p>
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