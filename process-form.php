<?php
// load wordpress environment
if (!isset($GLOBALS['wpdb'])) {
    require_once('../../wordpress/wp-load.php');
}

// Check nonce for security
// check_ajax_referer('ajax_nonce', 'nonce');

function send_form_email($data): void
{
    $to = 'a.greuzard@influactive.com'; // Replace with your email address
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
if (!empty($_POST['form_data'])) {
    parse_str($_POST['form_data'], $form_data); // This will convert the form data into an array
    send_form_email($form_data);
    echo 'Form submitted successfully';
} else {
    echo 'Missing form data';
}

die();
