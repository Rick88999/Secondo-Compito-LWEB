<?php
/*La pagina di game list serve per visualizzare il gioco in maggior dettavglio. Inoltre è la pgina che permette, se non abbiamo già il gioco o DLC, di acquistarlo.
Possiamo arrivarci dalla HOME con il pulsante BUY o dalla Libreria con il pulsante SEE*/
session_name('HillDownService');
session_start();

$db_name='HillDownGameStore_db';
$add_on_table='games_table';
$users_game_list='users_game_list';
$active_cart_table='active_cart';
$flag=0;


$sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}

if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {
  $query="SELECT * FROM `{$users_game_list}` WHERE id_user=\"{$_SESSION['id']}\";"; //Inizio richiedendo tutte le tuple contenenti la corrispondenza id_user | id_game dalla tabella users_game_list
  $return=mysqli_query($sqlConnect, $query);
  $query="SELECT id_user, id_prodotto FROM `{$active_cart_table}` WHERE id_user=\"{$_SESSION['id']}\";"; //Richiedo tutte le tuple nel carrello attivo
  $cart_check=mysqli_query($sqlConnect, $query);

  if (isset($_POST['send'])) {

    if ($_POST['send']=='Al carrello-->') {
      if (isset($_POST['games_into_cart']) && !(empty($_POST['games_into_cart']))) { //Se ho selzionato giochi da acquistare e vado sul pulsante AL Carrello
        while($row0=mysqli_fetch_array($return)){                       //Verifico se non possiedo già i giochi
          foreach ($_POST['games_into_cart'] as $v) {
            if($row0['id_game']==$v) $flag++;
          }
        }
        while($row0=mysqli_fetch_array($cart_check)){               //E verifico se non gli ho già messi nel carrello
          foreach ($_POST['games_into_cart'] as $v) {
            if($row0['id_prodotto']==$v) $flag++;
          }
        }
      }

      if($flag==0 && !(empty($_POST['games_into_cart']))){ //In caso è tutto ok vado nel carrello per acquistare altrimenti la pagina si aggiornerà con un messaggio
        $_SESSION['ids_to_cart']=$_POST['games_into_cart'];//che invitera gli utenti a deselezionare il gioco che già hanno acquistato o posto nel carrello attivo
        $sqlConnect->close();
        header('Location: cartPage.php');
      }

    }
    elseif ($_POST['send']=='logout') {
      unset($_SESSION);
      $sqlConnect->close();
      session_destroy();
      header('Location: login.php');
    }
  }

/*In seguito alla verifica della condizione isset($_POST), il programma inizialmente proseguira con una Query
che selezionerà tutti i giochi avente l'id passato dalla pagina precedente*/
  $query="SELECT * FROM `{$add_on_table}` WHERE id_prodotto=\"{$_SESSION['id_game']}\";";
  $return=mysqli_query($sqlConnect, $query);
  $row1=mysqli_fetch_array($return);//Qui mettiamo la tupla del titolo in questione
  if($row1){
    $query="SELECT id_prodotto, titolo, prezzo FROM `{$add_on_table}` WHERE titolo LIKE 'DLC({$row1['titolo']})%';"; //Prendermo inoltre tutte le tuple DLC da poi mostrare come prodotti ricoleggati al main gioco e
    $return=mysqli_query($sqlConnect, $query);                                                                      //ulteriormente acquistabili(anche separatamente)
  }
  $sqlConnect->close();
  $_SESSION['ttk']--;
}
else {
  $sqlConnect->close();
  unset($_SESSION);
  session_destroy();
  header('Location: login.php');
}

/*Divido poi nel codice HTML la pagina in due parti: una informativa con tutte le info del gioco principale; una di selezione, dove potremmo condermare in prodotti
da voler acquistare e proseguire per il carrello*/
 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="gameList_.css" media="screen">
  </head>
  <body id="bodyGameList">
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
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input type="submit" name="send" value="logout">
                </form>
              </td>
            </tr>
          </table>

        </div>
      </div>

      <div class="flex_showing_box">
        <div class="flex_showing_innerbox_imageAndData">
          <div class="show_image">
            <?php
            if($row1){
              echo "<img src=\"{$row1['img']}\" alt=\"{$row1['titolo']} mancante\">";
            }
             ?>
          </div>
          <div class="show_data">
            <?php
            if ($row1) {
              echo "<table>";
              echo "<tr>";
              echo "<td>".$row1['titolo']."</td>";
              echo "</tr>";
              echo "<td>".$row1['versione']."ver.</td>";
              echo "<tr>";
              echo "</tr>";
              echo "<td>".$row1['descrizione']."</td>";
              echo "<tr>";
              echo "</tr>";
              echo "</table>";
            }

            ?>

          </div>
        </div>
        <div class="orderContainer">
          <div class="gamesBox">
            <form class="" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <?php
            if($row1){
              $ids_to_cart=array();
              $ids_to_cart[]=$row1[0];
              echo "<h3>Seleziona giochi o DLC che vuoi acquistare:</h3>";
              echo "<label>";
              echo "<input type=\"checkbox\" name=\"games_into_cart[]\" value=\"{$row1['id_prodotto']}\"> {$row1['titolo']} | {$row1['prezzo']}€"; //Mostro il gioco principale
              echo "</label><br>";
              while ($row2=mysqli_fetch_array($return)) {                      //Carico anche i vari DLC ancquistbili
                $ids_to_cart[]=$row2[0];
                echo "<label>";
                echo "<input type=\"checkbox\" name=\"games_into_cart[]\" value=\"{$row2['id_prodotto']}\"> {$row2['titolo']} | {$row2['prezzo']}€";
                echo "</label><br>";
              }
            }

             ?>

          </div>
          <div class="sendBox">
            <div>
              <?php
              if ($flag>0) {
                echo "<p>Sei già in possesso di uno o più dei contenuti selezionati, oppure lo hai già messo nel carrello</p>";
              }
               ?>
            </div>
            <div id="button_cart">
              <button type="submit" name="send" value="Al carrello-->">Al carrello--></button>
            </div>


          </form>

          </div>

        </div>

      </div>


    </div>

  </body>
</html>
