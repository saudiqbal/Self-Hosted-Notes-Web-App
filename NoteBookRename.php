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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteBookEdit'])) {
$NoteBookEdit = $_GET['NoteBookEdit'];
if (preg_match('/[^0-9]/i',$NoteBookEdit)) {
$formerror = 1;
$msgcode[] = "3";
}
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
$NoteBookEdit = $_POST['NoteBookEdit'];
if(empty($NoteBookEdit))
{
$formerror = 1;
$msgcode[] = "3";
}
if (preg_match('/[^0-9]/i',$NoteBookEdit)) {
$formerror = 1;
$msgcode[] = "3";
}
$new_notebook = $_POST['notebook'];
if (empty($new_notebook)) {
$formerror = 1;
$msgcode[] = "7";
}
if (strlen($new_notebook) < 3)
{
$formerror = 1;
$msgcode[] = "13";
}
elseif(strlen($new_notebook) > 50)
{
$formerror = 1;
$msgcode[] = "14";
}
$stmt = $db->prepare('SELECT NoteBook_name FROM NoteBook WHERE NoteBook_name LIKE :NoteBook_name LIMIT 1');
// 4. Fetch all matching items
$stmt->execute([':NoteBook_name' => $new_notebook]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
if (strtolower($row['NoteBook_name']) === strtolower($new_notebook)) {
$formerror = 1;
$msgcode[] = "18";
}
}
if(isset($msgcode) && in_array('3', $msgcode) && (in_array('18', $msgcode) || in_array('7', $msgcode) || in_array('13', $msgcode) || in_array('14', $msgcode)))
{
	$msgcode = [];
	$msgcode[] = "3";
}
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Insert new Notebook
$stmt = $db->prepare("UPDATE NoteBook SET NoteBook_name = :name WHERE NoteBook_id = :RowID");
$stmt->bindValue(':name', $new_notebook);
$stmt->bindValue(':RowID', $NoteBookEdit);
$stmt->execute();
header("Location: index.php?status=15");
exit;
}
// Get current Notebook name for diplay.
if(!empty($NoteBookEdit))
{
$stmt = $db->prepare("SELECT NoteBook_name FROM NoteBook WHERE NoteBook_id = :RowID");
$stmt->execute([':RowID' => $NoteBookEdit]);
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
</head>
<body>
<div class='all'>
<div class='admin-header'>
<div class='header-text'>
<h3><a href="./"><?php echo $Title;?></a> <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> Notebook Rename<?php if (isset($Notebook_Name)) { echo ' <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> '.$Notebook_Name; } ?></h3>
</div>
</div>
<div class='admin-sidebar'>
<nav><li><form action="./NoteSearch.php" method="POST" style="vertical-align: middle;line-height: 16px;"><input name="search" placeholder='Search...' class='search-input' type='search' autocomplete="off"></form></li></nav>
<?php
foreach ($notebookitems as $notebooks) {
echo "<nav><a href=\"./NoteBookView.php?NoteBookView=" . $notebooks['NoteBook_id'] . "\"><li>" . $notebooks['NoteBook_name'] . "</li></a></nav>\n";
}
?>
<nav><a href="./NoteBookAdd.php"><li><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" style="vertical-align: middle;line-height: 16px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> New Notebook</li></a></nav>
</div>
<div class='center-content'>
<?php
if(!empty($NoteBookEdit))
{?>
<form action="NoteBookRename.php" class="notebook-form" method="POST">
<input type="hidden" name="NoteBookEdit" value="<?php if(isset($NoteBookEdit)) { echo $NoteBookEdit; } ?>">
<input name="notebook" placeholder="Notebook Name" type="text" id="text" class="notebook-form__label"<?php
if(isset($Notebook_Name))
{
	echo ' value="'.$Notebook_Name.'"';
}
elseif(isset($_POST['submit'])){
echo ' value="'.$_POST['notebook'].'"';
} ?> required>
<button class="notebook-form__submit" type="submit" name="submit">Rename</button>
</form>
<?php
}
else
{
echo '<div id="WarningMainContent">
<div id="Warningalignleft"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path style="fill:#ff5500" d="M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg></div>
<div id="Warningalignright">No Notebook ID provided to rename!</div>
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