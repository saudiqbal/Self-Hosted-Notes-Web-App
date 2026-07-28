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
$NoteBookCount = count($notebookitems);

// 3. Prepare and execute the query safely
$stmt = $db->prepare('SELECT Notes_id, Notes_name, NoteBook_id FROM Notes');
$stmt->execute();
// 4. Fetch all matching items
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$NoteCount = count($items);

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['tag'])) {
$tag = $_GET['tag'];
$tag = strtolower($tag);
if (preg_match('/[^a-zA-Z0-9\%\s]/',$tag)) {
$formerror = 1;
$msgcode[] = "3";
}
}

// Querying Items by Tag
/**
* Retrieves all articles associated with a specific tag name.
*/
function getArticlesByTag(PDO $db, string $tagName): array {
	$sql = "SELECT a.* FROM Notes a
			INNER JOIN article_tags at ON a.Notes_id = at.article_id
			INNER JOIN tags t ON t.id = at.tag_id
			WHERE t.name = ?";
	$stmt = $db->prepare($sql);
	$stmt->execute([trim(strtolower($tagName))]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$NoteTags = getArticlesByTag($db, $tag);
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
<div class='header-left-block'><span class="header-title"><a href="./"><?php echo $Title;?></a> <svg width="14" height="14" viewBox="0 0 100 100"><polygon fill="#fff" points="20,15 80,50 20,85" /></svg> Tags <svg width="14" height="14" viewBox="0 0 100 100"><polygon fill="#fff" points="20,15 80,50 20,85" /></svg> <?php echo $tag;?></span></div>
</div>
</div>
<div class='admin-sidebar'>
<nav class="nav-items"><form action="./NoteSearch.php" method="POST" style="vertical-align: middle;line-height: 16px;"><input name="search" placeholder='Search...' class='search-input' type='search' autocomplete="off"></form></nav>
<a href="./NoteBookAdd.php"><nav class="nav-items"><svg viewBox="0 0 24 24" width="16" height="16" style="vertical-align: middle;line-height: 16px;transform: translateY(-1px);" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> New Notebook</nav></a>
<a href="./NoteStarred.php"><nav class="nav-items">Starred Notes And Tags</nav></a>
<?php
foreach ($notebookitems as $notebooks) {
echo "<a href=\"./NoteBookView.php?NoteBookView=" . $notebooks['NoteBook_id'] . "\"><nav class=\"nav-items\">" . $notebooks['NoteBook_name'] . "</nav></a>\n";
}
?>
</div>
<div class='center-content'>
<div class="row-container">
<div class="row-flex">
<?php
if (isset($NoteTags)) {
	foreach ($NoteTags as $tags_array_item => $tags_array_value) {
		echo '<a class="card" href="NoteView.php?NoteBookView='.$tags_array_value['Notes_id'].'">' . "\xA";
		echo '<div class="cardtext flexible">' . "\xA";
		echo $tags_array_value['Notes_name'];
		echo '</div>' . "\xA";
		echo '<div class="cardtitle"><svg width="16" height="16" viewBox="0 0 24 24" style="vertical-align: middle;"><path d="M14 2h-7.229l7.014 7h-13.785v6h13.785l-7.014 7h7.229l10-10z" fill="#6edb00" /></svg></div>' . "\xA";
		echo '</a>' . "\xA";
	}
}
?>
</div>
</div>
<?php echo '<div class="md-card">
<div class="infoboxleft"></div>
<div class="infoboxmiddle"></div>
<div class="infoboxright"><span style="font-size: small;">Total Notebooks: '.$NoteBookCount.' / Total Notes: '.$NoteCount.'</span></div>
</div>';
?>
</div>
</div>
<?php
include "toast-code.php";
?>
</body>
</html>