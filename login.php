<?php
$db_name='HillDownGameStore_db';
$users_table='user_table';

$sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}

if(isset($_POST['send'])){
  if ($_POST['send']=='Login') {
    if(!(empty($_POST['email'])) && !(empty($_POST['password']))){
      if(!(preg_match("/^.*@.*/", $_POST['email']))){
        echo "<p>Inserire l'email non un nickname</p>";
      }
      else{
        $query="SELECT * FROM `user_table` WHERE email=\"{$_POST['email']}\" AND password=\"{$_POST['password']}\";";
        $return=mysqli_query($sqlConnect, $query);
        $row=mysqli_fetch_array($return);

        if($row){
          if($row['kick_check']!=0){
            echo "<p>Mi dispiace {$row['nickname']}, kick di ancora {$row['kick_check']}s</p>";
            exit();
          }
          session_name('HillDownService');
          session_start();
          $_SESSION['id']=$row['id'];
          $_SESSION['email']=$row['email'];
          $_SESSION['nickname']=$row['nickname'];
          $_SESSION['role']=$row['role'];
          $_SESSION['ttk']=1000;
          if($_SESSION['role']==1){
            header('Location: AdminPage.php');
          }
          else{
            header('Location: StoreHomePage.php');
          }
        }
        else{
          echo "<p>Login fallito: email o password errata</p>";
        }
      }
    }
    else{
      echo "<p>Dati mancanti i uno o entrambi i campi email e/o password</p>";
    }
  }
  elseif ($_POST['send']=='Sign In') {
    header('Location: signIn.php');
  }
 else{
    echo "<p>Login fallito: email o password errata</p>";
  }
}

  $sqlConnect->close();

 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>HillDown Game Store</title>
  </head>
  <body>
    <div class="">
      <form class="" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

        <div class="">
          <label for="email">Email:</label>
          <input type="text" name="email" value="">
        </div>

        <div class="">
          <label for="password">Password:</label>
          <input type="password" name="password" value="">
        </div>
        <div class="">
          <input type="submit" name="send" value="Login">
          <input type="submit" name="send" value="Sign In">

        </div>


      </form>

    </div>


  </body>
</html>
