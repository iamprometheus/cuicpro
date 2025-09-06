<?php

function send_email_from_contact_form() {
    $name = sanitize_text_field($_POST['name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $tournament_name = sanitize_text_field($_POST['tournament_name']);
    $tournament_date = sanitize_text_field($_POST['tournament_date']);
    $tournament_location = sanitize_text_field($_POST['tournament_location']);
    $tournament_contact = sanitize_text_field($_POST['tournament_contact']);

    $to = 'aly.berna26@gmail.com';
    $subject = 'Contacto para nuevo torneo';
    $message = "Nombre: $name\nApellido: $last_name\nNombre del torneo: $tournament_name\nFecha del torneo: $tournament_date\nUbicación: $tournament_location\nEmail: $tournament_contact";
    wp_mail($to, $subject, $message);
}

function handle_contact_form() {
    send_email_from_contact_form();
    wp_send_json_success(array('message' => 'Email sent successfully!'));
}

add_action('wp_ajax_handle_contact_form', 'handle_contact_form');
add_action('wp_ajax_nopriv_handle_contact_form', 'handle_contact_form');