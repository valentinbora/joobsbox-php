<?php

$xml = simplexml_load_file("phpcs.xml");

$files = array();
foreach($xml->file as $file) {
  $file['name'] = substr($file['name'], strpos($file['name'], 'Joobsbox/'));
  $files[] = $file;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>JoobsBox PHP - PHP Code Sniffer Report</title>
	<style type="text/css">
	  body {
	    font-family: "Corbel", "Myriad Pro", "Calibri", "Verdana", "Helvetica", "Arial", sans-serif;
	    text-align: center;
	  }
	  
	  table {
	    font-size: 13px;
	    text-align: left;
	    margin: 0 auto;
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
	    text-align: center;
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
	  
	  ul, ul li {
	    list-style: none;
	    margin: 0;
	    padding: 0;
	  }
	  
	  .file-name {
	    text-align: center;
	    font-size: 16px;
	    padding: 7px;
	    margin: 0;
	  }
	</style>
</head>

<body>
  <h1>JoobsBox PHP - PHP Code Sniffer Report</h1>
  
  <ul id="files">
  <?php foreach($files as $file): ?>
    <li class="file">
      <div class="file-name"><?php echo $file['name']; ?></div>
      <table class="errors">
        <thead>
          <tr>
            <th>Error</th>
            <th>Line</th>
            <th>Column</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($file->error as $error): ?>
          <tr class="error">
            <td><?php echo $error; ?></td>
            <td class="right"><?php echo $error['line']; ?></td>
            <td class="right"><?php echo $error['column']; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </li>
  <?php endforeach; ?>
  </ul>
</body>
</html>