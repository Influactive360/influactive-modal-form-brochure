<?php
/**
 * Plugin Name: Modal Forms Brochure by Influactive
 * Description: A plugin to display a modal with a form on a link click (#brochure and a parameter ?file=ID).
 * Version: 1.5.2
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-modal-form-brochure
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Modal Forms Brochure by Influactive
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

add_action(
	'admin_init',
	static function () {
		if ( ! influactive_is_active() ) {
			add_action( 'admin_notices', 'show_influactive_forms_error_notice' );
			if ( function_exists( 'deactivate_plugins' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
			}
		}
	}
);

/**
 * Checks if the plugin Forms everywhere by Influactive is active.
 *
 * @return bool True if the plugin is active, false otherwise.
 */
function influactive_is_active(): bool {
	$active_plugins = get_option( 'active_plugins' );

	return in_array( 'influactive-forms/functions.php', $active_plugins, true );
}

/**
 * Displays an error notice when the plugin Modal Form Brochure requires the plugin Forms everywhere by Influactive to be activated.
 *
 * @return void
 */
function show_influactive_forms_error_notice(): void {
	?>
	<div class="error notice">
		<p>
			<?php esc_html_e( 'The plugin Modal Form Brochure requires the plugin Forms everywhere by Influactive to be activated.', 'influactive-modal-form-brochure' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Enqueues the necessary scripts and styles for the Modal Form Brochure plugin.
 *
 * @return void
 */
function influactive_load_modal_form_scripts(): void {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_script( 'influactive-modal-form-brochure', plugin_dir_url( __FILE__ ) . 'dist/frontEnd.bundled.js', array(), '1.5.2', true );
	wp_enqueue_style( 'influactive-modal-form-brochure', plugin_dir_url( __FILE__ ) . 'dist/modal-form-script.bundled.css', array(), '1.5.2' );
}

add_action( 'wp_enqueue_scripts', 'influactive_load_modal_form_scripts' );

/**
 * Loads the necessary admin scripts for the plugin Modal Form Brochure.
 *
 * @param string $hook The current admin page hook.
 *
 * @return void
 */
function influactive_load_admin_scripts( string $hook ): void {
	if ( 'influactive-forms_page_modal-form-options' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'influactive-modal-form-brochure-admin', plugin_dir_url( __FILE__ ) . 'dist/backEnd.bundled.js', array(), '1.5.2', true );
	wp_enqueue_style( 'influactive-modal-form-brochure-admin', plugin_dir_url( __FILE__ ) . 'dist/admin.bundled.css', array(), '1.5.2' );
}

add_action( 'admin_enqueue_scripts', 'influactive_load_admin_scripts' );

/**
 * Displays the modal form for downloading product sheets.
 *
 * @return void
 */
function influactive_add_modal_form(): void {
	$title       = get_option( 'modal_form_title', __( 'Do you want to download this product sheet?', 'influactive-modal-form-brochure' ) );
	$description = get_option( 'modal_form_description', __( 'In order to receive your product sheet, please fill in your information below, we will send you a link by email to download it.', 'influactive-modal-form-brochure' ) );
	$posts       = get_option( 'modal_form_posts', array() );
	$display     = '';

	if ( ! empty( $posts['modal_form_posts'] ) && ( in_array( get_the_ID(), $posts['modal_form_posts'], true ) ) ) {
		$display = "style='display: block;'";
	}

	$form_id = (int) get_option( 'modal_form_select', false );
	?>
	<div
		id="modal-form"
		class="modal-form influactive-modal-form-brochure" <?php echo esc_attr( $display ); ?>>
		<div class="modal-content">
			<span id="modal-form-close" class="close">&times;</span>
			<h2><?php echo esc_html( $title ); ?></h2>
			<hr>
			<div class="description"><?php echo wp_kses_post( $description ); ?></div>
			<?php echo do_shortcode( "[influactive_form id='$form_id']" ); ?>
		</div>
	</div>
	<?php
}

add_action( 'wp_footer', 'influactive_add_modal_form' );

/**
 * Displays the options page for the Modal Form Brochure plugin.
 *
 * This function is accessible only for users with the capability to edit posts. It starts output buffering and then displays the options page content.
 *
 * @return void
 */
function influactive_modal_form_options_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	ob_start();
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'modal_form_options' );
			do_settings_sections( 'modal-form-options' );
			submit_button( __( 'Save Settings', 'influactive-modal-form-brochure' ) );
			?>
		</form>
	</div>
	<?php
	echo wp_kses_post( ob_get_clean() );
}

