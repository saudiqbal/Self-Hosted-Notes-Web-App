<?php
if (isset($_GET['status'])) {
$msgcode[] = $_GET['status'];
}
if(isset($msgcode))
{
$codes=array(
1 => array('No message to display.', '#ff2424'),
2 => array('SQL injection detected in input.', '#ff2424'),
3 => array('Invalid ID detected', '#333'),
4 => array('Link cannot be empty', '#ff2424'),
5 => array('Description cannot be empty', '#ff2424'),
6 => array('Invalid URL detected', '#ff2424'),
7 => array('Main content cannot be empty', '#ff2424'),
8 => array('Invalid IP Address detected', '#ff2424'),
9 => array('Invalid characters in input', '#ff2424'),
10 => array('Input is too short, minimum is 10 characters (160 max)', '#ff2424'),
11 => array('Input is too long, maximum is 160 characters (10 min)', '#ff2424'),
12 => array('Mobile version of this web site is not supported', '#ff2424'),
13 => array('Notebook name is too short, minimum is 3 characters (160 max)', '#ff2424'),
14 => array('Notebook name is too long, maximum is 160 characters (3 min)', '#ff2424'),
15 => array('Notebook has been successfully added!', '#009000'),
16 => array('Event has been successfully deleted!', '#009000'),
17 => array('Event has been successfully updated!', '#009000'),
18 => array('Notebook already exists, choose another name', '#ff2424'),
19 => array('Notebook successfully deleted!', '#009000'),
20 => array('Note content is too short, minimum is 3 characters (1,000,000 max)', '#ff2424'),
21 => array('Content is too long, maximum is 1,000,000 characters (3 min)', '#ff2424'),
22 => array('Note title is empty', '#ff2424'),
23 => array('Note title is too short, minimum is 3 characters (160 max)', '#ff2424'),
24 => array('Note title is empty', '#ff2424'),
);
echo '<script src="js/toastify.js"></script>' . "\xA";
foreach($msgcode as $toastcode)
{
$message = $codes[$toastcode][0];
$messagecolor = $codes[$toastcode][1];
echo <<<EOD
<script>
var myToast = Toastify({text: "$message", duration: 5000, gravity: "bottom", position: 'center', close: false, backgroundColor: "$messagecolor",}); myToast.showToast();
</script>

EOD;
}
}
?>