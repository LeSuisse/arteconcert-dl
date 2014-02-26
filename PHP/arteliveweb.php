<?php
function getVideoPage($url) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    if (!$dom->loadHTMLFile($url)) {
        throw new Exception("La page n'a pas pu être chargée");
    }
    $domXpath = new DOMXPath($dom);
    $res = $domXpath->query("//@arte_vp_url");
    if (!$res->length > 0) {
        throw new Exception("La page contenant la vidéo n'a pas pu être trouvée");
    }
    return $res->item(0)->value;
}

function getUrlArteConcert($url) {
    $jsonVideo = json_decode(file_get_contents(getVideoPage($url)));
    $videoUrls = $jsonVideo->videoJsonPlayer->VSR;
    if ($videoUrls == NULL) {
        throw new Exception("L'URL de la vidéo n'a pu être trouvée");
    }
    $res = array();
    foreach ($videoUrls as $url) {
        if ($url->quality != '') {
            $res[$url->quality] = $url->url;
        }
    }
    return $res;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>ARTE Concert urls extracter</title>
</head>
<body>
<div>
    <?php
    try {
        $urls = getUrlArteConcert($_GET['url']);
        foreach ($urls as $quality => $url) {
            echo "$quality : <a href=\"$url\">$url</a><br />";
        }
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>
</div>
</body>
</html>