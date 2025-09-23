<?php
function vite_manifest(): array {
    static $manifest = null;
    if ($manifest === null) {
        $manifestPath = __DIR__ . '/../dist/manifest.json';
        if (!file_exists($manifestPath)) {
            throw new Exception("Vite manifest not found. Run npm run build.");
        }
        $manifest = json_decode(file_get_contents($manifestPath), true);
    }
    return $manifest;
}

function vite_asset(string $entry): string {
    $manifest = vite_manifest();
    if (!isset($manifest[$entry])) {
        throw new Exception("Asset $entry not found in manifest.");
    }
    return '/dist/' . $manifest[$entry]['file'];
}

function vite_css(string $entry): array {
    $manifest = vite_manifest();
    if (!isset($manifest[$entry]['css'])) {
        return [];
    }
    return array_map(fn($css) => '/dist/' . $css, $manifest[$entry]['css']);
}
