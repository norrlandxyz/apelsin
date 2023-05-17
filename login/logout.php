<?PHP
echo "<h1>logout</h1>";
session_start();
session_unset();
session_destroy();
session_write_close();
header("Refresh:2; url='../index.php'");
?>
