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
		<title>Libri</title>
		
		
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
			<div class="navigator"><h2>men&ugrave;</h2>
			<nav>
				<p><a href="home.php">Home</a></p>
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
			<div class="content">
		<?php 
			if(!isset($_SESSION["dentro"])){
				
				$con = mysqli_connect("localhost","uReadOnly","posso_solo_leggere","biblioteca");
				if ( mysqli_connect_errno() ){
                        printf("<p>Errore nel collegamento al DB : %s</p>\n", mysqli_connect_error());
                    }else{
					$totbooks=0;
					$totbooksdisp=0;
					$query="SELECT COUNT(*) FROM books";
					$stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt, $totbooks);				
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
					$query="SELECT COUNT(*) FROM `books`WHERE prestito= ''";
					$stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt, $totbooksdisp);				
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_close($stmt);
					}
				printf ("<p>Libri esistenti in biblioteca: %s</p>",$totbooks);
				printf ("<p>Libri disponibili al prestito: %s</p>",$totbooksdisp);
				echo'<p><a href="new.php">Registrati</a> o effettua il <a href="login.php">login</a> per prendere in prestito dei libri.  </p>';
				mysqli_close($con);
				}
			else {
				echo '<h2>Libri sul tuo scaffale </h2>';
				$con = mysqli_connect("localhost","uReadOnly","posso_solo_leggere","biblioteca");
				if ( mysqli_connect_errno() ){
							printf("<p>Errore nel collegamento al DB : %s</p>\n", mysqli_connect_error());
						}else{
					$sql = "SELECT * FROM books";
					$resultlibri = mysqli_query($con, $sql);
					
                  if (mysqli_num_rows($resultlibri) > 0) {
					
                    
                    echo "<form name='restituisci' action='restituzione.php' method='GET'>";
                    echo "<table><tr><th>Numero libro </th><th> Autore </th><th> Titolo </th><th> Data </th><th> Giorni </th><th>Restituisci</th></tr>";
					while ($row = mysqli_fetch_assoc($resultlibri)) {
						if ($row["prestito"] == $_SESSION["username"]){
                    echo "<tr><td>".$row["id"]."</td><td>".$row["autori"]."</td><td>".$row["titolo"]."</td><td>".$row["data"]."</td><td>".$row["giorni"]."</td>" ;
                    echo "<td><button type='submit' name='ciao' value='" . $row["id"] . "' formaction='restituzione.php'>Restituisci</button></td></tr>";
                    }
					}
					}
                    
                    
                    
                    echo "</table>";
                    echo "</form>";
					
					
					echo '<h2>Libri disponibili in biblioteca</h2>';
					$query = " SELECT autori,titolo,data,giorni,prestito FROM books WHERE prestito <> ''";
                    $stmt1 = mysqli_prepare($con, $query);
					
                    
                    mysqli_stmt_execute($stmt1);
                    mysqli_stmt_bind_result($stmt1, $author_books,  $title_books, $date_books, $days_books,$nomprestito);
                    echo "<form name='restituisci' action='libri.php' method='get'>";
                    echo "<table><tr><th> Autore </th><th> Titolo </th><th> Stato</th></tr>";
                    while (mysqli_stmt_fetch($stmt1)){
                         echo "<tr><td>".$author_books."</td><td>".$title_books."</td>" ;
						if ($nomprestito <> $_SESSION["username"])
							echo "<td>Non disponibile</td>";
					    else
						{if (time() - strtotime($date_books) > $days_books * 24*60*60)
							echo "<td>Prestito scaduto</td>";
						
						else
							echo "<td>In prestito</td>";
						}
						
                    }
					$query= " SELECT autori,titolo,id FROM books WHERE prestito = ''";
                    $stmt1 = mysqli_prepare($con, $query);
					$resultlibri = mysqli_query($con, $query);
                    
                    mysqli_stmt_execute($stmt1);
                    mysqli_stmt_bind_result($stmt1, $author_books,  $title_books, $id_y);
                    echo "<form name='restituisci' action='libri.php' method='GET' >";
                    echo "<table><tr><th> Autore </th><th> Titolo </th><th> Stato</th></tr>";
                    while (mysqli_stmt_fetch($stmt1)&& $row = mysqli_fetch_assoc($resultlibri)){
                         echo "<tr><td>".$author_books."</td><td>".$title_books."</td>" ;
                        echo "<td><input type='checkbox' name='libro[]' value='" . $row["id"] . "'> Prendi in prestito</td></tr>";
					
                    }
					
					mysqli_stmt_close($stmt1);
                  
                    
                    mysqli_close($con);
                    echo "</table>";
                    echo "<p><button type='submit'>Prendi in prestito</button></p>"; 
					echo "Numero di giorni: <input type='text' name='day' id='day'>";
					echo "</form>";
					
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
				
				}
				}
					
					$numlibri=$totbook_u;
					
					if (isset($_REQUEST["day"])){
					if(is_numeric($_REQUEST["day"]))
					{
						 if (isset($_REQUEST["libro"])){
							$selezionati = $_REQUEST["libro"];
						if (count($selezionati)+ $numlibri <= 3 ){
							$con1 = mysqli_connect("localhost","uReadWrite","SuperPippo!!!","biblioteca");
							if ( mysqli_connect_errno() ){
                        printf("<p>Errore nel collegamento al DB : %s</p>\n", mysqli_connect_error());
							}else{
							foreach($_REQUEST["libro"] as $selected){
							$sql = "UPDATE books SET prestito = '" . $_SESSION["username"] . "',data=NOW(),giorni='" . $_REQUEST["day"] . "'  WHERE id=$selected;";
							
							mysqli_query($con1, $sql);}
							header('Location: successo_prestito.php');
							 
							 }
							
							mysqli_close($con1);
						}
						else 
							echo "<p class='errore'>ERRORE: numero massimo superato! Puoi prendere in prestito al massimo 3 libri contemporaneamente</p>";
						  }
						 else
							echo "<p class='errore'>ERRORE: seleziona i libri da prendere in prestito</p>";
						
						
					}	
						else if ($_REQUEST["day"] == NULL)
							echo "<p class='errore'>ERRORE: inserisci il numero di giorni e seleziona i libri da prendere in prestito</p>";
						else
							echo "<p class='errore'>ERRORE: il formato &egrave errato! Introduci solo  numeri</p>";
					}
					}
				}
		
		 
		  
		
			
		?>
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












