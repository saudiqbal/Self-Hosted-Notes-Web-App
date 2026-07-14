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

$formerror = 0;
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteBookView'])) {
$NoteBookView = $_GET['NoteBookView'];
if (preg_match('/[^0-9]/i',$NoteBookView)) {
$formerror = 1;
$msgcode[] = "3";
}
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
$stmt = $db->prepare('SELECT Notes_id, Notes_name, Notes_content, NoteBook_id, Notes_TimeStamp, Notes_TimeStamp_Modified, S FROM Notes WHERE Notes_id = :category');
$stmt->execute(['category' => $NoteBookView]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
$NoteID = $item['Notes_id'];
$NoteName = $item['Notes_name'];
$NoteContent = $item['Notes_content'];
$NoteBookID = $item['NoteBook_id'];
$NoteStarred = $item['S'];
}
if($NoteStarred == '1')
{
$NoteStarred = '<svg width="20" height="20" viewBox="0 0 24 24" fill="#FFD700" stroke="#ffa5a5" stroke-width="1"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" /></svg>';
}
else
{
$NoteStarred = '<svg width="20" height="20" viewBox="0 0 24 24" fill="#000" stroke="#FFD700" stroke-width="1"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" /></svg>';
}
// Get current Notebook name
$stmt = $db->prepare('SELECT NoteBook_name FROM NoteBook WHERE NoteBook_id = :id LIMIT 1');
$stmt->execute([':id' => $NoteBookID]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
}
else
{
echo "Error getting ID";
exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $Title;?></title>
<meta name="viewport" content="user-scalable=yes, initial-scale=1, width=device-width">
<link rel="stylesheet" href="./css/stylesheet.css">
<script src="js/prism.js"></script>
<link rel="stylesheet" type="text/css" href="css/prism.css">
<script src="js/jsmodal.js"></script>
<script>
function modalfunction()
{
Modal.open({
content: '<span style="color:#000;"><strong>Delete Confirmation</strong><br><br>Are you sure you want to delete this Note?</span><br><br><br><a href="./NoteDelete.php?NoteID=<?php if(isset($NoteID)) {echo $NoteID;} ?>" style="color:#000;background-color: #f44336;padding: 10px 24px;border-radius: 4px;cursor: default;text-decoration: none;">Delete Note</a> <span onClick="Modal.close(); return false;" style="color:#000;background-color: #4CAF50;padding: 10px 24px;border-radius: 4px;margin-left:20px;cursor: default;">Close</span>',
draggable: true
});
}
</script>
</head>
<body class="line-numbers">
<div class='all'>
<div class='admin-header'>
<div class='header-text'>
<h3><a href="./"><?php echo $Title; echo '</a>'; echo ' <a href="NoteBookView.php?NoteBookView='.$NoteBookID.'"><svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> '. $row['NoteBook_name'];?></a></h3>
<div class='header-greet'>
<a href='NoteStarToggle.php?NoteID=<?php echo $NoteID; ?>' title="Add / Remove Star" style="text-decoration:none;color:#7ebcff;margin-right: 20px;"><?php echo $NoteStarred;?></a> <a href='NoteEdit.php?NoteID=<?php echo $NoteID; ?>' title="Edit" style="text-decoration:none;color:#7ebcff;margin-right: 20px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a><a id="modal-1" onclick="modalfunction(); return false;" title="Delete" style="color:#FF0000;text-decoration:none;margin-right: 20px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg></a>
</div>
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
<?php echo '<div class="md-card">
<div class="infoboxleft"><span style="font-size: x-large;">'. $NoteName .'</span></div>
<div class="infoboxmiddle"></div>
<div class="infoboxright"><span style="font-size: small;">Created: '. date("Y-m-d H:i:s", $item['Notes_TimeStamp']) .' / Modified: '. date("Y-m-d H:i:s", $item['Notes_TimeStamp_Modified']) .'</span></div>
</div>';
echo $NoteContent;
?>
</div>
</div>
<?php
include "toast-code.php";
?>
</body>
</html>