<?php
require_once __DIR__ . '/config.php';
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
$stmt = $db->prepare('SELECT Notes_id, Notes_name, Notes_content, NoteBook_id, Notes_TimeStamp, Notes_TimeStamp_Modified FROM Notes WHERE Notes_id = :category');
$stmt->execute(['category' => $NoteBookView]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
$NoteID = $item['Notes_id'];
$NoteName = $item['Notes_name'];
$NoteContent = $item['Notes_content'];
$NoteBookID = $item['NoteBook_id'];
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
<script>
window.onload=function(){
var acc = document.getElementsByClassName("accordion");
var panel = document.getElementsByClassName('panel');
for (var i = 0; i < acc.length; i++) {
acc[i].onclick = function() {
var setClasses = !this.classList.contains('active');
setClass(acc, 'active', 'remove');
setClass(panel, 'show', 'remove');
if (setClasses) {
this.classList.toggle("active");
this.nextElementSibling.classList.toggle("show");
}
}
}
function setClass(els, className, fnName) {
for (var i = 0; i < els.length; i++) {
els[i].classList[fnName](className);
}
}
}
</script>
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
<h3><a href="./"><?php echo $Title; echo ' <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> '. $row['NoteBook_name'];?></a> <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> View Note</h3>
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
<?php echo '<div class="md-card">'. $NoteName .'
<div class="float-right"><a href="NoteEdit.php?NoteID='.$NoteID.'" class="ClassicButton" title="Edit" style="text-decoration:none;">Edit</a> <a id="modal-1" onclick="modalfunction(); return false;" class="ClassicButton" title="Edit" style="text-decoration:none;">Delete</a> <span class="ClassicButton">Created: '. date("Y-m-d H:i:s", $item['Notes_TimeStamp']) .'</span> <span class="ClassicButton">Modified: '. date("Y-m-d H:i:s", $item['Notes_TimeStamp_Modified']) .'</span></div><div style="clear: both;"></div>
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