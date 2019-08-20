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
		<title>New</title>
		
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
			<div class="content"> <h2>Inserisci username e password per registrarti</h2>
				<form name="new" action="new.php" method="POST" onsubmit="return checkForm()">
					<p>Username: <input type="text" name="username" id="username" required></p>
					<p>Password: <input type="password" name="psw" id="password" required></p>
					<p>Ripeti password: <input type="password" name="psw2" id="password2" required></p>
					<button type="submit">Registra</button>
					<button type="reset" >Annulla</button>
				</form>
				<p id="avvisoUsername" class='errore'></p>
				<p id="avvisoPsw" class='errore'></p>
				<p id="avvisoDiv" class='errore'></p>
					<?php
					$result=true;
					$esci=false;
					$generale_ok_length = '/^[a-zA-Z0-9%]{3,6}$/';
					$inizio = '/^([a-zA-Z]|[%])/';
					$num_nonnum = '/[^0-9][0-9]/';
					$username_tot = true;
					$psw_tot = true;
						if(isset($_REQUEST["username"])){
							if($_REQUEST["username"]==null){
							echo "<p class='errore'>Inserire username!</p>";
								$username_tot = false;
						} elseif( preg_match( $generale_ok_length,$_REQUEST["username"] ) && preg_match( $inizio,$_REQUEST["username"] )
								&& preg_match( $num_nonnum , $_REQUEST["username"] )) {
							$username_tot = true;
						} else {
								$username_tot = false;
								echo "<p class='errore'>Formato username errato!</p>";
							}
						if(isset($_REQUEST["psw"])&& isset($_REQUEST["psw2"])){
							if($_REQUEST["psw"]==null) {
								echo "<p class='errore'>Inserire password!</p>";
								$psw_tot = false;
							}if($_REQUEST["psw2"]==null) {
								echo "<p class='errore'>Ripeti la password!</p>";
								$psw_tot = false;
							} elseif(!((preg_match("/^[a-zA-Z]{4,8}$/", $_REQUEST["psw"])) || (preg_match("/(?=.*[a-z])(?=.*[A-Z])/", $_REQUEST["psw"])))){
									echo "<p class='errore'>Formato password errato!</p>";
									$psw_tot = false;
								}
								elseif($_REQUEST["psw"] != $_REQUEST["psw2"]){
									echo "<p class='errore'>Le password non coincidono</p>";
									$psw_tot=false;
									
								}
								else {
									$psw_tot = true;
								}
							}
						}
						
						if($username_tot == true && $psw_tot == true){
						error_reporting(0);
						$con = mysqli_connect("localhost","uReadWrite","SuperPippo!!!","biblioteca");
						 if (mysqli_connect_errno()) 
                        echo "<p>Errore connessione al DBMS: ".mysqli_connect_error()."</p>\n";
						  elseif($_REQUEST["username"]!="" && $_REQUEST["psw"]!=""){
						
						
						$username=$_REQUEST["username"];
						$password=$_REQUEST["psw"];
                        $query = "INSERT INTO users(username,pwd) VALUES (?,?)";
                        $stmt = mysqli_prepare($con,$query);
                        mysqli_stmt_bind_param($stmt,"ss",$username,$password);
                        $result = mysqli_stmt_execute($stmt);
				
						
                        
                        if(!$result){
						header('Location: fallito.php');
						}
                            
                        else{
						header('Location: successo_registrazione.php');
                          }  
                        mysqli_stmt_close($stmt);
						}
                    
                    mysqli_close($con);
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







<script>
	    function checkForm(){
		
		document.getElementById('avvisoDiv').value="";
        document.getElementById('avvisoUsername').value = "";
        document.getElementById('avvisoPsw').value = "";
        var user = document.getElementById('username').value;
        var reGenerale = /^[a-zA-Z0-9%]{3,6}$/;
        var reInizio = /^([a-zA-Z]|[%])/;
        var reContiene = /[^0-9][0-9]/;
        if(!(reGenerale.test(user)&&reInizio.test(user)&&reContiene.test(user))){
            document.getElementById("avvisoUsername").innerHTML="Formato utente errato!";
            return false;
        }
		var pass2 = document.getElementById('password2').value;
        var pass = document.getElementById('password').value;
        var rePwd = /^[a-zA-Z]{4,8}$/;
		var reContieneP= /(?=.*[a-z])(?=.*[A-Z])/;
		
        if (!(rePwd.test(pass) && reContieneP.test(pass))){
            document.getElementById("avvisoPsw").innerHTML = "Formato password errato!";
            return false;
        }
		
		if (pass != pass2) {
			document.getElementById("avvisoPsw").innerHTML="Le password non coincidono!";
			passwordForm.password.focus();
			passwordForm.password.select();
			return false;
		} 
		

        return true;
		}
    
	
</script>