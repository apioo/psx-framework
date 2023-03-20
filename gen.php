<?php

$schemas = [
    [
        'source' => 'resources/typeschema.json',
        'target' => 'src/Model',
        'config' => 'namespace=PSX\Framework\Model',
    ]
];

foreach ($schemas as $row) {
    $folder = __DIR__ . '/' . $row['target'];
    if (!is_dir($folder)) {
        continue;
    }

    deleteFilesInFolder($folder);

    $cmd = sprintf('php vendor/psx/schema/bin/schema schema:parse %s %s --format=php --config=%s', ...array_values(array_map('escapeshellarg', $row)));

    echo 'Generate ' . $row['source'] . "\n";
    echo '> ' . $cmd . "\n";

    shell_exec($cmd);
}

function deleteFilesInFolder(string $folder): void
{
    $files = scandir($folder);
    foreach ($files as $file) {
        if ($file[0] === '.') {
            continue;
        }

        $path = $folder . '/' . $file;
        if (!is_file($path)) {
            continue;
        }

        unlink($path);
    }
}
