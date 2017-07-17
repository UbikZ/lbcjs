<?php

try {
    $config = json_decode(file_get_contents("conf.json"));

    $pdo = new PDO('sqlite:' . __DIR__ . '/db/' . $config->sqliteDb);
    $pdo->exec("CREATE TABLE IF NOT EXISTS ad (id INTEGER PRIMARY KEY, external_id INTEGER, url TEXT, bit sent);");
    $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS ad_idx_externalId ON ad (external_id);");

    foreach ($config->urlList as $url) {
        $index = 0;
        do {
            $dom = new DOMDocument();
            $finalUrl = str_replace("##index##", $index, $url);
            $content = file_get_contents($finalUrl);

            echo "Iteration $index : $finalUrl";

            libxml_use_internal_errors(true);
            $dom->loadHTML($content);
            libxml_use_internal_errors(false);

            $xpath = new DOMXPath($dom);

            $elements = $xpath->query("//li[@itemtype=\"http://schema.org/Offer\"]/a");

            if (!is_null($elements)) {
                /** @var DOMElement $element */
                foreach ($elements as $element) {
                    echo $element->getAttribute("href") + PHP_EOL;
                }
            }
            $enabled = false;
            $index++;
        } while ($enabled);
    }
} catch (Exception $e) {
    echo $e->getTraceAsString();
}