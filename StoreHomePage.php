<?php
session_name('HillDownService');
session_start();

$db_name='HillDownGameStore_db';
$add_on_table='games_table';

if (isset($_SESSION['ttk']) || $_SESSION['ttk']>0) {
  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
  if (mysqli_connect_errno()) {
      printf("Errore di connessione: %s\n", mysqli_connect_error());
      exit();
  }

  $query="SELECT * FROM `{$add_on_table}` WHERE titolo NOT LIKE 'DLC%';";
  $return=mysqli_query($sqlConnect, $query);


  if(isset($_GET['game'])){
    $_SESSION['id_game']=$_GET['game'];
    $sqlConnect->close();
    header('Location: gameList.php');
  }

  if (isset($_GET['logout'])) {
    unset($_SESSION);
    session_destroy();
    header('Location: login.php');
  }

   $sqlConnect->close();
   $_SESSION['ttk']--;
}


 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>HillDown Game-Store</title>
    <link rel="stylesheet" href="StoreHomePage.css" media="screen">
    <link rel="stylesheet" href="Init_Struct.css" media="screen">
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
          $struct.="<td id=\"image\"><img src=\"{$row['img']}\" alt=\"{$row['titolo']}\"></td>";
          $struct.="<td>{$row['titolo']}</td>";
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
