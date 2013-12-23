<?php
function parseRtmpUrl($url) {
    preg_match('@rtmp://(.*?)/(.*?/.*?)/(.*?)\?@', $url, $parsedUrl);
    if(!isset($parsedUrl[1])) {
        throw new Exception("L'adresse du flux n'a pas pu être récupérée ou n'est pas dans le format attendu");
        
    }
    $host = $parsedUrl[1];
    $app = $parsedUrl[2];
    $playpath = $parsedUrl[3];
    preg_match('@.*?/(\d+.*)@', $playpath, $name);
    return "rtmpdump --host $host --app $app --playpath $playpath -o $name[1]";
}

function getRtmpdumpArteliveweb($url) {
    $data = file_get_contents($url);
    preg_match('@eventId=(\d+)@', $data, $eventId);
    if(!isset($eventId[1])) {
        throw new Exception("L'event ID n'a pas pu être lu pour la page demandée");
    }
    $urlXml = "http://download.liveweb.arte.tv/o21/liveweb/events/event-$eventId[1].xml";
    $docXml = simplexml_load_string(file_get_contents($urlXml));
    return array(
        'SD' => parseRtmpUrl((string) $docXml->event->video->urlSd),
        'HD' => parseRtmpUrl((string) $docXml->event->video->urlHd)
    );
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>ArteLiveWeb rtmpdump commands extracter</title>
</head>
<body>
<div>
    <?php
    try {
        $rtmpdumps = getRtmpdumpArteliveweb($_GET['url']);
        foreach ($rtmpdumps as $format => $command) {
            echo "$format : $command<br />";
        }
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>
</div>
</body>
</html>