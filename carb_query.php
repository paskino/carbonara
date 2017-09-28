<!DOCTYPE html>
<html>
<head><title>Carbonara Recipe Results</title></head>
<meta charset="UTF-8">
<body bgcolor=ccddff>
<?
include "config.php";

$link = mysql_connect($CONFIG["database"]["host"],$CONFIG["database"]["user"],$CONFIG["database"]["pass"])or die("Connect Error: ".mysql_error());
//print "Successfully connected.\n";
mysql_select_db($CONFIG["database"]["name"], $link);

mysql_query("SET NAMES 'utf8'");

//$tlista=mysql_list_tables($database);
//print "ciao $tlista";
//$numtables = mysql_num_rows($tlista);
//print "Ci sono $numtables tables."

$query="SELECT * FROM feedback;";
$result=mysql_query($query);

$num_rows = mysql_num_rows($result);

setlocale(LC_ALL,"it_IT.UTF8");

$today = ucwords(strftime("%A %e %B %Y, ore %H:%M"));
print "Oggi $today, il sondaggio &egrave; stato effettuato da $num_rows persone.";

echo "<p> Vediamo come i (sedicenti) cuochi italiani fanno la pasta alla carbonara:<br>";

$query="SELECT chiara, COUNT(*) FROM feedback GROUP BY chiara";
$result=mysql_query($query);
$chiara = mysql_num_rows($result);

//print "<table>";

for ($i=0; $i < $chiara; $i++){
//	print "<tr>";
	$dati=mysql_data_seek($result,$i);
	$dato=mysql_fetch_row($result);
	$chiara_stats[$i]=$dato[1]/$num_rows*100;
//	print "<td>$dato[0]</td><td>";
//	printf("%.1f%c</td>",$chiara_stats[$i], 37);
}

print "<table border=0, width=100%>";
print "<tr><td>";

print "<h3> La questione SOFFRITTO...</h3>";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
$query="SELECT soffritto, COUNT(*) FROM feedback GROUP BY soffritto";

$result=mysql_query($query);
$chiara = mysql_num_rows($result);

//print "<table>";

for ($i=0; $i < $chiara; $i++){
print "<tr>";
$dati=mysql_data_seek($result,$i);
$dato=mysql_fetch_row($result);
$soffritto_stats[$i]=$dato[1]/$num_rows*100;
//print "<td>$dato[0]</td><td>";
//printf("%.1f%c</td>",$soffritto_`stats[$i], 37);
}


print "<td>Con CIPOLLA</td><td>";
	printf("%.1f%c</td>",$soffritto_stats[3], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$soffritto_stats[3]);

print "<tr><td>Con AGLIO</td><td>";
	printf("%.1f%c</td>",$soffritto_stats[2], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$soffritto_stats[2]);

print "<tr><td>SOLO Pancetta o Guanciale</td><td>";
	printf("%.1f%c</td>",$soffritto_stats[1], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$soffritto_stats[1]);

print "<tr><td>NON SA,<br> NON DICE</td><td>";
	printf("%.1f%c</td>",$soffritto_stats[0], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/blue.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$soffritto_stats[0]);

print "</table></td>";



print "<tr>";

print "<tr><td><h3>La Questione UOVA...</h3></td></tr><tr>
";
print "<td>La Chiara...";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
print "<td>CON</td><td>";
	printf("%.1f%c</td>",$chiara_stats[1], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$chiara_stats[1]);

print "<tr><td>SENZA</td><td>";
	printf("%.1f%c</td>",$chiara_stats[2], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$chiara_stats[2]);

print "<tr><td>NON SA,<br> NON DICE</td><td>";
	printf("%.1f%c</td>",$chiara_stats[0], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$chiara_stats[0]);



print "</table></td>";



/*
print "<p>Chiara rows $chiara ";

print "<table>";

while ($get_info = mysql_fetch_row($result)){
print "<tr>\n";
foreach ($get_info as $field)
print "\t<td>$field</td>\n";
print "</tr>\n";
}
*/


