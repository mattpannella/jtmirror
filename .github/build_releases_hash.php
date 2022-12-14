<?php
chdir("jtbin/pocket/raw");

$dir = getcwd();
$subdirectories = glob($dir . '/Cores/*', GLOB_ONLYDIR);
foreach ($subdirectories as $subdirectory) {
    $core = basename($subdirectory);
    $hash = getMostRecentHash($core);
    echo "The most recent hash for {$core} is {$hash}" . PHP_EOL;
    $zipfile = "{$core}_{$hash}.zip";
    if(file_exists("../../../{$zipfile}")) {
        continue;
    }
    echo "Deleting old {$core} zip";
    exec("rm ../../../{$core}_*.zip");
    updateVersion($core, $hash);
    echo "Building new zip";

    $command = "zip ../../../{$zipfile} Cores/{$core}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -r ../../../{$zipfile} Presets/{$core}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $names = explode('.', $core);
    $platform = $names[1];
    $command = "zip ../../../{$zipfile} Platforms/{$platform}.json";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip ../../../{$zipfile} Platforms/_images/{$platform}.bin";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -r ../../../{$zipfile} Assets/{$platform}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
}

function getMostRecentHash($core)
{
    $mostRecentHash = "";
    $mostRecentTimestamp = 0;

    $names = explode('.', $core);
    $platform = $names[1];

    $filePaths = [
        "Cores/{$core}",
        "Assets/{$platform}",
        "Platforms/{$platform}.json",
        "Platforms/_images/{$platform}.bin",
        "Presets/{$core}"
    ];
    
    foreach($filePaths as $filePath) {
        $hash = exec("git log -1 --format='%h' -- " . $filePath);
        $timestamp = exec("git log -1 --format=%ct -- ". $filePath);
        if($timestamp > $mostRecentTimestamp) {
            $mostRecentHash = $hash;
            $mostRecentTimestamp = $timestamp;
        }
    }
    
    return $mostRecentHash;
}

function updateVersion($core, $hash)
{
    $file = "Cores/{$core}/core.json";
    $data = file_get_contents($file);
    $data = json_decode($data);
    $data->core->metadata->version = $hash;
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file, $json);
}
