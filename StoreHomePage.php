<?php
/*Alcune cose commentate si ripetono nei programmi, quindi le citerò qui e non più avanti come per esempio la condizione di ingresso per la verifica del TTK */
session_name('HillDownService');
session_start();//starto la sessione

$db_name='HillDownGameStore_db';
$add_on_table='games_table';

if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {  //Come in ogni pagina da qui in poi, il corpo principale del sito verra eseguito solo se nella SESSION la variavile ttk esisterà e sarà maggiore di 0;
  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);           //ho messo il doppio fattore isset() e >0 per una doppia sicurezza di esecuzione dello script nel modo corretto
  if (mysqli_connect_errno()) {
      printf("Errore di connessione: %s\n", mysqli_connect_error());
      exit();
  }

  $query="SELECT * FROM `{$add_on_table}` WHERE titolo NOT LIKE 'DLC%';"; //Nel catalogo mostro solo i giochi non i DLC
  $return=mysqli_query($sqlConnect, $query);

/*Le due funzioni succesive isset($_GET(...)) servono per indirizzare l'utente verso la pagina di gameList dove il gioco verra presentato per il suo acquisto e quello di ulteriri DLC.
Nella mia idea originale (un pò come avviene anche su STEAM) si possono comprare anche DLC non avendo in libreria il gioco originale.
Certo come poi si potrà notare i dlc in libreria non verranno mostrati. Ma saranno cmq presenti e aggiunti al gioco (anche se non si vedono)
Inoltre non ho voluto creare un campo univoco per i due brach di codice, differenziandoli poi in base al value, per una questione di velocità. In seguito nel progetto farò il contrario (per esercizio ulteriore e diffrenziare il codice)*/

  if(isset($_GET['game'])){
    $_SESSION['id_game']=$_GET['game']; //Quando selezioneremo il pulsante BUY verremo trasportati nella pagina gameList dove attraverso $_SESSION['id_game'], la pagina si configurerà per mostarci il gioco in questione
    $sqlConnect->close();
    header('Location: gameList.php');
  }

  if (isset($_GET['logout'])) {
    unset($_SESSION);
    session_destroy();
    $sqlConnect->close();
    header('Location: login.php');
  }

   $sqlConnect->close();
   $_SESSION['ttk']--; //Esco e sottraggo il TTK
}
else {
  $sqlConnect->close();
  unset($_SESSION);
  session_destroy();
  header('Location: login.php');
}



 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="StoreHomePage_.css" media="screen">
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
  </head>
  <body>
    <div class="flexContainer">
      <div class="flexNavBar">
        <div>
          <img src="logo2.png" alt="logo">

        </div>


        <div class="navBarStruct">
          <table>
            <tr>
              <td><a href="StoreHomePage.php">Home</a></td>
              <td><a href="profile.php">Profilo</a></td>
              <td><a href="library.php">Libreria</a></td>
              <td><img src="cart.png" alt="cart" usemap="#cart">
                <map name="cart">
                  <area shape="rect" coords="0,82,89,8" href="cartPage.php" alt="cart">
                </map>
              </td>
              <td>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                    <input type="submit" name="logout" value="logout">
                </form>
              </td>
            </tr>
          </table>




        </div>

        <div class="">


        </div>

      </div>
      <div class="table">
        <form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">


        <?php
        $struct="<table>";
        while ($row=mysqli_fetch_array($return)) {
          $struct.="<tr>";
          $struct.="<td id=\"image\"><img src=\"{$row['img']}\" alt=\"{$row['titolo']}\"></td>"; //Ho preferito usare una tabella per mostrare i giochi per una maggiore comodità nell'organizzazione,
          $struct.="<td>{$row['titolo']}</td>";                                                  //non che una maggiore organizzazione e riuso per aggiungere vari giochi in modo semplice ede efficace
          $struct.="<td>{$row['prezzo']}€</td>";
          $struct.="<td>{$row['descrizione']}</td>";
          $struct.="<td><button type=\"submit\" name=\"game\" value=\"{$row['id_prodotto']}\">Buy</button></td>";
          $struct.="</tr>";
        }
        $struct.="</table>";

        echo $struct;

         ?>
       </form>


      </div>

    </div>

  </body>
</html>
