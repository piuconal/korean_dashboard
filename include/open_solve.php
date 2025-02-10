<?php
$output = shell_exec("python ../solve.py 2>&1"); // Chạy Python
echo "Script executed: " . nl2br(htmlspecialchars($output));
?>
