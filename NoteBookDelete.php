<?php
require_once __DIR__ . '/config.php';
$db = new PDO("sqlite:$db_filename");
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('PRAGMA foreign_keys = ON;');
// Get Note Books
$stmt = $db->prepare('SELECT NoteBook_id, NoteBook_name FROM NoteBook');
$stmt->execute();
// 4. Fetch all matching items
$notebookitems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process Form
$formerror = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteBookDelete'])) {
$NoteBookDelete = $_GET['NoteBookDelete'];
if (preg_match('/[^0-9]/i',$NoteBookDelete)) {
$formerror = 1;
$msgcode[] = "3";
}
}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteBookDeleteConfirm'])) {
$NoteBookDeleteConfirm = $_GET['NoteBookDeleteConfirm'];
if (preg_match('/[^0-9]/i',$NoteBookDeleteConfirm)) {
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
$stmt = $db->prepare("DELETE FROM NoteBook WHERE NoteBook_id = :id");
$stmt->execute([':id' => $NoteBookDeleteConfirm]);
header("Location: index.php?status=19");
exit;
}
// Get current Notebook name for diplay.
if(!empty($NoteBookDelete))
{
$stmt = $db->prepare("SELECT NoteBook_name FROM NoteBook WHERE NoteBook_id = :RowID");
$stmt->execute([':RowID' => $NoteBookDelete]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$Notebook_Name = $row['NoteBook_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $Title;?></title>
<meta name="viewport" content="user-scalable=yes, initial-scale=1, width=device-width">
<link rel="stylesheet" href="./css/stylesheet.css">
<script src="js/jsmodal.js"></script>
<script>
function modalfunction()
{
Modal.open({
content: '<span style="color:#000;"><strong>Delete Notebook?</strong><br><br>Are you sure you want to delete this Notebook?</span><br>Deleting this notebook will also delete all child notes.<br><br><br><a href="./NoteBookDelete.php?NoteBookDeleteConfirm=<?php if(isset($NoteBookDelete)) {echo $NoteBookDelete;} ?>" style="color:#000;background-color: #f44336;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;">Delete Notebook</a> <span onClick="Modal.close(); return false;" style="color:#000;background-color: #4CAF50;padding: 10px 24px;border-radius: 4px;margin-left:20px;cursor: default;">Close</span>',
draggable: true
});
}
</script>
</head>
<body>
<div class='all'>
<div class='admin-header'>
<div class='header-text'>
<span class="header-title"><a href="./"><?php echo $Title;?></a> <svg width="14" height="14" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" /></svg> Notebook Delete<?php if (isset($Notebook_Name)) { echo ' <svg width="14" height="14" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" /></svg> '.$Notebook_Name; } ?></span>
</div>
</div>
<div class='admin-sidebar'>
<nav class="nav-items"><form action="./NoteSearch.php" method="POST" style="vertical-align: middle;line-height: 16px;"><input name="search" placeholder='Search...' class='search-input' type='search' autocomplete="off"></form></nav>
<a href="./NoteBookAdd.php"><nav class="nav-items"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" style="vertical-align: middle;line-height: 16px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> New Notebook</nav></a>
<a href="./NoteStarred.php"><nav class="nav-items">Starred Notes</nav></a>
<?php
foreach ($notebookitems as $notebooks) {
echo "<a href=\"./NoteBookView.php?NoteBookView=" . $notebooks['NoteBook_id'] . "\"><nav class=\"nav-items\">" . $notebooks['NoteBook_name'] . "</nav></a>\n";
}
?>
</div>
<div class='center-content'>
<a id="modal-1" onclick="modalfunction(); return false;"></a>
<?php if(isset($NoteBookDelete))
{
	echo '<a id="modal-1" onclick="modalfunction(); return false;" class="notebook-form__submit" style="margin: 50px 50px;">Delete Notebook</a>';
}
else
{
echo '<div id="WarningMainContent">
<div id="Warningalignleft"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path style="fill:#ff5500" d="M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg></div>
<div id="Warningalignright">No Notebook ID provided to delete!</div>
</div>';
}
?>
</div>
</div>
<?php
include "toast-code.php";
?>
</body>
</html>