<?php
if (isset($_GET['delete']))
{
$delete = $_GET['delete'];
if($delete == "true")
{
unlink("setup.php");
echo "file deleted!";
exit();
}
}
echo "<a href=\"setup.php?delete=true\">Delete this file</a><br><br>";

if (isset($_GET['setup']))
{
$setup = $_GET['setup'];
if($setup == "true")
{
  try
  {
    //open the database
require_once __DIR__ . '/config.php';

$db->exec("CREATE TABLE NoteBook (NoteBook_id INTEGER PRIMARY KEY AUTOINCREMENT, NoteBook_name TEXT NOT NULL)");
$db->exec("CREATE TABLE Notes (Notes_id INTEGER PRIMARY KEY AUTOINCREMENT, Notes_name TEXT NOT NULL, Notes_content TEXT NOT NULL, Notes_TimeStamp TEXT, Notes_TimeStamp_Modified TEXT, NoteBook_id INTEGER, FOREIGN KEY (NoteBook_id) REFERENCES NoteBook(NoteBook_id) ON DELETE CASCADE)");
// 3. Create Tables with ON DELETE CASCADE
echo "Database Created.\n";

	// for ($i = 1; $i <= 25; $i++)
	// {
	// $db->exec("INSERT INTO Contents (Id, Content, S) VALUES ('$i', '', '')");
	// }
	echo "Values inserted into the database.<br>";

    // close the database connection
    $db = NULL;

  }
  catch(PDOException $e)
  {
    print 'Exception : '.$e->getMessage();
  }
exit();
}
}
echo "<a href=\"setup.php?setup=true\">Start the setup</a><br><br>";
?>