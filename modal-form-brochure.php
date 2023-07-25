<?php
/**
 * Plugin Name: Modal Forms Brochure by Influactive
 * Description: A plugin to display a modal with a form on a link click (#brochure and a parameter ?file=ID).
 * Version: 1.2
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-modal-form-brochure
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 **/

if (!defined('ABSPATH')) {
    throw new RuntimeException("WordPress environment not loaded. Exiting...");
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

/**
 * Check if the Forms everywhere by Influactive plugin is active.
 *
 * This function checks if the Forms everywhere by Influactive plugin is currently active in WordPress.
 *
 * @return bool Returns true if the plugin is active, false otherwise.
 */
function is_influactive_active(): bool
{
    return is_plugin_active('influactive-forms/functions.php');
}

/**
 * Display error notice for missing required plugin.
 *
 * This function outputs an error notice when the plugin Modal Form Brochure is active but the required plugin
 * Forms everywhere by Influactive is not activated. The error notice informs the user about the dependency and suggests
 * activating the required plugin.
 *
 * @return void
 */
function show_influactive_forms_error_notice(): void
{
    ?>
    <div class="error notice">
        <p><?php _e('The plugin Modal Form Brochure requires the plugin Forms everywhere by Influactive to be activated.', 'influactive-modal-form-brochure'); ?></p>
    </div>
    <?php
}

if (!is_influactive_active()) {
    add_action('admin_notices', 'show_influactive_forms_error_notice');
    deactivate_plugins(plugin_basename(__FILE__));

    return;
}

/**
 * Load the scripts and styles for the modal form.
 *
 * This function checks if the user is an admin. If not, it includes the necessary JavaScript and CSS files
 * for the modal form.
 *
 * @return void
 */
function load_modal_form_scripts(): void
{
    if (is_admin()) {
        return;
    }
    wp_enqueue_script('influactive-modal-form-brochure', plugin_dir_url(__FILE__) . 'assets/js/modal-form-script.min.js', array(), '1.0', true);
    wp_enqueue_style('influactive-modal-form-brochure', plugin_dir_url(__FILE__) . 'assets/css/modal-form-style.min.css', array(), '1.0');
}

add_action('wp_enqueue_scripts', 'load_modal_form_scripts');

/**
 * Load admin scripts for Modal Form options page.
 *
 * This function is used to enqueue necessary scripts and stylesheets for the admin page of Modal Form settings.
 * It checks if the current page is the Modal Form options page and then enqueues the required scripts and stylesheets
 * using the WordPress `wp_enqueue_script()` and `wp_enqueue_style()` functions.
 * The function also includes the Choices.js library for enhancing select elements.
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function load_admin_scripts(string $hook): void
{
    if ('influactive-forms_page_modal-form-options' !== $hook) {
        return;
    }
    wp_enqueue_media(); // Ajoutez cette ligne
    wp_enqueue_script('influactive-modal-form-brochure-admin', plugin_dir_url(__FILE__) . 'assets/js/admin.min.js', array('choices-js'), '1.0', true);
    wp_enqueue_style('influactive-modal-form-brochure-admin', plugin_dir_url(__FILE__) . 'assets/css/admin-style.min.css', array(), '1.0');

    // Enqueue choices.js CSS et JS
    wp_enqueue_style('choices-css', 'https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.css', array(), '10.2.0');
    wp_enqueue_script('choices-js', 'https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js', array(), '10.2.0', true);
}

add_action('admin_enqueue_scripts', 'load_admin_scripts');

/**
 * Add a modal form to the page.
 *
 * This function adds a modal form to the page. The form is displayed based on the configuration settings. It retrieves the
 * form title, description, and posts to display the form on from the options saved in the database. It also retrieves the
 * form ID to display from the options.
 *
 * @return void
 */
function add_modal_form(): void
{
    $title = get_option('modal_form_title', __('Do you want to download this product sheet?', 'influactive-modal-form-brochure'));
    $description = get_option('modal_form_description', __('In order to receive your product sheet, please fill in your information below, we will send you a link by email to download it.', 'influactive-modal-form-brochure'));
    $posts = get_option('modal_form_posts', array());
    $display = "";

    if (!empty($posts['modal_form_posts']) && (in_array(get_the_ID(), $posts['modal_form_posts'], true))) {
        $display = "style='display: block;'";
    }

    $form_id = (int)get_option('modal_form_select', false);

    ?>
    <div id="modal-form" class="modal-form influactive-modal-form-brochure" <?= $display ?>>
        <div class="modal-content">
            <span id="modal-form-close" class="close">&times;</span>
            <h2><?= $title ?></h2>
            <hr>
            <div class="description"><?= $description ?></div>
            <?= do_shortcode("[influactive_form id='$form_id']") ?>
        </div>
    </div>
    <?php
}

add_action('wp_footer', 'add_modal_form', 10);

/**
 * Display options page for Modal Form plugin.
 *
 * This function outputs the option page for the Modal Form plugin. It checks the user's capabilities to manage options,
 * and if the user does not have the required capabilities, nothing is displayed.
 *
 * The option page includes a form with settings fields and sections. The form is submitted to the 'options.php' file
 * and includes a submitted button to save the settings.
 *
 * @return void
 */
function modal_form_options_page(): void
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Start output buffering
    ob_start();
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('modal_form_options');
            do_settings_sections('modal-form-options');
            submit_button(__('Save Settings', 'influactive-modal-form-brochure'));
            ?>
        </form>
    </div>
    <?php
    // Output the content of the buffer
    echo ob_get_clean();
}

