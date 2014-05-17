			</main>
		</div> <!-- #container -->
    <footer id="site-footer">
			<p id="copyright">&copy; <?php echo date('Y') . ' ' . get_bloginfo('name'); ?></p>
			<p id="site-credit">Created by <a href="http://iamgarrett.com" title="iamgarrett">Garrett Vorbeck</a>.</p>
			<p id="github-link"><?php echo '<a target="_blank" href="https://github.com/gvorbeck/all-my-books/releases/tag/v1.3" title="github repo"><span>all-my-books v1.3.1 (Dostoevsky)</span>'.trim(file_get_contents(locate_template("_images/icons/github.svg"))).'</a>';
				?>
			</p>
    </footer>
    <?php wp_footer(); ?>
	</body>
</html>