/**
 * Initializes the settings for the Modal Form Brochure plugin.
 *
 * Registers the necessary settings and settings sections for the plugin.
 *
 * @return void
 */
function influactive_modal_form_settings_init(): void {
	register_setting( 'modal_form_options', 'modal_form_title', 'sanitize_text_field' );
	register_setting( 'modal_form_options', 'modal_form_description', 'wp_kses_post' );
	register_setting( 'modal_form_options', 'modal_form_posts', 'influactive_modal_posts_select_validate' );
	register_setting( 'modal_form_options', 'modal_form_file_select' );
	register_setting( 'modal_form_options', 'modal_form_select' );
	add_settings_section( 'modal_form_main', __( 'Main Settings', 'influactive-modal-form-brochure' ), 'influactive_modal_form_fields_callback', 'modal-form-options' );
}

add_action( 'admin_init', 'influactive_modal_form_settings_init' );

/**
 * Renders the fields for the influactive_modal_form_fields_callback
 *
 * @return void
 */
function influactive_modal_form_fields_callback(): void {
	$form_title       = get_option( 'modal_form_title', esc_html__( 'Do you want to download this product sheet?', 'influactive-modal-form-brochure' ) );
	$form_description = get_option( 'modal_form_description', esc_html__( 'In order to receive your product sheet, please fill in your information below, we will send you a link by email to download it.', 'influactive-modal-form-brochure' ) );
	$form_submit_text = get_option( 'modal_form_submit_text', esc_html__( 'Submit', 'influactive-modal-form-brochure' ) );
	$file             = get_option( 'modal_form_file_select', false );
	$form_select      = get_option( 'modal_form_select', false );
	?>
	<div class="columns-brochure">
		<div id="content-edit">
			<label
				for="modal_form_title"><?php echo esc_html__( 'Modal Title:', 'influactive-modal-form-brochure' ); ?></label>
			<input
				id="modal_form_title" type="text" name="modal_form_title"
				value="<?php echo esc_attr( $form_title ?? 'Do you want to download this product sheet?' ); ?>">
			<label
				for="modal_form_description"><?php echo esc_html__( 'Modal Description:', 'influactive-modal-form-brochure' ); ?></label>
			<?php
			wp_editor(
				$form_description,
				'modal_form_description',
				array(
					'textarea_name' => 'modal_form_description',
					'media_buttons' => false,
					'textarea_rows' => 6,
					'tinymce'       => true,
				)
			);
			?>
			<label for="modal_form_select">
				<?php echo esc_html__( 'Select Form to use:', 'influactive-modal-form-brochure' ); ?>
			</label>
			<select id="modal_form_select" name="modal_form_select">
				<option value="" disabled><?php echo esc_html__( '- Select -', 'influactive-modal-form-brochure' ); ?></option>
				<?php
				$args        = array(
					'post_type'   => 'influactive-forms',
					'post_status' => 'publish',
					'nopaging'    => true,
				);
				$forms_query = new WP_Query( $args );
				if ( $forms_query->have_posts() ) {
					while ( $forms_query->have_posts() ) {
						$forms_query->the_post();
						$selected = get_the_ID() === (int) $form_select ? 'selected' : '';
						?>
						<option value="<?php the_ID(); ?>" <?php echo esc_html( $selected ); ?>><?php the_title(); ?></option>
						<?php
					}
					wp_reset_postdata();
				}
				?>
			</select>
			<label
				for="modal_form_submit_text">
				<?php echo esc_html__( 'Submit Button Text:', 'influactive-modal-form-brochure' ); ?>
			</label>
			<input
				id="modal_form_submit_text" type="text" name="modal_form_submit_text"
				value="<?php echo esc_attr( $form_submit_text ?? 'Submit' ); ?>">
		</div>

		<div id="select_file_general_from_library">
			<label
				for="modal_form_file_select">
				<?php echo esc_html__( 'Select File to to show a modal at load (also default file to not use ?file=ID for general case):', 'influactive-modal-form-brochure' ); ?>
			</label>
			<input
				type="text"
				id="modal_form_file_select" name="modal_form_file_select"
				readonly
				value="<?php echo esc_html( $file ); ?>"
			>
			<button type="button"
							id="upload-button">
				<?php echo esc_html__( 'Select File', 'influactive-modal-form-brochure' ); ?>
			</button>
		</div>

		<div id="content-select-posts">
			<label
				for="modal_form_posts">
				<?php echo esc_html__( 'Select Posts to show a modal at load:', 'influactive-modal-form-brochure' ); ?>
			</label>
			<?php
			$selected_posts = get_option( 'modal_form_posts' ) ?? array(
				'modal_form_posts' => array(
					0 => 0,
				),
			);
			$args           = array(
				'post_type'   => 'any',
				'post_status' => 'publish',
				'nopaging'    => true,
			);
			$posts_query    = new WP_Query( $args );
			if ( empty( $selected_posts ) ) {
				$selected_posts['modal_form_posts'] = array();
			}
			?>
			<select id="modal_form_posts" name="modal_form_posts[]" multiple>
				<option value="" disabled>
					<?php echo esc_html__( '- Select -', 'influactive-modal-form-brochure' ); ?>
				</option>
				<?php
				if ( $posts_query->have_posts() ) {
					while ( $posts_query->have_posts() ) {
						$posts_query->the_post();
						$selected  = $selected_posts['modal_form_posts'] && in_array( get_the_ID(), $selected_posts['modal_form_posts'], true ) ? 'selected' : '';
						$permalink = get_permalink();
						$post_type = get_post_type();

						if ( $permalink && 'attachment' !== $post_type && 'influactive-forms' !== $post_type && ! is_wp_error( $permalink ) ) {
							?>
							<option value="<?php the_ID(); ?>" <?php echo esc_html( $selected ); ?>><?php the_title(); ?></option>
							<?php
						}
					}
					wp_reset_postdata();
				}
				?>
			</select>
		</div>
	</div>
	<?php
}

