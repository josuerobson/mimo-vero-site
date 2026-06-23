<?php
/**
 * Admin Logout Handler
 * SuaNet Fibra
 */

require_once __DIR__ . '/../../config/site.php';

session_unset();
session_destroy();
header('Location: ../index.php');
exit;
