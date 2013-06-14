<?php

class ArticlesController extends Controller
{
  
  public function home() {
    $this->set('title', 'Articles :: Home');
  }

  /**
   * View
   */
  public function view() {
    Errors::debugLogger(10, __METHOD__.'@'.__LINE__);
    $getReq = func_get_args();
    $this->set('title', 'Articles :: View');
    if (empty($getReq[0])) {
      $this->set('resultsMsg', 'Missing article ID...');
      $this->set('article', NULL);
      $this->set('articleBody', NULL);
      return;
    }
    $articleID = $getReq[0];
    
    // Get article info
    $article = $this->Article->getArticleInfo($articleID);
    if (empty($article)
        || $article['articleActive'] == 0) {
      $this->set('resultsMsg', 'Article not found...');
      $this->set('article', NULL);
      $this->set('articleBody', NULL);
      return;
    }

    // Get article body
    $articleBody = $this->Article->getBodyContents($articleID, 1);
    
    // Get article comments
    $articleComments = $this->Article->getArticleComments($articleID);
    $articleCommentsList = array();
    
    $comment = new Comment();
    
    // Check each comment for children, construct ordered list with levels
    for ($ac=0; $ac<count($articleComments); $ac++) {
      $tid = $articleComments[$ac]['commentID'];
      
      $thisComment = $comment->getBodyContents($tid, 2, NULL, 1);
      if (!empty($thisComment[0]['bodyContentText'])) {
          $body = $thisComment[0]['bodyContentText'];
        } else {
          $body = "Empty......";
        }
      $articleCommentsList[] = array('level' => 1,
        'commentID' => $tid,
        'commentBodyText' => $body);
      
      $level = 1;
      while (count($as = $this->Article->getArticleComments($articleID, $tid)) > 0) {
        $level++;
        $tid = $as[0]['commentID'];
        
        $thisComment = $comment->getBodyContents($tid, 2, NULL, 1);
        if (!empty($thisComment[0]['bodyContentText'])) {
          $body = $thisComment[0]['bodyContentText'];
        } else {
          $body = "Empty......";
        }
        $articleCommentsList[] = array('level' => $level,
          'commentID' => $tid,
          'commentBodyText' => $body);
      }
      
    }

    // Template data
    $this->set('article', $article);
    $this->set('articleBody', $articleBody);
    $this->set('articleComments', $articleCommentsList);
  }
  
  /**
   * View All
   */
  public function viewall() {
    $this->set('title', 'Articles :: View All');
    $articleIDs = $this->Article->getArticleIDs('articleActive=1');
    $articles = array();

    // Get all article info
    foreach ($articleIDs as $a) {
      $article = $this->Article->getArticleInfo($a['articleID']);
      $articles[] = $article;
    }
    if (empty($articles)) {
      $this->set('resultsMsg', 'No articles yet!');
      $this->set('articles', $articles);
      $this->set('articleBodies', NULL);
      return;
    }

    // Get all article bodies (active only)
    foreach ($articleIDs as $a) {
      $articleBody = $this->Article->getBodyContents($a['articleID'], 1, NULL, 1);
      $articleBodies[] = $articleBody;
    }

    // Template data
    $this->set('articles', $articles);
    $this->set('articleBodies', $articleBodies);
  }
  
  /**
   * Write
   */
  public function write() {
    
    // Get step or assume 1st step
    empty($_REQUEST['step']) ? $step = 1 : $step = $_REQUEST['step'];
    $this->set('step', $step);
    $this->set('title', 'Articles :: Write');

    // Step 2: Save article
    if ($step == 2) {

      // Data array to be passed to sql
      $data = array();
      
      // @todo Not sure where to put user ID.. 1=anon
      $data['userID'] = 1;
      
      // Post time
      $data['articleDatePublished'] = Utility::getDateTimeUTC();

      // Gets input data from post, must begin with "inp_"
      foreach ($_POST as $k=>$v) {
        if (!preg_match('/^inp_(.*)/', $k, $m)) {
          continue;
        }
        $this->set($k, $v);
        // Article id (new or existing)
        if ($k == 'inp_articleID') {
          $inp_articleID = $v;
        }
        // Article info and body are in separate tables
        if ($k == 'inp_articleBody') {
          $inp_articleBody = $v;
          continue;
        }
        // Article info col/data
        $data[$m[1]] = $v;
      }

      // Save article info, getting ID
      $articleID = $this->Article->saveArticleInfo($data);
      if ($inp_articleID != 'DEFAULT') {
        // Updated (existing ID)
        $articleID = $inp_articleID;
      }
      $this->set('articleID', $articleID);
      
      // Save article body
      $bodyType = 1; // Article=1
      $bodyContentID = $this->Article->saveBodyContents($articleID, $bodyType, $inp_articleBody, $data['userID']);
      $this->set('bodyContentID', $bodyContentID);
              
      // Set previous bodies to inactive
      $this->Article->setBodyContentActive($articleID, $bodyType, $bodyContentID);
    }
  }
  
  /**
   * Edit
   */
  public function edit() {
    
    // Get step or assume 1st step
    empty($_REQUEST['step']) ? $step = 1 : $step = $_REQUEST['step'];
    $this->set('step', $step);

    // Step 2: Save article
    if ($step == 2) {
      self::write();
    }
    
    // Step 1 & after Step 2: Load article
    #if ($step == 1) {
      $getReq = func_get_args();
      if (!empty($getReq[0])) {
        $articleID = $getReq[0];
        $this->set('articleID', $articleID);

        // Get article info
        $article = $this->Article->getArticleInfo($articleID);
        if (empty($article)
            || $article['articleActive'] == 0) {
          $this->set('resultsMsg', 'Article not found...');
          $this->set('article', NULL);
          $this->set('articleBody', NULL);
          return;
        }

        // Get article body
        $articleBody = $this->Article->getBodyContents($articleID, 1);
        
        // Template variables
        foreach ($article as $a=>$b) {
          $this->set($a, $b);
        }
        foreach ($articleBody as $body) {
          if ($body['bodyContentActive'] == 1) {
            $this->set('articleBody', $body);
            break;
          }
        }
      }
    #}
    
    // Title gets set in step 2 so this stays at end
    $this->set('title', 'Articles :: Edit');
  }
}