add_action(
	'admin_menu',
	static function () {
		add_submenu_page(
			'edit.php?post_type=influactive-forms',
			__( 'Modal Form Options', 'influactive-modal-form-brochure' ),
			__( 'Modal Form Options', 'influactive-modal-form-brochure' ),
			'manage_options',
			'modal-form-options',
			'influactive_modal_form_options_page'
		);
	}
);

/**
 * Validates the input for the influactive_modal_posts_select form field.
 *
 * @param array $input The input value from the form field.
 *
 * @return array The sanitized and filtered input array.
 */
function influactive_modal_posts_select_validate( array $input ): array {
	$new_input = array();

	$posts                         = array_map( 'absint', $input );
	$new_input['modal_form_posts'] = array_filter( $posts, 'get_post' );

	return $new_input;
}

/**
 * Adds action links to the plugin page in the WordPress admin dashboard.
 *
 * @param array $links An array of existing action links.
 *
 * @return array The modified array of action links.
 */
function influactive_add_action_links( array $links ): array {
	$mylinks = array(
		'<a href="' . admin_url( 'edit.php?post_type=influactive-forms&page=modal-form-options' ) . '">' . __( 'Settings', 'influactive-modal-form-brochure' ) . '</a>',
	);

	return array_merge( $links, $mylinks );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'influactive_add_action_links' );

/**
 * Loads the text domain for the plugin Modal Form Brochure.
 *
 * @return void
 */
function influactive_load_modal_form_textdomain(): void {
	load_plugin_textdomain( 'influactive-modal-form-brochure', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'influactive_load_modal_form_textdomain' );
