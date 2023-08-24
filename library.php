<?php
/*La pagina libreria presenta i giochi acquistati e a lato i propri DLC acquistati*/
session_name('HillDownService');
session_start();

$db_name='HillDownGameStore_db';
$add_on_table='games_table';
$users_game_list='users_game_list';

if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {
  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
  if (mysqli_connect_errno()) {
      printf("Errore di connessione: %s\n", mysqli_connect_error());
      exit();
    }

    if (isset($_GET['send'])) {
      if ($_GET['send']=='logout') {
        unset($_SESSION);
        session_destroy();
        $sqlConnect->close();
        header('Location: login.php');
      }
    }
    if (isset($_GET['game'])) { //Codice simile da StoreHomePage, infatti segue lo stesso principio e ci riporta lla gameList selezionando il codice del gioco che vogliamo visualizzare tramite bottone SEE
      $_SESSION['id_game']=$_GET['game'];
      $sqlConnect->close();
      header('Location: gameList.php');


    }
/*La seguente query selezionera i giochi che l'utente possiede, ricavando tramite JOIN le info dalla tabella games_table (esclusi i soli DLC)*/
    $query="SELECT * FROM `{$users_game_list}` JOIN `{$add_on_table}` ON  `{$users_game_list}`.id_game=`{$add_on_table}`.id_prodotto WHERE  `{$users_game_list}`.id_user=\"{$_SESSION['id']}\" AND titolo NOT LIKE 'DLC%';";
    $return=mysqli_query($sqlConnect, $query);
    $_SESSION['ttk']--;
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
                     <input type="submit" name="send" value="logout">
                 </form>
               </td>
             </tr>
           </table>
         </div>
       </div>
       <div class="table">
         <form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">


        <?php
        //Si aggiunge una riga lla tabella per ogni gioco posseduto, se non si possiedono giochi la tabella non viene mostrata
        $flag=0;
        $struct="<table>";
        while ($row=mysqli_fetch_array($return)) {
          $struct.="<tr>";
          $struct.="<td id=\"image\"><img src=\"{$row['img']}\" alt=\"{$row['titolo']}\"></td>";
          $struct.="<td>{$row['titolo']}</td>";
          $struct.="<td>{$row['prezzo']}€</td>";
          $struct.="<td>{$row['descrizione']}</td>";
          $struct.="<td>";
          $struct.="<button type=\"submit\" name=\"game\" value=\"{$row['id_prodotto']}\">See</button>";
          $struct.="</td>";
          /*Effettuo la query che mi darà tutti i DLC acquistati per singolo gioco*/
          $query="SELECT titolo FROM`{$users_game_list}` JOIN `{$add_on_table}` ON  `{$users_game_list}`.id_game=`{$add_on_table}`.id_prodotto WHERE  `{$users_game_list}`.id_user=\"{$_SESSION['id']}\" AND titolo LIKE 'DLC({$row['titolo']})%';";
          $dlc_check=mysqli_query($sqlConnect, $query);
          $struct.="<td>";
          while($dlc_row=mysqli_fetch_array($dlc_check)) {
            $struct.="{$dlc_row['titolo']}"."<br>";
          }
          $struct.="</td>";
          $struct.="</tr>";
          $flag++;
        }

        if ($flag>0) {
          echo $struct;
          echo "</table>";
        }
        $sqlConnect->close(); //Solo dopo aver effettuato tutte le query che mi servono chiudo la connessione
         ?>
       </form>
       </div>


     </div>


   </body>
 </html>
