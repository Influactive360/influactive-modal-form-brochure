<?php
// load wordpress environment
if (!isset($GLOBALS['wpdb'])) {
    require_once('../../wordpress/wp-load.php');
}

// Check nonce for security
// check_ajax_referer('ajax_nonce', 'nonce');

function send_form_email($data): void
{
    $to = get_option('modal_form_email_recipient', get_bloginfo('admin_email')); // Replace with your email address
    $subject = 'New Brochure Request';
    $message = '';

    // Loop through all form fields
    foreach ($data as $key => $value) {
        $message .= ucwords($key) . ': ' . $value . "\n";
    }

    if (wp_mail($to, $subject, $message)) {
        echo 'Email sent successfully';
    } else {
        echo 'An error occurred';
    }
}

// Check if the necessary data is available
$data_to_check = get_option('modal_form_fields');
$errors = [];
foreach ($data_to_check as $key => $value) {
    if (empty($_POST[$value['name']])) {
        $errors[] = 'Missing ' . $value['name'];
    }
}

if (empty($errors)) {
    // parse_str($_POST['form_data'], $form_data); // This will convert the form data into an array
    $form_data = $_POST;
    send_form_email($form_data);
    echo 'Form submitted successfully';
} else {
    echo implode("\n", $errors);
}

die();
