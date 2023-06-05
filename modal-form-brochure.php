<?php
/*
Plugin Name: Modal Form Brochure
Description: Un simple plugin qui affiche une modal avec un formulaire lorsque l'utilisateur clique sur un lien avec l'identifiant #brochure.
Version: 1.0
Author: Influactive
*/

function load_modal_form_scripts(): void
{
    wp_enqueue_script('modal-form-script', plugin_dir_url(__FILE__) . 'modal-form-script.js', array(), '1.0', true);
    wp_enqueue_style('modal-form-style', plugin_dir_url(__FILE__) . 'modal-form-style.css');
}

add_action('wp_enqueue_scripts', 'load_modal_form_scripts');

function load_admin_scripts($hook): void
{
    if ('settings_page_modal-form-options' !== $hook) {
        return;
    }
    wp_enqueue_script('modal-form-admin', plugin_dir_url(__FILE__) . 'admin.js', array(), '1.0', true);
}

add_action('admin_enqueue_scripts', 'load_admin_scripts');

function add_modal_form(): void
{
    $fields = get_option('modal_form_fields', array());

    ob_start(); ?>
    <div id="modal-form" class="modal-form">
        <div class="modal-content">
            <span id="modal-form-close" class="close">&times;</span>
            <h2>Vous souhaitez télécharger cette fiche produit ?</h2>
            <hr>
            <p>Afin de recevoir votre fiche produit, merci de remplir vos informations ci-dessous, nous vous enverrons
                un lien par email pour le télécharger.</p>
            <form action="<?= plugin_dir_url(__FILE__) . 'process-form.php' ?>" method="post">
                <?php foreach ($fields as $field) : ?>
                    <label for="<?= $field['name']; ?>"><?= $field['label']; ?></label>
                    <input type="<?= $field['type']; ?>" id="<?= $field['name']; ?>" name="<?= $field['name']; ?>">
                <?php endforeach; ?>
                <input type="submit" value="Soumettre">
            </form>
            <div class="message"></div>
        </div>
    </div>
<?= ob_get_clean();
}

add_action('wp_footer', 'add_modal_form');

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
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
    // Output the content of the buffer
    echo ob_get_clean();
}

function modal_form_settings_init(): void
{
    register_setting('modal_form_options', 'modal_form_fields', 'modal_form_options_validate');

    add_settings_section('modal_form_main', 'Main Settings', 'modal_form_fields_callback', 'modal-form-options');
}

add_action('admin_init', 'modal_form_settings_init');

function modal_form_fields_callback(): void
{
    $form_fields = get_option('modal_form_fields');
?>
    <div id="form-fields">
        <?php if (is_array($form_fields)) { ?>
            <?php foreach ($form_fields as $field) : ?>
                <div class="field">
                    <label for="type">Type:</label>
                    <input id="type" type="text" name="modal_form_fields[field_type][]" value="<?= esc_attr($field['type'] ?? ''); ?>">
                    <label for="label">Label:</label>
                    <input id="label" type="text" name="modal_form_fields[field_label][]" value="<?= esc_attr($field['label'] ?? ''); ?>">
                    <label for="name">Name:</label>
                    <input id="name" type="text" name="modal_form_fields[field_name][]" value="<?= esc_attr($field['name'] ?? ''); ?>">
                    <?php submit_button('Delete', 'delete-field', 'delete-field', false, array('data-id' => $field['name'])); ?>
                </div>
            <?php endforeach; ?>
        <?php } ?>
    </div>
    <?php submit_button('Add Field', 'add-field', 'add-field', false); ?>
<?php
}

add_action('admin_menu', static function () {
    add_options_page('Modal Form Brochure Options', 'Modal Form Options', 'manage_options', 'modal-form-options', 'modal_form_options_page');
});

function modal_form_options_validate($input): array
{
    // Initialize the new array that will hold the sanitize values
    $new_input = array();

    if (is_array($input)) {
        for ($i = 0; $i < count($input['field_type']); $i++) {
            // Check if the input is a string, if it is, sanitize it
            if (is_string($input['field_type'][$i]) && is_string($input['field_label'][$i]) && is_string($input['field_name'][$i])) {
                $new_input[] = array(
                    'type' => sanitize_text_field($input['field_type'][$i]),
                    'label' => sanitize_text_field($input['field_label'][$i]),
                    'name' => sanitize_text_field($input['field_name'][$i])
                );
            }
        }
    }

    return $new_input;
}

function add_action_links($links): array
{
    $mylinks = array(
        '<a href="' . admin_url('options-general.php?page=modal-form-options') . '">Settings</a>',
    );
    return array_merge($links, $mylinks);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links');
