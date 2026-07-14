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
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['NoteBookView'])) {
$NoteBookView = $_POST['NoteBookView'];
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
foreach ($notebookitems as $notebooks) {
	if($notebooks['NoteBook_id'] == $NoteBookView)
	{
		$current_Notebook = $notebooks['NoteBook_name'];
	}
}
// 3. Prepare and execute the query safely
$stmt = $db->prepare('SELECT Notes_id, Notes_name, NoteBook_id FROM Notes WHERE NoteBook_id = :id');
$stmt->execute([':id' => $NoteBookView]);
// 4. Fetch all matching items
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$NoteCount = count($items);
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
</head>
<body>
<div class='all'>
<div class='admin-header'>
<div class='header-text'>
<h3><a href="./"><?php echo $Title;?></a> <div class="dropdownClickable">
<span class="dropbtnClickable"><?php if(isset($current_Notebook)) { echo ' <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> '. $current_Notebook; } ?> </span>
<div class="dropdownClickable-content">
<a href="./NoteBookRename.php?NoteBookEdit=<?php echo $NoteBookView;?>">Notebook Rename</a>
<a href="./NoteBookDelete.php?NoteBookDelete=<?php echo $NoteBookView;?>">Notebook Delete</a>
</div>
</div>
<a href="./NoteAdd.php?NoteBookID=<?php echo $NoteBookView;?>">Add New Note <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" style="vertical-align: middle;line-height: 20px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
</h3>
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
<div class="row-container">
<?php
//$result = $db->query("SELECT rowid FROM PersonalEvents");
if(isset($_GET['page_id']))
{
$page_id = $_GET['page_id'];
if(preg_match('/[^0-9]/', $page_id))
{
exit;
}
$page = $page_id;
}
$result = $db->query('SELECT Notes_id, Notes_name, NoteBook_id FROM Notes WHERE NoteBook_id = :id');
$result->execute([':id' => $NoteBookView]);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
$total_pages = count($rows);
$limit = 25;
$adjacents = 3;
if(isset($_GET['page']))
{
$page = $_GET['page'];
if(preg_match('/[^0-9]/i', $page))
{
echo "SQL Injection detected!";
exit();
}
}
if(isset($page))
$start = ($page - 1) * $limit; 			//first item to display on this page
else
$start = 0;

$result = $db->query("SELECT Notes_id, Notes_name, NoteBook_id FROM Notes WHERE NoteBook_id = :id ORDER BY Notes_TimeStamp_Modified DESC, rowid DESC LIMIT '$start', '$limit'");
$result->execute([':id' => $NoteBookView]);
$i = 1;
echo '<div class="row-flex">' . "\xA";
foreach($result as $row)
{
echo '<a class="card" href="NoteView.php?NoteBookView='.$row['Notes_id'].'">' . "\xA";
echo '<div class="cardtext flexible">' . "\xA";
echo $row['Notes_name'] . "\xA";
echo '</div>' . "\xA";
echo '<div class="cardtitle"><svg width="16" height="16" viewBox="0 0 24 24" style="vertical-align: middle;"><path d="M14 2h-7.229l7.014 7h-13.785v6h13.785l-7.014 7h7.229l10-10z" fill="#6edb00" /></svg></div>' . "\xA";
echo '</a>' . "\xA";
}
echo '</div>' . "\xA";
$db = NULL;
// Pagination Starts
function pagination($total_pages,$limit,$page,$file,$adjacents){
		if($page)
				$start = ($page - 1) * $limit; 			//first item to display on this page
			else
				$start = 0;								//if no page var is given, set start to 0

		/* Setup page vars for display. */
		if ($page == 0) $page = 1;					//if no page var is given, default to 1.
		$prev = $page - 1;							//anterior page is page - 1
		$siguiente = $page + 1;							//siguiente page is page + 1
		$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;						//last page minus 1
		$link_previous = "&#10094; Previous";
		$link_next = "Next &#10095;";

		$p = false;
		if(strpos($file,"?")>0)
			$p = true;

		//ob_start();
		if($lastpage > 1){
			echo "<nav class=\"pagination_keyboard\">\n";
			echo "<div class=\"pagination_style\">\n";
				//anterior button
				if($page > 1)
								if($p)
									echo "<span class=\"pagination-prev\"><a href=\"$file$prev\" class=\"pagination-button left\">$link_previous</a></span>";
									else
									echo "<span class=\"pagination-prev\"><a href=\"$file$prev\" class=\"pagination-button left\">$link_previous</a></span>";
					else
						echo "<span class=\"buttonDisabled leftDisabled\">$link_previous</span>";
				//pages
				if ($lastpage < 7 + ($adjacents * 2)){//not enough pages to bother breaking it up
						for ($counter = 1; $counter <= $lastpage; $counter++){
								if ($counter == $page)
										echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
									else
												if($p)
												echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
												else
												echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
							}
					}
				elseif($lastpage > 5 + ($adjacents * 2)){//enough pages to hide some
						//close to beginning; only hide later pages
						if($page < 1 + ($adjacents * 2)){
								for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
										if ($counter == $page)
												echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
											else
														if($p)
														echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
														else
														echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
									}
								echo "";
										if($p){
										echo "<a href=\"$file$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}else{
										echo "<a href=\"$file?page=$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file?page=$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}

							}
						//in middle; hide some front and some back
						elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)){
										if($p){
										echo "<a href=\"{$file}1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"{$file}2\" class=\"pagination-button middle\">2</a>";
										}else{
										echo "<a href=\"$file?page=1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"$file?page=2\" class=\"pagination-button middle\">2</a>";
										}
								echo "";
								for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
									if ($counter == $page)
											echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
										else
													if($p)
													echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
													else
													echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
								echo "";
										if($p){
										echo "<a href=\"$file$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}else{
										echo "<a href=\"$file?page=$lpm1\" class=\"pagination-button middle\">$lpm1</a>";
										echo "<a href=\"$file?page=$lastpage\" class=\"pagination-button middle\">$lastpage</a>";
										}
							}
						//close to end; only hide early pages
						else{
										if($p){
										echo "<a href=\"{$file}1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"{$file}2\" class=\"pagination-button middle\">2</a>";
										}else{
										echo "<a href=\"$file?page=1\" class=\"pagination-button middle\">1</a>";
										echo "<a href=\"$file?page=2\" class=\"pagination-button middle\">2</a>";
										}
								echo "";
								for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
									if ($counter == $page)
											echo "<span class=\"pagination-button middleCurrent\">$counter</span>";
										else
													if($p)
													echo "<a href=\"$file$counter\" class=\"pagination-button middle\">$counter</a>";
													else
													echo "<a href=\"$file?page=$counter\" class=\"pagination-button middle\">$counter</a>";
							}
					}
				if ($page < $counter - 1)
								if($p)
								echo "<span class=\"pagination-next\"><a href=\"$file$siguiente\" class=\"pagination-button right\">$link_next</a></span>";
								else
								echo "<span class=\"pagination-next\"><a href=\"$file?page=$siguiente\" class=\"pagination-button rightDisabled\">$link_next</a></span>";
					else
						echo "<span class=\"buttonDisabled rightDisabled pagination-next\">$link_next</span>";

			echo "\n</div>\n";
			echo "</nav>\n";
			}
	}
