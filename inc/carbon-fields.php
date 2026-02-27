<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'crb_attach_theme_options');
function crb_attach_theme_options()
{
    // SEO ПОЛЯ: Для всех страниц и товаров
    Container::make('post_meta', __('SEO настройки'))
        ->where('post_type', 'IN', array('page', 'product', 'post'))
        ->add_fields(array(
            Field::make('text', 'seo_title', 'SEO Title')
                ->set_help_text('Мета-заголовок для поисковиков (до 60 символов). Если не заполнено - используется заголовок страницы.')
                ->set_attribute('maxLength', 60),
            Field::make('textarea', 'seo_description', 'SEO Description')
                ->set_help_text('Мета-описание для поисковиков (до 160 символов)')
                ->set_rows(3)
                ->set_attribute('maxLength', 160),
            Field::make('text', 'seo_keywords', 'Ключевые слова')
                ->set_help_text('Ключевые слова через запятую (например: металлочерепица, кровля, профлист)'),
        ));

    // ОПЦИИ ТЕМЫ: Контакты и Соц. сети
    $theme_options = Container::make('theme_options', __('Контакты'));

    // Таб: Контактная информация
    $theme_options->add_tab('Основные', array(
        Field::make('text', 'theme_phone', 'Номер телефона')
            ->set_help_text('Основной номер телефона (используется везде на сайте)')
            ->set_width(50),

        Field::make('text', 'theme_phone_alt', 'Альтернативный номер телефона')
            ->set_width(50),

        Field::make('text', 'theme_email', 'Email')
            ->set_help_text('Основной email для контактов')
            ->set_width(50),

        Field::make('text', 'theme_email_alt', 'Альтернативный email')
            ->set_width(50),

        Field::make('textarea', 'theme_address', 'Адрес')
            ->set_help_text('Полный адрес компании')
            ->set_rows(3),

        Field::make('text', 'theme_city', 'Город')
            ->set_width(50),

        Field::make('text', 'theme_zip', 'Почтовый индекс')
            ->set_width(50),
    ));

    // Таб: Социальные ссылки
    $theme_options->add_tab('Соц. сети', array(
        Field::make('text', 'theme_whatsapp', 'WhatsApp (номер с кодом страны, например +79991234567)')
            ->set_help_text('Только цифры и +')
            ->set_width(50),

        Field::make('text', 'theme_vk', 'VK профиль (полная ссылка)')
            ->set_width(50),

        Field::make('text', 'theme_telegram', 'Telegram (полная ссылка или @username)')
            ->set_width(50),
    ));

    // Глобальные отзывы для всего сайта
    $theme_options->add_tab('Отзывы на сайте', array(
        Field::make('text', 'global_reviews_title', 'Заголовок секции'),
        Field::make('textarea', 'global_reviews_description', 'Описание секции')
            ->set_rows(3),
        Field::make('radio', 'global_reviews_display_type', 'Способ отображения отзывов')
            ->add_options(array(
                'widget' => 'Яндекс виджет (код будет вставлен в поле ниже)',
                'manual' => 'Вручную (добавьте отзывы в поле ниже)',
            ))
            ->set_default_value('manual'),
        Field::make('textarea', 'global_reviews_widget_code', 'Код Яндекс виджета отзывов')
            ->set_rows(4)
            ->set_help_text('Вставьте код встроенного виджета Яндекс.Карт с отзывами')
            ->set_conditional_logic(array(
                array(
                    'field' => 'global_reviews_display_type',
                    'value' => 'widget',
                    'compare' => '=',
                ),
            )),
        Field::make('text', 'global_reviews_link_text', 'Текст ссылки на отзывы')
            ->set_width(50),
        Field::make('text', 'global_reviews_link_url', 'Ссылка на отзывы')
            ->set_width(50),
        Field::make('complex', 'global_reviews_items', 'Отзывы вручную')
            ->set_help_text('Добавьте отзывы вручную (используется при выборе "Вручную" выше)')
            ->set_conditional_logic(array(
                array(
                    'field' => 'global_reviews_display_type',
                    'value' => 'manual',
                    'compare' => '=',
                ),
            ))
            ->add_fields(array(
                Field::make('text', 'author', 'Автор')
                    ->set_width(50),
                Field::make('text', 'date', 'Дата')
                    ->set_width(50),
                Field::make('textarea', 'text', 'Текст отзыва')
                    ->set_rows(4),
            ))
            ->set_header_template('<%- author %>'),
        Field::make('checkbox', 'global_reviews_card_enabled', 'Показывать блок связи в отзывах')
            ->set_help_text('Отобразить блок связи внутри секции отзывов')
            ->set_option_value('yes')
            ->set_default_value('yes'),
    ));

    // Глобальный блок связи (используется на отдельных страницах)
    $theme_options->add_tab('Блок связи', array(
        Field::make('text', 'contact_us_title', 'Заголовок блока')
            ->set_help_text('Например: Гарантия на изделия БалтСеверСталь <br>до 20 лет!'),
        Field::make('textarea', 'contact_us_description', 'Описание блока')
            ->set_rows(3)
            ->set_help_text('Текст под заголовком в блоке'),
        Field::make('text', 'contact_us_button_text', 'Текст кнопки'),
        Field::make('radio', 'contact_us_action_type', 'Тип действия кнопки')
            ->add_options(array(
                'modal' => 'Открыть модалку (modal-open)',
                'link'  => 'Перейти по ссылке',
            ))
            ->set_default_value('link'),
        Field::make('text', 'contact_us_button_url', 'Ссылка кнопки')
            ->set_help_text('Например: https://example.com или /#section')
            ->set_conditional_logic(array(
                array(
                    'field' => 'contact_us_action_type',
                    'value' => 'link',
                    'compare' => '=',
                ),
            )),
        Field::make('image', 'contact_us_bg_image', 'Фоновое изображение блока')
            ->set_help_text('Изображение фона для блока (вместо contact-us-bg.png)'),
        Field::make('image', 'contact_us_main_image', 'Главное изображение блока')
            ->set_help_text('Главное изображение блока (вместо contact-us-card.png)'),
        Field::make('image', 'contact_us_sm_image', 'Изображение для мобильных устройств')
            ->set_help_text('Изображение для мобильных устройств (вместо contact-us-card-sm.png)'),
    ));

    // ХЕДЕР: Отдельные поля в админке
    Container::make('theme_options', __('Хедер'))
        ->add_fields(array(
            Field::make('text', 'theme_header_phone', 'Номер телефона')
                ->set_help_text('Если не заполнено — используется основной номер из "Контакты"')
                ->set_width(50),

            Field::make('text', 'theme_header_email', 'Email')
                ->set_help_text('Если не заполнено — используется основной email из "Контакты"')
                ->set_width(50),

            Field::make('rich_text', 'theme_header_info', 'Доп. информация')
                ->set_help_text('Краткая информация или приветствие')
                ->set_rows(2),

            Field::make('text', 'theme_header_logo_text', 'Текст логотипа')
                ->set_help_text('Текст рядом с логотипом в шапке сайта'),

            Field::make('image', 'theme_header_logo', 'Логотип'),
        ));

    // // Таб: Форма заявки
    // $theme_options->add_tab('Форма заявки', array(
    //     Field::make('text', 'form_inquiry_title', 'Заголовок формы')
    //         ->set_default_value('Оставить заявку'),

    //     Field::make('textarea', 'form_inquiry_description', 'Описание')
    //         ->set_rows(3),

    //     Field::make('text', 'form_inquiry_email_recipient', 'Email для получения заявок')
    //         ->set_help_text('По умолчанию - основной email из "Контакты"')
    //         ->set_width(50),

    //     Field::make('text', 'form_inquiry_button_text', 'Текст кнопки')
    //         ->set_default_value('Отправить')
    //         ->set_width(50),

    //     Field::make('textarea', 'form_inquiry_success_message', 'Сообщение об успехе')
    //         ->set_default_value('Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.')
    //         ->set_rows(2),
    // ));

    // ФУТЕР: Отдельные поля в админке
    Container::make('theme_options', __('Футер'))
        ->add_fields(array(
            Field::make('rich_text', 'theme_footer_copyright', 'Copyright текст')
                ->set_help_text('Например: © 2026 BCC Project. Все права защищены.')
                ->set_rows(2),

            Field::make('complex', 'theme_footer_links', 'Дополнительные ссылки')
                ->set_help_text('Ссылки которые выводятся в правой колонке футера')
                ->add_fields(array(
                    Field::make('text', 'text', 'Текст ссылки')
                        ->set_width(50),
                    Field::make('text', 'url', 'URL ссылки')
                        ->set_width(50),
                ))
                ->set_header_template('<%- text %>'),

            Field::make('textarea', 'theme_footer_address', 'Адрес')
                ->set_help_text('Если не заполнено — используется адрес из "Контакты"')
                ->set_rows(2),

            Field::make('text', 'theme_footer_phone', 'Номер телефона')
                ->set_help_text('Если не заполнено — используется основной номер из "Контакты"')
                ->set_width(50),

            Field::make('text', 'theme_footer_email', 'Email')
                ->set_help_text('Если не заполнено — используется основной email из "Контакты"')
                ->set_width(50),

            Field::make('complex', 'theme_footer_ratings', 'Рейтинги (блок "Отзывы")')
                ->set_help_text('Выводятся в левой колонке футера. Пустые элементы не показываются.')
                ->add_fields(array(
                    Field::make('text', 'title', 'Площадка (например: Яндекс)')
                        ->set_width(70),
                    Field::make('text', 'rating_value', 'Оценка (например: 4,7)')
                        ->set_width(30),
                ))
                ->set_header_template('<%- title %> — <%- rating_value %>'),
        ));

    // ГЛАВНАЯ СТРАНИЦА: Поля по табам (только кастомные)
    // Пока что показываем на всех страницах для диагностики
    // TODO: ограничить только главной страницей после проверки
    $front_page = Container::make('post_meta', 'Главная страница')
               ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'templates/front-page.php');

    $front_page->add_tab('Первый экран', array(

        Field::make('textarea', 'home_hero_title_line', 'Заголовок')
            ->set_width(100),
        Field::make('rich_text', 'home_hero_text_1', 'Текст — абзац 1')
            ->set_width(50),

        Field::make('rich_text', 'home_hero_text_2', 'Текст — абзац 2')
            ->set_width(50),

        // Тип действия
        Field::make('radio', 'home_hero_action_type', 'Тип действия')
            ->add_options(array(
                'button' => 'Кнопка',
                'link'   => 'Ссылка',
            ))
            ->set_default_value('button')
            ->set_width(100),


        Field::make('text', 'home_hero_button_text', 'Название кнопки')
            ->set_width(100)
            ->set_conditional_logic(array(
                array(
                    'field' => 'home_hero_action_type',
                    'value' => 'button',
                    'compare' => '=',
                ),
            )),

        Field::make('text', 'home_hero_link_text', 'Название ссылки')
            ->set_width(50)
            ->set_conditional_logic(array(
                array(
                    'field' => 'home_hero_action_type',
                    'value' => 'link',
                    'compare' => '=',
                ),
            )),

        Field::make('text', 'home_hero_link_url', 'URL ссылки')
            ->set_width(50)
            ->set_conditional_logic(array(
                array(
                    'field' => 'home_hero_action_type',
                    'value' => 'link',
                    'compare' => '=',
                ),
            )),

        Field::make('image', 'home_hero_image_desktop', 'Изображение (desktop)')
            ->set_width(50),

        Field::make('image', 'home_hero_image_mobile', 'Изображение (mobile)')
            ->set_width(50),

        Field::make('text', 'home_hero_bg_text', 'Фоновый текст (БСС)')
            ->set_width(50),

    ));
    $front_page->add_tab('Каталог', array(
        Field::make('text', 'home_catalog_title', 'Заголовок секции'),
        Field::make('textarea', 'home_catalog_description', 'Описание секции')
            ->set_rows(4),
        Field::make('association', 'home_catalog_categories', 'Категории товаров для вывода')
            ->set_help_text('Выберите категории, которые должны отображаться. Если не выбрано — будут показаны все категории.')
            ->set_types(array(
                array(
                    'type' => 'term',
                    'taxonomy' => 'product_category',
                )
            )),
        Field::make('text', 'home_catalog_button_text', 'Текст кнопки')
            ->set_width(50),
        Field::make('text', 'home_catalog_button_url', 'Ссылка кнопки')
            ->set_width(50),
    ));

    $front_page->add_tab('О компании', array(
        Field::make('text', 'home_company_title', 'Заголовок секции'),
        Field::make('textarea', 'home_company_text_1', 'Текст — абзац 1')
            ->set_rows(3),
        Field::make('textarea', 'home_company_text_2', 'Текст — абзац 2')
            ->set_rows(3),
        Field::make('text', 'home_company_button_text', 'Текст кнопки')
            ->set_width(50),
        Field::make('text', 'home_company_button_url', 'Ссылка кнопки')
            ->set_width(50),
        Field::make('text', 'home_company_bg_text', 'Фоновый текст (БСС)')
            ->set_width(50),
        Field::make('image', 'home_company_image_desktop', 'Изображение справа (desktop)')
            ->set_width(50),
        Field::make('image', 'home_company_image_mobile', 'Изображение справа (mobile)')
            ->set_width(50),
        Field::make('complex', 'home_company_stats', 'Карточки статистики')
            ->set_help_text('Добавьте карточки с информацией о компании (кол-во товаров, цветов, лет на рынке и т.д.)')
            ->add_fields(array(
                Field::make('text', 'stat_value', 'Значение (например: <150, +30)')
                    ->set_width(50),
                Field::make('text', 'stat_label', 'Описание (например: Позиций товаров)')
                    ->set_width(50),
                Field::make('image', 'stat_image', 'Изображение фона карточки')
                    ->set_help_text('Опционально. Если не задано, карточка будет без фона.')
            ))
            ->set_header_template('<%- stat_value %> — <%- stat_label %>'),
    ));

    $front_page->add_tab('Преимущества', array(
        Field::make('text', 'home_skills_title', 'Заголовок секции'),
        Field::make('image', 'home_skills_bg_image', 'Фоновое изображение'),
        Field::make('complex', 'home_skills_items', 'Пункты преимуществ')
            ->add_fields(array(
                Field::make('text', 'title', 'Заголовок'),
                Field::make('textarea', 'text', 'Описание')
                    ->set_rows(3),
            ))
            ->set_header_template('<%- title %>'),
    ));

    $front_page->add_tab('FAQ', array(
        Field::make('checkbox', 'home_faq_enabled', 'Показывать секцию FAQ')
            ->set_help_text('Отобразить или скрыть всю секцию с вопросами и ответами')
            ->set_option_value('yes')
            ->set_default_value('yes'),
        Field::make('text', 'home_faq_title', 'Заголовок секции'),
        Field::make('textarea', 'home_faq_description', 'Описание секции')
            ->set_rows(3),
        Field::make('text', 'home_faq_button_text', 'Текст кнопки'),
        Field::make('radio', 'home_faq_action_type', 'Тип действия кнопки')
            ->add_options(array(
                'modal' => 'Открыть модалку (modal-open)',
                'link'  => 'Перейти по ссылке',
            ))
            ->set_default_value('link'),
        Field::make('text', 'home_faq_button_url', 'Ссылка кнопки')
            ->set_help_text('Например: https://example.com или /#section')
            ->set_conditional_logic(array(
                array(
                    'field' => 'home_faq_action_type',
                    'value' => 'link',
                    'compare' => '=',
                ),
            )),
        Field::make('text', 'home_faq_secondary_button_text', 'Текст кнопки "Еще вопросы"')
            ->set_default_value('Еще вопросы'),
        Field::make('radio', 'home_faq_secondary_action_type', 'Тип действия кнопки "Еще вопросы"')
            ->add_options(array(
                'modal' => 'Открыть модалку (modal-open)',
                'link'  => 'Перейти по ссылке',
            ))
            ->set_default_value('link'),
        Field::make('text', 'home_faq_secondary_button_url', 'Ссылка кнопки "Еще вопросы"')
            ->set_default_value('#')
            ->set_conditional_logic(array(
                array(
                    'field' => 'home_faq_secondary_action_type',
                    'value' => 'link',
                    'compare' => '=',
                ),
            )),
        Field::make('complex', 'home_faq_items', 'Вопросы и ответы')
            ->set_layout('tabbed-vertical')
            ->add_fields(array(
                Field::make('text', 'question', 'Вопрос'),
                Field::make('textarea', 'answer', 'Ответ')
                    ->set_rows(4),
            ))
            ->set_header_template('<%- question %>'),  
    ));

    // Поля для товаров (по табам)
    $product = Container::make('post_meta', 'Информация о товаре')
        ->where('post_type', '=', 'product');

    $product->add_tab('Основное', array(
        Field::make('text', 'product_price', 'Цена (руб/м2)')
            ->set_help_text('Укажите цену товара за квадратный метр'),

        Field::make('rich_text', 'product_head_text', 'Текст в шапке товара (product-head)')
            ->set_help_text('Если не заполнено — будет взят первый абзац (<p>) из основного контента товара.'),
    ));

    $product->add_tab('Галерея', array(
        Field::make('media_gallery', 'product_gallery', 'Галерея изображений')
            ->set_help_text('Добавьте изображения товара для слайдера'),
    ));

    $product->add_tab('Характеристики', array(
        Field::make('complex', 'product_specifications', 'Спецификация')
            ->set_help_text('Технические характеристики товара')
            ->set_layout('tabbed-horizontal')
            ->add_fields(array(
                Field::make('text', 'label', 'Название характеристики')
                    ->set_width(50),
                Field::make('text', 'spec_value', 'Значение')
                    ->set_width(50),
            ))
            ->set_header_template('<%- label %>'),
    ));

    $product->add_tab('Цвета', array(
        Field::make('checkbox', 'product_colors_enabled', 'Показывать блок "Цветовая палитра"')
            ->set_help_text('Можно выключить блок целиком для конкретного товара.')
            ->set_option_value('yes'),

        Field::make('text', 'product_colors_title', 'Заголовок блока (палитра)')
            ->set_help_text('Например: "Цветовая палитра модели".'),

        Field::make('textarea', 'product_colors_description', 'Текст блока (палитра)')
            ->set_help_text('Описание под заголовком.')
            ->set_rows(4),

        Field::make('checkbox', 'product_colors_show_right_image', 'Показывать картинку справа (палитра)')
            ->set_option_value('yes'),

        Field::make('image', 'product_colors_right_image', 'Картинка справа (палитра)')
            ->set_help_text('Если не задано — будет использоваться стандартная картинка темы.'),

        Field::make('association', 'product_palette_colors', 'Цвета (выбор)')
            ->set_help_text('Выберите заранее созданные "Цвета". Это основной способ заполнения палитры.')
            ->set_types(array(
                array(
                    'type' => 'term',
                    'taxonomy' => 'product_color',
                )
            ))

    ));

    $product->add_tab('Схема', array(
        Field::make('image', 'product_schema', 'Схема (изображение)')
            ->set_help_text('Техническая схема или чертеж товара'),

        Field::make('rich_text', 'product_schema_description', 'Описание схемы')
            ->set_help_text('Описание для блока со схемой')
            ->set_rows(4),
    ));

    // Поля для категорий товаров
    Container::make('term_meta', 'Категория товаров')
        ->where('term_taxonomy', '=', 'product_category')
        ->add_fields(array(
            Field::make('image', 'product_category_menu_image', 'Изображение (меню каталога в шапке)')
                ->set_help_text('Используется в выпадающем каталоге в шапке (левая колонка).'),
        ));

    // Поля для цветов товаров
    Container::make('term_meta', 'Цвет')
        ->where('term_taxonomy', '=', 'product_color')
        ->add_fields(array(
            Field::make('image', 'product_color_image', 'Изображение цвета')
                ->set_help_text('Используется в блоке "Цветовая палитра" у товара.'),
        ));

    // СТРАНИЦА: Доставка и оплата
    $payment_delivery = Container::make('post_meta', 'Доставка и оплата')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'templates/template-payment-delivery.php');



    $payment_delivery->add_tab('Доставка', array(
        Field::make('text', 'pd_delivery_banner_text', 'Текст в банере (БСС)'),
        Field::make('image', 'pd_delivery_banner_image', 'Изображение банера'),
        Field::make('rich_text', 'pd_delivery_content', 'Контент раздела (может содержать HTML)'),
    ));

    $payment_delivery->add_tab('Оплата', array(
        Field::make('text', 'pd_payment_banner_text', 'Текст в банере (БСС)'),
        Field::make('image', 'pd_payment_banner_image', 'Изображение банера'),
        Field::make('rich_text', 'pd_payment_content', 'Контент раздела (может содержать HTML)')
            ->set_rows(8),
    ));

    $payment_delivery->add_tab('График работы', array(
        Field::make('text', 'pd_schedule_banner_text', 'Текст в банере (БСС)'),
        Field::make('image', 'pd_schedule_banner_image', 'Изображение банера'),
        Field::make('textarea', 'pd_schedule_content', 'Контент раздела (может содержать HTML)')
            ->set_rows(8),
    ));

    // СТРАНИЦА: О Компании
    $company = Container::make('post_meta', 'О Компании')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'templates/template-company.php');

    $company->add_tab('О компании блок', array(      
        Field::make('text', 'company_goals_title', 'Заголовок целей')
            ->set_default_value('Цели и задачи компании'),
        Field::make('complex', 'company_goals_items', 'Пункты целей и задач')
            ->add_fields(array(
                Field::make('textarea', 'text', 'Текст пункта')
                    ->set_rows(6),
            ))
            ->set_header_template('Пункт'),
        Field::make('complex', 'company_stats', 'Статистика')
            ->add_fields(array(
                Field::make('text', 'stat_value', 'Значение (например: 30+)')
                    ->set_width(50),
                Field::make('textarea', 'label', 'Описание')
                    ->set_rows(3)
                    ->set_width(50),
            ))
            ->set_header_template('<%- stat_value %>'),
        Field::make('image', 'company_card_image', 'Изображение карточки'),
    ));

    $company->add_tab('Каталоги продукции', array(
        Field::make('text', 'company_product_title', 'Заголовок секции')
            ->set_default_value('Каталоги продукции 2026'),
        Field::make('textarea', 'company_product_description', 'Описание секции')
            ->set_rows(4),
        Field::make('text', 'company_product_button_text', 'Текст кнопки "К покупкам"')
            ->set_default_value('К покупкам'),
        Field::make('text', 'company_product_button_url', 'Ссылка кнопки "К покупкам"')
            ->set_default_value('#'),
        Field::make('complex', 'company_product_items', 'Каталоги')
            ->add_fields(array(
                Field::make('text', 'catalog_title', 'Название каталога'),
                Field::make('textarea', 'catalog_description', 'Описание каталога'),
                
                Field::make('image', 'catalog_image', 'Изображение'),
                Field::make('text', 'catalog_button_text', 'Текст кнопки')
                    ->set_default_value('Скачать каталог PDF'),
                Field::make('file', 'catalog_pdf_file', 'PDF файл')
                    ->set_help_text('Загружение PDF документа'),
            ))
            ->set_header_template('<%- catalog_title %>'),
    ));

    // СТРАНИЦА: Контакты
    $contact = Container::make('post_meta', 'Контакты')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'templates/template-contact.php');

    $contact->add_tab('Основная информация', array(
        Field::make('text', 'contact_title', 'Заголовок страницы')
            ->set_default_value('Контакты'),
        Field::make('textarea', 'contact_address', 'Адрес офиса')
            ->set_rows(3),
        Field::make('text', 'contact_phone_1', 'Первый телефон'),
        Field::make('text', 'contact_phone_2', 'Второй телефон'),
        Field::make('text', 'contact_email', 'Email'),
    ));

    $contact->add_tab('График работы', array(
        Field::make('complex', 'contact_office_schedule', 'График работы офиса')
            ->add_fields(array(
                Field::make('text', 'day', 'День недели'),
                Field::make('text', 'time', 'Время'),
            ))
            ->set_header_template('<%- day %>'),
        Field::make('complex', 'contact_warehouse_schedule', 'График работы склада')
            ->add_fields(array(
                Field::make('text', 'day', 'День недели'),
                Field::make('text', 'time', 'Время'),
            ))
            ->set_header_template('<%- day %>'),
    ));

    $contact->add_tab('Реквизиты', array(
        Field::make('complex', 'contact_details', 'Реквизиты компании')
            ->add_fields(array(
                Field::make('text', 'label', 'Название (например: ИНН)'),
                Field::make('text', 'value_text', 'Значение'),
            ))
            ->set_header_template('<%- label %>'),
        Field::make('text', 'contact_map_url', 'URL Яндекс карты')
            ->set_help_text('Вставьте ссылку src из встроенной карты Яндекс (например: https://yandex.ru/map-widget/v1/?um=constructor%3A...'),
    ));

    // СТРАНИЦА: Сертификаты и лицензии
    $certs = Container::make('post_meta', 'Сертификаты')
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'templates/template-certificates.php');

  
    $certs->add_tab('Гарантии и сертификаты', array(
        Field::make('complex', 'cert_guarantees', 'Сертификаты')
            ->add_fields(array(
                Field::make('text', 'title', 'Название сертификата'),
                Field::make('image', 'image', 'Изображение'),
            ))
            ->set_header_template('<%- title %>'),
    ));

    $certs->add_tab('Техническая документация', array(
        Field::make('complex', 'cert_technical', 'Техническая документация')
            ->add_fields(array(
                Field::make('text', 'title', 'Название документа'),
                Field::make('image', 'image', 'Изображение'),
            ))
            ->set_header_template('<%- title %>'),
    ));
}

add_action('after_setup_theme', 'crb_load');
function crb_load()
{
    require_once get_template_directory() . '/inc/composer/vendor/autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
}

