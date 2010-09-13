#!/usr/bin/php5
<?php

$config = parse_ini_file('svn-fetcher.ini');

$db_host = $config['db_host'];
$db_name = $config['db_name'];
$db_user = $config['db_user'];
$db_password = $config['db_password'];
$url = $config['svn_repos'];

$dsn = "mysql:host=$db_host;dbname=$db_name";
$dbh = new PDO($dsn, $db_user, $db_password);

$sql0 = "SELECT * FROM `configuration` WHERE `option` = 'revision'";
$sth0 = $dbh->prepare($sql0);
$sth0->execute();
$result = $sth0->fetch(PDO::FETCH_OBJ);
$revision = $result->value + 1;

$sql1 = "INSERT INTO svn_revision_log (revision, author, commit_msg, commit_time, file_changed) VALUES (?, ?, ?, ?, ?)";
$sth1 = $dbh->prepare($sql1);

$sql2 = "INSERT INTO svn_commit_log (revision, action, path) VALUES (?, ?, ?)";
$sth2 = $dbh->prepare($sql2);

// or use $to_revision = $revision + 1000 to fetch log each time.

echo "from $revision to SVN_REVISION_HEAD\n";

$array = svn_log($url, $revision, SVN_REVISION_HEAD);

foreach($array as $row)
{
	if(! empty($row))
	{
		$time =  strtotime($row['date']);
		$datetime = date('Y-m-d H:i:s', $time);
		$sth1->execute(array($row['rev'], $row['author'], $row['msg'], $datetime, count($row['paths'])));
		foreach($row['paths'] as $sub_row)
		{		
		    if( ! empty($sub_row))
			{
				$sth2->execute(array($row['rev'], $sub_row['action'], $sub_row['path']));
			}
		}
		$revision = $row['rev'];
	}    
}

if(!empty($array))
{
	$sql3 = "UPDATE  `configuration` SET  `value` = ? WHERE  `option` = 'revision'";
	$sth3 = $dbh->prepare($sql3);
	$sth3->execute(array("$revision"));

	echo "final revision: $revision\n";
}
/*
$diff = svn_diff_same_item($url, $from, $to);

list($result, $errors) = $diff;

$contents = '';
while (!feof($result)) 
{
    $contents .= fread($result, 8192);
}

//var_dump($contents);

function svn_diff_same_item($path, $rev1, $rev2) {
      return svn_diff($path, $rev1, $path, $rev2);
}

*/
?>
