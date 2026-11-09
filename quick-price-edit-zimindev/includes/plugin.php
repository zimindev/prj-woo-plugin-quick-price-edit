<?php
/**
* Quick Price Edit for WooCommerce: everything except the loader (see ../quick-price-edit-zimindev.php).
*/

if ( ! defined( 'ABSPATH' ) ) {
   exit;
}

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
       dirname( plugin_basename( QPE_FILE ) ) . '/languages'
   );

   // Only this plugin's text domain: a global «gettext» filter would run for every string on every page.
   add_filter( 'gettext_quick-price-edit', 'qpe_translate_strings', 20, 3 );
   add_filter( 'all_plugins', 'qpe_plugin_description' );

   if ( ! class_exists( 'WooCommerce' ) ) {
       add_action( 'admin_notices', 'qpe_woocommerce_missing_notice' );
       return;
   }

   add_action( 'admin_menu', 'qpe_add_admin_menu' );
   add_action( 'admin_enqueue_scripts', 'qpe_enqueue_admin_assets' );
   add_action( 'admin_post_qpe_export', 'qpe_export_csv' );
   add_action( 'admin_post_qpe_import', 'qpe_import_csv' );
   add_filter( 'admin_footer_text', 'qpe_footer_text' );
}

/**
* Author credit at the bottom of the plugin screen (only there).
*
* @param string $text Default footer text.
* @return string
*/
function qpe_footer_text( $text ) {
   $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
   if ( ! $screen || ! qpe_is_page( $screen->id ) ) {
       return $text;
   }
   return 'Design &amp; Development by <a href="https://zimin.dev" target="_blank" rel="noopener">Sasha Zimin</a> ❤️';
}

