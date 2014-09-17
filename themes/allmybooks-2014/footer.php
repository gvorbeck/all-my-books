      </main>
      <footer id="site-footer" class="content">
        <p id="copyright">&copy; <?php echo date('Y') . ' ' . get_bloginfo('name'); ?></p>
        <p id="site-credit">Created by <a href="http://iamgarrett.com" title="iamgarrett">Garrett Vorbeck</a>.</p>
        <?php $amb_theme = wp_get_theme(); ?>
        <p id="github-link"><?php echo '<a target="_blank" href="https://github.com/gvorbeck/all-my-books/releases/latest" title="github repo"><span>' . $amb_theme->get( 'Name' ) . ' (' . $amb_theme->get( 'Version' ) . ')</span>'. svg_github() .'</a>';
          ?>
        </p>
      </footer>
    <div id="logged-out-warning"><a href="javascript:;"><?php echo svg_logout(); ?></a><p></p></div>
    <div id="loading-container"><div class="loading-spinner"></div></div>
    </div><!-- #container -->
    <?php wp_footer(); ?>
  </body>
</html>
