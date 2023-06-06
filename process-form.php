<?php

if (!defined('ABSPATH')) {
    exit;
}

// load wordpress environment
if (!isset($GLOBALS['wpdb'])) {
    require_once('../../wordpress/wp-load.php');
}

function send_form_email($data): void
{
    $to_admin = get_option('modal_form_email_recipient', get_bloginfo('admin_email')); // Replace with your email address
    $email_field = get_option('modal_form_email_field', '');
    $name_field = get_option('modal_form_name_field', '');
    $rgpd_field = get_option('modal_form_rgpd_field', '');
    $to = $data[$email_field] ?? '';
    $name = $data[$name_field] ?? '';
    $subject_admin = 'New Brochure Request from ' . $name;
    $subject = 'Download your brochure';
    $message = '';

    // Loop through all form fields
    foreach ($data as $key => $value) {
        // If the field is empty, skip it
        if (empty($value)) {
            continue;
        }

        if ($key === 'file') {
            continue;
        }

        if ($key === 'rgpd') {
            continue;
        }
        $message .= ucwords($key) . ': ' . $value . "<br>\n\r";
    }

    $file_id = $data['file'];
    $file_url = wp_get_attachment_url($file_id);
    $message .= '<a href="' . $file_url . '">Download your brochure</a>';
    wp_mail($to, $subject, $message, ['Content-Type: text/html; charset=UTF-8', 'From: ' . $to_admin]);
    wp_mail($to_admin, $subject_admin, $message, ['Content-Type: text/html; charset=UTF-8', 'From: ' . $to_admin]);
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
    $form_data = $_POST;
    send_form_email($form_data);
    echo 'Email sent successfully';
} else {
    echo implode("\n", $errors);
}

die();
