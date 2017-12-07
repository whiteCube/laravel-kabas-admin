# Kabas Admin for Laravel 5.5

This package generates a Kabas-style admin interface for your Laravel projects.

Install:  `composer require whitecube/laravel-kabas-admin`

Don't forget `php artisan vendor:publish` to copy the necessary configuration file to `config/kabas-admin.php` and the assets to your project directory.

Within the config file, you must specify your own middleware which will be used to protect your admin interface, and other very important things. Please have a look.

That's it. To view your admin panel, go to `yourdomain.com/admin`.

## Pages

To make a page editable in the admin interface, you must simply create a `json` file describing its fields (we call this the structure file).  It must be placed directly in a `resources/structures` folder, and be named exactly like the route it corresponds to.

For example, given you have the following route defined:
```php
// The important part here is the ->name('contact')
Route::get('/contact', 'ContactController@show')->name('contact');
```
You must create the following file:  `resources/structures/contact.json`.

Here's an example of what it could contain:
```json
{
  "kabas": {
    "name": "Contact",
    "icon": "contact"
  },
  "introduction": {
    "type": "wysiwyg",
    "label": "Introduction text"
  },
  "phonenumber": {
    "type": "text",
    "label": "Phone number"
  }
}
```

Let's go through this. The first entry, with the key `kabas`, is used to store some general configuration for the page. It must contain a `"name"` which will be displayed in the admin navigation, and `"icon"` is used to show a beautiful decorative icon on the page's card. You can find a list of available icons [here](./icons.png).



## Models

Todo