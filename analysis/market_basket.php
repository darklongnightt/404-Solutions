<?php
include('../templates/header.php');

// Access Control Check
if (substr($uid, 0, 3) != 'ANL') {
  echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

?>

<!DOCTYPE html>
<html>
<div class="analytics-padding">
  <iframe class="analytics" src="https://super-data.shinyapps.io/arules-dashboard/"></iframe>
</div>
<?php include('../templates/footer.php'); ?>

</html>