/**
 * Initialize the modal form settings.
 *
 * This function registers the necessary settings and sections for the modal form options page.
 *
 * @return void
 */
function modal_form_settings_init(): void
{
    register_setting('modal_form_options', 'modal_form_title', 'sanitize_text_field');
    register_setting('modal_form_options', 'modal_form_description', 'wp_kses_post');
    register_setting('modal_form_options', 'modal_form_posts', 'modal_posts_select_validate');
    register_setting('modal_form_options', 'modal_form_file_select');
    register_setting('modal_form_options', 'modal_form_select');
    add_settings_section('modal_form_main', __('Main Settings', 'influactive-modal-form-brochure'), 'modal_form_fields_callback', 'modal-form-options');
}

add_action('admin_init', 'modal_form_settings_init');

/**
 * Callback function for displaying form fields in a settings page.
 *
 * This function is invoked when rendering the form fields in a settings page for the plugin. It retrieves the necessary
 * form data from options and displays the fields accordingly. The form fields include the modal title, modal description,
 * select a form to use, submit button text, and options to select file and posts to show a modal at a load.
 *
 * @return void
 */
function modal_form_fields_callback(): void
{
    $form_title = get_option('modal_form_title', __('Do you want to download this product sheet?', 'influactive-modal-form-brochure'));
    $form_description = get_option('modal_form_description', __('In order to receive your product sheet, please fill in your information below, we will send you a link by email to download it.', 'influactive-modal-form-brochure'));
    $form_submit_text = get_option('modal_form_submit_text', __('Submit', 'influactive-modal-form-brochure'));
    $file = get_option('modal_form_file_select', false);
    $form_select = get_option('modal_form_select', false);
    ?>
    <div class="columns-brochure">
        <div id="content-edit">
            <label for="modal_form_title"><?= __('Modal Title:', 'influactive-modal-form-brochure') ?></label>
            <input id="modal_form_title" type="text" name="modal_form_title"
                   value="<?= esc_attr($form_title ?? 'Do you want to download this product sheet?') ?>">
            <label for="modal_form_description"><?= __('Modal Description:', 'influactive-modal-form-brochure') ?></label>
            <?php
            wp_editor($form_description, 'modal_form_description', array(
                    'textarea_name' => 'modal_form_description',
                    'media_buttons' => false,
                    'textarea_rows' => 6,
                    'tinymce' => true,
            ));
            ?>
            <label for="modal_form_select">
                <?= __('Select Form to use:', 'influactive-modal-form-brochure') ?>
            </label>
            <select id="modal_form_select" name="modal_form_select">
                <option value="" disabled><?= __('- Select -', 'influactive-modal-form-brochure') ?></option>
                <?php
                $args = array(
                        'post_type' => 'influactive-forms',
                        'post_status' => 'publish',
                        'nopaging' => true,
                );
                $forms_query = new WP_Query($args);
                if ($forms_query->have_posts()) {
                    while ($forms_query->have_posts()) {
                        $forms_query->the_post();
                        $selected = get_the_ID() === (int)$form_select ? 'selected' : '';
                        ?>
                        <option value="<?php the_ID(); ?>" <?= $selected ?>><?php the_title(); ?></option>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </select>
            <label for="modal_form_submit_text"><?= __('Submit Button Text:', 'influactive-modal-form-brochure') ?></label>
            <input id="modal_form_submit_text" type="text" name="modal_form_submit_text"
                   value="<?= esc_attr($form_submit_text ?? 'Submit') ?>">
        </div>

        <div id="select_file_general_from_library">
            <label for="modal_form_file_select"><?= __('Select File to to show a modal at load (also default file to not use ?file=ID for general case):', 'influactive-modal-form-brochure') ?></label>
            <input type="text" id="modal_form_file_select" name="modal_form_file_select" readonly
                   value="<?= $file ?>">
            <button type="button"
                    id="upload-button"><?= __('Select File', 'influactive-modal-form-brochure') ?></button>
        </div>

        <div id="content-select-posts">
            <label for="modal_form_posts"><?= __('Select Posts to show a modal at load:', 'influactive-modal-form-brochure') ?></label>
            <?php
            // Get selected posts
            $selected_posts = get_option('modal_form_posts') ?? [
                    'modal_form_posts' => [
                            0 => 0,
                    ],
            ];
            // Query for all posts
            $args = array(
                    'post_type' => 'any',
                    'post_status' => 'publish',
                    'nopaging' => true,
            );
            $posts_query = new WP_Query($args);
            if (empty($selected_posts)) {
                $selected_posts['modal_form_posts'] = array();
            }
            ?>
            <select id="modal_form_posts" name="modal_form_posts[]" multiple>
                <option value="" disabled><?= __('- Select -', 'influactive-modal-form-brochure') ?></option>
                <?php
                if ($posts_query->have_posts()) {
                    while ($posts_query->have_posts()) {
                        $posts_query->the_post();
                        $selected = $selected_posts['modal_form_posts'] && in_array(get_the_ID(), $selected_posts['modal_form_posts'], true) ? 'selected' : '';
                        $permalink = get_permalink();
                        $post_type = get_post_type();

                        if ($permalink && $post_type !== 'attachment' && $post_type !== 'influactive-forms' && !is_wp_error($permalink)) {
                            ?>
                            <option value="<?php the_ID(); ?>" <?= $selected ?>><?php the_title(); ?></option>
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

add_action('admin_menu', static function () {
    add_submenu_page('edit.php?post_type=influactive-forms', __('Modal Form Options', 'influactive-modal-form-brochure'), __('Modal Form Options', 'influactive-modal-form-brochure'), 'manage_options', 'modal-form-options', 'modal_form_options_page');
});

/**
 * Validate and sanitize selected posts for modal form.
 *
 * This function takes an input array and performs validation and sanitization on the selected posts.
 * It filters out any invalid or non-existent posts and returns the sanitized array with valid post IDs.
 *
 * @param array $input The input array containing selected posts.
 * @return array The sanitized array with valid post IDs.
 */
function modal_posts_select_validate(array $input): array
{
    // Initialize the new array that will hold the sanitize values
    $new_input = array();

    // Validation for posts
    $posts = array_map('absint', $input);
    $new_input['modal_form_posts'] = array_filter($posts, 'get_post');

    return $new_input;
}

/**
 * Add action links to the plugin's settings page.
 *
 * This function adds custom action links to the plugin's settings page in the WordPress admin interface.
 * The action links provide easy access to the plugin's settings page.
 *
 * @param array $links The existing action links.
 *
 * @return array The modified action links.
 */
function add_action_links(array $links): array
{
    $mylinks = array(
            '<a href="' . admin_url('edit.php?post_type=influactive-forms&page=modal-form-options') . '">' . __("Settings", "influactive-modal-form-brochure") . '</a>',
    );

    return array_merge($links, $mylinks);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links');

add_action('plugins_loaded', 'load_modal_form_textdomain');
/**
 * Load the text domain for the Modal Form Brochure plugin.
 *
 * This function loads the text domain for the Modal Form Brochure plugin, allowing translation of plugin strings.
 * The text domain is loaded from the 'languages' directory in the plugin's file path.
 *
 * @return void
 */
function load_modal_form_textdomain(): void
{
    load_plugin_textdomain('influactive-modal-form-brochure', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
