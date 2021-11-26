<?php
  /* templates are automatically pulled from the theme directory as long as it has the single-{custom posttype}.php format */
?>

<?php
  /* we can move these functions into the includes file single-gamejam-parts.php */

  add_action("wp_head", function(){
    echo '<link href="https://fonts.cdnfonts.com/css/04b30" rel="stylesheet">';
  });
  
  add_action("wp_head", function(){wp_enqueue_script(
      'gamejamteamjs',
      PIXELPAD_PLUGIN_URL . 'customposts/includes/gamejamteam.js',
      array('jquery'),
      '1.0.1',
      true
    );}
  );
  
  add_action("wp_head", function(){wp_enqueue_script(
      'countdown',
      PIXELPAD_PLUGIN_URL . 'customposts/includes/countdown.js',
      array('jquery'),
      '1.0.1',
      true
    );}
  );
  
  add_action("wp_head", function(){wp_enqueue_script(
      'gamejamjs',
      PIXELPAD_PLUGIN_URL . 'customposts/includes/gamejam.js',
      array('jquery'),
      '1.0.1',
      true
    );}
  );
  
  add_action("wp_head", function(){  wp_enqueue_style( 
      'gamejamcss', 
      PIXELPAD_PLUGIN_URL . 'customposts/includes/gamejam.css',
      array(), 
      "1.0.0",
      "all"
    );}
  );

  function renderRegisterNowButton(){ ?>
    <?php $startTime = strtotime(get_post_meta(get_the_ID(), "gamejamDateStart", true)); ?>
    <?php if(true){return;} //future, change if true to if NOW() > startTime; ?>
    <div class="text-center mt-5 mb-4">
        <a href="./?tab=team">
          <button type="button" class="btn btn-success font-04b" style="font-size:1.5rem;">
            REGISTER NOW!
          </button>
        </a>
    </div>
  <?php
  }
  
  function renderClock(){ ?>
    <?php $startTime = strtotime(get_post_meta(get_the_ID(), "gamejamDateStart", true)); ?>
    <?php if(true){return;} //future, change if true to if NOW() > startTime; ?>
    <div class="text-center border bg-light p-4 position-relative" style="z-index:10; font-size:2rem;">
      <div class="bg-splash"></div>
      <div class="my-4 text-white small">Game Jam Starts in: </div>
      <span class="text-light-pink font-weight-bold" id="gamejamCountDown"></span>
      <div class="text-light-pink small">(Extended Deadline)</div>
      <?php renderRegisterNowButton(); ?>
    </div>
  <?php
  }
  
  function renderGameJamSubmissionsPage(){
    if (!is_user_logged_in()){ ?>
      <div class="row my-5">
        <h1 class="alert alert-danger">You must be logged in to submit your project.</h1>
      </div>
      <?php 
      return;
    } 

    $teams = get_posts(array("post_type"=>"gamejamteam", "numberposts"=>1, "author"=> get_current_user_id() ) );
    if (sizeof($teams)==0){ ?>
      <div class="row my-5">
        <h5 class="alert alert-danger">You must register a team before submitting a game. 
        If you have already registered, please login using the account you registered with.</h5>
      </div>
      <?php 
      return;
    }
    $apps = get_posts(array(
      "post_type" => "pp-project",
      "numberposts" => -1,
      "author" => get_current_user_id()
    ));

    $submitText = "Submit Game";
    $teamMeta = get_post_meta($teams[0]->ID);

    ?>
    
    <form id="gamejamgame" class="my-5">
      <div id="gamejam-game-submit-message" class="my-5" style="display:none;"></div>
        <div class="form-group">
          <select class="form-control" name="gameSubmitted">
            <option>Choose App</option>
            <?php foreach($apps as $p): ?>
              <?php $sel=$teamMeta["gamejamteamSubmission"][0]==$p->ID?"selected":""; ?>
              <option value="<?=$p->ID; ?>" <?=$sel?>>
                <?= get_the_title($p->ID); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php 
          $datetimeSubmitted = $teamMeta["gamejamteamSubmissionDateTime"][0]; 
          if (!empty($datetimeSubmitted)){
            echo "<small class='text-success'>".get_the_title($teamMeta["gamejamteamSubmission"][0])." was submitted at ". $datetimeSubmitted . " (Pacific Time)</small>";
          }
        
        ?>
        
        <div class="my-3">
          Before you submit your game, ensure your game has <strong>thumbnail, game title, and game description.</strong>
          In the description of your game, please ensure you have the following information:
          <ul class="font-italic">
            <li>How does this game represent the Theme?</li>
            <li>How do we play your game?</li>
            <li>What challenges did you run into?</li>
            <li>What are the highlights for your game?</li>
            <li>If you had more time, how would you improve the game</li>
          </ul>
          <span style="color:#e45f83;">
          IMPORTANT: This information will be presented to the Public (via social media), 
          Mentors, and the PixelPAD team when they are selecting award recipients!
          To edit the thumbnail, game title, and game description, <a href="/apps">edit your app here.</a>
          </span>
        </div>
        
        <div class="my-3">
          <div class="form-group">
            <button type="submit" class="btn btn-success font-weight-bold"><?=$submitText;?></button>
            <input type="hidden" name="action" value="submit_gamejam_game">
            <?php wp_nonce_field("submit_gamejam_game", "submit_gamejam_game_nonce"); ?>
          </div>
        </div>
        
    </form>
    
  <?php
  }

  function renderGameJamMentorComment($reviewId){
    $comments = get_posts(array(
      "post_type" => "comment",
      "numberposts" => -1,
      "meta_query" => array(
        "relation" => "AND",
        array(
          "key" => "commentPostId",
          "value" => $reviewId,
          "compare" => "="
        ),
      )
    ));
    
    ?>
    <div class="row my-3">
      <div class="col-12">
        <a href="<?=get_permalink($reviewId);?>">You have <?=sizeof($comments)?> <?=sizeof($comments)==1?"response.":"responses.";?></a>
      </div>
    </div>
      
    <?php
  }

  function renderGameJamReviewPage(){
    if (!is_user_logged_in()){ ?>
      <div class="row my-5">
        <h5 class="alert alert-danger">You must be logged in to submit your project for a code review.</h5>
      </div>
      <?php 
      return;
    }

    $teams = get_posts(array("post_type"=>"gamejamteam", "numberposts"=>1, "author"=> get_current_user_id() ) );
    if (sizeof($teams)==0){ ?>
      <div class="row my-5">
        <h5 class="alert alert-danger">You must register a team before submitting your project for a code review. 
        If you have already registered, please login using the account you registered with.</h5>
      </div>
      <?php 
      return;
    }
    
    $apps = get_posts(array(
      "post_type" => "pp-project",
      "numberposts" => -1,
      "author" => get_current_user_id()
    ));

    $submitText = "Request Feedback";
    $reviews = get_posts(array("post_type"=>"gamejamreview", "numberposts"=>-1, "author"=> get_current_user_id(),"orderby"=>"date" ) ); ?>
    
    <div class="my-5 bg-light p-2">
      You can use this page to request feedback for your game. 
      <br><br>
      Please note that it may take some time for a mentor to get back to you, especially as we approach the deadline, so submit early!
      <br><br>
      You may submit one request a day.
    </div>

    <?php
    foreach($reviews as $review){
      $reviewMeta = get_post_meta($review->ID); ?>
      <hr>
      <div class="my-5">
        <div class="row my-3">
          <div class="col-12">
            <strong>Game Submitted </strong>
          </div>

          <div class="col-12">
            <a href="<?=get_permalink($reviewMeta["gamejamreviewGameId"][0]) . "?edit=1";?>">
              <?=get_the_title($reviewMeta["gamejamreviewGameId"][0]); ?>
            </a>
          </div>
          <div class="col-12">
            <small class="text-secondary"><em>Submitted on: <?=get_the_date("M d Y",$review->ID); ?></em></small>
          </div>
        </div>
      
        <div class="row my-3">
          <div class="col-12">
            <strong>Comment for Mentor</strong>
          </div>
          <div class="col-12">
            <?php
            $com = $reviewMeta["gamejamreviewTeamComment"][0];
            echo empty($com)?"No comment provided.":$com;
            ?>
          </div>
        </div>
        <?php renderGameJamMentorComment($review->ID); ?>
      </div>
      <?php
    } ?>
    
    <form id="gamejamreview" class="my-5">

      <hr>
      
      <div class="my-3">
        <div class="alert alert-primary">Code Review Requests are now closed.</div>
      </div>
      <?php 
      /*
      $longestTime = 0;
      foreach($reviews as $review){
        $reviewTime = strtotime(get_the_date("Y-m-d",$review->ID));
        if ($reviewTime > $longestTime){
          $longestTime = $reviewTime; 
          $latestReview = $review->ID;
        }
      }

      if (get_the_date("Y-m-d", $latestReview) != date("Y-m-d")): ?>
        <div id="gamejam-review-message" class="my-5" style="display:none;"></div>
        <div class="form-group">
          <label for="gameSubmitted">Submit Game for Code Review</label>
          <select class="form-control" name="gameSubmitted" id="gameSubmitted">
            <option>Choose App</option>
            <?php foreach($apps as $p): ?>
              <?php $sel=in_array($p->ID,$reviewGameIds)?"selected":""; ?>
              <option value="<?=$p->ID; ?>" <?=$sel?>>
                <?= get_the_title($p->ID); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="team-comment">Comment for Mentor</label>
          <textarea class="form-control" placeholder="Requesting general feedback" 
          name="teamComment" id="team-comment" rows="5"></textarea>
          <small class="text-primary">
            <strong>You may ask any question here. If you are stuck, here are examples of questions you can ask.</strong>
              <ul>
                <li>Requesting General Feedback</li>
                <li>What do you think of the Game Play, how would you suggest I improve?</li>
                <li>Requesting Performance Feedback, the game is running a bit slow, any suggestions on how I could improve performance?</li>
                <li>Requesting Art / Theme Feedback</li>
                <li>Requesting Sound Design Feedback</li>
                <li>Requesting Level Design Feedback</li>
              </ul>
          </small>
        </div>
        
        <div class="my-3">
          <div class="form-group">
            <button type="submit" class="btn btn-success font-weight-bold"><?=$submitText;?></button>
            <input type="hidden" name="action" value="submit_gamejam_review">
            <input type="hidden" name="gamejamid" value=<?=get_the_ID();?>>
            <?php wp_nonce_field("submit_gamejam_review", "submit_gamejam_review_nonce"); ?>
          </div>
        </div>
        <?php 
      else: ?>
        <div class="my-3">
          <div class="alert alert-warning">You have already submitted your project for review today.</div>
        </div>
      <?php 
      endif; ?>
      */ 
      ?>
    </form>
    <?php
  }

  function renderMentorRegistration(){ ?>
    <div class="col-12">
      All information provided here will be public. Only provide information here you are willing to share with teams and games 
      you review. First name and company is required.
    </div>
    <form id="gamejamMentorRegistration" class="my-5">
      <div id="gamejam-mentor-register-message" style="display:none;"></div>
      
      <?php 
      if (!is_user_logged_in()){ ?>
        <div class="bg-light my-3 p-3">
          <div class="form-group">
            <label for="mentorfname">Your First Name</label>
            <input name="mentorfname" type="text" class="form-control" id="mentorfname" required>
          </div>
          <div class="form-group">
            <label for="mentorlname">Your Last Name</label>
            <input name="mentorlname" type="text" class="form-control" id="mentorlname">
          </div>
          <div class="form-group">
            <label for="mentoremail">
              <span class="mr-2">Your Email</span>
              <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="This will create a new account. Please login if you already have an account."></i>
            </label>
            <input name="mentoremail" type="email" class="form-control" id="mentoremail" required>
          </div>
          <div class="form-group">
            <label for="mentorpassword">Create Account Password</label>
            <input name="mentorpassword" type="password" class="form-control" id="mentorpassword" required>
          </div>
          
          <div class="form-group">
            <label for="mentorconpassword">Confirm Password</label>
            <input name="mentorconpassword" type="password" class="form-control" id="mentorconpassword" required>
          </div>
          
          <small class="text-secondary"><a href="#" data-toggle="modal" data-target="#login_modal">Already have an account? Click here to login.</a></small>

        </div>
        <hr>
      <?php 
      }

      $mentorCompanyName = "";
      $registrationText = "Register";
      if (is_user_logged_in()){
        $mentorBio = get_user_meta(get_current_user_id(), "description", true);
        $mentors = get_posts(array(
          "numberposts" => 1,
          "post_type" => "gamejammentor",
          "author" => get_current_user_id(),
          "meta_query" => array(
            "key" => "gamejammentorGameJamId",
            "value" => get_the_ID(),
            "compare" => "="
          )
        ));
        
        if (sizeof($mentors) > 0){
          $mentorCompanyName = get_post_meta($mentors[0]->ID, "gamejammentorMentorCompany", true);
          $registrationText = "Save";
        }
      }

      ?>
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <label for="mentorCompanyName">Organization Name</label>
          <input name="mentorCompanyName" type="text" class="form-control" id="mentorCompanyName" 
          value="<?=$mentorCompanyName;?>" maxlength="100" required>
        </div>
      </div>
      
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <label for="mentorBio">Your Bio</label>
          <textarea name="mentorBio" class="form-control" id="mentorBio" rows="10"><?=$mentorBio;?></textarea>
        </div>
      </div>
      
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <button type="submit" id="gamejam-mentor-submit" class="btn btn-success"><?=$registrationText;?></button>
          <input type="hidden" name="action" value="save_gamejam_mentor">
          <input hidden type="text" name="gamejammentorGameJamId" value="<?=get_the_ID();?>">
          <?php wp_nonce_field("save_gamejam_mentor", "save_gamejam_mentor_nonce"); ?>
        </div>
      </div>
    </form>
    <?php
  }
  
  function renderGameJamMentorsArchive(){
    //page for mentors to see all the games that haven't been reviewed yet.
    if (!is_user_logged_in()){ ?>
      <div class="alert alert-danger">
        You must be logged in to see archive.
      </div>
    <?php
      return;
    }

    $mentors = get_posts(array(
      "post_type" => "gamejammentor",
      "numberposts" => -1,
      "meta_query" => array(
        array(
          "key" => "gamejammentorGameJamId",
          "value" => get_the_ID(),
          "compare" => "="
        ),
      )
    ));
    
    $mentorArray = array();
    foreach($mentors as $mentor){
      array_push($mentorArray, get_post_meta($mentor->ID, "gamejammentorId", true));
    }
    if (!in_array(get_current_user_id(), $mentorArray)){ ?>
      <div class="alert alert-danger">
        You are not registered as a mentor, you cannot view this page.
      </div>
      <?php
      return;
    }
    
    $reviews = get_posts(array(
      "post_type" => "gamejamreview",
      "numberposts" => -1,
      "orderby" => "date",
      "order" => "DESC",
      "meta_query" => array(
        "relation" => "AND",
        array(
          "key" => "gamejamreviewGameJamId",
          "value" => get_the_ID(),
          "compare" => "="
        ),
      )
    ));
    
    $reviewCount = 0;
    foreach($reviews as $review){
      $comments = get_posts(array(
        "post_type" => "comment",
        "numberposts" => -1,
        "meta_query" => array(
          "relation" => "AND",
          array(
            "key" => "commentPostId",
            "value" => $review->ID,
            "compare" => "="
          ),
        )
      ));
      
      $myComment = get_posts(array(
        "post_type" => "comment",
        "numberposts" => -1,
        "meta_query" => array(
          "relation" => "AND",
          array(
            "key" => "commentPostId",
            "value" => $review->ID,
            "compare" => "="
          ),
          array(
            "key" => "commentAuthorId",
            "value" => get_current_user_id(),
            "compare" => "="
          )
        )
      ));
      
      if (sizeof($comments) >= 3 && empty($myComment) ){
        continue;
      }

      $reviewMeta = get_post_meta($review->ID);
        $reviewCount +=1; ?>
        <hr />
        <div class="row my-3">
          <div class="col-12">
            <strong>Team Name</strong>
          </div>
          <div class="col-12">
            <?=get_the_title($reviewMeta["gamejamreviewTeamId"][0]);?>
          </div>
        </div>
        
        <div class="row my-3">
          <div class="col-12">
            <strong>Game Submitted </strong>
          </div>
          <div class="col-12">
            <a href="<?=get_permalink($reviewMeta["gamejamreviewGameId"][0]) . "?edit=1";?>">
              <?=get_the_title($reviewMeta["gamejamreviewGameId"][0]); ?>
            </a>
          </div>
          <div class="col-12">
            <small class="text-secondary"><em>Submitted on: <?=get_the_date("M d Y",$review->ID); ?></em></small>
          </div>
        </div>
      
        <div class="row my-3">
          <div class="col-12">
            <strong>Comment for Mentor</strong>
          </div>
          <div class="col-12">
            <?php
            $com = $reviewMeta["gamejamreviewTeamComment"][0];
            echo empty($com)?"No comment provided.":$com;
            ?>
          </div>
        </div>
        
        <?php 
        

        
        if (!empty($myComment)){
          $respondText = "Edit Response";
          $respondClass = "btn-secondary";
          ?>
          <div class="row my-3">
            <div class="col-12">
              <strong>Your Response</strong>
            </div>
            <div class="col-12">
              <pre class=" alert alert-primary"><?=get_post_meta($myComment[0]->ID, "commentContent", true);?></pre>
            </div>
          </div>
          <?php
        } else {
          $respondText = "Respond";
          $respondClass = "btn-success";
        }
        ?>
          <div class="row my-5">
            <div class="col-12">
              <a class="btn <?=$respondClass;?>" href=<?=get_permalink($review->ID);?>><?=$respondText;?></a>
            </div>
          </div>
        <?php 
      
    }
    
    if ($reviewCount === 0){ ?>
      <div class="row my-3">
        <div class="col-12">
          <div class="alert alert-warning">
            All Review Requests have been responded to at this time. Please check back later!
          </div>
        </div>
      </div>
    <?php
    }
  }
  
  
  function renderGameJamTeamPage(){ ?>
    <?php 
    //future, change if true to if NOW() > startTime; 
    $registrationClosed = true;
    if($registrationClosed){
      echo "<div class='text-center alert alert-danger'>Registration Closed.</div>"; 
    }
    ?>
    <div class="mt-5">
      Once you register, we'll show up in your inbox to let you (and your parent) know what's coming up next for the Game Jam! 
      Early registrants will receive tips & tutorials before the jam. During the jam, we'll communicate with each team via email, 
      <a href="https://discord.gg/UP3fFWb">Discord<a/>, <a href="https://www.facebook.com/PixelPadio-656095701826065">Facebook</a> and 
      <a href="https://www.instagram.com/pixelpadofficial/">Instagram.</a>
    </div>
    
    <form id="gamejamteam" class="my-5">
      <div id="gamejam-team-submit-message" style="display:none;"></div>
      
      <?php 
      if (!is_user_logged_in()){ ?>
        <div class="bg-light my-3 p-3">
          <div class="form-group">
            <label for="leadfname">Your First Name</label>
            <input name="leadfname" type="text" class="form-control" id="leadfname">
          </div>
          <div class="form-group">
            <label for="leadlname">Your Last Name</label>
            <input name="leadlname" type="text" class="form-control" id="leadlname">
          </div>
          <div class="form-group">
            <label for="leademail">
              <span class="mr-2">Your Email</span>
              <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="This will create a new account. Please login if you already have an account."></i>
            </label>
            <input name="leademail" type="email" class="form-control" id="leademail">
          </div>
          <div class="form-group">
            <label for="leadpassword">Create Account Password</label>
            <input name="leadpassword" type="password" class="form-control" id="leadpassword">
          </div>
          
          <div class="form-group">
            <label for="leadconpassword">Confirm Password</label>
            <input name="leadconpassword" type="password" class="form-control" id="leadconpassword">
          </div>
          
          <small class="text-secondary"><a href="#" data-toggle="modal" data-target="#login_modal">Already have an account? Click here to login.</a></small>

        </div>
        <hr>
      <?php 
      }
      
      if (is_user_logged_in()){
        $teams = get_posts(array(
          "post_type" => "gamejamteam",
          "numberposts" => 1,
          "author" => get_current_user_id()
        ));

      }
      
      if (sizeof($teams) == 0){
        $teamname = "";
        $leadage =  "";
        $schoolcode = "";
        $parentname = "";
        $parentemail = "";
        $registrationText = "REGISTER";
      } else {
        $team = $teams[0];
        $teamMeta = get_post_meta($team->ID);
        $teamname = $teamMeta["gamejamteamName"][0];
        $leadage =  $teamMeta["gamejamteamLeadAge"][0];
        $schoolcode = $teamMeta["gamejamteamSchoolCode"][0];
        $schoolname = $teamMeta["gamejamteamSchoolName"][0];
        $parentfname = $teamMeta["gamejamteamParentFName"][0];
        $parentlname = $teamMeta["gamejamteamParentLName"][0];
        $parentemail = $teamMeta["gamejamteamParentEmail"][0];
        $waiveragree = $teamMeta["gamejamteamLeadWaiver"][0];
        $gamejamid = $teamMeta["gamejamteamGameJamId"][0];
        $teammates = array_values(json_decode($teamMeta["gamejamteamTeammates"][0], true));
        $registrationText = "SAVE";
      }

      ?>
      
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <label for="teamname">Team Name</label>
          <input name="teamname" type="text" class="form-control" id="teamname" 
          value="<?=$teamname;?>" maxlength="100" required>
          <small class="text-secondary user-select-none"><span id="generateName" class="hover-pointer text-primary hover-lighten">Generate a Name!</span> (Can change later)</small>
        </div>
      </div>
      
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <label for="leadage">Your Age</label>
          <input name="leadage" type="text" class="form-control" id="leadage"
          value="<?=$leadage;?>" maxlength="2" required>
          <small class="text-secondary">Student must be 13 - 18 years old to attend.</small>
        </div>
        
        <div class="form-group">
          <label for="schoolcode">Your School's Postal Code</label>
          <input name="schoolcode" type="text" class="form-control schoolcode" id="schoolcode" 
          value="<?=$schoolcode;?>" maxlength="7" required>
        </div>
        
        <div class="form-group">
          <label for="schoolname">Your School's Name</label>
          <input name="schoolname" type="text" class="form-control schoolname" id="schoolname" 
          value="<?=$schoolname;?>" maxlength="100" required>
        </div>
        
      </div>
      
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <label for="parentfname">Parent/Guardian First Name</label>
          <input name="parentfname" type="text" class="form-control" id="parentfname"
          value="<?=$parentfname;?>" maxlength="100" required>
        </div>
        
        <div class="form-group">
          <label for="parentlname">Parent/Guardian Last Name</label>
          <input name="parentlname" type="text" class="form-control" id="parentlname"
          value="<?=$parentlname;?>" maxlength="100" required>
        </div>
        
        <div class="form-group">
          <label for="parentemail">Parent/Guardian Email</label>
          <input name="parentemail" type="email" class="form-control" id="parentemail"
          value="<?=$parentemail;?>" maxlength="300" required>
        </div>
          <div class="form-group">
            <input type="checkbox" id="gamejam-waiver" name="leadWaiver" class="waiver" value="agree" <?=$waiveragree=="agree"?"checked disabled":"";?> />
            <label for="gamejam-waiver">
              I have read and agreed to the <a href="./?tab=waiver">waiver and limitations of liability set here.</a>
            </label>
          </div>
      </div>
      
      <hr>
      
      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <div id="gamejam-addteammate" data-count="<?=count($teammates)+1;?>" class="btn btn-pink">Add Teammate</div>
        </div>
      </div>
      
      <div id="teammates">
        <?php
        foreach($teammates as $num=>$teammate){ ?>
          <div id="teammate-<?=$num;?>" class="border border-dark rounded p-3 mb-3 teammate-parent">
          <h5>Teammate <?=$num+1;?></h5>
            <?php
            foreach($teammate as $k => $v) {
              switch ($k){
                case "age":
                  $type = "number";
                  $max = "2";
                  $label = "Age";
                  $class = "";
                break;
                case "email":
                  $type = "email";
                  $max = "100";
                  $label = "Email";
                  $class = "";
                break;
                case "parentfname":
                  $type = "text";
                  $max = "100";
                  $label = "Parent/Guardian First Name";
                  $class = "";
                break;
                case "parentlname":
                  $type = "text";
                  $max = "100";
                  $label = "Parent/Guardian Last Name";
                  $class = "";
                break;
                case "parentemail":
                  $type = "email";
                  $max = "300";
                  $label = "Parent/Guardian Email";
                  $class = "";
                break;
                case "schoolcode":
                  $type = "text";
                  $max = "7";
                  $label = "School Postal Code";
                  $class = "schoolcode";
                break;
                case "schoolname":
                  $type = "text";
                  $max = "7";
                  $label = "School Name";
                  $class = "schoolname";
                break;
                case "fname":
                  $type = "text";
                  $max = "100";
                  $label = "First Name";
                  $class = "";
                break;
                case "lname":
                  $type = "text";
                  $max = "100";
                  $label = "Last Name";
                  $class = "";
                break;
                default:
                  $type = "text";
                  $max = "100";
                  $class = "";
              }
            ?>
            
              <?php if ($k=="waiver"): ?>
                  <div class="form-group">
                    <input type="checkbox" name="teammates[<?=$num;?>][<?=$k;?>]" id="<?=$k.$num;?>" value="agree" class="waiver" <?=$v=="agree"?"checked disabled":"";?> />
                    <label for="<?=$k.$num;?>">
                      I have read and agreed to the <a href="./?tab=waiver">waiver and limitations of liability set out here.</a>
                    </label>
                  </div>
              <?php else: ?>
                <div class="bg-light my-3 p-3">
                  <div class="form-group">
                    <label for="<?=$k.$num;?>"><?=$label;?></label>
                    <input class="form-control <?=$class;?>" name="teammates[<?=$num;?>][<?=$k;?>]" id="<?=$k.$num;?>" type="<?=$type;?>" value="<?=$v;?>" maxlength="<?=$max;?>" required /> 
                  </div>
                </div>
              <?php endif; ?>
            <?php
            }
            ?>
            <div class="btn btn-danger remove-teammate">
              Remove
            </div>
          </div>
        <?php
        }
        ?>
      </div>

      <div class="bg-light my-3 p-3">
        <div class="form-group">
          <?php 
          if ($registrationClosed && sizeof($teams) == 0){ // change to if date > gamejam date in future.
             echo "<div class='text-center alert alert-danger'>Registration Closed.</div>"; 
          } else { ?>
            <button type="submit" id="gamejam-team-submit" class="btn btn-success"><?=$registrationText;?></button>
          <?php 
          } ?>
          <input type="hidden" name="action" value="save_gamejam_team">
          <input hidden type="text" name="gamejamteamGameJamId" value="<?=get_the_ID();?>">
          <?php wp_nonce_field("save_gamejam_team", "save_gamejam_team_nonce"); ?>
        </div>
      </div>
    </form>
    
<?php
  }
