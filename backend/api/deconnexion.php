<?php
require_once __DIR__ . '/_helpers.php';
session_unset(); session_destroy();
json_success([], 'Déconnecté');