/**
* Inline translations: uk, ru, be, kk, de, pl, fr (any other language stays English).
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
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Завантажте CSV-файл, експортований з Excel. Товари шукаються спочатку за артикулом, а якщо артикул порожній — за назвою товару.',
           'Import finished:' => 'Імпорт завершено:',
           'updated' => 'оновлено',
           'failed' => 'помилок',
           'not found' => 'не знайдено',
           'No file was uploaded.' => 'Файл не завантажено.',
           'Could not read the uploaded file.' => 'Не вдалося прочитати завантажений файл.',
           'The uploaded file is too large.' => 'Завантажений файл занадто великий.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Деякі поля не оновлено через некоректне або дубльоване значення, або відсутність прав на редагування товару.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Редагуйте артикули, звичайні та акційні ціни всього каталогу WooCommerce в одному місці.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Окрема сторінка в адмінці для швидкого редагування артикулів, звичайних і акційних цін простих товарів і варіацій, з імпортом і експортом CSV для Excel.',
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
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Загрузите CSV-файл, экспортированный из Excel. Товары ищутся сначала по артикулу, а если артикул пустой — по названию товара.',
           'Import finished:' => 'Импорт завершён:',
           'updated' => 'обновлено',
           'failed' => 'ошибок',
           'not found' => 'не найдено',
           'No file was uploaded.' => 'Файл не загружен.',
           'Could not read the uploaded file.' => 'Не удалось прочитать загруженный файл.',
           'The uploaded file is too large.' => 'Загруженный файл слишком большой.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Некоторые поля не обновлены из-за некорректного или дублированного значения, либо отсутствия прав на редактирование товара.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Редактируйте артикулы, обычные и акционные цены всего каталога WooCommerce в одном месте.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Отдельная страница в админке для быстрого редактирования артикулов, обычных и акционных цен простых товаров и вариаций, с импортом и экспортом CSV для Excel.',
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
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Laden Sie eine aus Excel exportierte CSV-Datei hoch. Produkte werden zuerst über die SKU, dann über den Produktnamen (falls SKU leer) zugeordnet.',
           'Import finished:' => 'Import abgeschlossen:',
           'updated' => 'aktualisiert',
           'failed' => 'fehlgeschlagen',
           'not found' => 'nicht gefunden',
           'No file was uploaded.' => 'Es wurde keine Datei hochgeladen.',
           'Could not read the uploaded file.' => 'Die hochgeladene Datei konnte nicht gelesen werden.',
           'The uploaded file is too large.' => 'Die hochgeladene Datei ist zu groß.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Einige Felder wurden aufgrund eines ungültigen oder doppelten Werts oder fehlender Bearbeitungsberechtigung nicht aktualisiert.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Bearbeiten Sie SKU, reguläre und Angebotspreise Ihres gesamten WooCommerce-Katalogs an einem Ort.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Eine eigene Admin-Seite zum schnellen Bearbeiten von Artikelnummern, regulären und Angebotspreisen einfacher Produkte und Varianten, mit CSV-Import und -Export für Excel.',
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
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Prześlij plik CSV wyeksportowany z Excela. Produkty są dopasowywane najpierw po SKU, a jeśli SKU jest puste — po nazwie produktu.',
           'Import finished:' => 'Import zakończony:',
           'updated' => 'zaktualizowano',
           'failed' => 'błędów',
           'not found' => 'nie znaleziono',
           'No file was uploaded.' => 'Nie przesłano pliku.',
           'Could not read the uploaded file.' => 'Nie udało się odczytać przesłanego pliku.',
           'The uploaded file is too large.' => 'Przesłany plik jest zbyt duży.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Niektóre pola nie zostały zaktualizowane z powodu nieprawidłowej lub zduplikowanej wartości bądź braku uprawnień do edycji produktu.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Edytuj SKU, ceny regularne i promocyjne całego katalogu WooCommerce w jednym miejscu.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Osobna strona w panelu do szybkiej edycji SKU, cen regularnych i promocyjnych produktów prostych i wariantów, z importem i eksportem CSV dla Excela.',
       ),
       'be' => array(
           'Quick Price Edit' => 'Рэдагаванне цэн',
           'requires WooCommerce to be installed and active.' => 'патрабуе ўсталяванага і актыўнага WooCommerce.',
           'You do not have sufficient permissions to access this page.' => 'У вас недастаткова правоў для доступу да гэтай старонкі.',
           'Prices updated successfully.' => 'Цэны паспяхова абноўлены.',
           'Tip:' => 'Парада:',
           'Use your browser\'s built-in search to quickly find a product — press' => 'Скарыстайцеся ўбудаваным пошукам браўзера, каб хутка знайсці тавар — націсніце',
           '(or' => '(або',
           'on Mac) and search by product name or SKU on this page.' => 'на Mac) і шукайце па назве тавару або артыкуле на гэтай старонцы.',
           'Product' => 'Тавар',
           'SKU' => 'Артыкул',
           'Regular Price' => 'Звычайная цана',
           'Sale Price' => 'Цана распродажу',
           'Variable' => 'Варыятыўны',
           'No products found.' => 'Тавараў не знойдзена.',
           'Save Changes' => 'Захаваць змены',
           'Export to Excel' => 'Экспарт у Excel',
           'Import from Excel' => 'Імпарт з Excel',
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Загрузіце CSV-файл, экспартаваны з Excel. Тавары шукаюцца спачатку па артыкуле, а калі артыкул пусты — па назве тавару.',
           'Import finished:' => 'Імпарт завершаны:',
           'updated' => 'абноўлена',
           'failed' => 'памылак',
           'not found' => 'не знойдзена',
           'No file was uploaded.' => 'Файл не загружаны.',
           'Could not read the uploaded file.' => 'Не ўдалося прачытаць загружаны файл.',
           'The uploaded file is too large.' => 'Загружаны файл занадта вялікі.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Некаторыя палі не абноўлены праз некарэктнае або паўторнае значэнне ці адсутнасць правоў на рэдагаванне тавару.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Рэдагуйце артыкулы, звычайныя і акцыйныя цэны ўсяго каталога WooCommerce у адным месцы.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Асобная старонка ў адмінцы для хуткага рэдагавання артыкулаў, звычайных і акцыйных цэн простых тавараў і варыяцый, з імпартам і экспартам CSV для Excel.',
       ),
       'kk' => array(
           'Quick Price Edit' => 'Бағаларды өңдеу',
           'requires WooCommerce to be installed and active.' => 'орнатылған және белсенді WooCommerce қажет етеді.',
           'You do not have sufficient permissions to access this page.' => 'Бұл бетке кіру үшін құқықтарыңыз жеткіліксіз.',
           'Prices updated successfully.' => 'Бағалар сәтті жаңартылды.',
           'Tip:' => 'Кеңес:',
           'Use your browser\'s built-in search to quickly find a product — press' => 'Тауарды жылдам табу үшін браузердің кірістірілген іздеуін пайдаланыңыз — басыңыз',
           '(or' => '(немесе',
           'on Mac) and search by product name or SKU on this page.' => 'Mac-та) және осы бетте тауар атауы немесе артикулы бойынша іздеңіз.',
           'Product' => 'Тауар',
           'SKU' => 'Артикул',
           'Regular Price' => 'Әдеттегі баға',
           'Sale Price' => 'Сату бағасы',
           'Variable' => 'Нұсқалы',
           'No products found.' => 'Тауарлар табылмады.',
           'Save Changes' => 'Өзгерістерді сақтау',
           'Export to Excel' => 'Excel-ге экспорт',
           'Import from Excel' => 'Excel-ден импорт',
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Excel-ден экспортталған CSV файлын жүктеңіз. Тауарлар алдымен артикул бойынша, ал артикул бос болса — тауар атауы бойынша ізделеді.',
           'Import finished:' => 'Импорт аяқталды:',
           'updated' => 'жаңартылды',
           'failed' => 'қате',
           'not found' => 'табылмады',
           'No file was uploaded.' => 'Файл жүктелмеді.',
           'Could not read the uploaded file.' => 'Жүктелген файлды оқу мүмкін болмады.',
           'The uploaded file is too large.' => 'Жүктелген файл тым үлкен.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Кейбір өрістер қате немесе қайталанған мән не тауарды өңдеуге құқықтың жоқтығы себебінен жаңартылмады.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Бүкіл WooCommerce каталогының артикулдарын, әдеттегі және акциялық бағаларын бір жерде өңдеңіз.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Қарапайым тауарлар мен нұсқалардың артикулдарын, әдеттегі және акциялық бағаларын жылдам өңдеуге арналған жеке әкімші беті, Excel үшін CSV импорты мен экспорты бар.',
       ),
       'fr' => array(
           'Quick Price Edit' => 'Modification rapide des prix',
           'requires WooCommerce to be installed and active.' => 'nécessite que WooCommerce soit installé et activé.',
           'You do not have sufficient permissions to access this page.' => 'Vous n’avez pas les droits suffisants pour accéder à cette page.',
           'Prices updated successfully.' => 'Prix mis à jour avec succès.',
           'Tip:' => 'Astuce :',
           'Use your browser\'s built-in search to quickly find a product — press' => 'Utilisez la recherche intégrée du navigateur pour trouver rapidement un produit — appuyez sur',
           '(or' => '(ou',
           'on Mac) and search by product name or SKU on this page.' => 'sur Mac) et recherchez par nom de produit ou UGS sur cette page.',
           'Product' => 'Produit',
           'SKU' => 'UGS',
           'Regular Price' => 'Prix normal',
           'Sale Price' => 'Prix promo',
           'Variable' => 'Variable',
           'No products found.' => 'Aucun produit trouvé.',
           'Save Changes' => 'Enregistrer les modifications',
           'Export to Excel' => 'Exporter vers Excel',
           'Import from Excel' => 'Importer depuis Excel',
           'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.' => 'Téléversez un fichier CSV exporté depuis Excel. Les produits sont recherchés d’abord par UGS, puis par nom si l’UGS est vide.',
           'Import finished:' => 'Import terminé :',
           'updated' => 'mis à jour',
           'failed' => 'en échec',
           'not found' => 'introuvables',
           'No file was uploaded.' => 'Aucun fichier n’a été téléversé.',
           'Could not read the uploaded file.' => 'Impossible de lire le fichier téléversé.',
           'The uploaded file is too large.' => 'Le fichier téléversé est trop volumineux.',
           'Some fields were not updated because they were invalid, duplicated, or you do not have permission to edit the product.' => 'Certains champs n’ont pas été mis à jour car ils étaient invalides, en double, ou vous n’avez pas le droit de modifier le produit.',
           'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.' => 'Modifiez les UGS, prix normaux et prix promo de tout votre catalogue WooCommerce en un seul endroit.',
           'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.' => 'Une page d’administration dédiée pour modifier rapidement les UGS, prix normaux et prix promo des produits simples et des variations, avec import et export CSV pour Excel.',
       ),
   );
}

function qpe_translate_strings( $translated, $original, $domain ) {
   if ( 'quick-price-edit' !== $domain ) {
       return $translated;
   }

   // A switched locale (e.g. an email sent in the site language) wins over the admin's language.
   // The WordPress Belarusian locale is «bel», so the first two letters are enough.
   $locale = ( function_exists( 'is_locale_switched' ) && is_locale_switched() ) ? get_locale() : determine_locale();
   $lang   = strtolower( substr( $locale, 0, 2 ) );

   if ( 'en' === $lang ) {
       return $translated;
   }

   $translations = qpe_get_translations();

   return isset( $translations[ $lang ][ $original ] )
       ? $translations[ $lang ][ $original ]
       : $translated;
}

/**
* Plugin description on the Plugins screen (the file header does not go through gettext without .mo).
*
* @param array $plugins Plugins.
* @return array
*/
function qpe_plugin_description( $plugins ) {
   $basename = plugin_basename( QPE_FILE );
   if ( isset( $plugins[ $basename ] ) ) {
       $plugins[ $basename ]['Description'] = __( 'Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel.', 'quick-price-edit' );
   }
   return $plugins;
}

