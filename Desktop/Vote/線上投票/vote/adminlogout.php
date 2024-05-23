<?php

$isadmin = !empty($_COOKIE['vote_admin']);

setcookie("vote_admin", "", time()-3600);
setcookie("vote_user", "", time()-3600);
setcookie("vote_id", "", time()-3600);

$id = empty($_GET['id'])?'':$_GET['id'];

if($isadmin)
    header('Location: adminlogin.php');
else
    header('Location: login.php?id='.$id);