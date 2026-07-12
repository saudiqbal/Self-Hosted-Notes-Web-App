<?php
$error = '1';
$message = '<div style="text-align: center;">Create New SQLite Database</div><div style="text-align: center;margin: 50px;"><a href="setup.php?setup=true" style="color:#000;background-color: #f44336;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;">Continue</a> <a href="./" style="color:#000;background-color: #4CAF50;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;">Cancel</a></div>';
if (isset($_GET['setup']))
{
	$setup = $_GET['setup'];
	if($setup == "true")
	{
		require_once __DIR__ . '/config.php';
		if (file_exists($db_filename))
		{
			$message = "<div style=\"text-align: center;margin: 25px;\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\"><path style=\"fill:#ff5500\" d=\"M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z\"/></svg> SQLite Database file already exists.</div><div style=\"text-align: center;margin: 25px;\">You can delete the existing SQLite Database file and recreate the database, you will lose all your existing data forever if you choose to continue.</div><div style=\"text-align: center;margin: 50px;\"><a href=\"setup.php?setup=true&delete=true\" style=\"color:#000;background-color: #f44336;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;\">Delete and continue</a> <a href=\"./\" style=\"color:#000;background-color: #4CAF50;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;\">Cancel</a></div>";
			$error = '1';
			if (isset($_GET['delete']))
			{
				$delete = $_GET['delete'];
				if($delete == "true")
				{
					unlink($db_filename);
					$error = '0';
				}
			}
		} else {
		$error = '0';
		}
		if ($error == '0')
		{
			try
			{
				$db = new PDO("sqlite:$db_filename");
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->exec('PRAGMA foreign_keys = ON;');
				$db->exec("CREATE TABLE NoteBook (NoteBook_id INTEGER PRIMARY KEY AUTOINCREMENT, NoteBook_name TEXT NOT NULL)");
				$db->exec("CREATE TABLE Notes (Notes_id INTEGER PRIMARY KEY AUTOINCREMENT, Notes_name TEXT NOT NULL, Notes_content TEXT NOT NULL, Notes_TimeStamp TEXT, Notes_TimeStamp_Modified TEXT, NoteBook_id INTEGER, FOREIGN KEY (NoteBook_id) REFERENCES NoteBook(NoteBook_id) ON DELETE CASCADE)");
				$message = "<div style=\"text-align: center;\">Database Created</div><div style=\"text-align: center;margin: 25px;\"><a href=\"./\" style=\"color:#000;background-color: #4CAF50;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;\">Continue</a></div>\n";
				$db = NULL;
			}
			catch(PDOException $e)
			{
				$message = 'Exception : '.$e->getMessage();
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Database Setup</title>
<style type="text/css">
.outer {
display: table;
position: absolute;
height: 100%;
width: 100%;
}
.middle {
display: table-cell;
vertical-align: middle;
}
.inner {
margin-left: auto;
margin-right: auto;
width: /*whatever width you want*/;
}
body {
background: #ffffff;
width:100%
top:0;
left:0;
margin:0;
padding:0;
text-align: center;
}
a {
color: #000;
text-decoration: none;
}
#mainContent {
border: 1px solid #ff870d;
margin: 0px 0px;
padding:0px 0px 0px 0px;
width: 600px;
-webkit-border-radius: 5px 5px 5px 5px;
-moz-border-radius: 5px 5px 5px 5px;
border-radius: 5px 5px 5px 5px;
display: inline-block;
}
#aligntop {
background-color:#ff870d;
color:#FFFFFF;
font-size: 14pt;
text-align: center;
padding: 5px 20px;
-moz-box-shadow: 0 0 25px #FF9633;
-webkit-box-shadow: 0 0 25px #FF9633;
box-shadow: 0 0 25px #FF9633;
-webkit-border-radius: 5px 5px 0px 0px;
-moz-border-radius: 5px 5px 0px 0px;
border-radius: 5px 5px 0px 0px;
}
#alignbottom {
background-color:#ffe2c3;
color:#000000;
font-size: 14pt;
padding: 15px 20px;
text-align: left;
-moz-box-shadow: 0 0 25px #FF9633;
-webkit-box-shadow: 0 0 25px #FF9633;
box-shadow: 0 0 25px #FF9633;
-webkit-border-radius: 0px 0px 5px 5px;
-moz-border-radius: 0px 0px 5px 5px;
border-radius: 0px 0px 5px 5px;
}
</style>
</head>
<body>
<div class="outer">
<div class="middle">
<div class="inner">
<div id="mainContent">
<div id="aligntop">New Database Setup</div>
<div id="alignbottom"><?php echo $message;?></div>
</div>
</div>
</div>
</div>
</body>
</html>