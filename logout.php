<?php
session_start();
session_unset(); // מנקה את כל המשתנים של הסשן
session_destroy(); // מסיים את הסשן
header("Location: index.php"); // מפנה את המשתמש לדף הבית או ההתחברות
exit;
?>
