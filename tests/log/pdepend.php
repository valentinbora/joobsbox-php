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
      html, body {
          margin: 0;
          padding: 0;
      }
      
	  body {
	    font-family: "Corbel", "Myriad Pro", "Calibri", "Verdana", "Helvetica", "Arial", sans-serif;
	    padding-bottom: 80px;
	  }
	  
	  table {
	      margin: 0;
	      padding: 0;
	    font-size: 13px;
	  }
	  
	  table th {
	    min-width: 50px;
	    padding: 5px;
	  }
	  
	  table tbody tr:nth-child(2n) {
	    background-color: #EEE;
	  }
	  
	  tr.header th {
	    background-color: #444;
	    color: #FFF;
	    padding: 5px 10px;
	  }
	  
	  tr.header th:first-child {
	    -moz-border-radius-topleft: 10px;
	  }
	  
	  tr.header th:last-child {
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
	  
	  table td.warning {
	      background-color: #FF8181;
	      color: #000;
	  }
	  
	  table {
	      margin: 0 auto;
	    border-collapse: collapse;
	    border-bottom: 1px solid #999;
	  }
	  
	  ul, ul li {
	      list-style: none;
	  }
	  
	  ul {
	      padding: 0;
	      margin: 0 auto;
	      text-align: center;
	  }
	  
	  h1, h2, h3 {
	      text-align: center;
	  }
	  
	  li table {
	      text-align: left;
	      margin: 0 auto;
	      margin-top: 10px;
	  }
	  
	  table tbody tr.class {
	      background-color: #FFE682;
	  }
	  
	  table tbody tr.method-head {
	      background-color: #444;
	      font-weight: bold;
	      color: #FFF;
	  }
	  
	  table tbody tr.method {
  	      background-color: #EDEEF3;
  	  }
  	  
  	  table tbody tr.method:nth-child(2n) {
	      background-color: #CBCFE0;
	  }
	  
	  table tbody th.methods-info-top, table tbody th.methods-info-bottom {
	      padding: 0;
	      background-color: #FFF;
	      text-align: left;
	  }
	  
	  table tbody th.methods-info-top div {
	      padding-left: 38px;
	      height: 25px;
	      line-height: 25px;
	      -moz-border-radius-bottomleft: 10px;
	      -moz-border-radius-bottomright: 10px;
	      -webkit-border-bottom-left-radius: 10px;
	      -webkit-border-bottom-right-radius: 10px;
	      background-color: #444;
	  }
	  
	  table tbody th.methods-info-bottom div {
  	      padding-left: 38px;
  	      height: 25px;
  	      line-height: 25px;
  	      -moz-border-radius-topleft: 10px;
  	      -moz-border-radius-topright: 10px;
  	      -webkit-border-top-left-radius: 10px;
  	      -webkit-border-top-right-radius: 10px;
  	      background-color: #444;
  	  }
  	  
  	  .blank {
  	      background-color: #FFF;
  	      border: none;
  	  }
  	  
  	  #jdepend, #pyramid {
  	      width: 390px;
  	      height: 250px;
  	      margin: 0 auto;
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
  
  <h2>Packages</h2>
  <ul>
    <?php foreach($xml->package as $package): ?>
    <li>
        Name: <?php echo $package['name']; ?><br/>
        Number of classes: <?php echo $package['noc']; ?><br/>
        Number of methods: <?php echo $package['nom']; ?><br/>
        <h3>Classes</h3>
        <table>
          <thead>
          </thead>
          <tbody>
              <?php foreach($package->class as $class): ?>
              <tr class="header">
                <th>Name</th>
                <th><abbr title="Class interface size - Number of non-private methods + variables defined by class">CIS</abbr></th>
                <th><abbr title="Comment lines of code">CLOC</abbr></th>
                <th><abbr title="Number of methods + variables defined by class">Size</abbr></th>
                <th><abbr title="Depth of Inheritance Tree">DIT</abbr></th>
                <th><abbr title="Number of implemented interfaces">IMPL</abbr></th>
                <th><abbr title="Number of methods">NOM</abbr></th>
                <th><abbr title="Number of class defined properties">VARS</abbr></th>
                <th><abbr title="Number of class defined properties">VARSi</abbr></th>
                <th><abbr title="Number of public class properties">VARSNP</abbr></th>
                <th><abbr title="Weighted Method per Class (Sum of method Cyclomatic Complexity) - Should be low">WMC</abbr></th>
                <th><abbr title="Weighted Method per Class + Inherited (Sum of method Cyclomatic Complexity) - Should be low">WMCi</abbr></th>
              </tr>
              <tr class="class">
                  <td><?php echo $class['name']; ?></td>
                  <td class="right"><?php echo $class['cis']; ?></td>
                  <td class="right"><?php echo $class['cloc']; ?></td>
                  <td class="right"><?php echo $class['csz']; ?></td>
                  <td class="right"><?php echo $class['dit']; ?></td>
                  <td class="right"><?php echo $class['impl']; ?></td>
                  <td class="right"><?php echo $class['nom']; ?></td>
                  <td class="right"><?php echo $class['vars']; ?></td>
                  <td class="right"><?php echo $class['varsi']; ?></td>
                  <td class="right"><?php echo $class['varsnp']; ?></td>
                  <td class="right <?php if($class['wmc'] >= 30) echo 'warning';?>"><?php echo $class['wmc']; ?></td>
                  <td class="right <?php if($class['wmci'] >= 30) echo 'warning';?>"><?php echo $class['wmci']; ?></td>
              </tr>
              <tr class="method-head">
                  <th class="methods-info-top" colspan="12"><div>Methods</div></th>
              </tr>
              <tr class="method-head">
                  <th class="blank"></th>
                  <th>Name</th>
                  <th><abbr title="Cyclomatic Complexity">CCN</abbr></th>
                  <th><abbr title="Extended Cyclomatic Complexity">CCN2</abbr></th>
                  <th><abbr title="Lines of code">LOC</abbr></th>
                  <th><abbr title="Comment lines of code">CLOC</abbr></th>
                  <th><abbr title="Effective lines of code">ELOC</abbr></th>
                  <th><abbr title="Non-Comment lines of code">NLOC</abbr></th>
                  <th><abbr title="Number of possible execution paths">NPATH</abbr></th>
              </tr>
              <?php foreach($class->method as $method): ?>
              <tr class="method">
                  <td class="blank"></td>
                  <td><?php echo $method['name']; ?></td>
                  <td class="right"><?php echo $method['ccn']; ?></td>
                  <td class="right"><?php echo $method['ccn2']; ?></td>
                  <td class="right"><?php echo $method['loc']; ?></td>
                  <td class="right"><?php echo $method['cloc']; ?></td>
                  <td class="right"><?php echo $method['eloc']; ?></td>
                  <td class="right"><?php echo $method['nloc']; ?></td>
                  <td class="right"><?php echo $method['npath']; ?></td>
              </tr>
              <?php endforeach; ?>
          <?php endforeach; ?>
          </tbody>
        </table>
    </li>
    <?php endforeach; ?>
    </ul>
    
    <h2>JDepend Chart</h2>
    <div id="jdepend">
        <embed src="jdepend.svg" width="390" height="250"
        type="image/svg+xml"
        pluginspage="http://www.adobe.com/svg/viewer/install/" />
    </div>
    
    <h2>Pyramid</h2>
    <div id="pyramid">
        <embed src="pyramid.svg" width="390" height="250"
        type="image/svg+xml"
        pluginspage="http://www.adobe.com/svg/viewer/install/" />
    </div>
</body>
</html>