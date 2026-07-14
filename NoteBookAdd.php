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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
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
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Insert new note book
$new_notebook = trim($new_notebook);
$stmt = $db->prepare("INSERT INTO NoteBook (NoteBook_name) VALUES (:name)");
$stmt->bindValue(':name', $new_notebook);
$stmt->execute();
header("Location: index.php?status=15");
exit;
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
<h3><a href="./"><?php echo $Title;?></a> <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> Add New Notebook</h3>
</div>
</div>
<div class='admin-sidebar'>
<nav><li><form action="./NoteSearch.php" method="POST" style="vertical-align: middle;line-height: 16px;"><input name="search" placeholder='Search...' class='search-input' type='search' autocomplete="off"></form></li></nav>
<nav><a href="./NoteBookAdd.php"><li><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" style="vertical-align: middle;line-height: 16px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> New Notebook</li></a></nav>
<nav><a href="./NoteStarred.php"><li>Starred Notes</li></a></nav>
<?php
foreach ($notebookitems as $notebooks) {
echo "<nav><a href=\"./NoteBookView.php?NoteBookView=" . $notebooks['NoteBook_id'] . "\"><li>" . $notebooks['NoteBook_name'] . "</li></a></nav>\n";
}
?>
</div>
<div class='center-content'>
<form action="NoteBookAdd.php" class="notebook-form" method="POST">
<input name="notebook" placeholder="Notebook Name" type="text" id="text" tabindex="1" class="notebook-form__label" <?php if (isset($_POST['submit'])){
echo 'value="'.$_POST['notebook'].'"';
} ?> autocomplete="off" required>
<button class="notebook-form__submit" tabindex="2" type="submit" name="submit">Add</button>
</form>
</div>
</div>
<?php
include "toast-code.php";
?>
</body>
</html>