# 🧩 Quick Price Edit for WooCommerce

A simple WordPress plugin for quickly editing WooCommerce product prices and SKUs from one admin page.

## ✨ Features

* 💰 Edit **Regular Price**
* 🏷️ Edit **Sale Price**
* 🔖 Edit **SKU**
* 📦 Supports **simple products**
* 🔄 Supports **variable products and variations**
* 📥 Import prices from **CSV**
* 📤 Export products and prices to **CSV**
* 🔎 Quickly find products with **Ctrl + F** / **Cmd + F**
* 🌍 Supports **English, Ukrainian, Russian, German and Polish**
* 🔐 Includes basic security checks and validation

## 📋 Requirements

* WordPress 5.0+
* WooCommerce
* PHP 7.4+

## 🚀 How It Works

After installation, the plugin adds a separate page in the WordPress admin panel.

You can see your products in a table and quickly change:

**SKU → Regular Price → Sale Price**

Then save the changes.

CSV import and export can be used to edit a large number of products at once.

## 👨‍💻 Author

**Sasha Zimin**

[zimin.dev](https://zimin.dev?utm_source=chatgpt.com)

## 📌 Plugin Version

**1.7.0**

## 📄 Source Code

The complete plugin source code is included below.

 ```php
<?php
/**
 * Plugin Name: Quick Price Edit for WooCommerce
 * Plugin URI: https://zimin.dev
 * Description: Adds a separate admin page to quickly edit product SKU, prices (regular and sale) for simple products and variations, with CSV import/export for Excel. Displays a configurable number of products per page with browser-based Ctrl+F search. Supports English, Ukrainian, Russian, German and Polish.
 * Version: 1.7.0
 * Author: Sasha Zimin
 * Author URI: https://zimin.dev
 * Text Domain: quick-price-edit
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'QPE_VERSION', '1.7.0' );

/*
 * Keep the catalog page reasonably small. This can be overridden in wp-config.php:
 *
 * define( 'QPE_PER_PAGE', 100 );
 */
if ( ! defined( 'QPE_PER_PAGE' ) ) {
    define( 'QPE_PER_PAGE', 1000 );
}

add_action( 'plugins_loaded', 'qpe_init' );

function qpe_init() {
    load_plugin_textdomain(
        'quick-price-edit',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );

    add_filter( 'gettext', 'qpe_translate_strings', 20, 3 );

    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'qpe_woocommerce_missing_notice' );
        return;
    }

    add_action( 'admin_menu', 'qpe_add_admin_menu' );
    add_action( 'admin_enqueue_scripts', 'qpe_enqueue_admin_assets' );
    add_action( 'admin_post_qpe_export', 'qpe_export_csv' );
    add_action( 'admin_post_qpe_import', 'qpe_import_csv' );
}

/**
 * Inline translations for UK / RU / DE / PL.
 *
 * NOTE: keys here must match the exact English strings passed to
 * esc_html_e() / __() elsewhere in this file, otherwise the gettext
 * filter below will silently fail to find a match and fall back to English.
 *
 * @return array<string, array<string, string>>
 */
function qpe_get_translations() {
    return array(
        'uk' => array(
            'Quick Price Edit' => 'Редагування цін',
            'requires WooCommerce to be installed and active.' => 'потребує встановленого та активного WooCommerce.',
            'You do not have sufficient permissions to access this page.' => 'У вас недостатньо прав для доступу до цієї сторінки.',
            'Prices updated successfully.' => 'Ціни успішно оновлено.',
            'Tip:' => 'Порада:',
            "Use your browser's built-in search to quickly find a product — press" => 'Скористайтеся вбудованим пошуком браузера, щоб швидко знайти товар — натисніть',
            '(or' => '(або',
            'on Mac) and search by product name or SKU on this page.' => 'на Mac) і шукайте за назвою товару або артикулом на цій сторінці.',
            'Product' => 'Товар',
            'SKU' => 'Артикул',
            'Regular Price' => 'Звичайна ціна',
            'Sale Price' => 'Ціна розпродажу',
            'Variable' => 'Варіативний',
            'No products found.' => 'Товарів не знайдено.',
            'Save Changes' => 'Зберегти зміни',
            'Export to Excel' => 'Експорт в Excel',
            'Import from Excel' => 'Імпорт з Excel',
            'Import Prices' => 'Імпортувати ціни',
            'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Завантажте CSV-файл, експортований з Excel. Товари шукаються спочатку за артикулом, а якщо артикул порожній — за назвою товару.',
            'Import finished:' => 'Імпорт завершено:',
            'updated' => 'оновлено',
            'failed' => 'помилок',
            'not found' => 'не знайдено',
            'No file was uploaded.' => 'Файл не завантажено.',
            'Could not read the uploaded file.' => 'Не вдалося прочитати завантажений файл.',
            'The uploaded file is too large.' => 'Завантажений файл занадто великий.',
            'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Деякі поля не оновлено через некоректне або дубльоване значення, або відсутність прав на редагування товару.',
            'Plugin developed by Sasha Zimin.' => 'Плагін створив Sasha Zimin',
            'Order plugin development at' => '— замовляй свій на',
            'Created and developed by' => 'Створено та розроблено',
            'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Редагуйте артикули, звичайні та акційні ціни всього каталогу WooCommerce в одному місці.',
        ),
        'ru' => array(
            'Quick Price Edit' => 'Быстрое редактирование цен',
            'requires WooCommerce to be installed and active.' => 'требует установленного и активного WooCommerce.',
            'You do not have sufficient permissions to access this page.' => 'У вас недостаточно прав для доступа к этой странице.',
            'Prices updated successfully.' => 'Цены успешно обновлены.',
            'Tip:' => 'Совет:',
            "Use your browser's built-in search to quickly find a product — press" => 'Используйте встроенный поиск браузера, чтобы быстро найти товар — нажмите',
            '(or' => '(или',
            'on Mac) and search by product name or SKU on this page.' => 'на Mac) и ищите по названию товара или артикулу на этой странице.',
            'Product' => 'Товар',
            'SKU' => 'Артикул',
            'Regular Price' => 'Обычная цена',
            'Sale Price' => 'Цена распродажи',
            'Variable' => 'Вариативный',
            'No products found.' => 'Товары не найдены.',
            'Save Changes' => 'Сохранить изменения',
            'Export to Excel' => 'Экспорт в Excel',
            'Import from Excel' => 'Импорт из Excel',
            'Import Prices' => 'Импортировать цены',
            'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Загрузите CSV-файл, экспортированный из Excel. Товары ищутся сначала по артикулу, а если артикул пустой — по названию товара.',
            'Import finished:' => 'Импорт завершён:',
            'updated' => 'обновлено',
            'failed' => 'ошибок',
            'not found' => 'не найдено',
            'No file was uploaded.' => 'Файл не загружен.',
            'Could not read the uploaded file.' => 'Не удалось прочитать загруженный файл.',
            'The uploaded file is too large.' => 'Загруженный файл слишком большой.',
            'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Некоторые поля не обновлены из-за некорректного или дублированного значения, либо отсутствия прав на редактирование товара.',
            'Plugin developed by Sasha Zimin.' => 'Плагин создал Sasha Zimin',
            'Order plugin development at' => '— закажи свой на',
            'Created and developed by' => 'Создано и разработано',
            'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Редактируйте артикулы, обычные и акционные цены всего каталога WooCommerce в одном месте.',
        ),
        'de' => array(
            'Quick Price Edit' => 'Schnelle Preisbearbeitung',
            'requires WooCommerce to be installed and active.' => 'erfordert ein installiertes und aktives WooCommerce.',
            'You do not have sufficient permissions to access this page.' => 'Sie haben nicht genügend Berechtigungen für den Zugriff auf diese Seite.',
            'Prices updated successfully.' => 'Preise erfolgreich aktualisiert.',
            'Tip:' => 'Tipp:',
            "Use your browser's built-in search to quickly find a product — press" => 'Verwenden Sie die integrierte Browsersuche, um ein Produkt schnell zu finden — drücken Sie',
            '(or' => '(oder',
            'on Mac) and search by product name or SKU on this page.' => 'auf dem Mac) und suchen Sie auf dieser Seite nach Produktname oder Artikelnummer.',
            'Product' => 'Produkt',
            'SKU' => 'Artikelnummer',
            'Regular Price' => 'Regulärer Preis',
            'Sale Price' => 'Angebotspreis',
            'Variable' => 'Variabel',
            'No products found.' => 'Keine Produkte gefunden.',
            'Save Changes' => 'Änderungen speichern',
            'Export to Excel' => 'Nach Excel exportieren',
            'Import from Excel' => 'Aus Excel importieren',
            'Import Prices' => 'Preise importieren',
            'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Laden Sie eine aus Excel exportierte CSV-Datei hoch. Produkte werden zuerst über die SKU, dann über den Produktnamen (falls SKU leer) zugeordnet.',
            'Import finished:' => 'Import abgeschlossen:',
            'updated' => 'aktualisiert',
            'failed' => 'fehlgeschlagen',
            'not found' => 'nicht gefunden',
            'No file was uploaded.' => 'Es wurde keine Datei hochgeladen.',
            'Could not read the uploaded file.' => 'Die hochgeladene Datei konnte nicht gelesen werden.',
            'The uploaded file is too large.' => 'Die hochgeladene Datei ist zu groß.',
            'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Einige Felder wurden aufgrund eines ungültigen oder doppelten Werts oder fehlender Bearbeitungsberechtigung nicht aktualisiert.',
            'Plugin developed by Sasha Zimin.' => 'Plugin von Sasha Zimin',
            'Order plugin development at' => '— dein eigenes Plugin gibt es auf',
            'Created and developed by' => 'Erstellt und entwickelt von',
            'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Bearbeiten Sie SKU, reguläre und Angebotspreise Ihres gesamten WooCommerce-Katalogs an einem Ort.',
        ),
        'pl' => array(
            'Quick Price Edit' => 'Szybka edycja cen',
            'requires WooCommerce to be installed and active.' => 'wymaga zainstalowanego i aktywnego WooCommerce.',
            'You do not have sufficient permissions to access this page.' => 'Nie masz wystarczających uprawnień, aby uzyskać dostęp do tej strony.',
            'Prices updated successfully.' => 'Ceny zostały pomyślnie zaktualizowane.',
            'Tip:' => 'Wskazówka:',
            "Use your browser's built-in search to quickly find a product — press" => 'Użyj wbudowanej wyszukiwarki przeglądarki, aby szybko znaleźć produkt — naciśnij',
            '(or' => '(lub',
            'on Mac) and search by product name or SKU on this page.' => 'na Macu) i wyszukaj według nazwy produktu lub SKU na tej stronie.',
            'Product' => 'Produkt',
            'SKU' => 'SKU',
            'Regular Price' => 'Cena regularna',
            'Sale Price' => 'Cena promocyjna',
            'Variable' => 'Zmienny',
            'No products found.' => 'Nie znaleziono produktów.',
            'Save Changes' => 'Zapisz zmiany',
            'Export to Excel' => 'Eksport do Excela',
            'Import from Excel' => 'Import z Excela',
            'Import Prices' => 'Importuj ceny',
            'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Prześlij plik CSV wyeksportowany z Excela. Produkty są dopasowywane najpierw po SKU, a jeśli SKU jest puste — po nazwie produktu.',
            'Import finished:' => 'Import zakończony:',
            'updated' => 'zaktualizowano',
            'failed' => 'błędów',
            'not found' => 'nie znaleziono',
            'No file was uploaded.' => 'Nie przesłano pliku.',
            'Could not read the uploaded file.' => 'Nie udało się odczytać przesłanego pliku.',
            'The uploaded file is too large.' => 'Przesłany plik jest zbyt duży.',
            'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Niektóre pola nie zostały zaktualizowane z powodu nieprawidłowej lub zduplikowanej wartości bądź braku uprawnień do edycji produktu.',
            'Plugin developed by Sasha Zimin.' => 'Wtyczkę stworzył Sasha Zimin',
            'Order plugin development at' => '— zamów swoją na',
            'Created and developed by' => 'Stworzone i opracowane przez',
            'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Edytuj SKU, ceny regularne i promocyjne całego katalogu WooCommerce w jednym miejscu.',
        ),
    );
}

function qpe_translate_strings( $translated, $original, $domain ) {
    if ( 'quick-price-edit' !== $domain ) {
        return $translated;
    }

    $locale = determine_locale();
    $lang   = strtolower( substr( $locale, 0, 2 ) );

    if ( 'en' === $lang ) {
        return $translated;
    }

    $translations = qpe_get_translations();

    return isset( $translations[ $lang ][ $original ] )
        ? $translations[ $lang ][ $original ]
        : $translated;
}

function qpe_woocommerce_missing_notice() {
    echo '<div class="notice notice-error"><p><strong>Quick Price Edit</strong> ' .
        esc_html__( 'requires WooCommerce to be installed and active.', 'quick-price-edit' ) .
        '</p></div>';
}

function qpe_add_admin_menu() {
    add_menu_page(
        __( 'Quick Price Edit', 'quick-price-edit' ),
        __( 'Quick Price Edit', 'quick-price-edit' ),
        'manage_woocommerce',
        'quick-price-edit',
        'qpe_render_admin_page',
        'dashicons-tag',
        56
    );
}

function qpe_enqueue_admin_assets( $hook ) {
    if ( 'toplevel_page_quick-price-edit' !== $hook ) {
        return;
    }

    $css = '
        .qpe-ctrl-f-notice {
            margin: 20px 0;
            padding: 18px 22px;
            background: #fff8e5;
            border-left: 5px solid #ffb900;
            border-radius: 4px;
            font-size: 17px;
            line-height: 1.6;
        }
        .qpe-ctrl-f-notice strong {
            font-size: 18px;
        }
        .qpe-ctrl-f-notice kbd {
            display: inline-block;
            background: #f0f0f1;
            border: 1px solid #c3c4c7;
            border-bottom-width: 2px;
            border-radius: 4px;
            padding: 2px 8px;
            margin: 0 2px;
            font-family: Consolas, Monaco, monospace;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
        }
        .qpe-import-panel {
            margin: 0 0 20px;
            padding: 14px 18px;
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 4px;
        }
        .qpe-import-panel summary {
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #2271b1;
            outline: none;
        }
        .qpe-import-panel summary::-webkit-details-marker {
            color: #2271b1;
        }
        .qpe-import-panel[open] summary {
            margin-bottom: 10px;
        }
        .qpe-import-panel p.qpe-import-help {
            margin: 0 0 12px;
            color: #646970;
            font-size: 13px;
        }
        .qpe-import-panel form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }
        .qpe-import-result {
            margin: 0 0 20px;
            padding: 12px 18px;
            border-left: 5px solid #00a32a;
            background: #ecf7ed;
            border-radius: 4px;
            font-size: 14px;
        }
        .qpe-import-result.qpe-import-result-error {
            border-left-color: #d63638;
            background: #fcf0f1;
        }
        .qpe-import-result strong {
            font-weight: 600;
        }
        .qpe-table-wrap {
            margin-top: 15px;
            overflow-x: auto;
        }
        .qpe-table-wrap input.wc_input_price {
            max-width: 140px;
        }
        .qpe-table-wrap input.qpe_input_sku {
            max-width: 160px;
            width: 100%;
        }
        .qpe-parent-row td {
            background: #f6f7f7;
        }
        .qpe-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 20px 0 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #dcdcde;
        }
        .qpe-header-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #2271b1, #72aee6);
            color: #fff;
            box-shadow: 0 2px 6px rgba(34, 113, 177, 0.3);
        }
        .qpe-header-icon svg {
            width: 26px;
            height: 26px;
            fill: currentColor;
        }
        .qpe-header-text h1 {
            margin: 0;
            padding: 0;
            font-size: 23px;
            line-height: 1.3;
        }
        .qpe-tagline {
            margin: 4px 0 0;
            font-size: 14px;
            color: #646970;
        }
        .qpe-credit {
            margin: 6px 0 0;
            font-size: 12px;
            color: #8c8f94;
        }
        .qpe-credit a {
            color: #2271b1;
            font-weight: 600;
            text-decoration: none;
        }
        .qpe-credit a:hover {
            text-decoration: underline;
        }
        .qpe-floating-actions {
            position: fixed;
            top: 50%;
            right: 24px;
            transform: translateY(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        .qpe-floating-save {
            padding: 10px 20px;
            height: auto;
            line-height: 1.4;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }
        .qpe-floating-save:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }
        .qpe-floating-export {
            padding: 8px 16px;
            height: auto;
            line-height: 1.4;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            display: inline-block;
        }
        .qpe-floating-export:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }
        .qpe-floating-notice {
            padding: 8px 14px;
            border-radius: 0;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            max-width: 260px;
            text-align: right;
            transition: opacity 0.4s ease;
        }
        .qpe-floating-notice.qpe-fade-out {
            opacity: 0;
        }
        .qpe-floating-notice-success {
            background: #00a32a;
        }
        .qpe-floating-notice-warning {
            background: #dba617;
        }
        @media screen and (max-width: 782px) {
            .qpe-floating-actions {
                top: auto;
                bottom: 20px;
                right: 16px;
                transform: none;
            }
        }
    ';

    wp_register_style( 'qpe-admin-style', false, array(), QPE_VERSION );
    wp_enqueue_style( 'qpe-admin-style' );
    wp_add_inline_style( 'qpe-admin-style', $css );
}

function qpe_render_admin_page() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        wp_die(
            esc_html__( 'You do not have sufficient permissions to access this page.', 'quick-price-edit' ),
            '',
            array( 'response' => 403 )
        );
    }

    $save_result = null;

    if ( isset( $_POST['qpe_save_prices'] ) ) {
        $save_result = qpe_process_save();
    }

    $paged = isset( $_GET['paged'] )
        ? max( 1, absint( wp_unslash( $_GET['paged'] ) ) )
        : 1;

    $args = array(
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => QPE_PER_PAGE,
        'paged'                  => $paged,
        'orderby'                => 'title',
        'order'                  => 'ASC',
        'no_found_rows'          => false,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'cache_results'          => true,
    );

    $products_query = new WP_Query( $args );
    ?>
    <div class="wrap">
        <div class="qpe-header">
            <div class="qpe-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.41l9 9c.36.36.86.59 1.41.59.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>
            </div>
            <div class="qpe-header-text">
                <h1><?php esc_html_e( 'Quick Price Edit', 'quick-price-edit' ); ?></h1>
                <p class="qpe-tagline"><?php esc_html_e( 'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.', 'quick-price-edit' ); ?></p>
                <p class="qpe-credit">
                    ⚡ <?php esc_html_e( 'Created and developed by', 'quick-price-edit' ); ?> <a href="https://zimin.dev" target="_blank" rel="noopener noreferrer">Sasha Zimin</a>
                </p>
            </div>
        </div>

        <?php
        /* --------------------------------------------------------------
         * Import results notice (after redirect from admin-post).
         * ------------------------------------------------------------ */
        if ( isset( $_GET['qpe_import_done'] ) ) {
            $imp_updated  = isset( $_GET['qpe_updated'] ) ? absint( $_GET['qpe_updated'] ) : 0;
            $imp_failed   = isset( $_GET['qpe_failed'] ) ? absint( $_GET['qpe_failed'] ) : 0;
            $imp_notfound = isset( $_GET['qpe_notfound'] ) ? absint( $_GET['qpe_notfound'] ) : 0;

            echo '<div class="qpe-import-result">';
            echo '<strong>' . esc_html__( 'Import finished:', 'quick-price-edit' ) . '</strong> ';
            echo esc_html( $imp_updated ) . ' ' . esc_html__( 'updated', 'quick-price-edit' ) . ', ';
            echo esc_html( $imp_failed ) . ' ' . esc_html__( 'failed', 'quick-price-edit' ) . ', ';
            echo esc_html( $imp_notfound ) . ' ' . esc_html__( 'not found', 'quick-price-edit' ) . '.';
            echo '</div>';
        } elseif ( isset( $_GET['qpe_import_error'] ) ) {
            $err_code = sanitize_key( wp_unslash( $_GET['qpe_import_error'] ) );
            $err_map  = array(
                'nofile'  => __( 'No file was uploaded.', 'quick-price-edit' ),
                'unread'  => __( 'Could not read the uploaded file.', 'quick-price-edit' ),
                'toolarge'=> __( 'The uploaded file is too large.', 'quick-price-edit' ),
            );
            $err_msg = isset( $err_map[ $err_code ] ) ? $err_map[ $err_code ] : __( 'Could not read the uploaded file.', 'quick-price-edit' );

            echo '<div class="qpe-import-result qpe-import-result-error">';
            echo '<strong>' . esc_html( $err_msg ) . '</strong>';
            echo '</div>';
        }
        ?>

        <div class="qpe-ctrl-f-notice">
            <strong><?php esc_html_e( 'Tip:', 'quick-price-edit' ); ?></strong>
            <?php esc_html_e( "Use your browser's built-in search to quickly find a product — press", 'quick-price-edit' ); ?>
            <kbd>Ctrl</kbd> + <kbd>F</kbd>
            <?php esc_html_e( '(or', 'quick-price-edit' ); ?>
            <kbd>Cmd</kbd> + <kbd>F</kbd>
            <?php esc_html_e( 'on Mac) and search by product name or SKU on this page.', 'quick-price-edit' ); ?>
        </div>

        <details class="qpe-import-panel">
            <summary><?php esc_html_e( 'Import from Excel', 'quick-price-edit' ); ?></summary>
            <p class="qpe-import-help">
                <?php esc_html_e( 'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.', 'quick-price-edit' ); ?>
            </p>
            <form method="post"
                  action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                  enctype="multipart/form-data">
                <input type="hidden" name="action" value="qpe_import" />
                <?php wp_nonce_field( 'qpe_import_action', 'qpe_import_nonce' ); ?>
                <input type="file" name="qpe_import_file" accept=".csv,text/csv" required />
                <button type="submit" class="button button-primary">
                    <?php esc_html_e( 'Import Prices', 'quick-price-edit' ); ?>
                </button>
            </form>
        </details>

        <form method="post">
            <?php wp_nonce_field( 'qpe_save_prices_action', 'qpe_nonce' ); ?>

            <div class="qpe-floating-actions">
                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=qpe_export' ), 'qpe_export_action', 'qpe_export_nonce' ) ); ?>"
                   class="button qpe-floating-export">
                    <?php esc_html_e( 'Export to Excel', 'quick-price-edit' ); ?>
                </a>
                <button type="submit" name="qpe_save_prices" class="button button-primary qpe-floating-save" value="1">
                    <?php esc_html_e( 'Save Changes', 'quick-price-edit' ); ?>
                </button>

                <?php if ( ! empty( $save_result['updated'] ) ) : ?>
                    <div class="qpe-floating-notice qpe-floating-notice-success">
                        <?php esc_html_e( 'Prices updated successfully.', 'quick-price-edit' ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $save_result['failed'] ) ) : ?>
                    <div class="qpe-floating-notice qpe-floating-notice-warning">
                        <?php esc_html_e( 'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.', 'quick-price-edit' ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $save_result['updated'] ) || ! empty( $save_result['failed'] ) ) : ?>
            <script>
                document.addEventListener( 'DOMContentLoaded', function () {
                    var qpeNotices = document.querySelectorAll( '.qpe-floating-notice' );

                    qpeNotices.forEach( function ( notice ) {
                        setTimeout( function () {
                            notice.classList.add( 'qpe-fade-out' );

                            setTimeout( function () {
                                notice.style.display = 'none';
                            }, 400 );
                        }, 4000 );
                    } );
                } );
            </script>
            <?php endif; ?>

            <div class="qpe-table-wrap">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Product', 'quick-price-edit' ); ?></th>
                            <th><?php esc_html_e( 'SKU', 'quick-price-edit' ); ?></th>
                            <th><?php esc_html_e( 'Regular Price', 'quick-price-edit' ); ?></th>
                            <th><?php esc_html_e( 'Sale Price', 'quick-price-edit' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ( $products_query->have_posts() ) {
                        while ( $products_query->have_posts() ) {
                            $products_query->the_post();

                            $product = wc_get_product( get_the_ID() );

                            if ( ! $product ) {
                                continue;
                            }

                            if ( $product->is_type( 'simple' ) ) {
                                qpe_render_price_row( $product );
                            } elseif ( $product->is_type( 'variable' ) ) {
                                echo '<tr class="qpe-parent-row">';
                                echo '<td><strong>' . esc_html( $product->get_name() ) . '</strong> (' .
                                    esc_html__( 'Variable', 'quick-price-edit' ) . ')</td>';
                                echo '<td>' . qpe_render_sku_input( $product ) . '</td>';
                                echo '<td colspan="2"></td>';
                                echo '</tr>';

                                /*
                                 * get_children() is intentionally used instead of loading
                                 * full variation WP_Post objects. Each variation is then
                                 * converted to a WC_Product_Variation only when rendered.
                                 */
                                foreach ( $product->get_children() as $variation_id ) {
                                    $variation = wc_get_product( $variation_id );

                                    if ( $variation instanceof WC_Product_Variation ) {
                                        qpe_render_price_row( $variation, true );
                                    }
                                }
                            }
                        }
                    } else {
                        echo '<tr><td colspan="4">' .
                            esc_html__( 'No products found.', 'quick-price-edit' ) .
                            '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <?php
            $total_pages = (int) $products_query->max_num_pages;

            if ( $total_pages > 1 ) {
                echo '<div class="tablenav"><div class="tablenav-pages">';
                echo wp_kses_post(
                    paginate_links(
                        array(
                            'base'      => esc_url_raw( add_query_arg( 'paged', '%#%' ) ),
                            'format'    => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total'     => $total_pages,
                            'current'   => $paged,
                        )
                    )
                );
                echo '</div></div>';
            }
            ?>

            <p class="submit">
                <button type="submit" name="qpe_save_prices" class="button button-primary" value="1">
                    <?php esc_html_e( 'Save Changes', 'quick-price-edit' ); ?>
                </button>
            </p>
        </form>
    </div>
    <?php

    wp_reset_postdata();
}

/**
 * Build the SKU input field markup.
 *
 * @param WC_Product $product
 * @return string
 */
function qpe_render_sku_input( $product ) {
    return sprintf(
        '<input type="text" name="qpe_prices[%1$s][sku]" value="%2$s" class="qpe_input_sku" autocomplete="off" />',
        esc_attr( $product->get_id() ),
        esc_attr( $product->get_sku( 'edit' ) )
    );
}

/**
 * Render a single row for a simple product or a variation.
 *
 * @param WC_Product $product
 * @param bool       $is_variation
 */
function qpe_render_price_row( $product, $is_variation = false ) {
    $product_id    = $product->get_id();
    $regular_price = $product->get_regular_price( 'edit' );
    $sale_price    = $product->get_sale_price( 'edit' );
    $indent        = $is_variation ? 'style="padding-left: 30px;"' : '';
    ?>
    <tr>
        <td <?php echo $indent; ?>>
            <?php if ( $is_variation ) : ?>
                &mdash; <?php echo esc_html( $product->get_name() ); ?>
            <?php else : ?>
                <strong><?php echo esc_html( $product->get_name() ); ?></strong>
            <?php endif; ?>
        </td>
        <td><?php echo qpe_render_sku_input( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></td>
        <td>
            <input
                type="text"
                name="qpe_prices[<?php echo esc_attr( $product_id ); ?>][regular_price]"
                value="<?php echo esc_attr( $regular_price ); ?>"
                class="wc_input_price"
                inputmode="decimal"
                autocomplete="off"
            />
        </td>
        <td>
            <input
                type="text"
                name="qpe_prices[<?php echo esc_attr( $product_id ); ?>][sale_price]"
                value="<?php echo esc_attr( $sale_price ); ?>"
                class="wc_input_price"
                inputmode="decimal"
                autocomplete="off"
            />
        </td>
    </tr>
    <?php
}

/**
 * Validate a submitted price.
 *
 * Empty values are allowed because WooCommerce uses an empty price to clear it.
 *
 * @param mixed $value
 * @return string|WP_Error
 */
function qpe_validate_price( $value ) {
    if ( ! is_scalar( $value ) ) {
        return new WP_Error( 'invalid_price', 'Invalid price type.' );
    }

    $value = trim( (string) $value );

    if ( '' === $value ) {
        return '';
    }

    /*
     * Accept normal decimal prices only:
     * 123
     * 123.45
     * .99
     *
     * A comma is deliberately rejected instead of silently changing the
     * submitted amount. This avoids ambiguous locale conversions.
     */
    if ( ! preg_match( '/^(?:\d+(?:\.\d{1,6})?|\.\d{1,6})$/', $value ) ) {
        return new WP_Error( 'invalid_price', 'Invalid price format.' );
    }

    if ( function_exists( 'wc_format_decimal' ) ) {
        $value = wc_format_decimal( $value, wc_get_price_decimals() );
    }

    /*
     * WooCommerce price fields should never be negative.
     * Use a numeric comparison after normalization.
     */
    if ( ! is_numeric( $value ) || (float) $value < 0 ) {
        return new WP_Error( 'invalid_price', 'Invalid price value.' );
    }

    return (string) $value;
}

/**
 * Validate a submitted SKU.
 *
 * Empty values are allowed because WooCommerce uses an empty SKU to clear it.
 * Non-empty values must be unique across products and variations.
 *
 * @param mixed $value
 * @param int   $product_id
 * @return string|WP_Error
 */
function qpe_validate_sku( $value, $product_id ) {
    if ( ! is_scalar( $value ) ) {
        return new WP_Error( 'invalid_sku', 'Invalid SKU type.' );
    }

    $value = trim( (string) $value );

    if ( '' === $value ) {
        return '';
    }

    $value = sanitize_text_field( $value );

    /*
     * Reject control characters and enforce a reasonable maximum length
     * (WooCommerce itself stores SKU in a varchar(100) column).
     */
    if ( strlen( $value ) > 100 ) {
        return new WP_Error( 'invalid_sku', 'SKU too long.' );
    }

    /*
     * Reject characters that would break URLs or lookalike SKUs.
     * WooCommerce by default sanitizes SKU with sanitize_title(), but we
     * only warn on obvious problems by allowing typical SKU characters.
     */
    if ( ! preg_match( '/^[\p{L}\p{N}\-_.\/ ]+$/u', $value ) ) {
        return new WP_Error( 'invalid_sku', 'Invalid SKU format.' );
    }

    /*
     * Check for uniqueness. wc_get_product_id_by_sku() returns the ID of
     * the product/variation that already uses this SKU, or 0 if none.
     */
    if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
        $existing_id = (int) wc_get_product_id_by_sku( $value );

        if ( $existing_id && $existing_id !== (int) $product_id ) {
            return new WP_Error( 'duplicate_sku', 'SKU already exists.' );
        }
    }

    return $value;
}

/**
 * Process submitted changes (SKU, regular price, sale price).
 *
 * Security:
 * - capability check
 * - nonce verification
 * - strict POST array validation
 * - product edit capability check
 * - strict price / SKU validation (including SKU uniqueness)
 *
 * Only fields that were actually present in the POST payload are updated.
 * This allows the parent row of a variable product to submit only the SKU
 * without clearing its prices.
 *
 * @return array{updated:int,failed:int}
 */
function qpe_process_save() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        wp_die(
            esc_html__( 'You do not have sufficient permissions to access this page.', 'quick-price-edit' ),
            '',
            array( 'response' => 403 )
        );
    }

    check_admin_referer( 'qpe_save_prices_action', 'qpe_nonce' );

    if ( empty( $_POST['qpe_prices'] ) || ! is_array( $_POST['qpe_prices'] ) ) {
        return array(
            'updated' => 0,
            'failed'  => 0,
        );
    }

    $submitted_prices = wp_unslash( $_POST['qpe_prices'] );

    if ( ! is_array( $submitted_prices ) ) {
        return array(
            'updated' => 0,
            'failed'  => 1,
        );
    }

    $updated = 0;
    $failed  = 0;

    foreach ( $submitted_prices as $product_id => $prices ) {
        $product_id = absint( $product_id );

        if ( ! $product_id || ! is_array( $prices ) ) {
            $failed++;
            continue;
        }

        /*
         * Do not trust the posted product type or any other client-side data.
         * Load the product from WooCommerce and verify its edit capability.
         */
        $product = wc_get_product( $product_id );

        if ( ! $product || ! current_user_can( 'edit_product', $product_id ) ) {
            $failed++;
            continue;
        }

        $changed = false;

        /* --------------------------------------------------------------
         * SKU
         * ------------------------------------------------------------ */
        if ( array_key_exists( 'sku', $prices ) ) {
            $sku = qpe_validate_sku( $prices['sku'], $product_id );

            if ( is_wp_error( $sku ) ) {
                $failed++;
                continue;
            }

            if ( (string) $product->get_sku( 'edit' ) !== (string) $sku ) {
                try {
                    $product->set_sku( $sku );
                    $changed = true;
                } catch ( WC_Data_Exception $e ) {
                    $failed++;
                    continue;
                }
            }
        }

        /* --------------------------------------------------------------
         * Regular price
         * ------------------------------------------------------------ */
        if ( array_key_exists( 'regular_price', $prices ) ) {
            $regular_price = qpe_validate_price( $prices['regular_price'] );

            if ( is_wp_error( $regular_price ) ) {
                $failed++;
                continue;
            }

            if ( (string) $product->get_regular_price( 'edit' ) !== (string) $regular_price ) {
                $product->set_regular_price( $regular_price );
                $changed = true;
            }
        }

        /* --------------------------------------------------------------
         * Sale price
         * ------------------------------------------------------------ */
        if ( array_key_exists( 'sale_price', $prices ) ) {
            $sale_price = qpe_validate_price( $prices['sale_price'] );

            if ( is_wp_error( $sale_price ) ) {
                $failed++;
                continue;
            }

            if ( (string) $product->get_sale_price( 'edit' ) !== (string) $sale_price ) {
                $product->set_sale_price( $sale_price );
                $changed = true;
            }
        }

        /*
         * Avoid unnecessary database writes when nothing changed.
         */
        if ( ! $changed ) {
            continue;
        }

        try {
            $product->save();
        } catch ( WC_Data_Exception $e ) {
            $failed++;
            continue;
        }

        $updated++;
    }

    return array(
        'updated' => $updated,
        'failed'  => $failed,
    );
}

/**
 * Export all products (simple + variations) to a CSV file that opens
 * natively in Excel with correct UTF-8 encoding.
 *
 * Columns: Product | SKU | Regular Price | Sale Price
 *
 * Security:
 * - capability check
 * - nonce verification (handled by wp_nonce_url + check_admin_referer)
 */
function qpe_export_csv() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        wp_die(
            esc_html__( 'You do not have sufficient permissions to access this page.', 'quick-price-edit' ),
            '',
            array( 'response' => 403 )
        );
    }

    check_admin_referer( 'qpe_export_action', 'qpe_export_nonce' );

    $args = array(
        'post_type'              => 'product',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'orderby'                => 'title',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'cache_results'          => false,
    );

    $products_query = new WP_Query( $args );

    $filename = 'products-prices-' . gmdate( 'Y-m-d-His' ) . '.csv';

    nocache_headers();
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    $output = fopen( 'php://output', 'w' );

    /*
     * UTF-8 BOM so Excel on Windows/Mac detects the encoding correctly
     * and displays Cyrillic characters without mojibake.
     */
    fwrite( $output, "\xEF\xBB\xBF" );

    // Header row.
    fputcsv(
        $output,
        array(
            __( 'Product', 'quick-price-edit' ),
            __( 'SKU', 'quick-price-edit' ),
            __( 'Regular Price', 'quick-price-edit' ),
            __( 'Sale Price', 'quick-price-edit' ),
        )
    );

    if ( $products_query->have_posts() ) {
        while ( $products_query->have_posts() ) {
            $products_query->the_post();

            $product = wc_get_product( get_the_ID() );

            if ( ! $product ) {
                continue;
            }

            if ( $product->is_type( 'simple' ) ) {
                qpe_export_write_row( $output, $product );
            } elseif ( $product->is_type( 'variable' ) ) {
                /*
                 * Parent row for the variable product itself.
                 * Prices are left empty because the parent has no own price.
                 */
                fputcsv(
                    $output,
                    array(
                        $product->get_name() . ' (' . __( 'Variable', 'quick-price-edit' ) . ')',
                        $product->get_sku( 'edit' ),
                        '',
                        '',
                    )
                );

                foreach ( $product->get_children() as $variation_id ) {
                    $variation = wc_get_product( $variation_id );

                    if ( $variation instanceof WC_Product_Variation ) {
                        qpe_export_write_row( $output, $variation, true );
                    }
                }
            }
        }
    }

    fclose( $output );
    wp_reset_postdata();
    exit;
}

/**
 * Write a single product row to the CSV output stream.
 *
 * @param resource   $output       fopen() handle for php://output
 * @param WC_Product $product
 * @param bool       $is_variation
 */
function qpe_export_write_row( $output, $product, $is_variation = false ) {
    $name = $product->get_name();

    if ( $is_variation ) {
        // Em-dash marker matches the on-screen indentation style.
        $name = '— ' . $name;
    }

    fputcsv(
        $output,
        array(
            $name,
            $product->get_sku( 'edit' ),
            $product->get_regular_price( 'edit' ),
            $product->get_sale_price( 'edit' ),
        )
    );
}

/**
 * Import prices from an uploaded CSV file (admin-post handler).
 *
 * Expected columns (order-independent, matched by header text):
 *   Product / Товар / Produkt | SKU / Артикул / Artikelnummer
 *   Regular Price / Звичайна ціна / Regulärer Preis / Cena regularna
 *   Sale Price / Ціна розпродажу / Angebotspreis / Cena promocyjna
 *
 * Matching strategy:
 *   1. by SKU via wc_get_product_id_by_sku()
 *   2. fallback to exact post_title match (handles rows without SKU)
 *
 * Only regular_price and sale_price are updated. SKUs in the import file
 * are ignored to avoid accidental renames/conflicts.
 *
 * @return void
 */
function qpe_import_csv() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        wp_die(
            esc_html__( 'You do not have sufficient permissions to access this page.', 'quick-price-edit' ),
            '',
            array( 'response' => 403 )
        );
    }

    check_admin_referer( 'qpe_import_action', 'qpe_import_nonce' );

    $redirect_base = admin_url( 'admin.php?page=quick-price-edit' );

    if ( empty( $_FILES['qpe_import_file'] ) || ! isset( $_FILES['qpe_import_file']['tmp_name'] ) ) {
        wp_safe_redirect( add_query_arg( 'qpe_import_error', 'nofile', $redirect_base ) );
        exit;
    }

    $file = $_FILES['qpe_import_file'];

    if ( ! empty( $file['error'] ) ) {
        wp_safe_redirect( add_query_arg( 'qpe_import_error', 'unread', $redirect_base ) );
        exit;
    }

    // 5 MB ceiling — plenty for tens of thousands of rows.
    if ( isset( $file['size'] ) && (int) $file['size'] > 5 * 1024 * 1024 ) {
        wp_safe_redirect( add_query_arg( 'qpe_import_error', 'toolarge', $redirect_base ) );
        exit;
    }

    if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
        wp_safe_redirect( add_query_arg( 'qpe_import_error', 'unread', $redirect_base ) );
        exit;
    }

    $handle = fopen( $file['tmp_name'], 'r' );

    if ( ! $handle ) {
        wp_safe_redirect( add_query_arg( 'qpe_import_error', 'unread', $redirect_base ) );
        exit;
    }

    $header = fgetcsv( $handle );

    if ( ! is_array( $header ) || empty( $header ) ) {
        fclose( $handle );
        wp_safe_redirect( add_query_arg( 'qpe_import_error', 'unread', $redirect_base ) );
        exit;
    }

    /*
     * Strip UTF-8 BOM from the first header cell (Excel adds it on export).
     */
    if ( isset( $header[0] ) ) {
        $header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $header[0] );
    }

    $header = array_map( 'trim', $header );

    $idx_name = -1;
    $idx_sku  = -1;
    $idx_reg  = -1;
    $idx_sale = -1;

    foreach ( $header as $i => $label ) {
        $label_lc = function_exists( 'mb_strtolower' ) ? mb_strtolower( $label, 'UTF-8' ) : strtolower( $label );

        if ( $idx_sku === -1 && (
            false !== strpos( $label_lc, 'sku' ) ||
            false !== strpos( $label_lc, 'артикул' ) ||
            false !== strpos( $label_lc, 'artikelnummer' )
        ) ) {
            $idx_sku = $i;
            continue;
        }

        if ( $idx_name === -1 && (
            false !== strpos( $label_lc, 'product' ) ||
            false !== strpos( $label_lc, 'товар' ) ||
            false !== strpos( $label_lc, 'тов' ) ||
            false !== strpos( $label_lc, 'produkt' )
        ) ) {
            $idx_name = $i;
            continue;
        }

        if ( $idx_reg === -1 && (
            false !== strpos( $label_lc, 'regular' ) ||
            false !== strpos( $label_lc, 'звичайна' ) ||
            false !== strpos( $label_lc, 'обычная' ) ||
            false !== strpos( $label_lc, 'regul' ) ||
            false !== strpos( $label_lc, 'cena regular' )
        ) ) {
            $idx_reg = $i;
            continue;
        }

        if ( $idx_sale === -1 && (
            false !== strpos( $label_lc, 'sale' ) ||
            false !== strpos( $label_lc, 'розпродаж' ) ||
            false !== strpos( $label_lc, 'распродаж' ) ||
            false !== strpos( $label_lc, 'angebot' ) ||
            false !== strpos( $label_lc, 'promocyj' )
        ) ) {
            $idx_sale = $i;
            continue;
        }
    }

    /*
     * Fallback to positional layout if header labels are unrecognized.
     * Matches the plugin's own export order.
     */
    if ( $idx_name === -1 ) { $idx_name = 0; }
    if ( $idx_sku  === -1 ) { $idx_sku  = 1; }
    if ( $idx_reg  === -1 ) { $idx_reg  = 2; }
    if ( $idx_sale === -1 ) { $idx_sale = 3; }

    $updated  = 0;
    $failed   = 0;
    $notfound = 0;

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        // Skip completely empty lines (Excel sometimes adds trailing newlines).
        if ( ! is_array( $row ) || ( count( $row ) === 1 && '' === trim( (string) $row[0] ) ) ) {
            continue;
        }

        $sku_raw  = isset( $row[ $idx_sku ] )  ? trim( (string) $row[ $idx_sku ] )  : '';
        $name_raw = isset( $row[ $idx_name ] ) ? trim( (string) $row[ $idx_name ] ) : '';
        $reg_raw  = isset( $row[ $idx_reg ] )  ? trim( (string) $row[ $idx_reg ] )  : '';
        $sale_raw = isset( $row[ $idx_sale ] ) ? trim( (string) $row[ $idx_sale ] ) : '';

        // 1) Try by SKU.
        $product_id = 0;
        if ( '' !== $sku_raw && function_exists( 'wc_get_product_id_by_sku' ) ) {
            $product_id = (int) wc_get_product_id_by_sku( $sku_raw );
        }

        // 2) Fallback by name.
        if ( ! $product_id && '' !== $name_raw ) {
            $product_id = qpe_find_product_by_name( $name_raw );
        }

        if ( ! $product_id ) {
            $notfound++;
            continue;
        }

        if ( ! current_user_can( 'edit_product', $product_id ) ) {
            $failed++;
            continue;
        }

        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            $failed++;
            continue;
        }

        $regular_price = qpe_validate_price( $reg_raw );
        $sale_price    = qpe_validate_price( $sale_raw );

        if ( is_wp_error( $regular_price ) || is_wp_error( $sale_price ) ) {
            $failed++;
            continue;
        }

        $changed = false;

        if ( (string) $product->get_regular_price( 'edit' ) !== (string) $regular_price ) {
            $product->set_regular_price( $regular_price );
            $changed = true;
        }

        if ( (string) $product->get_sale_price( 'edit' ) !== (string) $sale_price ) {
            $product->set_sale_price( $sale_price );
            $changed = true;
        }

        if ( ! $changed ) {
            continue;
        }

        try {
            $product->save();
        } catch ( Exception $e ) {
            $failed++;
            continue;
        }

        $updated++;
    }

    fclose( $handle );

    wp_safe_redirect(
        add_query_arg(
            array(
                'qpe_import_done' => '1',
                'qpe_updated'     => $updated,
                'qpe_failed'      => $failed,
                'qpe_notfound'    => $notfound,
            ),
            $redirect_base
        )
    );
    exit;
}

/**
 * Find a product or variation ID by exact post title.
 *
 * Handles the plugin's export conventions:
 *  - strips a leading "— " (em-dash) used for variation rows
 *  - strips a trailing " (Variable)" suffix used for parent rows
 *
 * @param string $name
 * @return int
 */
function qpe_find_product_by_name( $name ) {
    global $wpdb;

    $name = trim( (string) $name );

    if ( '' === $name ) {
        return 0;
    }

    $name = preg_replace( '/^—\s*/u', '', $name );
    $name = preg_replace( '/\s*\(Variable\)\s*$/u', '', $name );
    $name = trim( $name );

    if ( '' === $name ) {
        return 0;
    }

    $post_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts}
             WHERE post_title = %s
               AND post_type IN ('product', 'product_variation')
               AND post_status = 'publish'
             LIMIT 1",
            $name
        )
    );

    return $post_id ? (int) $post_id : 0;
}

```
