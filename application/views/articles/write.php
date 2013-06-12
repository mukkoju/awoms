<!-- Results -->
<div id='results'>
  <?php
  if (isset($articleID)
    && $articleID != 'DEFAULT') {
    echo "
      Article (#".$articleID.") updated! (Copy #".$bodyContentID.") <a href='".BRANDURL."articles/view/".$articleID."'>View Article</a><hr />";
  }
  ?>
</div>

<!-- Template Output -->
<div id='view'>

  <form method='POST'>
    <input type='hidden' name='step' value='2' />
    <?php
    if (!isset($articleID)) {
      $articleID = 'DEFAULT';
    }?>
    <input type='hidden' name='inp_articleID' value='<?php echo $articleID; ?>' />
    
    
    <table cellpadding='2' cellspacing='0'>

      <tr>
        <td>
          <!-- Title -->
          Title
        </td>
        <td>
          <input type='text' name='inp_articleName' value='<?php
            if (isset($inp_articleName)) {
              echo $inp_articleName;
            }
          ?>' size='60' />
        </td>
      </tr>
      
      <tr>
        <td>
            <!-- Short Desc -->
            Short Description
        </td>
        <td>
          <textarea name='inp_articleShortDescription' cols='20' rows='3'><?php
            if (isset($inp_articleShortDescription)) {
              echo $inp_articleShortDescription;
            }
        ?></textarea>
        </td>
      </tr>
      
      <tr>
        <td>
          <!-- Long Desc -->
          Long Description
        </td>
        <td>
          <textarea name='inp_articleLongDescription' cols='40' rows='4'><?php
            if (isset($inp_articleLongDescription)) {
              echo $inp_articleLongDescription;
            }
        ?></textarea>
        </td>
      </tr>

      <tr>
        <td>
          <!-- Body -->
          Body
        </td>
        <td>
          <textarea name='inp_articleBody' cols='60' rows='8'><?php
            if (isset($inp_articleBody)) {
              echo $inp_articleBody; // @todo [] NL to BR
            }
          ?></textarea>
        </td>
      </tr>

    <!-- Adv Options -->
    <div id='advancedOptions'>

      <tr>
        <td>
            Active
        </td>
        <td>
            <select name='inp_articleActive'>
              <option value='1'<?php
                if (!isset($inp_articleActive)
                  || $inp_articleActive == 1) {
                  echo ' selected';
                }
              ?>>Active</option>
              <option value='0'<?php
                if (isset($inp_articleActive)
                  && $inp_articleActive == 0) {
                  echo ' selected';
                }
              ?>>Inactive</option>
            </select>
        </td>
      </tr>
      
    </div>
    
      <tr>
        <td>
          <center>
            <!-- Submit -->
            <button type='submit'>Go</button>
          </center>
        </td>
        <td>
          &nbsp;
        </td>
      </tr>
    
    </table>

  </form>

</div>