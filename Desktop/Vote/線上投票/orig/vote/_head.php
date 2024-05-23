<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=Config::sysname?><?= isset($title)?"-".$title:"" ?></title>
<link rel="stylesheet" href="css/jquery-ui.min.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/datatables.min.css">
<link rel="stylesheet" href="css/main.css">
<style>
body {
    font-size: 16px;
    font-weight: bold;
}
.backcount{
    color: red;
}
.backmark{
    color: red;
    font-weight:bold;
    font-size:1.2em;
}
.navbar-default .navbar-brand, .navbar-default .navbar-nav>li>a{
    color: black;
    font-size:18px;
}
.navbar-brand {
    font-size:20px;
}
.navbar-brand img{
    max-width: 130%;
    max-height: 130%;
    position: relative;
    top: -3px;
    display: inline;
}
table tr th,table tr td{
    border: 1px solid black !important;
}
.red{
    color:red;
    font-weight:bolder;
}
.progessing{
    background-color: #feffe1;
}
.progress0,.progress0:hover,.progress0:visited{
    background-color:rgb(161, 132, 105) !important;
    border-color:rgb(161, 132, 105) !important;
    color:white;
}
.progress1,.progress1:hover,.progress1:visited{
    background-color:#427e9b !important;
    border-color:#427e9b !important;
    color:white;
}
.progress2,.progress2:hover,.progress2:visited{
    background-color:rgb(150, 165, 145) !important;
    border-color:rgb(150, 165, 145) !important;
    color:white;
}
.progress3,.progress3:hover,.progress3:visited{
    background-color:rgb(161, 132, 105) !important;
    border-color:rgb(161, 132, 105) !important;
    color:white;
}
.progress4,.progress4:hover,.progress4:visited{
    background-color:rgb(150, 165, 145) !important;
    border-color:rgb(150, 165, 145) !important;
    color:white;
}
.navbar-brand-centered {
        position: absolute;
        left: 50%;
        display: block;
        width: 400px;
        text-align: center;
        background-color: transparent;
    }
    .navbar>.container .navbar-brand-centered, 
    .navbar>.container-fluid .navbar-brand-centered {
        margin-left: -200px;
    }
    
</style>