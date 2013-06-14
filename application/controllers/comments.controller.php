<?php

class CommentsController extends Controller
{
  
  public function home() {
    $this->set('title', 'Comments :: Home');
  }

  /**
   * View
   */
  public function view() {
    Errors::debugLogger(10, __METHOD__.'@'.__LINE__);
    $getReq = func_get_args();
    $this->set('title', 'Comments :: View');
    if (empty($getReq[0])) {
      $this->set('resultsMsg', 'Missing comment ID...');
      $this->set('comment', NULL);
      $this->set('commentBody', NULL);
      return;
    }
    $commentID = $getReq[0];
    
    // Get comment info
    $comment = $this->Comment->getCommentInfo($commentID);
    if (empty($comment)
        || $comment['commentActive'] == 0) {
      $this->set('resultsMsg', 'Comment not found...');
      $this->set('comment', NULL);
      $this->set('commentBody', NULL);
      return;
    }

    // Get comment body
    $commentBody = $this->Comment->getBodyContents($commentID, 2);

    // Template data
    $this->set('comment', $comment);
    $this->set('commentBody', $commentBody);
  }
  
  /**
   * View All
   */
  public function viewall() {
    $this->set('title', 'Comments :: View All');
    $commentIDs = $this->Comment->getCommentIDs('commentActive=1');
    $comments = array();

    // Get all comment info
    foreach ($commentIDs as $a) {
      $comment = $this->Comment->getCommentInfo($a['commentID']);
      $comments[] = $comment;
    }
    if (empty($comments)) {
      $this->set('resultsMsg', 'No comments yet!');
      $this->set('comments', $comments);
      $this->set('commentBodies', NULL);
      return;
    }

    // Get all comment bodies (active only)
    foreach ($commentIDs as $a) {
      $commentBody = $this->Comment->getBodyContents($a['commentID'], 2, NULL, 1);
      $commentBodies[] = $commentBody;
    }

    // Template data
    $this->set('comments', $comments);
    $this->set('commentBodies', $commentBodies);
  }
  
  /**
   * Write
   */
  public function write() {
    
    // Get step or assume 1st step
    empty($_REQUEST['step']) ? $step = 1 : $step = $_REQUEST['step'];
    $this->set('step', $step);
    $this->set('title', 'Comments :: Write');

    $getReq = func_get_args();
    // Selected Article
    if (!empty($getReq[0])) {
      $articleID = $getReq[0];
      $this->set('articleID', $articleID);
      $parentItemID = $articleID;
      $parentItemTypeID = 1; // Article=1, Comment=2
      
      // Selected Comment
      if (!empty($getReq[1])) {
        $parentCommentID = $getReq[1];
        $this->set('parentCommentID', $parentCommentID);
        $parentItemID = $parentCommentID;
        $parentItemTypeID = 2; // Article=1, Comment=2
      }
      
      $this->set('parentItemID', $parentItemID);
    }
      
    // Step 2: Save comment
    if ($step == 2) {

      // Data array to be passed to sql
      $data = array();
      
      // @todo Not sure where to put user ID.. 1=anon
      $data['userID'] = 1;
      
      // Parent Item ID
      $data['parentItemID'] = $parentItemID;
      
      // Parent Item Type ID
      $data['parentItemTypeID'] = $parentItemTypeID;
      
      // Post time
      $data['commentDatePublished'] = Utility::getDateTimeUTC();

      // Gets input data from post, must begin with "inp_"
      foreach ($_POST as $k=>$v) {
        if (!preg_match('/^inp_(.*)/', $k, $m)) {
          continue;
        }
        $this->set($k, $v);
        // Comment id (new or existing)
        if ($k == 'inp_commentID') {
          $inp_commentID = $v;
        }
        // Comment info and body are in separate tables
        if ($k == 'inp_commentBody') {
          $inp_commentBody = $v;
          continue;
        }
        // Comment info col/data
        $data[$m[1]] = $v;
      }

      // Save comment info, getting ID
      $commentID = $this->Comment->saveCommentInfo($data);
      if ($inp_commentID != 'DEFAULT') {
        // Updated (existing ID)
        $commentID = $inp_commentID;
      }
      $this->set('commentID', $commentID);
      
      // Save comment body
      $bodyType = 2; // Comment=2
      $bodyContentID = $this->Comment->saveBodyContents($commentID, $bodyType, $inp_commentBody, $data['userID']);
      $this->set('bodyContentID', $bodyContentID);

      // Set previous bodies to inactive
      $this->Comment->setBodyContentActive($parentItemID, $bodyType, $bodyContentID);
    }
  }
  
  /**
   * Edit
   */
  public function edit() {
    
    // Get step or assume 1st step
    empty($_REQUEST['step']) ? $step = 1 : $step = $_REQUEST['step'];
    $this->set('step', $step);

    // Step 2: Save comment
    if ($step == 2) {
      self::write();
    }
    
    // Step 1 & after Step 2: Load comment
    #if ($step == 1) {
      $getReq = func_get_args();
      if (!empty($getReq[0])) {
        $commentID = $getReq[0];
        $this->set('commentID', $commentID);

        // Get comment info
        $comment = $this->Comment->getCommentInfo($commentID);
        if (empty($comment)
            || $comment['commentActive'] == 0) {
          $this->set('resultsMsg', 'Comment not found...');
          $this->set('comment', NULL);
          $this->set('commentBody', NULL);
          return;
        }

        // Get comment body
        $commentBody = $this->Comment->getBodyContents($commentID, 2);
        
        // Template variables
        foreach ($comment as $a=>$b) {
          $this->set($a, $b);
        }
        foreach ($commentBody as $body) {
          if ($body['bodyContentActive'] == 1) {
            $this->set('commentBody', $body);
            break;
          }
        }
      }
    #}
    
    // Title gets set in step 2 so this stays at end
    $this->set('title', 'Comments :: Edit');
  }
}