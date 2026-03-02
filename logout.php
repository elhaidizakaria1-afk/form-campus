<?php
session_start();

// ── Anti-cache ──
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>