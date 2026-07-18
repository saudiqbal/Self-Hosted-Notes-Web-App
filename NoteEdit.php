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
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['NoteID'])) {
$NoteID = $_GET['NoteID'];
if (preg_match('/[^0-9]/i',$NoteID)) {
$formerror = 1;
$msgcode[] = "3";
}
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
$NoteID = $_POST['NoteID'];
if(empty($NoteID))
{
$formerror = 1;
$msgcode[] = "3";
}
if (preg_match('/[^0-9]/i',$NoteID)) {
$formerror = 1;
$msgcode[] = "3";
}
// Note title
$new_note = $_POST['notebook'];
if (empty($new_note)) {
$formerror = 1;
$msgcode[] = "22";
}
if (strlen($new_note) < 3)
{
$formerror = 1;
$msgcode[] = "23";
}
elseif(strlen($new_note) > 50)
{
$formerror = 1;
$msgcode[] = "14";
}
// Note content
$notecontent = $_POST['notecontent'];
if (strlen($notecontent) < 1)
{
$formerror = 1;
$msgcode[] = "20";
}
elseif(strlen($notecontent) > 1000000)
{
$formerror = 1;
$msgcode[] = "21";
}
if(isset($msgcode) && in_array('3', $msgcode) && (in_array('18', $msgcode) || in_array('7', $msgcode) || in_array('13', $msgcode) || in_array('14', $msgcode)))
{
	$msgcode = [];
	$msgcode[] = "3";
}
if(isset($msgcode) && in_array('22', $msgcode) && (in_array('23', $msgcode)))
{
	$msgcode = [];
	$msgcode[] = "22";
}
}
else
{
	$formerror = 1;
}
if ($formerror == 0){
// Edit Note
$new_note = trim($new_note);
$stmt = $db->prepare("UPDATE Notes SET Notes_name = :notetitle, Notes_content = :notecontent, Notes_TimeStamp_Modified = :notetimestampmodified WHERE Notes_id = :RowID");
$stmt->execute([':notetitle' => $new_note, ':notecontent' => $notecontent, ':notetimestampmodified' => time(), ':RowID' => $NoteID]);
header("Location: NoteView.php?NoteBookView=$NoteID");
exit;
}
// Get current Notebook name for diplay.
if(!empty($NoteID))
{
function GlobalXSSFilter($param)
{
	$custom_entities = array(
	'&' => '&amp;',
	'"' => '&quot;',
	"'" => '&apos;',
	'/' => '&sol;',
	'<' => '&lt;',
	'>' => '&gt;',
	'\\' => '&bsol;'
	);
	$param = strtr($param, $custom_entities);
	return $param;
}
$stmt = $db->prepare('SELECT Notes_id, Notes_name, Notes_content, NoteBook_id, Notes_TimeStamp, Notes_TimeStamp_Modified FROM Notes WHERE Notes_id = :category');
$stmt->execute(['category' => $NoteID]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($items as $item) {
$NoteID = $item['Notes_id'];
$NoteName = $item['Notes_name'];
$NoteContent = GlobalXSSFilter($item['Notes_content']);
}
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
<script src="js/tinymce/tinymce.min.js"></script>
</head>
<body>
<div class='all'>
<div class='admin-header'>
<div class='header-text'>
<h3><a href="./"><?php echo $Title;?></a> <svg width="16" height="16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><polygon fill="#fff" points="20,15 80,50 20,85" fill="black" /></svg> Edit Note</h3>
</div>
</div>
<div class='admin-sidebar'>
<nav class="nav-items"><form action="./NoteSearch.php" method="POST" style="vertical-align: middle;line-height: 16px;"><input name="search" placeholder='Search...' class='search-input' type='search' autocomplete="off"></form></nav>
<nav class="nav-items"><a href="./NoteAdd.php"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" style="vertical-align: middle;line-height: 16px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> New Notebook</a></nav>
<a href="./NoteStarred.php"><nav class="nav-items">Starred Notes</nav></a>
<?php
foreach ($notebookitems as $notebooks) {
echo "<a href=\"./NoteBookView.php?NoteBookView=" . $notebooks['NoteBook_id'] . "\"><nav class=\"nav-items\">" . $notebooks['NoteBook_name'] . "</nav></a>\n";
}
?>
</div>
<div class='center-content'>
<?php
if(!empty($NoteID))
{?>
<form action="NoteEdit.php" method="POST">
<div class="notebook-form">
<input type="hidden" name="NoteID" value="<?php if(isset($NoteID)) { echo $NoteID; } ?>">
<input name="notebook" placeholder="Note Title" type="text" id="text" tabindex="1" class="notebook-form__fulllabel"<?php
if(isset($NoteName))
{
	echo ' value="'.$NoteName.'"';
}
elseif(isset($_POST['submit'])){
echo ' value="'.$_POST['notebook'].'"';
} ?> required></div>
<br>
<textarea id="myTextarea" name="notecontent" tabindex="2" style="width:100%;height:750px;"><?php
if(isset($NoteContent))
{
echo $NoteContent;
}
elseif(isset($_POST['submit'])){
echo $_POST['notecontent'];
} ?></textarea>
<div style="text-align:left; margin-top: 20px;">
<input type="submit" name="submit" id="submit" tabindex="3" value="Submit" class="btnlt">
<div class="dropdown">
<button class="btnrt" style="border-left:1px solid #0053a6;" onclick="return false;">
<svg width="8" height="8" viewBox="0 0 24 24"><path fill="#fff" d="M12 21l-12-18h24z"></svg>
<i class="fa fa-caret-down"></i>
</button>
<div class="dropdown-content">
<a href="./">Cancel</a>
</div>
</div>
</div>
<div style="height:50px;"></div>
</form>
<?php
}
else
{
echo '<div id="WarningMainContent">
<div id="Warningalignleft"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path style="fill:#ff5500" d="M19.64 16.36 11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg></div>
<div id="Warningalignright">No Note ID provided to edit!</div>
</div>';
}
?>
</div>
</div>
<?php
include "toast-code.php";
?>
<script>
const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;
tinymce.init({
  selector: '#myTextarea',
  license_key: 'gpl',
  toolbar_sticky: true,
  toolbar_sticky_offset: 40,
  plugins: [
    'accordion', 'advlist', 'anchor', 'autolink', 'autoresize', 'autosave', 'charmap', 'code',
    'codesample', 'directionality', 'emoticons', 'fullscreen', 'help', 'image',
    'importcss', 'insertdatetime', 'link', 'lists', 'media',
    'nonbreaking', 'pagebreak', 'preview', 'quickbars', 'save', 'searchreplace',
    'table', 'visualblocks', 'visualchars', 'wordcount',
  ],
  menubar: 'file edit view insert format tools table help',
  toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: '{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '10080m',
  image_advtab: true,
  importcss_append: true,
  height: 600,
  image_caption: true,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  noneditable_class: 'mceNonEditable',
  toolbar_mode: 'sliding',
  contextmenu: 'link image table',
  skin: useDarkMode ? 'oxide-dark' : 'oxide',
  content_css: useDarkMode ? 'dark' : 'default',
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
  codesample_languages: [
        {text: 'HTML/XML', value: 'markup'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'CSS', value: 'css'},
        {text: 'PHP', value: 'php'},
        {text: 'Ruby', value: 'ruby'},
        {text: 'Python', value: 'python'},
        {text: 'Java', value: 'java'},
        {text: 'C', value: 'c'},
        {text: 'C#', value: 'csharp'},
        {text: 'C++', value: 'cpp'},
        {text: 'Clike', value: 'clike'},
        {text: 'ASP.net', value: 'aspnet'},
        {text: 'Bash', value: 'bash'},
        {text: 'Basic', value: 'basic'},
        {text: 'bbcode', value: 'bbcode'},
        {text: 'DNS', value: 'dns'},
        {text: 'DNS zone file', value: 'dns-zone-file'},
        {text: 'git', value: 'git'},
        {text: 'ini', value: 'ini'},
        {text: 'Java doc like', value: 'javadoclike'},
        {text: 'Js extras', value: 'js-extras'},
        {text: 'json', value: 'json'},
        {text: 'Markdown', value: 'markdown'},
        {text: 'Markup templating', value: 'markup-templating'},
        {text: 'nginx', value: 'nginx'},
        {text: 'PHP Doc', value: 'phpdoc'},
        {text: 'PHP Extras', value: 'php-extras'},
        {text: 'wasm', value: 'wasm'},
        {text: 'Yaml', value: 'yaml'}
    ],
});
</script>
</body>
</html>