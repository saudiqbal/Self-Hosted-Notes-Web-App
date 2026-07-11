<?php
$Title='Personal Notes';
// 1. Connect to the SQLite database file
$db = new PDO('sqlite:PersonalNotes.db');
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// 2. Enable Foreign Key Constraints
$db->exec('PRAGMA foreign_keys = ON;');
?>
