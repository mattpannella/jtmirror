<?php
const MAPPING = [
    'jtcontra' => 'Contra',
    'jtdd' => 'Double Dragon',
    'jtdd2' => 'Double Dragon II',
    'jtgng' => "Ghosts 'n Goblins",
    'jtkicker' => 'Kicker',
    'jtkiwi' => 'Kageki',
    'jtkunio' => 'Renegade',
    'jtmikie' => 'Mikie',
    'jtpang' => 'Pang',
    'jtpinpon' => "Konami's Ping Pong",
    'jtroadf' => 'Road Fighter',
    'jtroc' => "Roc'n Rope",
    'jtsbaskt' => 'Super Basketball',
    'jttrack' => "Track & Field",
    'jtvigil' => "Vigilante",
    'jtyiear' => 'Yie Ar Kung-Fu',
    'jtoutrun' => 'Outrun',
    'jtcps1' => 'CPS1',
    'jtkarnov' => 'Karnov',
    'jtexed' => 'Exed Eyes',
    'jtgunsmk' => 'Gun Smoke',
    'jtarms' => 'Side Arms',
    'jtsectnz' => 'Section Z',
    'jtsf' => 'Street Fighter',
    'jttrojan' => 'Trojan',
    'jtvulgus' => 'Vulgus'
];

chdir("jtbin/pocket/raw");

$dir = getcwd();
$subdirectories = glob($dir . '/Cores/*', GLOB_ONLYDIR);
foreach ($subdirectories as $subdirectory) {
    $core = basename($subdirectory);
    $hash = getMostRecentHash($core);
    $names = explode('.', $core);
    $platform = $names[1];
    echo "The most recent hash for {$core} is {$hash}" . PHP_EOL;
    $zipfile = "{$core}_{$hash}.zip";
    if(file_exists("../../../{$zipfile}")) {
        continue;
    }
    echo "Deleting old {$core} zip";
    exec("rm ../../../{$core}_*.zip");
    updateVersion($core, $hash);
    updatePlatform($platform);
    echo "Building new zip";

    $command = "zip ../../../{$zipfile} Cores/{$core}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
    $command = "zip -r ../../../{$zipfile} Presets/{$core}/*";
    echo $command . PHP_EOL;
    echo shell_exec($command);
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

function updatePlatform($platform)
{
    $file = "Platforms/{$platform}.json";
    $data = file_get_contents($file);
    $data = json_decode($data);
    if (isset(MAPPING[$platform])) {
        $data->platform->name = MAPPING[$platform];
    }
    $data->platform->category = "Arcade";
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file, $json);
}
