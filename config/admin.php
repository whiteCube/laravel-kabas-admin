<?php 

return [
    
    /**
     * The middleware used to protect your admin interface
     */
    'middleware' => 'auth',

    /**
     * The locales your application supports
     */
    'locales' => [
        'fr', 'nl'
    ],

    /**
     * Any additional stylesheets you wish to load
     */
    'stylesheets' => [
        
    ],

    /**
     * The route to log out your user accounts
     */
    'logout' => [
        'method' => 'GET',
        'route' => ''
    ],

    /**
     * The path to your logo (path starts in the public folder)
     */
    'logo' => '',

    /**
     * Display index as a table instead of cards
     */
    'tableview' => false,

    /**
     * The name of the route to the home page
     */
    'home' => ''
];