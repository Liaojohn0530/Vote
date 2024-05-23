<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand navbar-brand-centered" href="javascript: void(0)"><img alt="Brand" src="img/logo.png"> <?= Config::sysname ?></a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        
      </ul>
      <ul class="nav navbar-nav navbar-right">
      <?php if(!empty($_COOKIE['vote_admin'])){ ?>
        <li><a href='#'><?= $_COOKIE['vote_admin_account'] ?> (<?= $_COOKIE['vote_admin_unit'] ?>)</a></li>
      <?php } ?>
      <?php if(!empty($_COOKIE['vote_admin']) || !empty($_COOKIE['vote_user'])){ ?>
        <li><a id='rtlogout' href='adminlogout.php?id=<?= empty($_GET['id'])?'':$_GET['id'] ?>'>登出</a></li>
      <?php } ?>
      </ul>
    </div>
  </div>
</nav>
<?php
if(!empty($_GET["msg"])){
?>
<div class="container-fluid">
  <div class="row">
      <div class="col-xs-6">
        <div id='alertmsg' class="alert alert-warning alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong><?=$_GET['msg']?></strong>
        </div>
      </div>
  </div>
</div>
<?php
}
?>