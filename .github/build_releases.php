<?php

chdir("jtbin/pocket");

$dir = '.';
$subdirectories = glob($dir . '/Cores/*', GLOB_ONLYDIR);
foreach ($subdirectories as $subdirectory) {
    $coreName = basename($subdirectory);
    $date = getReleaseDate($coreName);
    $zipfile = "{$coreName}_{$date}.zip";
    if(file_exists(dirname(__FILE__) . '/' . $zipfile)) {
        continue;
    }
    echo "Zipping {$coreName}" . PHP_EOL;
    $command = "zip ../../{$zipfile} Cores/{$coreName}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -r ../../{$zipfile} Presets/{$coreName}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $names = explode('.', $coreName);
    $core = $names[1];
    $command = "zip ../../{$zipfile} Platforms/{$core}.json";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip ../../{$zipfile} Platforms/_images/{$core}.bin";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -r ../../{$zipfile} Assets/{$core}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
}

function getReleaseDate($coreName)
{
    $file = "./Cores/{$coreName}/core.json";
    $json = file_get_contents($file);

    $core = json_decode($json);
    if(!empty($core->core->metadata->date_release)) {
        $d = $core->core->metadata->date_release;
        $date = date('Ymd', strtotime($d));
        
        return $date;
    }
    
    return false;
}