function qpe_woocommerce_missing_notice() {
   echo '<div class="notice notice-error"><p><strong>Quick Price Edit</strong> ' .
       esc_html__( 'requires WooCommerce to be installed and active.', 'quick-price-edit' ) .
       '</p></div>';
}

/**
* Plugin icon: a price tag with a pencil, one path (fill-rule evenodd), shown before the page heading in currentColor.
*/
function qpe_icon_path() {
   return 'M2 3.2V8.5L9.6 16.1 16.1 9.6 8.5 2H3.2A1.2 1.2 0 0 0 2 3.2ZM5.2 3.9A1.3 1.3 0 1 0 5.2 6.5 1.3 1.3 0 1 0 5.2 3.9ZM19.29 11.69 17.31 9.71 12.01 15.01 11.59 17.41 13.99 16.99Z';
}

function qpe_icon_svg( $fill = 'currentColor', $attrs = '' ) {
   return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"' . $attrs . '><path fill="' . $fill . '" fill-rule="evenodd" d="' . qpe_icon_path() . '"/></svg>';
}

function qpe_add_admin_menu() {
   // A sub-item of WooCommerce «Products»; the page keeps its slug, so admin.php?page=quick-price-edit still opens it.
   $hook = add_submenu_page(
       'edit.php?post_type=product',
       __( 'Quick Price Edit', 'quick-price-edit' ),
       __( 'Quick Price Edit', 'quick-price-edit' ),
       'manage_woocommerce',
       'quick-price-edit',
       'qpe_render_admin_page'
   );
   $GLOBALS['qpe_page_hook'] = (string) $hook;

   if ( $hook ) {
       // Save before any output, then redirect (POST/Redirect/GET): a page refresh never re-sends the form.
       add_action( 'load-' . $hook, 'qpe_handle_save_request' );
   }
}

/**
* Handle the «Save Changes» form on the plugin screen and redirect back with the result counts.
*/
function qpe_handle_save_request() {
   if ( ! isset( $_POST['qpe_save_prices'] ) ) {
       return;
   }

   $result = qpe_process_save();

   $args = array(
       'post_type'   => 'product',
       'page'        => 'quick-price-edit',
       'qpe_saved'   => 1,
       'qpe_updated' => (int) $result['updated'],
       'qpe_failed'  => (int) $result['failed'],
   );

   if ( ! empty( $_GET['paged'] ) ) {
       $args['paged'] = max( 1, absint( wp_unslash( $_GET['paged'] ) ) );
   }

   wp_safe_redirect( add_query_arg( $args, admin_url( 'edit.php' ) ) );
   exit;
}

/**
* Load the products of a catalog query together with their variations, with all caches primed.
*
* Post meta and terms of the products come with the WP_Query itself; the variations of all
* variable products on the page are read with one more query (instead of one transient and
* one product read per parent), so rendering does not query the database per row.
*
* @param WP_Query $query Query over products (post objects, meta and term caches enabled).
* @return array<int, array{product: WC_Product, variations: WC_Product_Variation[]}>
*/
function qpe_get_catalog_rows( $query ) {
   $rows         = array();
   $variable_ids = array();

   foreach ( $query->posts as $post ) {
       $product = wc_get_product( $post );

       if ( ! $product ) {
           continue;
       }

       if ( $product->is_type( 'variable' ) ) {
           $variable_ids[] = $product->get_id();
       } elseif ( ! $product->is_type( 'simple' ) ) {
           continue;
       }

       $rows[ $product->get_id() ] = array(
           'product'    => $product,
           'variations' => array(),
       );
   }

   if ( $variable_ids ) {
       // The same set and order as WC_Product_Variable::get_children() in the admin.
       $children = get_posts(
           array(
               'post_type'              => 'product_variation',
               'post_status'            => array( 'publish', 'private' ),
               'post_parent__in'        => $variable_ids,
               'orderby'                => array(
                   'menu_order' => 'ASC',
                   'ID'         => 'ASC',
               ),
               'posts_per_page'         => -1,
               'no_found_rows'          => true,
               'update_post_meta_cache' => true,
               'update_post_term_cache' => true, // A variation reads its shipping class term.
           )
       );

       foreach ( $children as $child ) {
           $parent_id = (int) $child->post_parent;

           if ( ! isset( $rows[ $parent_id ] ) ) {
               continue;
           }

           $variation = wc_get_product( $child );

           if ( $variation instanceof WC_Product_Variation ) {
               $rows[ $parent_id ]['variations'][] = $variation;
           }
       }
   }

   return $rows;
}

/**
* Display name of a product row; a variation gets its attribute values when the title lacks them
* («Shawl – Red, M» instead of three identical «Shawl» rows).
*
* @param WC_Product $product Product or variation.
* @return string Plain text (not escaped).
*/
function qpe_product_label( $product ) {
   $name = $product->get_name();

   if ( $product instanceof WC_Product_Variation && function_exists( 'wc_get_formatted_variation' ) ) {
       $extra = wc_get_formatted_variation( $product, true, false, true );

       if ( '' !== $extra ) {
           $name .= ' – ' . $extra;
       }
   }

   return html_entity_decode( $name, ENT_QUOTES, 'UTF-8' );
}

