<?php

$xml = simplexml_load_file("summary.xml");

$files = array();
foreach($xml->files->file as $file) {
  $files[] = $file;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>JoobsBox PHP - PHP_Depend Summary</title>
	<style type="text/css">
	  body {
	    font-family: "Corbel", "Myriad Pro", "Calibri", "Verdana", "Helvetica", "Arial", sans-serif;
	  }
	  
	  table {
	    font-size: 13px;
	  }
	  
	  table th {
	    min-width: 100px;
	    padding: 5px;
	  }
	  
	  table tbody tr:nth-child(2n) td {
	    background-color: #EEE;
	  }
	  
	  table thead th {
	    background-color: #444;
	    color: #FFF;
	    padding: 5px 10px;
	  }
	  
	  table thead th:first-child {
	    -moz-border-radius-topleft: 10px;
	  }
	  
	  table thead th:last-child {
	    -moz-border-radius-topright: 10px;
	  }
	  
	  table td {
	    padding: 3px 10px;
	    border-right: 1px solid #999;
	    border-left: 1px solid #999;
	  }
	  
	  table td.right {
	    text-align: right;
	  }
	  
	  table {
	    border-collapse: collapse;
	    border-bottom: 1px solid #999;
	  }
	</style>
</head>

<body>
  <h1>JoobsBox PHP - PHP Depend Code Metrics</h1>
  
  <h2>Files</h2>
  <table id="files">
    <thead>
      <tr>
        <th>Name</th>
        <th>Lines of code</th>
        <th>Comment lines of code</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($files as $file): ?>
        <tr>
          <td><?php echo $file['name']; ?></td>
          <td class="right"><?php echo $file['loc']; ?></td>
          <td class="right"><?php echo $file['cloc']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>