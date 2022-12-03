<?php

chdir("jtbin/pocket");

$dir = '.';
$subdirectories = glob($dir . '/Cores/*', GLOB_ONLYDIR);
foreach ($subdirectories as $subdirectory) {
    $coreName = basename($subdirectory);
    echo "Zipping {$coreName}" . PHP_EOL;
    $command = "zip {$coreName}.zip Cores/{$coreName}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -r {$coreName}.zip Presets/{$coreName}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $names = explode('.', $coreName);
    $core = $names[1];
    $command = "zip {$coreName}.zip Platforms/{$core}.json";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip {$coreName}.zip Platforms/_images/{$core}.bin";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -f {$coreName}.zip Assets/{$core}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
}
 