/**
* Whether a screen id / hook suffix is this plugin's page.
*
* @param string $id Screen id or hook suffix.
* @return bool
*/
function qpe_is_page( $id ) {
   return '' !== (string) $id && ( ! empty( $GLOBALS['qpe_page_hook'] ) && $GLOBALS['qpe_page_hook'] === $id );
}

function qpe_enqueue_admin_assets( $hook ) {
   if ( ! qpe_is_page( $hook ) ) {
       return;
   }

   $css = '
       .qpe-wrap {
           --qpe-accent: var(--wp-admin-theme-color, #2271b1);
           --qpe-border: #c3c4c7;
           --qpe-border-light: #dcdcde;
           --qpe-muted: #646970;
       }
       /* Heading with an icon, like other WordPress screens. */
       .qpe-wrap .qpe-title {
           display: flex;
           align-items: center;
           gap: 8px;
       }
       .qpe-wrap .qpe-title-icon {
           flex: none;
           width: 28px;
           height: 28px;
           color: #1d2327;
       }
       .qpe-tagline {
           margin: 4px 0 16px;
           color: var(--qpe-muted);
       }
       .qpe-wrap .notice.inline {
           margin: 0 0 16px;
       }
       .qpe-ctrl-f-notice kbd {
           display: inline-block;
           padding: 1px 6px;
           margin: 0 2px;
           font-family: Consolas, Monaco, monospace;
           font-size: 12px;
           line-height: 1.5;
           background: #f6f7f7;
           border: 1px solid var(--qpe-border);
           border-bottom-width: 2px;
           border-radius: 3px;
       }
       .qpe-table-wrap {
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
       /* Floating actions: always at hand on long catalogs. */
       .qpe-floating-actions {
           position: fixed;
           top: 50%;
           right: 24px;
           transform: translateY(-50%);
           z-index: 9999;
           display: flex;
           flex-direction: column;
           align-items: stretch;
           gap: 8px;
       }
       /* Both buttons: the same width and height, text centred (a link and a button render differently by default). */
       .qpe-floating-actions .button {
           display: block;
           box-sizing: border-box;
           min-height: 36px;
           margin: 0;
           padding: 0 16px;
           font-size: 13px;
           line-height: 34px;
           text-align: center;
           white-space: nowrap;
           box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
       }
       /* The button floats over the table: WordPress makes secondary buttons almost transparent
          on hover, so the rows would show through. Keep a solid background in every state. */
       .qpe-floating-actions .qpe-floating-import,
       .qpe-floating-actions .qpe-floating-import:focus,
       .qpe-floating-actions .qpe-floating-export,
       .qpe-floating-actions .qpe-floating-export:focus {
           background: #fff;
       }
       .qpe-floating-actions .qpe-floating-import:hover,
       .qpe-floating-actions .qpe-floating-import:active,
       .qpe-floating-actions .qpe-floating-export:hover,
       .qpe-floating-actions .qpe-floating-export:active {
           background: #f0f6fc;
       }
       .qpe-floating-notices {
           position: absolute;
           top: calc(100% + 8px);
           right: 0;
           display: flex;
           flex-direction: column;
           align-items: flex-end;
           gap: 8px;
           width: 260px;
       }
       @media screen and (max-width: 782px) {
           .qpe-floating-notices {
               top: auto;
               bottom: calc(100% + 8px);
           }
       }
       .qpe-floating-notice {
           max-width: 260px;
           padding: 8px 12px;
           font-size: 13px;
           text-align: right;
           color: #1d2327;
           background: #fff;
           border: 1px solid var(--qpe-border);
           border-left-width: 4px;
           box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
           transition: opacity .4s ease;
       }
       .qpe-floating-notice.qpe-fade-out {
           opacity: 0;
       }
       .qpe-floating-notice-success {
           border-left-color: #00a32a;
       }
       .qpe-floating-notice-warning {
           border-left-color: #dba617;
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

   // The form is saved on «load-{page}» and redirected here with the counts (see qpe_handle_save_request()).
   $save_result = null;

   if ( isset( $_GET['qpe_saved'] ) ) {
       $save_result = array(
           'updated' => isset( $_GET['qpe_updated'] ) ? absint( $_GET['qpe_updated'] ) : 0,
           'failed'  => isset( $_GET['qpe_failed'] ) ? absint( $_GET['qpe_failed'] ) : 0,
       );
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
       // Every row reads prices, SKU and the product type: load meta and terms for the whole page at once.
       'update_post_meta_cache' => true,
       'update_post_term_cache' => true,
   );

   $products_query = new WP_Query( $args );
   $catalog_rows   = qpe_get_catalog_rows( $products_query );
   ?>
   <div class="wrap qpe-wrap">
       <h1 class="qpe-title"><?php echo qpe_icon_svg( 'currentColor', ' class="qpe-title-icon" width="28" height="28" aria-hidden="true" focusable="false"' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?> <?php esc_html_e( 'Quick Price Edit', 'quick-price-edit' ); ?></h1>
       <p class="qpe-tagline"><?php esc_html_e( 'Edit SKU, regular and sale prices for your whole WooCommerce catalog in one place.', 'quick-price-edit' ); ?></p>

       <?php
       /* --------------------------------------------------------------
        * Import results notice (after redirect from admin-post).
        * ------------------------------------------------------------ */
       if ( isset( $_GET['qpe_import_done'] ) ) {
           $imp_updated  = isset( $_GET['qpe_updated'] ) ? absint( $_GET['qpe_updated'] ) : 0;
           $imp_failed   = isset( $_GET['qpe_failed'] ) ? absint( $_GET['qpe_failed'] ) : 0;
           $imp_notfound = isset( $_GET['qpe_notfound'] ) ? absint( $_GET['qpe_notfound'] ) : 0;

           echo '<div class="notice notice-success inline qpe-notice"><p>';
           echo '<strong>' . esc_html__( 'Import finished:', 'quick-price-edit' ) . '</strong> ';
           echo esc_html( $imp_updated ) . ' ' . esc_html__( 'updated', 'quick-price-edit' ) . ', ';
           echo esc_html( $imp_failed ) . ' ' . esc_html__( 'failed', 'quick-price-edit' ) . ', ';
           echo esc_html( $imp_notfound ) . ' ' . esc_html__( 'not found', 'quick-price-edit' ) . '.';
           echo '</p></div>';
       } elseif ( isset( $_GET['qpe_import_error'] ) ) {
           $err_code = sanitize_key( wp_unslash( $_GET['qpe_import_error'] ) );
           $err_map  = array(
               'nofile'  => __( 'No file was uploaded.', 'quick-price-edit' ),
               'unread'  => __( 'Could not read the uploaded file.', 'quick-price-edit' ),
               'toolarge'=> __( 'The uploaded file is too large.', 'quick-price-edit' ),
           );
           $err_msg = isset( $err_map[ $err_code ] ) ? $err_map[ $err_code ] : __( 'Could not read the uploaded file.', 'quick-price-edit' );

           echo '<div class="notice notice-error inline qpe-notice"><p>';
           echo '<strong>' . esc_html( $err_msg ) . '</strong>';
           echo '</p></div>';
       }
       ?>

       <div class="notice notice-info inline qpe-ctrl-f-notice"><p>
           <strong><?php esc_html_e( 'Tip:', 'quick-price-edit' ); ?></strong>
           <?php esc_html_e( "Use your browser's built-in search to quickly find a product — press", 'quick-price-edit' ); ?>
           <kbd>Ctrl</kbd> + <kbd>F</kbd>
           <?php esc_html_e( '(or', 'quick-price-edit' ); ?>
           <kbd>Cmd</kbd> + <kbd>F</kbd>
           <?php esc_html_e( 'on Mac) and search by product name or SKU on this page.', 'quick-price-edit' ); ?>
       </p></div>

       <?php // Import form without a visible panel: the «Import from Excel» button in the floating bar uses it. ?>
       <form method="post"
             action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
             enctype="multipart/form-data"
             id="qpe-import-form"
             hidden>
           <input type="hidden" name="action" value="qpe_import" />
           <?php wp_nonce_field( 'qpe_import_action', 'qpe_import_nonce' ); ?>
           <input type="file" name="qpe_import_file" id="qpe-import-file" accept=".csv,text/csv" required />
       </form>

       <form method="post" id="qpe-prices-form">
           <?php wp_nonce_field( 'qpe_save_prices_action', 'qpe_nonce' ); ?>

           <div class="qpe-floating-actions">
               <button type="button" class="button qpe-floating-import" id="qpe-floating-import" title="<?php esc_attr_e( 'Upload a CSV file exported from Excel. Products are matched by SKU first, then by product name if SKU is empty.', 'quick-price-edit' ); ?>">
                   <?php esc_html_e( 'Import from Excel', 'quick-price-edit' ); ?>
               </button>
               <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=qpe_export' ), 'qpe_export_action', 'qpe_export_nonce' ) ); ?>"
                  class="button qpe-floating-export">
                   <?php esc_html_e( 'Export to Excel', 'quick-price-edit' ); ?>
               </a>
               <button type="submit" name="qpe_save_prices" class="button button-primary qpe-floating-save" value="1">
                   <?php esc_html_e( 'Save Changes', 'quick-price-edit' ); ?>
               </button>

               <?php if ( ! empty( $save_result['updated'] ) || ! empty( $save_result['failed'] ) ) : ?>
                   <?php // Out of the flex column, so a long message does not widen the buttons. ?>
                   <div class="qpe-floating-notices">
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
               <?php endif; ?>
           </div>

           <script>
               ( function () {
                   // «Import from Excel» in the floating bar: choose a CSV, and the import form below is sent at once.
                   var button = document.getElementById( 'qpe-floating-import' );
                   var input  = document.getElementById( 'qpe-import-file' );
                   var form   = document.getElementById( 'qpe-import-form' );
                   if ( ! button || ! input || ! form ) {
                       return;
                   }
                   var picking = false;
                   button.addEventListener( 'click', function () {
                       picking = true;
                       input.click();
                   } );
                   input.addEventListener( 'change', function () {
                       if ( picking && input.files && input.files.length ) {
                           button.disabled = true;
                           form.submit();
                       }
                       picking = false;
                   } );
               } )();

               ( function () {
                   // Send only the fields that were changed. A catalog of a few hundred products has
                   // thousands of inputs: PHP max_input_vars (1000 by default) would silently cut the rest.
                   var form = document.getElementById( 'qpe-prices-form' );
                   if ( ! form ) {
                       return;
                   }
                   form.addEventListener( 'submit', function () {
                       var fields = form.querySelectorAll( 'input[name^="qpe_prices["]' );
                       for ( var i = 0; i < fields.length; i++ ) {
                           if ( fields[ i ].value === fields[ i ].defaultValue ) {
                               fields[ i ].disabled = true;
                           }
                       }
                   } );
                   // Back/forward cache: re-enable the fields if the page is shown again.
                   window.addEventListener( 'pageshow', function () {
                       var fields = form.querySelectorAll( 'input[name^="qpe_prices["]:disabled' );
                       for ( var i = 0; i < fields.length; i++ ) {
                           fields[ i ].disabled = false;
                       }
                   } );
               } )();
           </script>

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
                   if ( $catalog_rows ) {
                       foreach ( $catalog_rows as $row ) {
                           $product = $row['product'];

                           if ( $product->is_type( 'simple' ) ) {
                               qpe_render_price_row( $product );
                               continue;
                           }

                           // Variable product: the parent row has only a SKU, prices live in the variations.
                           echo '<tr class="qpe-parent-row">';
                           echo '<td><strong>' . esc_html( $product->get_name() ) . '</strong> (' .
                               esc_html__( 'Variable', 'quick-price-edit' ) . ')</td>';
                           echo '<td>' . qpe_render_sku_input( $product ) . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
                           echo '<td colspan="2"></td>';
                           echo '</tr>';

                           foreach ( $row['variations'] as $variation ) {
                               qpe_render_price_row( $variation, true );
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
               &mdash; <?php echo esc_html( qpe_product_label( $product ) ); ?>
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

   // Spaces between digits are thousands separators from Excel («1 250», also NBSP / narrow NBSP).
   $value = preg_replace( '/(?<=\d)[\s\x{00A0}\x{202F}]+(?=\d)/u', '', $value );

   /*
    * A decimal comma («12,50», how Excel writes numbers in Ukrainian, German, Polish… locales)
    * is accepted only in the unambiguous form: one comma, no dot, 1–2 digits after it.
    * «1,000» stays invalid instead of being guessed as one thousand or one.
    */
   if ( preg_match( '/^\d*,\d{1,2}$/', $value ) ) {
       $value = str_replace( ',', '.', $value );
   }

   /*
    * Accept normal decimal prices only:
    * 123
    * 123.45
    * .99
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
* Whether two prices are the same amount: «200», «200.00» and «200.0» are equal, so an import of an
* unchanged file does not rewrite every product.
*
* @param string $a Price ('' = none).
* @param string $b Price ('' = none).
* @return bool
*/
function qpe_same_price( $a, $b ) {
   $a = (string) $a;
   $b = (string) $b;

   if ( '' === $a || '' === $b ) {
       return $a === $b;
   }

   return is_numeric( $a ) && is_numeric( $b ) && abs( (float) $a - (float) $b ) < 0.000001;
}

/**
* Whether a regular / sale price pair is valid: when both are set, the sale price must be lower
* (the same rule as the WooCommerce product editor).
*
* @param string $regular Regular price ('' = none).
* @param string $sale    Sale price ('' = none).
* @return bool
*/
function qpe_prices_are_consistent( $regular, $sale ) {
   if ( '' === (string) $sale || '' === (string) $regular ) {
       return true;
   }

   return (float) $sale < (float) $regular;
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
       // An unchanged SKU needs no validation (the uniqueness check is a database query).
       if ( array_key_exists( 'sku', $prices ) && is_scalar( $prices['sku'] ) && trim( (string) $prices['sku'] ) !== (string) $product->get_sku( 'edit' ) ) {
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

           if ( ! qpe_same_price( $product->get_regular_price( 'edit' ), $regular_price ) ) {
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

           if ( ! qpe_same_price( $product->get_sale_price( 'edit' ), $sale_price ) ) {
               $product->set_sale_price( $sale_price );
               $changed = true;
           }
       }

       // A sale price must be lower than the regular one.
       if ( $changed && ! qpe_prices_are_consistent( $product->get_regular_price( 'edit' ), $product->get_sale_price( 'edit' ) ) ) {
           $failed++;
           continue;
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

   $products_query = new WP_Query(
       array(
           'post_type'              => 'product',
           'post_status'            => 'publish',
           'posts_per_page'         => -1,
           'orderby'                => 'title',
           'order'                  => 'ASC',
           'no_found_rows'          => true,
           'update_post_meta_cache' => true,
           'update_post_term_cache' => true,
       )
   );

   $filename = 'products-prices-' . gmdate( 'Y-m-d-His' ) . '.csv';

   nocache_headers();
   header( 'Content-Type: text/csv; charset=utf-8' );
   header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

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
       ),
       ',',
       '"',
       ''
   );

   foreach ( qpe_get_catalog_rows( $products_query ) as $row ) {
       $product = $row['product'];

       if ( $product->is_type( 'simple' ) ) {
           qpe_export_write_row( $output, $product );
           continue;
       }

       // Parent row of a variable product: its prices stay empty, the parent has no own price.
       fputcsv(
           $output,
           array(
               qpe_csv_cell( $product->get_name() . ' (' . __( 'Variable', 'quick-price-edit' ) . ')' ),
               qpe_csv_cell( $product->get_sku( 'edit' ) ),
               '',
               '',
           ),
           ',',
           '"',
           ''
       );

       foreach ( $row['variations'] as $variation ) {
           qpe_export_write_row( $output, $variation, true );
       }
   }

   fclose( $output );
   exit;
}

/**
* Make a text cell safe for spreadsheets: a value starting with = + - @ (or a tab / CR) would be
* run by Excel as a formula (CSV injection), so it gets a leading apostrophe, which Excel hides.
*
* @param string $value Cell text.
* @return string
*/
function qpe_csv_cell( $value ) {
   $value = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );

   if ( '' !== $value && false !== strpos( "=+-@\t\r", $value[0] ) ) {
       $value = "'" . $value;
   }

   return $value;
}

/**
* Write a single product row to the CSV output stream.
*
* @param resource   $output       fopen() handle for php://output
* @param WC_Product $product
* @param bool       $is_variation
*/
function qpe_export_write_row( $output, $product, $is_variation = false ) {
   $name = qpe_product_label( $product );

   if ( $is_variation ) {
       // Em-dash marker matches the on-screen indentation style.
       $name = '— ' . $name;
   }

   fputcsv(
       $output,
       array(
           qpe_csv_cell( $name ),
           qpe_csv_cell( $product->get_sku( 'edit' ) ),
           $product->get_regular_price( 'edit' ),
           $product->get_sale_price( 'edit' ),
       ),
       ',',
       '"',
       ''
   );
}


/**
* Import prices from an uploaded CSV file (admin-post handler).
*
* The file may come from this plugin's export or be saved by Excel / LibreOffice / Google Sheets:
* - the delimiter (comma, semicolon or tab) is detected from the header line;
* - columns are found by their titles in any of the plugin languages, in any order;
*   a file without recognisable titles is read in the export order (Product, SKU, Regular, Sale);
* - a file saved by Excel in a legacy encoding (Windows-1251 / Windows-1252) is converted to UTF-8;
* - prices may use a decimal comma and spaces as thousands separators.
*
* Matching: by SKU first, then by the exact product / variation name (only when it is unique).
* Only the regular and sale prices are updated; SKUs in the file are used for matching only.
* A price column missing from the file is left untouched; an empty cell clears that price.
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

   $redirect_base = admin_url( 'edit.php?post_type=product&page=quick-price-edit' );

   if ( empty( $_FILES['qpe_import_file'] ) || ! isset( $_FILES['qpe_import_file']['tmp_name'] ) ) {
       wp_safe_redirect( add_query_arg( 'qpe_import_error', 'nofile', $redirect_base ) );
       exit;
   }

   $file = $_FILES['qpe_import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- an uploaded file, read as CSV below.

   if ( ! empty( $file['error'] ) ) {
       $code = ( UPLOAD_ERR_INI_SIZE === (int) $file['error'] || UPLOAD_ERR_FORM_SIZE === (int) $file['error'] ) ? 'toolarge' : 'unread';
       wp_safe_redirect( add_query_arg( 'qpe_import_error', $code, $redirect_base ) );
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

   $handle = fopen( $file['tmp_name'], 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

   if ( ! $handle ) {
       wp_safe_redirect( add_query_arg( 'qpe_import_error', 'unread', $redirect_base ) );
       exit;
   }

   $first_line = fgets( $handle );

   if ( false === $first_line || '' === trim( $first_line ) ) {
       fclose( $handle );
       wp_safe_redirect( add_query_arg( 'qpe_import_error', 'unread', $redirect_base ) );
       exit;
   }

   // Strip the UTF-8 BOM (Excel and this plugin's export add it).
   $first_line = preg_replace( '/^\xEF\xBB\xBF/', '', $first_line );
   $delimiter  = qpe_detect_csv_delimiter( $first_line );
   $header     = qpe_csv_to_utf8( str_getcsv( rtrim( $first_line, "\r\n" ), $delimiter, '"', '' ) );
   $columns    = qpe_detect_import_columns( $header );
   $pending    = array();

   if ( null === $columns ) {
       // No titles recognised: the export column order. The first line is data too when it holds prices.
       $columns = array(
           'name'    => 0,
           'sku'     => 1,
           'regular' => 2,
           'sale'    => 3,
       );

       $reg_first = isset( $header[2] ) ? trim( $header[2] ) : '';
       if ( '' !== $reg_first && ! is_wp_error( qpe_validate_price( $reg_first ) ) ) {
           $pending[] = $header;
       }
   }

   $updated  = 0;
   $failed   = 0;
   $notfound = 0;

   while ( $pending || ( $row = fgetcsv( $handle, 0, $delimiter, '"', '' ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
       if ( $pending ) {
           $row = array_shift( $pending );
       } else {
           $row = qpe_csv_to_utf8( $row );
       }

       // Skip completely empty lines (Excel sometimes adds trailing newlines and «;;;» rows).
       if ( ! is_array( $row ) || '' === trim( implode( '', array_map( 'strval', $row ) ) ) ) {
           continue;
       }

       $cell = static function ( $key ) use ( $row, $columns ) {
           $index = isset( $columns[ $key ] ) ? $columns[ $key ] : -1;
           if ( $index < 0 || ! array_key_exists( $index, $row ) ) {
               return null;
           }
           return trim( (string) $row[ $index ] );
       };

       $sku_raw  = qpe_csv_unescape( (string) $cell( 'sku' ) );
       $name_raw = qpe_csv_unescape( (string) $cell( 'name' ) );
       $reg_raw  = $cell( 'regular' );
       $sale_raw = $cell( 'sale' );

       // 1) By SKU.
       $product_id = 0;
       if ( '' !== $sku_raw ) {
           $product_id = (int) wc_get_product_id_by_sku( $sku_raw );
       }

       // 2) By name, when it points to exactly one product or variation.
       if ( ! $product_id && '' !== $name_raw ) {
           $product_id = qpe_find_product_by_name( $name_raw );
       }

       if ( ! $product_id ) {
           $notfound++;
           continue;
       }

       $product = wc_get_product( $product_id );

       if ( ! $product || ! current_user_can( 'edit_product', $product_id ) ) {
           $failed++;
           continue;
       }

       // The parent of a variable product has no own prices (its export row is empty): skip it.
       if ( $product->is_type( 'variable' ) ) {
           if ( '' !== (string) $reg_raw || '' !== (string) $sale_raw ) {
               $failed++;
           }
           continue;
       }

       $regular_price = null === $reg_raw ? $product->get_regular_price( 'edit' ) : qpe_validate_price( $reg_raw );
       $sale_price    = null === $sale_raw ? $product->get_sale_price( 'edit' ) : qpe_validate_price( $sale_raw );

       if ( is_wp_error( $regular_price ) || is_wp_error( $sale_price ) || ! qpe_prices_are_consistent( $regular_price, $sale_price ) ) {
           $failed++;
           continue;
       }

       $changed = false;

       if ( ! qpe_same_price( $product->get_regular_price( 'edit' ), $regular_price ) ) {
           $product->set_regular_price( $regular_price );
           $changed = true;
       }

       if ( ! qpe_same_price( $product->get_sale_price( 'edit' ), $sale_price ) ) {
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
* Guess the CSV delimiter from the header line: the most frequent of «;», «,» and tab outside quotes.
* Excel uses «;» in most European locales and «,» in English ones.
*
* @param string $line First line of the file.
* @return string
*/
function qpe_detect_csv_delimiter( $line ) {
   $unquoted = preg_replace( '/"(?:[^"]|"")*"/', '', $line );
   $counts   = array(
       ';'  => substr_count( $unquoted, ';' ),
       ','  => substr_count( $unquoted, ',' ),
       "\t" => substr_count( $unquoted, "\t" ),
   );
   arsort( $counts );

   return reset( $counts ) > 0 ? (string) key( $counts ) : ',';
}

/**
* Convert CSV cells to UTF-8. Excel «CSV» (not «CSV UTF-8») is saved in the Windows code page:
* Cyrillic sites get Windows-1251, others Windows-1252.
*
* @param array|false $row Cells.
* @return array|false
*/
function qpe_csv_to_utf8( $row ) {
   if ( ! is_array( $row ) || ! function_exists( 'mb_check_encoding' ) ) {
       return $row;
   }

   static $legacy = null;
   if ( null === $legacy ) {
       $cyrillic = array( 'uk', 'ru', 'be', 'kk', 'bg', 'sr', 'mk' );
       $legacy   = ( in_array( strtolower( substr( get_locale(), 0, 2 ) ), $cyrillic, true ) || in_array( strtolower( substr( get_user_locale(), 0, 2 ) ), $cyrillic, true ) )
           ? 'Windows-1251'
           : 'Windows-1252';
   }

   foreach ( $row as $i => $value ) {
       if ( is_string( $value ) && ! mb_check_encoding( $value, 'UTF-8' ) ) {
           $row[ $i ] = mb_convert_encoding( $value, 'UTF-8', $legacy );
       }
   }

   return $row;
}

/**
* Remove the apostrophe that the export puts before cells starting with = + - @ (see qpe_csv_cell()).
*
* @param string $value Cell text.
* @return string
*/
function qpe_csv_unescape( $value ) {
   return preg_match( "/^'[=+\\-@\\t\\r]/", $value ) ? substr( $value, 1 ) : $value;
}

/**
* Find the import columns by their titles.
*
* Exact titles of the export in every plugin language are tried first, then keywords
* (so «Артикул товару», «Ціна зі знижкою», «Prix normal» etc. are understood too).
*
* @param string[] $header Header cells.
* @return array{name:int,sku:int,regular:int,sale:int}|null Column indexes (-1 = absent), null when nothing is recognised.
*/
function qpe_detect_import_columns( $header ) {
   $titles = array(
       'sku'     => array( 'sku' ),
       'regular' => array( 'regular price' ),
       'sale'    => array( 'sale price' ),
       'name'    => array( 'product' ),
   );
   $source = array(
       'sku'     => 'SKU',
       'regular' => 'Regular Price',
       'sale'    => 'Sale Price',
       'name'    => 'Product',
   );
   foreach ( qpe_get_translations() as $dict ) {
       foreach ( $source as $key => $original ) {
           if ( isset( $dict[ $original ] ) ) {
               $titles[ $key ][] = qpe_lower( $dict[ $original ] );
           }
       }
   }

   $keywords = array(
       'sku'     => array( 'sku', 'артикул', 'артыкул', 'artikel', 'ugs', 'référence', 'reference', 'indeks' ),
       'regular' => array( 'regular', 'regul', 'звичайн', 'обычн', 'звычайн', 'әдеттегі', 'normal', 'базов', 'основн' ),
       'sale'    => array( 'sale', 'розпродаж', 'распродаж', 'angebot', 'promo', 'сату', 'акці', 'акци', 'знижк', 'скидк', 'soldé' ),
       'name'    => array( 'product', 'produkt', 'produit', 'товар', 'тавар', 'тауар', 'назва', 'название', 'наименование', 'name', 'nom', 'nazwa' ),
   );

   $columns = array(
       'name'    => -1,
       'sku'     => -1,
       'regular' => -1,
       'sale'    => -1,
   );
   $labels  = array_map( 'qpe_lower', array_map( 'trim', array_map( 'strval', $header ) ) );

   // Pass 1: exact titles.
   foreach ( $labels as $i => $label ) {
       foreach ( $titles as $key => $list ) {
           if ( -1 === $columns[ $key ] && in_array( $label, $list, true ) ) {
               $columns[ $key ] = $i;
               continue 2;
           }
       }
   }

   // Pass 2: keywords for the columns still missing, on the labels not used yet.
   foreach ( $labels as $i => $label ) {
       if ( '' === $label || in_array( $i, $columns, true ) ) {
           continue;
       }
       foreach ( $keywords as $key => $list ) {
           if ( -1 !== $columns[ $key ] ) {
               continue;
           }
           foreach ( $list as $word ) {
               if ( false !== strpos( $label, $word ) ) {
                   $columns[ $key ] = $i;
                   continue 3;
               }
           }
       }
   }

   // Without a SKU or name column the rows cannot be matched, without a price column there is nothing to import.
   $can_match = -1 !== $columns['sku'] || -1 !== $columns['name'];
   $has_price = -1 !== $columns['regular'] || -1 !== $columns['sale'];

   return ( $can_match && $has_price ) ? $columns : null;
}

/**
* Lower-case a string (multibyte-safe when mbstring is available).
*
* @param string $value Text.
* @return string
*/
function qpe_lower( $value ) {
   return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string) $value, 'UTF-8' ) : strtolower( (string) $value );
}

/**
* Find a product or variation ID by its name, as shown on the plugin screen / in the export.
*
* - strips a leading «— » used for variation rows;
* - strips a trailing « (Variable)» suffix of parent rows, in any plugin language;
* - understands variation labels with attribute values («Shawl – Red, M»);
* - returns 0 when the name is ambiguous (several products share it), so a price never lands
*   on the wrong product.
*
* @param string $name Name from the file.
* @return int
*/
function qpe_find_product_by_name( $name ) {
   global $wpdb;

   $name         = trim( (string) $name );
   $is_variation = (bool) preg_match( '/^—\s*/u', $name );
   $name         = trim( preg_replace( '/^—\s*/u', '', $name ) );

   $suffixes = array( 'Variable' );
   foreach ( qpe_get_translations() as $dict ) {
       if ( isset( $dict['Variable'] ) ) {
           $suffixes[] = $dict['Variable'];
       }
   }
   $suffixes = array_map(
       static function ( $suffix ) {
           return preg_quote( $suffix, '/' );
       },
       array_unique( $suffixes )
   );
   $stripped  = trim( preg_replace( '/\s*\((?:' . implode( '|', $suffixes ) . ')\)\s*$/u', '', $name ) );
   $is_parent = $stripped !== $name;
   $name      = $stripped;

   if ( '' === $name ) {
       return 0;
   }

   // «— Name» rows are variations, «Name (Variable)» rows are parents: variations often share the parent's title.
   $types    = $is_variation ? "'product_variation'" : ( $is_parent ? "'product'" : "'product', 'product_variation'" );
   $statuses = "'publish', 'private'";

   // phpcs:ignore WordPress.DB.DirectDatabaseQuery
   $ids = $wpdb->get_col(
       $wpdb->prepare(
           "SELECT ID FROM {$wpdb->posts}
            WHERE post_title = %s
              AND post_type IN ({$types})
              AND post_status IN ({$statuses})
            LIMIT 2",
           $name
       )
   );

   if ( 1 === count( $ids ) ) {
       return (int) $ids[0];
   }

   if ( count( $ids ) > 1 || $is_parent ) {
       return 0;
   }

   // A variation label «Parent - values» / «Parent – values» (WooCommerce adds the attribute values
   // to the name): compare the label of every variation of a parent whose title is a prefix of it.
   if ( ! preg_match_all( '/ [-–] /u', $name, $matches, PREG_OFFSET_CAPTURE ) ) {
       return 0;
   }

   $titles = array();
   foreach ( $matches[0] as $match ) {
       $titles[] = substr( $name, 0, $match[1] );
   }

   $placeholders = implode( ', ', array_fill( 0, count( $titles ), '%s' ) );

   // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders
   $candidates = $wpdb->get_col(
       $wpdb->prepare(
           "SELECT v.ID FROM {$wpdb->posts} AS v
            INNER JOIN {$wpdb->posts} AS p ON p.ID = v.post_parent
            WHERE p.post_title IN ({$placeholders})
              AND p.post_type = 'product'
              AND v.post_type = 'product_variation'
              AND v.post_status IN ({$statuses})
            LIMIT 500",
           $titles
       )
   );

   $found = 0;

   foreach ( $candidates as $candidate ) {
       $variation = wc_get_product( (int) $candidate );

       if ( $variation instanceof WC_Product_Variation && qpe_product_label( $variation ) === $name ) {
           if ( $found ) {
               return 0;
           }
           $found = (int) $candidate;
       }
   }

   return $found;
}
