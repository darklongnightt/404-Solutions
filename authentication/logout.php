<?php
session_start();
session_unset();
$_SESSION['LASTACTION'] = 'LOGOUT';
echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
