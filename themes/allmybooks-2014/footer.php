			</main>
		</div> <!-- #container -->
    <footer id="site-footer">
			<p id="copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
			<p id="site-credit">Site developed and maintained by <a href="http://iamgarrett.com" title="iamgarrett">Garrett Vorbeck</a>.</p>
			<p id="github-link"><?php echo '<a target="_blank" href="https://github.com/gvorbeck/all-my-books/releases/tag/v1.2" title="github repo"><span>Running all-my-books v1.2 (Camus)</span>'.trim(file_get_contents(locate_template("_images/icons/github.svg"))).'</a>';
				?>
			</p>
    </footer>
    <?php wp_footer(); ?>
	</body>
</html>
