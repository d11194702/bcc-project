        <div class="error-404__form">
            <h3><?php echo esc_html( carbon_get_theme_option( 'form_inquiry_title' ) ?: 'Оставить заявку' ); ?></h3>
            <?php if ( $description = carbon_get_theme_option( 'form_inquiry_description' ) ) : ?>
                <p><?php echo wp_kses_post( $description ); ?></p>
            <?php endif; ?>
            
            <form id="inquiry-form" class="inquiry-form">
                <div class="form-group">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Ваше имя" 
                        required
                        class="inquiry-form__input"
                    >
                </div>

                <div class="form-group">
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Email" 
                        required
                        class="inquiry-form__input"
                    >
                </div>

                <div class="form-group">
                    <input 
                        type="tel" 
                        name="phone" 
                        placeholder="Контактный телефон" 
                        required
                        class="inquiry-form__input"
                    >
                </div>

                <div class="form-group">
                    <textarea 
                        name="message" 
                        placeholder="Сообщение" 
                        rows="4"
                        class="inquiry-form__textarea"
                    ></textarea>
                </div>

                <button 
                    type="submit" 
                    class="btn-blue inquiry-form__submit"
                >
                    <?php echo esc_html( carbon_get_theme_option( 'form_inquiry_button_text' ) ?: 'Отправить' ); ?>
                </button>

                <div id="form-message" class="inquiry-form__message" style="display: none;"></div>
            </form>
        </div>