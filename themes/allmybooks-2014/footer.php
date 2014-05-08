			</main>
		</div> <!-- #container -->
    <footer id="site-footer">
			<p id="copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
			<p id="site-credit">Site developed and maintained by <a href="http://iamgarrett.com" title="iamgarrett">Garrett Vorbeck</a>.</p>
			<p>
				<?php
				echo file_get_contents( locate_template( "_images/icons/wordpress.svg" ) );
				echo '<a target="_blank" href="https://github.com/gvorbeck/all-my-books" title="github repo">' . file_get_contents( locate_template( "_images/icons/github.svg" ) ) . ' Running AMB v1.1 (Bradbury)</a>';
				?>
			</p>
    </footer>
    <?php wp_footer(); ?>
	</body>
</html>
