<?php

return [
  'prefix' => env('LOCK_PREFIX', 'lock'),
  'lock_key_separator' => env('LOCK_KEY_SEPARATOR', ':'),
];
