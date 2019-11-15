<?php
session_start();
$_SESSION = array();
session_unset();
session_destroy();
echo "<script type='text/javascript'>window.top.location='/index.php?logout=true';</script>";
