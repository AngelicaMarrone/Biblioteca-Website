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
		<title>Login</title>
		
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
		
		 <div class="content"> <h2>Inserisci le credenziali per effettuare l'accesso</h2>
            <form name="login" action="login.php" method="POST" onsubmit="return checkForm()">
                <p>Username: <input type="text" name="username" id="username" required></p>
                <p>Password: <input type="password" name="psw" id="password" required></p>

                <button type="submit">Ok</button>
                <button type="reset" >Pulisci</button>
            </form>
            <p id="avvisoUsername" class='errore'></p>
            <p id="avvisoPsw" class='errore'></p>
                <?php
                $esci=false;
                $generale_ok_length = '/^[a-zA-Z0-9%]{3,6}$/';
                $inizio = '/^([a-zA-Z]|[%])/';
                $num_nonnum = '/[^0-9][0-9]/';
                $username_tot = true;
                $psw_tot = true;
                $gia_loggato = false;
                if(isset($_SESSION["dentro"])){
                    echo ('<p class="unloggato">ERRORE: E&grave; gi&agrave stato effettuato un accesso! <a href="logout.php">Disconnettiti</a> per cambiare account</p>');

                    $gia_loggato = true;
                } else {
					$gia_loggato = false;
                    if(isset($_REQUEST["username"])){
                        if($_REQUEST["username"]==null){
                        echo "<p class='errore'>Inserire username!</p>";
                            $username_tot = false;
                    } elseif( preg_match( $generale_ok_length,$_REQUEST["username"] ) && preg_match( $inizio,$_REQUEST["username"] )
                            && preg_match( $num_nonnum , $_REQUEST["username"] )) {
                        $username_tot = true;
                    } else {
                            $username_tot = false;
                            echo "<p class='errore'>Formato username non adatto!</p>";
                        }
                    if(isset($_REQUEST["psw"])){
                        if($_REQUEST["psw"]==null) {
                            echo "<p class='errore'>Inserire password!</p>";
                            $psw_tot = false;
                        } elseif(!((preg_match("/^[a-zA-Z]{4,8}$/", $_REQUEST["psw"])) || (preg_match("/(?=.*[a-z])(?=.*[A-Z])/", $_REQUEST["psw"])))){
                                echo "<p class='errore'>Formato password non adatto!</p>";
                                $psw_tot = false;
                            } else {
                                $psw_tot = true;
                            }
                        }
                    }
                }
                if($username_tot==true && $psw_tot==true && $gia_loggato==false){
                    error_reporting(0);
                    $con = mysqli_connect("localhost","uReadOnly","posso_solo_leggere","biblioteca");
                    if ( mysqli_connect_errno() ){
                        printf("<p>Errore nel collegamento al DB : %s</p>\n", mysqli_connect_error());
                    } else {
                        $users = array();
                        $passes = array();
                        $books = array();
                        $i = 0;
                        $query = "SELECT username, pwd, COUNT(books.id) FROM users LEFT JOIN books ON books.prestito= users.username WHERE username!= '' GROUP BY username";
                        $stmt = mysqli_prepare($con, $query);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $user, $pass, $book);
                        while (mysqli_stmt_fetch($stmt)) {
                            $users[$i] = $user;
                            $passes[$i] = $pass;
                            $books[$i] = $book;
                            $i++;
                        }
                        mysqli_stmt_close($stmt);
                        mysqli_close($con);

                        $user_ok=false;
                        $pass_ok=false;

                        foreach (array_keys($users) as $j) {
                            if ($users[$j] == $_REQUEST["username"]) {
                                $user_ok = true;
                                if($passes[$j] == $_REQUEST["psw"]) {
                                    if(session_status()!==PHP_SESSION_ACTIVE)
                                        session_start();
                                    $_SESSION["username"] = $_REQUEST["username"];
                                    $_SESSION["prest"] = $books[$j];
                                    $pass_ok = true;
                                    $scadenza = time()+3600*48;
                                    setcookie("username", $_REQUEST["username"],$scadenza);
                                    $_SESSION["dentro"]=true;
                                    header('Location: libri.php');
                                }
                            }
                        }
                        if($pass_ok==false && $user_ok==true)
                            echo "<p class='errore'>Password sbagliata!</p>";

                        if($user_ok==false  && isset($_REQUEST["username"]))
                            echo "<p class='errore'>ERRORE: utente non presente nel DB!</p>";
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

<script>
    window.onload = function stampaLast() {
        var all = document.cookie.split("=");
        if (all[0] == 'PHPSESSID')
            document.getElementById('username').value = '';
        else {
            var nearly = all[1].split(";");
            var lastUser = nearly[0];
            for (let i = 0; i < lastUser.length; ++i) {
                if (lastUser.charAt(i) == '%' && lastUser.charAt(i + 1) == '2' && lastUser.charAt(i + 2) == '5') {
                    var primaParte = lastUser.split("%25");
                    lastUser = primaParte[0] + '%' + primaParte[1];
                }
            }
            document.getElementById('avvisoUsername').value = "";
            document.getElementById('avvisoPsw').value = "";
            document.getElementById('username').value = lastUser;
        }
    }

    function checkForm(){

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
        var pass = document.getElementById('password').value;
        var rePwd = /^[a-zA-Z]{4,8}$/;
		var reContieneP= /(?=.*[a-z])(?=.*[A-Z])/;
		
        if (!(rePwd.test(pass) && reContieneP.test(pass))){
            document.getElementById("avvisoPsw").innerHTML = "Formato password errato!";
            return false;
        }
        return true;
    }
</script>