// Pagination Ends
if(!isset($page))
$page=1;
echo pagination($total_pages,$limit,$page,$_SERVER["PHP_SELF"]."?NoteBookView=$NoteBookView&page_id=",$adjacents);
if(empty($rows))
{
echo '<div id="WarningMainContent">
<div id="Warningalignleft"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path style="fill:#ff5500" d="M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg></div>
<div id="Warningalignright">No notes were found in this notebook!</div>
</div>';
}
?>
</div>
<?php echo '<div class="md-card">
<div class="infoboxleft"></div>
<div class="infoboxmiddle"></div>
<div class="infoboxright"><span style="font-size: small;">Total Notes: '.$NoteCount.'</span></div>
</div>';
?>
</div>
</div>
<script>
window.addEventListener('load', function() {
document.addEventListener('click', function(event) {
	document.querySelectorAll('.dropdownClickable-content').forEach(function(el) {
	if (el !== event.target) el.classList.remove('dropbtnClickableshow')
	// close any showing dropdownClickable that isn't the one just clicked
	});
	if (event.target.matches('.dropbtnClickable')) {
	event.target.closest('.dropdownClickable').querySelector('.dropdownClickable-content').classList.toggle('dropbtnClickableshow')
	}
})
})
</script>
<?php
include "toast-code.php";
?>
</body>
</html>