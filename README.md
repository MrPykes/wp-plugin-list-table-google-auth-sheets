# wp-plugin-list-table-google-auth-sheets

composer update


composer.json

{
    "require": {
        "google/apiclient": "^2.12.1"
    },
    "scripts": {
        "pre-autoload-dump": "Google\\Task\\Composer::cleanup"
    },
    "extra": {
        "google/apiclient-services": [
            "Sheets"
        ]
    }
}
