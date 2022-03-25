<?php

/*
 
Plugin Name:Google Auth
  
*/

define("GOOGLE_PATH", dirname(__FILE__));


add_action('admin_menu', 'register_my_custom_menu_page');
function register_my_custom_menu_page()
{
    add_menu_page(
        'Google Auth',
        'Google Auth',
        'manage_options',
        'google_auth.php',
        'google_auth',
        'dashicons-welcome-widgets-menus',
        50
    );
}

function google_auth()
{

    require_once(GOOGLE_PATH . '/vendor/autoload.php');
    include_once GOOGLE_PATH . '/admin/class_google_sheet_table.php';

    $client = new Google_Client();
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig(GOOGLE_PATH . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    $tokenPath = GOOGLE_PATH . '/token.json';

    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
        // return $client;
    } else {
        $authUrl = $client->createAuthUrl();
?>
        <a href="<?= $authUrl ?>">Click Here</a>
    <?php
    }

    $service = new Google_Service_Sheets($client);

    $spreadsheetId = '1KLkJ6hQxB3I13eatbGx10qhkYkXA6h32_pWCZyjdr2Q';

    $range = 'A7:L23';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $results = $response->getValues();

    ?>
    <form id="events-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

        <?php
        $googleSheet = new Google_Sheet_Table($results);
        $googleSheet->prepare_items();
        $googleSheet->search_box('Search', 'search');
        $googleSheet->display();
        ?>

    </form>
<?php
}
