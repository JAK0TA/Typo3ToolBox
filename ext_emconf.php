<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Typo3 ToolBox',
    'description' => 'ToolBox of Utility functions and ViewHelpers',
    'category' => 'misc',
    'author' => 'Thomas Lüder, Michael Krohn',
    'author_email' => 'lueder@jakota.de, krohn@jakota.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
