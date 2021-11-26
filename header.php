<?php

/* This file is the header template. It is rendered whenever another
 * template calls get_header(). It should contain everything up to the
 * start of the main page content. Some tags are left open, which should
 * then be closed in footer.php. */

?>

<!doctype html>

<html <?php language_attributes(); ?> style="font-size:14px;">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name="Description" CONTENT="">
    <link rel="shortcut icon" href="<?= get_stylesheet_directory_uri(); ?>/favicon.ico" />
    <?php wp_head(); ?>
    <title><?= get_the_title($post); ?></title>
</head>

<body <?php body_class(); ?>>



