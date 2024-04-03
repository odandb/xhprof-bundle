<?php

// Configuration for the profiler to send data to XHGui.
// Set the callable of "profiler.enable" to return false in order to disable the profiler.
return [
    'profiler.enable' => function() {
        return false;
    },

    'save.handler' => 'file',
    'save.handler.file' => array(
        'filename' => 'var/xhgui/data.jsonl',
    ),
    'profiler.simple_url' => null,
    'profiler.options' => [],
    'date.format' => 'M jS H:i:s',
    'detail.count' => 6,
    'page.limit' => 25,
];
