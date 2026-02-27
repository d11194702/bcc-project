<?php

/**
 * AJAX обработчик для отправки формы заявки
 */

add_action( 'wp_enqueue_scripts', function() {
    wp_localize_script( 'bcc-project-main', 'inquiryAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'inquiry_form_nonce' ),
    ) );
} );

add_action( 'wp_ajax_send_inquiry', 'bcc_send_inquiry' );
add_action( 'wp_ajax_nopriv_send_inquiry', 'bcc_send_inquiry' );

function bcc_send_inquiry() {
    // Проверка nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'inquiry_form_nonce' ) ) {
        wp_send_json_error( array(
            'message' => 'Ошибка безопасности. Пожалуйста, попробуйте еще раз.',
        ) );
    }

    // Получение и валидация данных
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

    // Валидация полей
    if ( empty( $name ) || empty( $email ) || empty( $phone ) ) {
        wp_send_json_error( array(
            'message' => 'Пожалуйста, заполните все обязательные поля.',
        ) );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array(
            'message' => 'Пожалуйста, введите корректный адрес email.',
        ) );
    }

    // Получение email для отправки
    $recipient_email = carbon_get_theme_option( 'form_inquiry_email_recipient' );
    if ( empty( $recipient_email ) ) {
        $recipient_email = carbon_get_theme_option( 'theme_email' );
    }
    if ( empty( $recipient_email ) ) {
        $recipient_email = get_option( 'admin_email' );
    }

    // Подготовка письма
    $subject = 'Новая заявка с сайта ' . get_bloginfo( 'name' );
    
    $message_body = sprintf(
        "Новая заявка поступила на сайт %s\n\n" .
        "Имя: %s\n" .
        "Email: %s\n" .
        "Телефон: %s\n" .
        "Сообщение:\n%s\n\n" .
        "---\n" .
        "Это автоматическое письмо с сайта %s",
        get_bloginfo( 'url' ),
        $name,
        $email,
        $phone,
        $message,
        get_bloginfo( 'name' )
    );

    // Отправка письма администратору
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>'
    );

    $mail_sent = wp_mail( $recipient_email, $subject, $message_body, $headers );

    if ( $mail_sent ) {
        // Отправка подтверждения клиенту
        $user_subject = 'Ваша заявка принята';
        $user_message = sprintf(
            "Здравствуйте, %s!\n\n" .
            "Спасибо за вашу заявку. Мы получили ваше сообщение и свяжемся с вами в ближайшее время.\n\n" .
            "С уважением,\n" .
            "%s",
            $name,
            get_bloginfo( 'name' )
        );

        wp_mail( $email, $user_subject, $user_message, $headers );

        wp_send_json_success( array(
            'message' => carbon_get_theme_option( 'form_inquiry_success_message' ) ?: 
                         'Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.',
        ) );
    } else {
        wp_send_json_error( array(
            'message' => 'Ошибка при отправке. Пожалуйста, попробуйте позже или свяжитесь с нами напрямую.',
        ) );
    }
}
