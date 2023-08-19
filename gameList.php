<?php
session_name('HillDownService');
session_start();

$db_name='HillDownGameStore_db';
$add_on_table='games_table';


$sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}

if (isset($_SESSION['ttk']) || $_SESSION['ttk']>0) {

  if (isset($_POST['send'])) {
    if ($_POST['send']=='Al carrello-->') {
      $_SESSION['id_game']=$ids_to_cart;
      $sqlConnect->close();
      header('Location: cartPage.php');
    }
    elseif ($_GET['send']=='logout') {
      unset($_SESSION);
      session_destroy();
      header('Location: login.php');
    }
  }


  $query="SELECT * FROM `{$add_on_table}` WHERE id_prodotto=\"{$_SESSION['id_game']}\";";
  $return=mysqli_query($sqlConnect, $query);
  $row1=mysqli_fetch_array($return);
  if($row1){
    $query="SELECT titolo, prezzo FROM `{$add_on_table}` WHERE titolo LIKE 'DLC%({$row1['titolo']})';";
    $return=mysqli_query($sqlConnect, $query);
  }
}
$sqlConnect->close();
$_SESSION['ttk']--;
 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>HillDown Game-Store</title>
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
              echo "<th>".$row1['titolo']."</th>";
              echo "</tr>";
              echo "<td>".$row1['versione']."</td>";
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
        <div class="gamesBox">
          <form class="" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
          <?php
          if($row1){
            $ids_to_cart=array();
            $ids_to_cart[]=$row1[0];
            echo "<label>";
            echo "<input type=\"checkbox\" name=\"games_into_cart[]\" value=\"{$row1['titolo']}\"> {$row1['titolo']} | {$row1['prezzo']}";
            echo "</label><br>";
            while ($row2=mysqli_fetch_array($return)) {
              $ids_to_cart[]=$row2[0];
              echo "<input type=\"checkbox\" name=\"games_into_cart[]\" value=\"{$row2['titolo']} {$row2['prezzo']}\"";
            }
          }

           ?>

        </div>
      </div>
      <div class="sendBox">
        <input type="submit" name="send" value="Al carrello-->">

      </form>

      </div>

    </div>

  </body>
</html>
