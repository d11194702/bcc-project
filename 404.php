<?php
get_header(); ?>

<section class="error-404">
    <div class="container">
        <div class="error-404__content">
            <div class="error-404__text">
                <h1>404</h1>
                <h2>Страница не найдена</h2>
                <p>К сожалению, страница, которую вы ищете, не существует или была удалена.</p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-blue">На главную</a>
            </div>           
        </div>


    </div>
</section>

<style>
.error-404 {
    padding: 60px 0;
}

.error-404__content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    margin-bottom: 80px;
}

.error-404__text h1 {
    font-size: 100px;
    font-weight: 700;
    color: #1e3a8a;
    margin: 0;
    line-height: 1;
}

.error-404__text h2 {
    font-size: 32px;
    margin: 20px 0;
}

.error-404__text p {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
}

.error-404__form {
    background: #f5f5f5;
    padding: 40px;
    border-radius: 8px;
    max-width: 600px;
    margin: 0 auto;
}

.error-404__form h3 {
    font-size: 24px;
    margin-bottom: 15px;
}

.error-404__form p {
    color: #666;
    margin-bottom: 25px;
}

.inquiry-form__input,
.inquiry-form__textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
    margin-bottom: 15px;
}

.inquiry-form__input:focus,
.inquiry-form__textarea:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.inquiry-form__submit {
    width: 100%;
    padding: 12px 20px;
    cursor: pointer;
    border: none;
    margin-top: 10px;
}

.inquiry-form__message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 4px;
    text-align: center;
}

.inquiry-form__message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.inquiry-form__message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .error-404__content {
        grid-template-columns: 1fr;
        margin-bottom: 40px;
    }

    .error-404__text h1 {
        font-size: 60px;
    }

    .error-404__text h2 {
        font-size: 24px;
    }
}
</style>

<?php get_footer(); ?>