print "<td>Quante ce ne va? di piu' o di meno delle persone?";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
  
  $query="SELECT persone_uova, COUNT(*) FROM feedback GROUP BY persone_uova";
  $result=mysql_query($query);
  $chiara = mysql_num_rows($result);


   for ($i=0; $i < $chiara; $i++){
    print "<tr>";
    $dati=mysql_data_seek($result,$i);
    $dato=mysql_fetch_row($result);
    $puova_stats[$i]=$dato[1]/$num_rows*100;
   }


	print "<tr><td>1/1</td><td>";
		printf("%.1f%c</td>",$puova_stats[1], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$puova_stats[1]);
	
	print "<tr><td>+</td><td>";
		printf("%.1f%c</td>",$puova_stats[2], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$puova_stats[2]);
		
	print "<tr><td>-</td><td>";
		printf("%.1f%c</td>",$puova_stats[3], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$puova_stats[2]);
		
	print "<tr><td>NON SA,<br> NON DICE</td><td>";
		printf("%.1f%c</td>",$puova_stats[0], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/blue.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$puova_stats[0]);



  print "</table></td>";


print "<tr>";
print "<td><h3> La questione SPEZIATURA...</h3></td></tr>";

print "<tr>";
print "<td>si usa il PEPE...";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
$query="SELECT pepe, COUNT(*) FROM feedback GROUP BY pepe";

$result=mysql_query($query);
$chiara = mysql_num_rows($result);

//print "<table>";

for ($i=0; $i < $chiara; $i++){
print "<tr>";
$dati=mysql_data_seek($result,$i);
$dato=mysql_fetch_row($result);
$pepe_stats[$i]=$dato[1]/$num_rows*100;
//print "<td>$dato[0]</td><td>";
//printf("%.1f%c</td>",$soffritto_stats[$i], 37);
}


print "<tr><td>SI</td><td>";
	printf("%.1f%c</td>",$pepe_stats[1], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$pepe_stats[1]);
		
print "<tr><td>NO</td><td>";
	printf("%.1f%c</td>",$pepe_stats[2], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$pepe_stats[2]);
		
print "<tr><td>NON SA,<br> NON DICE</td><td>";
	printf("%.1f%c</td>",$pepe_stats[0], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$pepe_stats[0]);
		



print "</table></td>";

print "<td>o il PEPERONCINO...";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
  
  $query="SELECT peperoncino, COUNT(*) FROM feedback GROUP BY peperoncino";
  $result=mysql_query($query);
  $chiara = mysql_num_rows($result);


   for ($i=0; $i < $chiara; $i++){
    print "<tr>";
    $dati=mysql_data_seek($result,$i);
    $dato=mysql_fetch_row($result);
    $peperoncino_stats[$i]=$dato[1]/$num_rows*100;
   }


	print "<tr><td>SI</td><td>";
		printf("%.1f%c</td>",$peperoncino_stats[1], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$peperoncino_stats[1]);
		
	print "<tr><td>NO</td><td>";
		printf("%.1f%c</td>",$peperoncino_stats[2], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$peperoncino_stats[2]);
		
	print "<tr><td>NON SA,<br> NON DICE</td><td>";
		printf("%.1f%c</td>",$peperoncino_stats[0], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$peperoncino_stats[0]);
		



  print "</table></td>";



print "<tr>";
print "<td><h3> La questione FORMAGGIO...</h3></td>";
print "<tr><td>";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
  
  $query="SELECT kindaformaggio, COUNT(*) FROM feedback GROUP BY kindaformaggio";
  $result=mysql_query($query);
  $chiara = mysql_num_rows($result);


   for ($i=0; $i < $chiara; $i++){
    print "<tr>";
    $dati=mysql_data_seek($result,$i);
    $dato=mysql_fetch_row($result);
    $cheese_stats[$i]=$dato[1]/$num_rows*100;
   }


	print "<tr><td>Parmigiano</td><td>";
		printf("%.1f%c</td>",$cheese_stats[2], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$cheese_stats[2]);
		
	print "<tr><td>Pecorino Romano</td><td>";
		printf("%.1f%c</td>",$cheese_stats[3], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$cheese_stats[3]);
		
	print "<tr><td>Altro</td><td>";
		printf("%.1f%c</td>",$cheese_stats[1], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$cheese_stats[1]);
		
	print "<tr><td>NON SA,<br> NON DICE</td><td>";
		printf("%.1f%c</td>",$cheese_stats[0], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/blue.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$cheese_stats[0]);
		



  print "</table></td>";

