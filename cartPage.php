<?php
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

    if ($_POST['send']=='pay All') {
      foreach ($_SESSION['row1_cache'] as $v) {
        $query="INSERT INTO $users_game_list (id_user, id_game) VALUES(\"{$_SESSION['id']}\", \"{$v[0]}\");";
        $return=mysqli_query($sqlConnect, $query);
        if (!$return) {
          $error2.="Errore durante l'inserimento: " . mysqli_error($sqlConnect);
        }
      }
      $query="DELETE FROM $active_cart_table WHERE id_user=\"{$_SESSION['id']}\";";
      $return=mysqli_query($sqlConnect, $query);

      unset($_SESSION['ids_to_cart']);
      $sqlConnect->close();
      header('Location: StoreHomePage.php');
    }

    elseif ($_POST['send']=='logout') {
      unset($_SESSION);
      session_destroy();
      header('Location: login.php');
    }
  }

  $flag=0;
  $query="SELECT id_prodotto, titolo, prezzo FROM `{$active_cart_table}` WHERE id_user=\"{$_SESSION['id']}\";";
  $cart_check=mysqli_query($sqlConnect, $query);

  $row1=array();
  $enter=false;
  while($row=mysqli_fetch_array($cart_check)){
    $enter=true;
    $row1[]=$row;
  }
  if ($enter) $flag++;


  $counter=0;

  if(isset($_SESSION['ids_to_cart'])){
    foreach ($_SESSION['ids_to_cart'] as $v) {
      foreach ($row1 as $i) {
        if ($v==$i[0]) $counter++;
      }
      if($counter==0){
        $query="SELECT id_prodotto, titolo, prezzo FROM `{$add_on_table}` WHERE id_prodotto=\"{$v}\";";
        $return=mysqli_query($sqlConnect, $query);
        $row1[]=mysqli_fetch_array($return);
      }
      $counter=0;
    }
    foreach ($row1 as $v) {
      $query="SELECT * FROM `{$active_cart_table}` WHERE id_user=\"{$_SESSION['id']}\" AND id_prodotto=\"{$v[0]}\";";
      $cart_check=mysqli_query($sqlConnect, $query);
      if(!($state=mysqli_fetch_array($cart_check))){
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

  if (isset($_POST['delete_checked'])) {
    if ($_POST['send']=='delete') {
      foreach ($_POST['delete_checked'] as $to_del) {
        $query="DELETE FROM $active_cart_table WHERE id_user=\"{$_SESSION['id']}\" AND id_prodotto=\"{$to_del}\";";
        $cart_check=mysqli_query($sqlConnect, $query);
        for ($i=0; $i <count($row1) ; $i++) {
          if ($row1[$i][0]==$to_del) array_splice($row1, $i, 1);
        }
      }
      if(empty($row1)) $flag=0;
    }
  }



  if ($flag==0) {
    $error.="Carrello Vuoto";
  }
  else{
    $_SESSION['row1_cache']=$row1;
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
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="cartPage.css" media="screen">
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
        </div>


        </form>


        <div class="table">
          <?php
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
            }
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
