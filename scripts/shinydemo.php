<?php 
include("../config/db_connect.php");
include('../templates/header.php');

?>

<!DOCTYPE html>
<html>

<head>
  <script src="shared/jquery.js" type="text/javascript"></script>
  <script src="shared/shiny.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="shared/shiny.css"/>
</head>

<body>

  <h1>HTML UI</h1>

  <p>
    <label>Distribution type:</label><br />
    <select name="dist">
      <option value="norm">Normal</option>
      <option value="unif">Uniform</option>
      <option value="lnorm">Log-normal</option>
      <option value="exp">Exponential</option>
    </select>
  </p>

  <p>

    <label>Number of observations:</label><br />
    <input type="number" name="n" value="500" min="1" max="1000" />

  </p>

  <h3>Summary of data:</h3>
  <pre id="summary" class="shiny-text-output"></pre>

  <h3>Plot of data:</h3>
  <div id="plot" class="shiny-plot-output"
       style="width: 100%; height: 300px"></div>

  <h3>Head of data:</h3>
  <div id="table" class="shiny-html-output"></div>

  <iframe src="http://glimmer.rstudio.com/stla/3Dsliced/" 
    style="border: black; width: 1500px; height: 700px"></iframe>    

</body>
</html>