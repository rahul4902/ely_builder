<?php
$files = glob('c:/ely_builder/propfincrm/API/*.php');
foreach ($files as $f) {
    $content = file_get_contents($f);
    $newContent = preg_replace('/mysqli_num_rows\s*\(\s*\$res_token\s*\)\s*==\s*1/', 'mysqli_num_rows($res_token) >= 1', $content);
    $newContent = preg_replace('/mysqli_num_rows\s*\(\s*\$res_key\s*\)\s*==\s*1/', 'mysqli_num_rows($res_key) >= 1', $newContent);
    $newContent = preg_replace('/\$count\s*==\s*1/', '$count >= 1', $newContent);
    if ($newContent !== $content) {
        file_put_contents($f, $newContent);
        echo "Updated: " . basename($f) . "\n";
    }
}
echo "All done.\n";
