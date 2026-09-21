<?php

namespace ThunderLocalDocs;

$segments = [];
for ($index = 1; $index <= 4; $index++) {
    $value = trim((string)URL($index), '/');
    $segments[$index] = $value === '' ? '' : rawurldecode($value);
}

$first = $segments[1];
$second = $segments[2];
$third = $segments[3];
$fourth = $segments[4];

if ($first === '') {
    require __DIR__ . '/index.php';
    return;
}

// Search must be resolved before the dynamic topic route.
if ($first === 'search') {
    if ($second !== '' || $third !== '' || $fourth !== '') {
        docs_not_found();
        return;
    }

    require __DIR__ . '/search.php';
    return;
}

if ($fourth !== '') {
    docs_not_found();
    return;
}

$topicSlug = $first;

if ($second === '') {
    require __DIR__ . '/topic.php';
    return;
}

$subtopicSlug = $second;

if ($third === '') {
    require __DIR__ . '/subtopic.php';
    return;
}

$sectionSlug = $third;
require __DIR__ . '/section.php';
