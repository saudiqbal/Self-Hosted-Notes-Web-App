<?php
require_once __DIR__ . '/config.php';
$db = new PDO("sqlite:$db_filename");
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('PRAGMA foreign_keys = ON;');

// Process Form
$formerror = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteID'])) {
$NoteID = $_GET['NoteID'];
if (preg_match('/[^0-9]/i',$NoteID)) {
$formerror = 1;
$msgcode[] = "3";
}
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Toggle Star
$stmt = $db->prepare('SELECT S FROM Notes WHERE Notes_id = :RowID');
$stmt->execute([':RowID' => $NoteID]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row['S'] == '1')
{
$stmt = $db->prepare("UPDATE Notes SET S = :starredstate WHERE Notes_id = :RowID");
$stmt->execute([':starredstate' => NULL, ':RowID' => $NoteID]);
header("Location: NoteView.php?NoteBookView=$NoteID");
exit;
}
else
{
$stmt = $db->prepare("UPDATE Notes SET S = :starredstate WHERE Notes_id = :RowID");
$stmt->execute([':starredstate' => '1', ':RowID' => $NoteID]);
header("Location: NoteView.php?NoteBookView=$NoteID");
exit;
}
}
else
{
echo "Error getting note";
}
?>