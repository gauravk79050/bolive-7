<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php foreach($content as $cont)
  {
 echo "<tr><td>".$cont->id."</td>";
 echo "<td>".$cont->company_name."</td>";
 echo "<td>".$cont->type_id."</td>";
  echo "<td>".$cont->first_name."</td>";
  echo "<td>".$cont->last_name."</td>";
  echo "<td>".$cont->email."</td>";
  echo "<td>".$cont->phone."</td></tr>";
 }
?>
</body>
</html>
