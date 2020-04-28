<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Ridez'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */


    'url' => env('APP_URL', 'http://192.168.8.104/'),

    'asset_url' => env('ASSET_URL', 'http://192.168.8.104/'),



    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        // LARAVEL FCM
        LaravelFCM\FCMServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [
        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Input' => Illuminate\Support\Facades\Input::class,
        'Icons' => App\Icons::class,
        'Theme' => App\Theme::class,
        'WalkerDocument' => App\WalkerDocument::class,
        'Requests' => App\Requests::class,
        'Owner' => App\Owner::class,
        'Walker' => App\Walker::class,
        'ScheduledRequests' => App\ScheduledRequests::class,
        'Settings' => App\Settings::class,
        'RequestMeta' => App\RequestMeta::class,
        'Helper' => App\Helper::class,
        'Ledger' => App\Ledger::class,
        'PromoCodes' => App\PromoCodes::class,
        'FCM'      => LaravelFCM\Facades\FCM::class,
        'FCMGroup' => LaravelFCM\Facades\FCMGroup::class,
    ],
    'menu_titles' => [
        'admin_control' => 'Admin Control',
        'income_history' => 'Income History',
        'log_out' => 'Log Out',
        'dashboard' => 'Dashboard',
        'map_view' => 'Map View',
        'providers' => 'Providers',
        'requests' => 'Requests',
        'customers' => 'Customers',
        'reviews' => 'Reviews',
        'information' => 'Information',
        'types' => 'Types',
        'documents' => 'Documents',
        'settings' => 'Settings',
        'balance' => 'Balance',
        'create_request' => 'Create Request',
        'promotional_codes' => 'Promotional Codes',
    ],
    'generic_keywords'=> [
        'Provider' => 'Service Provider',
        'User' => 'User',
        'Services' => 'Taxi',
        'Trip' => 'Trip',
        'Currency' => 'UGX',
        'total_trip' => '1',
        'cancelled_trip' => '3',
        'total_payment' => '5',
        'completed_trip' => '4',
        'card_payment' => '6',
        'credit_payment' => '2',
        'cash_payment' => '5',
        'promotional_payment' => '35',
        'schedules_icon' => '36',
    ],
    /* DEVICE PUSH NOTIFICATION DETAILS */
    'customer_certy_url' => 'http://192.168.0.197/taxi-anytime-v3/public/apps/ios_push/iph_cert/Client_certy.pem',
    'customer_certy_pass' => '123456',
    'customer_certy_type' => '1',
    'provider_certy_url' => 'http://192.168.0.197/taxi-anytime-v3/public/apps/ios_push/walker/iph_cert/Walker_certy.pem',
    'provider_certy_pass' => '123456',
    'provider_certy_type' => '1',
    'gcm_browser_key' => 'AIzaSyA9wgwd7gOfPASGneXAWRut6gs_PaBHBRM',
    //'gcm_browser_key' => 'AIzaSyBj2yjcv-nYtR05c1guFiIJiFKT4iajU4A',
    // 'gcm_browser_key' => 'AIzaSyBegXbpsyHXhz_QvOndzHKxwQNbpEbvVFE',
    //'gcm_browser_key' => 'AIzaSyD5FBryEU_fvVnmbHxUg0af3Et6C4atUJw',
    /* DEVICE PUSH NOTIFICATION DETAILS END */
    'currency_symb' => 'UGX', 
    
    /* Developer Company Details */
    'developer_company_name' => 'QuickTaxi',
    'developer_company_web_link' => 'http://www.quicktaxi.ug/', 
    'developer_company_email' => 'info@QuickTaxi.ug', 
    'developer_company_fb_link' => 'https://www.facebook.com/Quick-Taxi-Ug-645739858947205/', 
    'developer_company_twitter_link' => 'https://twitter.com/QuickTaxiug',
    /* Developer Company Details END */
    
    /* APP LINK DATA */
    'android_client_app_url'=>'https://play.google.com/store/apps/details?id=com.quick.taxi',
    'android_provider_app_url'=>'http://www.apple.com',
    'ios_client_app_url'=>'http://www.apple.com',
    'ios_provider_app_url'=>'https://play.google.com/store/apps/details?id=com.quick.taxi',
    /* APP LINK DATA END */
    
    'no_data_available' => 'History not availalbe.', 
    'data_not_available' => 'Data not availalbe.', 
    'blank_fiend_val' => 'N/A',

    'website_title' => 'Ridez',
    'referral_prefix' => 'TNN',
    'datenow'=>'Y-m-d H:i:s',
    'appdate'=>'d-m-Y 23:59:59',
    'referral_zero_len' => 10,
    'website_meta_description' => '',
    'website_meta_keywords' => '',

    's3_bucket' => '',

    'twillo_account_sid' => 'AC3256d3addf75da750f4460365ab1d220',
    'twillo_auth_token' => 'Carlr4jc.',
    'twillo_number' => '',

    'production' => false,

    'default_payment' => 'stripe',

    'stripe_secret_key' => 'sk_live_qkpSizKU9lHy66mC6nlynE36',
    'stripe_publishable_key' => ' pk_live_bwAI2gaVS0e9TCKhDWsKKWlv',
    'braintree_environment' => '',
    'braintree_merchant_id' => '',
    'braintree_public_key' => '',
    'braintree_private_key' => '',
    'braintree_cse' => '',
        
    'coinbaseAPIKey' => 'g0xKQqKRwNj84IW9',
    'coinbaseAPISecret' => 'iEHiMMeUXWEGHV02M3lcxt8evBPaOzlC',

    'paypal_sdk_mode' => 'sandbox',
    'paypal_sdk_UserName' => 'npavankumar34-buyer_api1.gmail.com',
    'paypal_sdk_Password' => 'WUUPVM3ETSJ6CARS',
    'paypal_sdk_Signature' => 'AnIGq3pWk8Gb1yRu1ZjCY0N3ccikAdq-3A6AHjDQPytHJVE2N4d6jeWH',
    'paypal_sdk_AppId' => 'APP-80W284485P519543T',

];
