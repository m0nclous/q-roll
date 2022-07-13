		<footer>
			<div class="menu-group">
				<?php wp_nav_menu(['sort_column' => 'menu_order', 'theme_location' => 'footer']) ?>
				<?php wp_nav_menu(['sort_column' => 'menu_order', 'theme_location' => 'footer-socials']) ?>
			</div>

			<div class="sub-footer">
				<div class="copyright"><?= implode(',&nbsp;', ['&copy;&nbsp;' . get_bloginfo('name'), '2021 - ' . date('Y')]) ?></div>
				<div class="company">
					<span>ИП Ильинский Вячеслав Игоревич</span>
					<span>ИНН: 550616525586</span>
					<span>ОГРНИП: 322554300018830</span>
				</div>
				<a href="<?= get_privacy_policy_url() ?>" target="_blank">Политика конфиденциальности</a>
			</div>
		</footer>

		<button class="scroll-to-top">▲</button>

		<?php wp_footer() ?>
	</body>
</html>