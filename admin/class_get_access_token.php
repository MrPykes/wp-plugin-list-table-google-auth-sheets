<?php

require_once(GOOGLE_PATH . '/vendor/autoload.php');

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */

class Google_Client_Access
{

    /**
     *
     * @since 3.1.0
     * @var object
     */
    protected $client;

    /**
     *
     * @since 3.1.0
     * @var string
     */
    protected $tokenPath;

    public function __construct()
    {
        require_once(GOOGLE_PATH . '/vendor/autoload.php');
        $this->client = new Google_Client();
        $this->client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        // $this->client->setScopes(
        //     [
        //         'https://www.googleapis.com/auth/spreadsheets',
        //         'https://www.googleapis.com/auth/drive'
        //     ]
        // );
        $this->client->setAuthConfig(GOOGLE_PATH . '/credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        $this->tokenPath = GOOGLE_PATH . '/token.json';


        if (file_exists($this->tokenPath)) {
            $accessToken = json_decode(file_get_contents($this->tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $authUrl = $this->client->createAuthUrl();

                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';

                $authCode = $_GET['code'];

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }

            // Save the token to a file.
            if (!file_exists(dirname($this->tokenPath))) {
                mkdir(dirname($this->tokenPath), 0700, true);
            }
            file_put_contents($this->tokenPath, json_encode($this->client->getAccessToken()));
        }

        return $this->client;
    }

    public function getAccessToken1()
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $authUrl = $this->client->createAuthUrl();

                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';

                $authCode = $_GET['code'];

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }

            // Save the token to a file.
            if (!file_exists(dirname($this->tokenPath))) {
                mkdir(dirname($this->tokenPath), 0700, true);
            }
            file_put_contents($this->tokenPath, json_encode($this->client->getAccessToken()));
        }

        return $this->client;
    }
}
