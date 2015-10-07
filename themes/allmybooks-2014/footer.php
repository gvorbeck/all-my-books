    </main>
    <footer id="site-footer">
      <div>
        <p>&copy; <?php echo date('Y') . ' ' . get_bloginfo('name'); ?></p>
        <p>AMB created by <a href="http://iamgarrett.com" title="iamgarrett">Garrett Vorbeck</a>.</p>
        <?php $amb_theme = wp_get_theme(); ?>
        <p id="site-footer--github-link"><?php echo '<a target="_blank" href="https://github.com/gvorbeck/all-my-books/releases/latest" title="github repo"><span>' . $amb_theme->get( 'Name' ) . ' (' . $amb_theme->get( 'Version' ) . ')</span>'. svg_github() .'</a>';
          ?>
        </p>
        <a id="site-footer--admin-link" href="<?php echo admin_url(); ?>" title="AMB Admin" target="_blank"><?php echo svg_cms(); ?></a>
      </div>
    </footer>
    <?php if (!is_user_logged_in()) { ?>
      <div id="logged-out-warning" class="site-message js-login-message"><p>You are not logged in. Any changes made are not permanent.</p><?php echo svg_logout(); ?></div>
    <?php } ?>
    <?php wp_footer(); ?>
  </body>
</html>
