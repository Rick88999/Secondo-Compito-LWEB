<?php
/*Questa pagina serve da cart per il sito. Ogni gioco o DLC verra mostrato e pota essere selezionato per la rimozione,
oppure si pota andare al pagamento. (Ovviamente essendo un HOMEWORK il pulsante payAll riporterà alla pagina di HOME e non ad una di transazione).
Altra cosa importante è come è organizzata la tabella active_cart: ogni utente avrà un id utente nel progetto. Quindi ho pensato che ogni carrello attivo per utente fosse un insieme di tuple riferite all'utente stesso
con chiave primaria la coppia [id_utente + id_prodotto]. Quindi più tuple per lo stesso utente, ma con prodotti diversi.*/
session_name('HillDownService');
session_start();

$db_name='HillDownGameStore_db';
$add_on_table='games_table';
$users_game_list='users_game_list';
$active_cart_table='active_cart';
$error="";
$error2="";


if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {

  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
  if (mysqli_connect_errno()) {
      printf("Errore di connessione: %s\n", mysqli_connect_error());
      exit();
  }


  if (isset($_POST['send'])) {

    if ($_POST['send']=='pay All') { //Al pagamento inserisco i giochi nella users_game_list
      foreach ($_SESSION['row1_cache'] as $v) {
        $query="INSERT INTO $users_game_list (id_user, id_game) VALUES(\"{$_SESSION['id']}\", \"{$v[0]}\");";
        $return=mysqli_query($sqlConnect, $query);
        if (!$return) {
          $error2.="Errore durante l'inserimento: " . mysqli_error($sqlConnect);
        }
      }
      $query="DELETE FROM $active_cart_table WHERE id_user=\"{$_SESSION['id']}\";"; //Cancello poi le varie tuple che compongono il carrello attivo dell'utente
      $return=mysqli_query($sqlConnect, $query);

      unset($_SESSION['ids_to_cart']);
      $sqlConnect->close();
      header('Location: StoreHomePage.php');
    }

    elseif ($_POST['send']=='logout') {
      unset($_SESSION);
      session_destroy();
      $sqlConnect->close();
      header('Location: login.php');
    }
  }
/*Il seguente codice verifica che ci siano prodotti in un carrello già attivo in precedenza per l'utente. Inseguito mette questi prodotti in "array di tuple", che conserva tutte le informazioni*/
  $flag=0;
  $query="SELECT id_prodotto, titolo, prezzo FROM `{$active_cart_table}` WHERE id_user=\"{$_SESSION['id']}\";";
  $cart_check=mysqli_query($sqlConnect, $query);

  $row1=array();//"array di tuple"
  $enter=false;
  while($row=mysqli_fetch_array($cart_check)){ //Inseriamo i vari prodotti in row1
    $enter=true;
    $row1[]=$row;
  }
  if ($enter) $flag++; //flag è una varibile il cui nome descrive già la funzione. Serve appunto per verificare che ci siano prodotti ne carrello:
                      // sia che il loro inserimento arrivi da un carrello già attivo, che dalla pagina di gameList.


  $counter=0;

  if(isset($_SESSION['ids_to_cart'])){ //Questo codice si attiva solo se proveniamo dalla pagina di gameList. Questo perchè io posso arrivare sul carrello sia da lì che da qualsiasi altra pagina
    foreach ($_SESSION['ids_to_cart'] as $v) {//Per ogni id proveniente da gameList
      foreach ($row1 as $i) { //controllo che non sia già presente nel carrello attivo. Se lo è aumento il counter di 1
        if ($v==$i[0]) $counter++;
      }
      if($counter==0){ //Se non ci sono "ripetizioni di tuple" per ogni ids_to_cart proveniente da gameList, allora il gioco(la sua tupla che richiederemo tramite query) verra messa in row1
        $query="SELECT id_prodotto, titolo, prezzo FROM `{$add_on_table}` WHERE id_prodotto=\"{$v}\";";
        $return=mysqli_query($sqlConnect, $query);
        $row1[]=mysqli_fetch_array($return);
      }
      $counter=0;
    }
    /*Ora controlliamo che il prodotto non sia già presente nel carrello attivo, nel caso lo aggiungeremo, aggiornando o creando un nuova lista di acquisti attivi per l'utente*/
    foreach ($row1 as $v) {
      $query="SELECT * FROM `{$active_cart_table}` WHERE id_user=\"{$_SESSION['id']}\" AND id_prodotto=\"{$v[0]}\";";
      $cart_check=mysqli_query($sqlConnect, $query);
      if(!($state=mysqli_fetch_array($cart_check))){    //Dato lo stato precedente del carrello, se la combo chiave (utente, gioco) non esiste nel carrello allora aggiungila
        $query="INSERT INTO $active_cart_table (id_user, id_prodotto, titolo, prezzo) VALUES(\"{$_SESSION['id']}\", \"{$v[0]}\", \"{$v[1]}\", \"{$v[2]}\");";
        $return=mysqli_query($sqlConnect, $query);
        if (!$return) {
          $error2.="Errore durante l'inserimento: " . mysqli_error($sqlConnect);
        }
      }

    }
    unset($_SESSION['ids_to_cart']);
    $flag++;
  }

  if (isset($_POST['delete_checked'])) { //Si attiva solo se abbiamo inviato dalla form alcuni giochi da eliminare
    if ($_POST['send']=='delete') {
      foreach ($_POST['delete_checked'] as $to_del) {
        $query="DELETE FROM $active_cart_table WHERE id_user=\"{$_SESSION['id']}\" AND id_prodotto=\"{$to_del}\";"; //Eliminiamo la coppia (utente, gioco) dal carrello
        $cart_check=mysqli_query($sqlConnect, $query);
        for ($i=0; $i <count($row1) ; $i++) {
          if ($row1[$i][0]==$to_del) array_splice($row1, $i, 1); //Eliminiamo il gioco da row1[] ridimensionando l'array
        }
      }
      if(empty($row1)) $flag=0; //Se, in seguito alla cancellazione di uno o più prodotti, row1[] diventasse vuoto, allora nache il carrello lo sarebbe. Dunque settiamo flag=0
    }
  }


//ATTENZIONE: uso la stringa error solo per fare economia di variabili
  if ($flag==0) {
    $error.="Carrello Vuoto"; //se falg è 0 allora il carrello è vuoto
  }
  else{
    $_SESSION['row1_cache']=$row1; //Salvo in SESSION il carrello ora attivo nel caso dovessi riutilizzarlo più tardi, tenendone così traccia.
  }
  $sqlConnect->close();
}
else {
  $sqlConnect->close();
  unset($_SESSION);
  session_destroy();
  header('Location: login.php');
}