?>

<?php
function renderSprites(){ ?>

  <div style="position:absolute; top:100px; left: 100px; z-index:1;">
    <img src="https://i.imgur.com/VrxnWJy.png">
  </div>
  <div style="position:absolute; top:500px; right: 0px; z-index:1;">
    <img src="https://i.imgur.com/5eNKnFh.png">
  </div>
  <div style="position:absolute; top:1200px; left: 100px; z-index:1;">
    <img src="https://i.imgur.com/52vRvny.gif">
  </div>
  
<?php
}
?>


<?php 
  get_header(); 
  $meta = get_post_meta(get_the_ID());
  $active = "text-decoration-none font-weight-bold border-success border-bottom text-success d-inline-block w-100 p-2 ";
  $inactive = "text-decoration-none font-weight-bold text-pink d-inline-block w-100 p-2";
?>

<meta property="og:type" content="website">
<meta property="og:description" content="<?=$meta["gamejamDescription"][0];?>">
<meta property="og:title" content="<?=get_the_title();?>">
<meta property="og:image" content="<?=$meta["gamejamThumbnail"][0]; ?>">
<meta property="og:url" content="<?=get_permalink();?>">

<style>.gamejam img{width:100%;}</style>
<div class="container-fluid">
  <?php 
  global $region;
  $region->render_navbar();
  ?>
  <div class="row">
    <?php render_sidebar( true ); ?>
    <div class="col-12 contentParent gamejam">
      <?php renderSprites(); ?>
      <div class="container my-5 position-relative" style="z-index:1000;">
        <div class="row my-5">
          <div class="col-12 col-md-3 col-lg-2">
            <img src="<?=$meta["gamejamThumbnail"][0];?>" class="w-100">
          </div>
          <div class="col-12 col-md-9 col-lg-10">
            <h1 class="d-none d-sm-block font-04b my-4" style="color:#520E76"><?=get_the_title();?></h1>
            <h1 class="d-block d-sm-none text-center font-04b my-4" style="color:#520E76"><?=get_the_title();?></h1>
          </div>
        </div>
        <div class="row text-center">
          <?php $tabclass = "col-xl-2 col-lg-2 col-md-3 col-sm-4 col-12";
          $tab = "";
          if (isset($_GET["tab"])){
            $tab = $_GET["tab"];
          }
          ?>
          <div class="<?=$tabclass;?>"><a class="<?=$tab=="overview" || empty($tab)?$active:$inactive;?>" href="<?=get_permalink();?>?tab=overview">OVERVIEW</a></div>
          <div class="<?=$tabclass;?>"><a class="<?=$tab=="team"?$active:$inactive;?>" href="<?=get_permalink();?>?tab=team">MY TEAM</a></div>
          <div class="<?=$tabclass;?>"><a class="<?=$tab=="faq"?$active:$inactive;?>" href="<?=get_permalink();?>?tab=faq">FAQ</a></div>
          <div class="<?=$tabclass;?>">
            <a class="<?=$tab=="review"?$active:$inactive;?>" href="<?=get_permalink();?>?tab=review">CODE REVIEW</a>
          </div>
          <div class="<?=$tabclass;?>">
            <a disabled class="<?=$tab=="submit"?$active:$inactive;?>" href="<?=get_permalink();?>?tab=submit">SUBMIT</a>
            <!--<span class="font-weight-bold d-inline-block w-100 p-2" style="color:#e4ccd3;" title="Open after jam begins">SUBMIT</span>-->
          </div>
          <div class="<?=$tabclass;?>"></div>
        </div>

        <div class="row position-relative" id="gamejam-parent">
          <div class="col mt-4 mb-5">
            <?php
            switch($tab){
              case "team":
                renderGameJamTeamPage();
              break;
              case "submit":
                renderGameJamSubmissionsPage();
              break;
              case "faq":
                echo $meta["gamejamFAQ"][0];
              break;
              case "review":
                renderGameJamReviewPage();
              break;
              case "mentor":
                renderMentorRegistration();
              break;
              case "archive":
                renderGameJamMentorsArchive();
              break;
              case "waiver":
                echo $meta["gamejamWaiver"][0];
              break;    
              default:
                renderClock();
                echo $meta["gamejamOverview"][0];
                renderRegisterNowButton();
              break;
            }
            ?>
          </div>
        </div>
        
        <div class="text-center">
          <h2>Supported By</h2>
          <img src="https://s3.us-west-1.amazonaws.com/media.pixelpad.io/__PIXELPAD_ASSET__.1.209495.firstcanada.png" style="width:286px;";>
        </div>
        
      </div>
    </div>
  </div>
</div>


<?php get_footer(); ?>







