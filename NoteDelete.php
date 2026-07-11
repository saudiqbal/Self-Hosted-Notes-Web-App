<?php
require_once __DIR__ . '/config.php';
// Get Note Books
$stmt = $db->prepare('SELECT NoteBook_id, NoteBook_name FROM NoteBook');
$stmt->execute();
// 4. Fetch all matching items
$notebookitems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process Form
$formerror = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteID'])) {
$NoteDelete = $_GET['NoteID'];
if (preg_match('/[^0-9]/i',$NoteDelete)) {
$formerror = 1;
$msgcode[] = "3";
}
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Delete Notebook
$stmt = $db->prepare("DELETE FROM Notes WHERE Notes_id = :id");
$stmt->execute([':id' => $NoteDelete]);
header("Location: index.php?status=19");
exit;
}
else
{
echo "Error deleting note";
}
?>