$_SESSION['ttk']--;
 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="cartPage_.css" media="screen">
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
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="submit" name="send" value="logout">
                </form>
              </td>
            </tr>
          </table>
        </div>
        </div>


        </form>


        <div class="table">
          <?php
          /*Stampo per ogni tupla gioco, il nome e il prezzo. Aggiungendo una casella di check, per la selezione e l'eventuale eliminazione del gioco dal carrello*/
          $total=0;
          if($flag>0){
            echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";
            echo "<table>";
            echo "<tr>";
            echo "<td>";
            foreach ($row1 as $v) {
              echo "<label>";
              echo "<input type=\"checkbox\" name=\"delete_checked[]\" value=\"{$v['id_prodotto']}\">".$v['titolo']." | ".$v['prezzo']."&euro;";
              echo "</label>";
              echo "<br>";
              $total += $v['prezzo'];
            }
            echo "</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>";
            echo "TOTALE: ";
            echo $total."&euro;";//Sommo e stampo il totale
            echo "</td>";
            echo "</tr>";
            echo "<tr><td><input type=\"submit\" name=\"send\" value=\"pay All\">";
            echo "<input type=\"submit\" name=\"send\" value=\"delete\">";
            echo "</td>";
            echo "</tr>";

            echo "</table>";
            echo "</form>";
          }
          else{
            echo "<p>".$error."</p>";
          }

          if ($error2!="") {
            echo "<p>".$error2."</p>";
          }

           ?>

        </div>

      </div>

    </body>
  </html>
