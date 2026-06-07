<?php
session_start();
require_once '../config.php';
require_once INCLUDES_DIR . '/functions.php';

session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

header('Location: ' . url('/admin/login.php'));
exit;
