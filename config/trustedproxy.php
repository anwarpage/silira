<?php

$proxies = env('TRUSTED_PROXIES');

if (is_string($proxies)) {
    $proxies = trim($proxies);
    if ($proxies === '') {
        $proxies = null;
    } elseif ($proxies !== '*') {
        $proxies = array_values(array_filter(array_map('trim', explode(',', $proxies))));
    }
}

return [
    'proxies' => $proxies,
];
