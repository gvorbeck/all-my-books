<?php
if (!is_user_logged_in()) {
  wp_login_form(array('form_id'  => 'login-form'));
} else {
  ?>
  <form method="post" name="add-book-form" id="add-book-form" class="site-form site-form--add-a-book">
    <fieldset>
      <legend>add a book</legend>
      <div>
        <label class="required">title
          <input id="add-book-form--title" name="add-book-form--title" type="text" placeholder="Lonesome Dove" required autofocus>
        </label>
      </div>
      <div>
        <label class="required">author
          <input id="add-book-form--author" name="add-book-form--author" type="text" placeholder="Harry Turtledove" required>
        </label>
      </div>
      <div> 
        <a title="submit" href="javascript:;" class="add-book-form--button button">submit</a> 
      </div> 
    </fieldset>
  </form>
<?php }
