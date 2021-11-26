<?php

 /*
 * when people go to pixelpad.io/gamejam instead of a particular gamejam like https://pixelpad.io/gamejam/217164/
  * this will redirect them to that specific game jam page. in the future we can run a game jam archive, so people 
  * will see all the game jams on one page
 */

  $p = get_posts(array(
    "post_type" => "gamejam",
    "posts_per_page" => 1
  ));
  
  wp_redirect(get_permalink($p[0]->ID));



