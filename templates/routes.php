<?php
/**
 * Change first param for a new error file name
 */
$Router->setRoutesError("error_404", "notfound");
$Router->setRoutesError("error_500", "internal");

/**
 * Define your routes under this comment
 * -------------------------------------
 */

$Router->get("/", "Home#index");

