<?php
require_once('init.php');
$title = '司法院線上投票';

if(empty($_GET['id'])){
    die;
}

$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <?php include '_head.php'; ?>
</head>
<body>
    <?php include '_nav.php'; ?>
    <div class="container-fluid text-center">
        <div class="row">
            <div class='col-xs-12'>
            <div class="jumbotron">
  <h2><?= $event['name'] ?></h2>
  <h2>您已完成投票</h2>

<?php 
$isShowLink = strpos( $event['name'], '考績委員' ) !== false && !in_array($_COOKIE["vote_votename"],$keynames);
if($isShowLink){ ?>
  <h2 ><a style="color:#d50000;font-weight:bold;border:5px solid #043682;border-radius:10px;" href='https://self.judicial.gov.tw/vote/index.php?id=87'>請點此繼續投甄審委員會</a></h2>
<?php } ?>

<br>
  <?php if((!empty($_COOKIE['vote_admin']) || !empty($_COOKIE['vote_user'])) && !$isShowLink){ ?>
        <a class='btn btn-lg btn-default' href='adminlogout.php?id=<?= empty($_GET['id'])?'':$_GET['id'] ?>'>登出</a>
      <?php } ?>
</div>
                
            </div>
            
        </div>
    </div>
    
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script>
        $('#rtlogout').remove();
    </script>
</body>
</html>