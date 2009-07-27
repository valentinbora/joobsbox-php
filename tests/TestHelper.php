<?php
/*
 * Start output buffering
 */
ob_start();

$joobsbox_render_var = "";
$testing = true;

/*
 * Set error reporting to the level to which code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Prepend the library/, tests/, and models/ directories to the
 * include_path. This allows the tests to run out of the box.
 */

/**
 * Register autoloader
 */
require_once("../index.php");
