<?php

chdir("pocket");

$dir = '.';
$subdirectories = glob($dir . '/*', GLOB_ONLYDIR);
foreach ($subdirectories as $subdirectory) {
    $coreName = basename($subdirectory);
    echo "Zipping {$coreName}");
    shell_exec("zip {$coreName}.zip Cores/{$coreName}/*");
    shell_exec("zip -r {$coreName}.zip Presets/{$coreName}/*");
    $names = explode('.', $coreName);
    $core = $names[1];
    shell_exec("zip {$coreName}.zip Platforms/{$core}.json");
    shell_exec("zip {$coreName}.zip Platforms/_images/{$core}.bin");
    shell_exec("zip -f {$coreName}.zip Assets/{$core}/*");
}
 
