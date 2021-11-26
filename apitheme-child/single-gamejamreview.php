<?php
/* templates are automatically pulled from the theme directory as long as it has the single-{custom posttype}.php format */
/* we can move these functions into the includes file single-gamejam-parts.php */

namespace CUSTOMPOSTS;

$comment = new Comment();

add_action("wp_head", function() {
    echo '<link href="https://fonts.cdnfonts.com/css/04b30" rel="stylesheet">';
});

add_action("wp_head", function() {
    wp_enqueue_style(
            'gamejamcss',
            PIXELPAD_PLUGIN_URL . 'customposts/gamejams/gamejam.css',
            array(),
            "1.0.0",
            "all"
    );
}
);

get_header();
global $post;
$reviewMeta = get_post_meta($post->ID);
?>

<div class="container-fluid">
    <?php 
    global $region;
    $region->render_navbar();
    ?>
    <div class="row">
        <?php render_sidebar(true); ?>
        <div class="col-12 contentParent">
            <div class="container my-5">
                <div class="row  p-3">
                    <div class="col-12 col-md-3 col-lg-2">
                        <img src="<?= get_post_meta($reviewMeta["gamejamreviewGameJamId"][0], "gamejamThumbnail", true); ?>" class="w-100">
                    </div>
                    <div class="col-12 col-md-9 col-lg-10">
                        <h1 class="d-none d-sm-block font-04b my-4" style="color:#520E76;">
                            <?= get_the_title($reviewMeta["gamejamreviewGameJamId"][0]); ?>
                        </h1>
                        <h3 class="font-04b my-4" style="color:#520E76;">Mentor Review</h3>
                    </div>
                </div>

                <div class="row p-3">
                    <div class="col-12">
                        <strong>Game Submitted </strong>
                    </div>

                    <div class="col-12">
                        <a href="<?= get_permalink($reviewMeta["gamejamreviewGameId"][0]) . "?edit=1"; ?>">
                            <?= get_the_title($reviewMeta["gamejamreviewGameId"][0]); ?>
                        </a>
                    </div>
                    <div class="col-12">
                        <small class="text-secondary"><em>
                                <div>Submitted on: <?= get_the_date("M d Y", $post->ID); ?></div>
                                <div>Code submitted updates in real time. Code may have been updated since this was submitted.</div>
                            </em></small>
                    </div>
                </div>

                <div class="row p-3">
                    <div class="col-12">
                        <strong>Comment for Mentor</strong>
                    </div>
                    <div class="col-12">
                        <?php
                        $com = $reviewMeta["gamejamreviewTeamComment"][0];
                        echo empty($com) ? "No comment provided." : $com;
                        ?>
                    </div>
                </div>

                <?php if ($comment->count_comments() >= 3) { ?>
                    <div class="row no-gutters">            
                        <div class="col-12">
                            <div class="alert alert-warning">This game has already been reviewed by 3 users. Please choose a different game to review.</div>
                        </div>
                    </div>
                    <?php
                } else {
                    $gamejamId = get_post_meta(get_the_ID(), "gamejamreviewGameJamId", true);
                    $gamejamArchiveLink = "/gamejam/$gamejamId/?tab=archive";
                    $mentors = get_posts(array(
                        "numberposts" => 1,
                        "post_type" => "gamejammentor",
                        "meta_query" => array(
                            "relation" => "AND",
                            array(
                                "key" => "gamejammentorGameJamId",
                                "value" => $gamejamId,
                                "compare" => "="
                            ),
                            array(
                                "key" => "gamejammentorId",
                                "value" => get_current_user_id(),
                                "compare" => "="
                            )
                        )
                    ));

                    $comments = get_posts(array(
                        "post_type" => "comment",
                        "numberposts" => -1,
                        "meta_query" => array(
                            "relation" => "AND",
                            array(
                                "key" => "commentPostId",
                                "value" => get_the_ID(),
                                "compare" => "="
                            ),
                            array(
                                "key" => "commentAuthorId",
                                "value" => get_current_user_id(),
                                "compare" => "="
                            )
                        )
                    ));

                    if (sizeof($mentors) == 1 && sizeof($comments) == 0) {
                        $comment->render_form("Comment added! Thank you for your submission. 
              <a href='$gamejamArchiveLink'>Click here to respond to more jammers.</a>");
                    }
                }

                $comments = get_posts(array(
                    "post_type" => "comment",
                    "numberposts" => -1,
                    "meta_query" => array(
                        "relation" => "AND",
                        array(
                            "key" => "commentPostId",
                            "value" => get_the_ID(),
                            "compare" => "="
                        )
                    )
                ));

                foreach ($comments as $comment) {
                    $commentAuthorId = get_post_meta($comment->ID, "commentAuthorId", true);
                    $commentContent = get_post_meta($comment->ID, "commentContent", true);
                    $mentorname = get_userdata($commentAuthorId)->first_name;
                    $mentorbio = get_user_meta($commentAuthorId, "description", true);
                    $gamejamId = get_post_meta(get_the_ID(), "gamejamreviewGameJamId", true);
                    $mentors = get_posts(array(
                        "numberposts" => 1,
                        "post_type" => "gamejammentor",
                        "meta_query" => array(
                            "relation" => "AND",
                            array(
                                "key" => "gamejammentorGameJamId",
                                "value" => $gamejamId,
                                "compare" => "="
                            ),
                            array(
                                "key" => "gamejammentorId",
                                "value" => $commentAuthorId,
                                "compare" => "="
                            )
                        )
                    ));

                    $mentororg = get_post_meta($mentors[0]->ID, "gamejammentorMentorCompany", true);
                    if (empty($mentororg)) {
                        $mentororg = "";
                    }
                    ?>

                    <div class="row p-3">   
                        <div class="col-12">
                            <strong><?= $mentorname; ?> (<?= $mentororg; ?>)</strong>
                            <div class="small">
                                <?= nl2br($mentorbio); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row p-3">
                        <div class="col-12">
                            <strong>Mentor Response: </strong>
                            <?php
                            if ($commentAuthorId == get_current_user_id()) {
                                $gamejamArchiveLink = "/gamejam/$gamejamId/?tab=archive";
                                $successMessage = "Comment added! Thank you for your submission. <a href='$gamejamArchiveLink'>Click here to respond to more jammers.</a>";
                                ?>
                                <div class="col-12"><div id="commentAjaxResponseMessage" style="display:none;"></div></div>
                                <form id="pixelpadCommentForm">
                                    <div class="form-group">
                                        <textarea class="form-control" name="pixelpadCommentContent" rows="10"><?= $commentContent; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="successMessage" value="<?= $successMessage; ?>">
                                        <input type="hidden" name="commentId" value="<?= $comment->ID; ?>">
                                        <input type="hidden" name="action" value="edit_pixelpad_comment">
                                        <input type="hidden" name="commentPostId" value=<?= get_the_ID(); ?>>
                                        <?php wp_nonce_field("edit_pixelpad_comment", "edit_pixelpad_comment_nonce"); ?>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </form>
                            <?php } else {
                                ?>
                                <pre class="alert alert-primary font-pixelpad text-wrap"><?= nl2br($commentContent); ?></pre>
                            <?php }
                            ?>
                        </div>
                    </div>

                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
  



