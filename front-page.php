<?php

/*
 * front-page.php is a wordpress file that automatically renders for homepages
 * if child them has a front-page class, will use that one instead
 */

$frontpage = new FrontPageParent();
if (class_exists("FrontPage")){
    $frontpage = new FrontPage();
}

$frontpage->render();


