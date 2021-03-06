<?php
	// Setup our default assets to load.
	Assets::add_js( array(
		Template::theme_url('js/jquery-1.8.0.min.js'),
		Template::theme_url('js/jquery.form.js'),
		Template::theme_url('js/jquery-ui-1.8.13.min.js'),
                Template::theme_url('js/jquery.bbq.js'),
		Template::theme_url('js/jwerty.js'),
	),
	'external',
	true);

	if (isset($shortcut_data) && is_array($shortcut_data['shortcut_keys'])) {
		Assets::add_js($this->load->view('ui/shortcut_keys', $shortcut_data, true), 'inline');
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<title><?php echo isset($toolbar_title) ? $toolbar_title .' : ' : ''; ?> <?php echo $this->settings_lib->item('site.title') ?></title>

	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

	<?php echo Assets::css(null, 'screen', true); ?>

	<!-- Fix the mobile Safari auto-zoom bug -->
	<meta name="viewport" content="width=device-width, initial-scale=1"/>

        <?php print Assets::external_js(null, false) ?>
	<script>
        
        function site_url(uri){
            var site_url = '<?php print site_url() ?>';
            return site_url + uri;
        }
        
	</script>
</head>
<body>

	<noscript>
		<p>Javascript is required to use Bonfire's admin.</p>
	</noscript>

	<div id="message">
		<?php echo Template::message(); ?>
	</div>

	<div id="header">
		<!-- Nav Bar -->
		<div id="toolbar">
			<div id="toolbar-right">
				<?php if(isset($shortcut_data) && is_array($shortcut_data['shortcuts']) && is_array($shortcut_data['shortcut_keys']) && count($shortcut_data['shortcut_keys'])):?><img src="<?php echo Template::theme_url('images/keyboard-icon.png') ?>" id="shortkeys_show" title="Keyboard Shortcuts" alt="Keyboard Shortcuts"/><?php endif;?>
				<a href="<?php echo site_url(SITE_AREA .'/settings/users/edit/'. $this->auth->user_id()) ?>" id="tb_email" title="<?php echo lang('bf_user_settings') ?>"><?php echo $this->settings_lib->item('auth.use_usernames') ? ($this->settings_lib->item('auth.use_own_names') ? $this->auth->user_name() : $this->auth->username()) : $this->auth->email() ?></a>
				<a href="<?php echo site_url('logout') ?>" id="tb_logout" title="Logout">Logout</a>
			</div>

			<h1><a href="<?php echo site_url(); ?>" target="_blank"><?php echo $this->settings_lib->item('site.title') ?></a></h1>

			<div id="toolbar-left">
				<?php echo context_nav() ?>
			</div>	<!-- /toolbar-left -->
		</div>

		<?php echo modules::run('subnav/subnav/index', $this->uri->segment(2)); ?>

		<div id="nav-bar">
			<?php if (isset($toolbar_title)) : ?>
				<h1><?php echo $toolbar_title ?></h1>
			<?php endif; ?>

			<?php Template::block('sub_nav', ''); ?>
		</div>
	</div> <!-- /header -->

	<div class="content-main <?php echo isset(Template::$blocks['nav_bottom']) ? 'with-bottom-bar' : '' ?>">
			<?php echo Template::yield(); ?>
	</div>

	<?php Template::block('nav_bottom', ''); ?>

	<div id="loader">
		<div class="box">
			<img src="<?php echo Template::theme_url()?>images/ajax_loader.gif" />
		</div>
	</div>

	<div id="shortkeys_dialog" title="Shortcut Keys" style="display: none">
		<p>
			<?php echo lang('bf_keyboard_shortcuts') ?>
			<?php if (isset($shortcut_data) && is_array($shortcut_data['shortcut_keys'])): ?>
			<ul>
			<?php foreach($shortcut_data['shortcut_keys'] as $key => $data): ?>
				<li><span><?php echo $data?></span> : <?php echo $shortcut_data['shortcuts'][$key]['description']; ?></li>
			<?php endforeach; ?>
			</ul>
			<?php endif;?>
		</p>
	</div>


</body>
</html>