print "<td>QUANDO va messo?";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
  
  $query="SELECT formaggio, COUNT(*) FROM feedback GROUP BY formaggio";
  $result=mysql_query($query);
  $chiara = mysql_num_rows($result);


   for ($i=0; $i < $chiara; $i++){
    print "<tr>";
    $dati=mysql_data_seek($result,$i);
    $dato=mysql_fetch_row($result);
    $wcheese_stats[$i]=$dato[1]/$num_rows*100;
   }


	print "<tr><td>PRIMA</td><td>";
		printf("%.1f%c</td>",$wcheese_stats[1], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$wcheese_stats[1]);
		
	print "<tr><td>DOPO</td><td>";
		printf("%.1f%c</td>",$wcheese_stats[2], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$wcheese_stats[2]);
		
	print "<tr><td>prima E dopo</td><td>";
		printf("%.1f%c</td>",$wcheese_stats[3], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$wcheese_stats[3]);
		
	print "<tr><td>NON SA,<br> NON DICE</td><td>";
		printf("%.1f%c</td>",$wcheese_stats[0], 37);
		print "<td width=100>
		<img src=\"http://mau.aperion.it/img/icons/blue.gif\" 
		height=10 width=";
		printf ("%.0f ></td>",$wcheese_stats[0]);
		



  print "</table></td>";

print "<tr><td><h3> E per Finire: la questione PADELLA...</h3></td>";
print "<tr><td>";
print "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
$query="SELECT padella, COUNT(*) FROM feedback GROUP BY padella";

$result=mysql_query($query);
$chiara = mysql_num_rows($result);

//print "<table>";

for ($i=0; $i < $chiara; $i++){
print "<tr>";
$dati=mysql_data_seek($result,$i);
$dato=mysql_fetch_row($result);
$padella_stats[$i]=$dato[1]/$num_rows*100;
//print "<td>$dato[0]</td><td>";
//printf("%.1f%c</td>",$soffritto_stats[$i], 37);
}


print "<tr><td>SI SALTA</td><td>";
	printf("%.1f%c</td>",$padella_stats[1], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/red.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$padella_stats[1]);
		
print "<tr><td>NON SI SALTA</td><td>";
	printf("%.1f%c</td>",$padella_stats[2], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/green.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$padella_stats[2]);
		
print "<tr><td>NON SA,<br> NON DICE</td><td>";
	printf("%.1f%c</td>",$padella_stats[0], 37);
	print "<td width=100>
	<img src=\"http://mau.aperion.it/img/icons/yellow.gif\" 
	height=10 width=";
	printf ("%.0f ></td>",$padella_stats[0]);
		



print "</table></td>";


print "</table>";

$sql = "select distinct identita, date, ricetta from feedback where
length(trim(ricetta))>2 order by date desc";

$result=mysql_query($sql);

?>

<h3>Ecco le vostre proposte di ricetta:</h3>
<table border="1" cellpadding="3" cellspacing="0">
<tr>
    <th>Chi</th>
    <th>Quando</th>
    <th>Ricetta</th>
</tr>

<?
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {

    ?>
    <tr>
        <td valign="top" nowrap><? if ($row['identita']) { echo
"<b>".$row['identita']."</b>";} else { echo "<i>Anonimo</i>";}?></td>
        <td align="center" valign="top" nowrap><?=strftime("%d %B
%Y",strtotime($row['date']));?></td>
        <td
valign="top"><?=str_replace("\n","<br/>\n",$row['ricetta'])?></td>
    </tr>
    <?
}


mysql_close($link);




?>
</body>
</html>

