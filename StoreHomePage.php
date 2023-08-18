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

$query="SELECT * FROM `{$add_on_table}` WHERE titolo NOT LIKE 'DLC%';";
$return=mysqli_query($sqlConnect, $query);


if(isset($_GET['game'])){
  $_SESSION['id_game']=$_GET['game'];
  $sqlConnect->close();
  header('Location: gameList.php');
}

 $sqlConnect->close();
 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>HillDown Game-Store</title>
    <style media="screen">
      .table img{
        height: 100%;
        width: 100%;

      }

      .table table{
        border: solid green 4px;
        width: 70%;
        height: 10%
      }

      .table td{
        border: solid red 4px;
        width: 200px
      }
    </style>
  </head>
  <body>
    <div class="">
      <div class="">
        <div class="">

        </div>


        <div class="">

        </div>

        <div class="">

        </div>

      </div>
      <div class="table">
        <form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">

        </form>
        <?php
        $struct="<table>";
        while ($row=mysqli_fetch_array($return)) {
          $struct.="<tr>";
          $struct.="<td id=\"image\"><img src=\"{$row['img']}\" alt=\"{$row['titolo']}\"></td>";
          $struct.="<td>{$row['titolo']}</td>";
          $struct.="<td>{$row['prezzo']}</td>";
          $struct.="<td>{$row['descrizione']}</td>";
          $struct.="<td><button type=\"submit\" name=\"game\" value=\"{$row['id_prodotto']}\">Buy</button></td>";
          $struct.="</tr>";
        }
        $struct.="</table>";

        echo $struct;

         ?>


      </div>

    </div>

  </body>
</html>
