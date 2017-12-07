# Kabas Admin for Laravel 5.5

> Warning: still under construction and does not work 100% yet.

This package generates a Kabas-style admin interface for your Laravel projects.

## Installation & setup
`composer require whitecube/laravel-kabas-admin:dev-master`

Don't forget `php artisan vendor:publish` to copy the necessary configuration file to `config/kabas-admin.php` and the assets to your project directory.

Within the config file, you must specify the [middleware](https://laravel.com/docs/5.5/middleware) which will be used to protect your admin interface, and other very important things. Please have a look.

You must also add the following to the `disks` array in `config/filesystems.php`.  
You are free to change these to whatever you think is best.
```php
'admin_structures' => [
    'driver' => 'local',
    'root' => resource_path('structures')
],

'admin_values' => [
    'driver' => 'local',
    'root' => resource_path('lang')
],
```

That's it. To view your admin panel, go to `yourdomain.com/admin`.

## Pages

To make a page editable in the admin interface, simply create a `json` file describing its fields; we call this _the structure file_.  
It must be placed directly in a `resources/structures` folder _(or wherever you specified in the filesystems config for the `admin_structures` disk)_, and be named exactly like the route it corresponds to.

For example, given you have the following route defined:
```php
Route::get('/contact', 'ContactController@show')->name('contact');
// The important part here is the ->name('contact')
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

**Let's go through this.**  
The first entry, with the key `"kabas"`, is used to store some general configuration for the page. It must contain a `"name"` which will be displayed in the navigation links and such, and `"icon"` is used to show a beautiful decorative icon on the page's card. You can find a list of available icons [here](./icons.png).
> Please note: you must reference the name of the icon without the `.svg` extension.

The other entries are a list of the fields you wish to have for the page. Each is an object that must contain at least a `"type"` and a `"label"`. The type must refer to one of the available field types, and the label is the text that is displayed above the field.  
Here is a [reference of all the supported field types and their respective configuration json](https://kabas.io/fieldtypes/).

## Models

Todo

## Creating custom fields

For maximum flexibility, we're letting you do whatever you please with the way a field is shown and behaves when the user submits the form.  
Maybe you've built a tool where the user can place pins on a map, and you'd like to include it in the admin interface, instead of one of our premade fieldtypes.  
Don't worry, we've made it super easy. You'll need to create a class, anywhere you want, and our package will instanciate it and call methods on it to handle your special fields.  
To make this work, your field's object will have to look something like this:

```json
"special_field": {
  "controllers": {
    "show": "App\\Namespace\\YourClass@someMethod",
    "save": "App\\Namespace\\YourClass@someOtherMethod"
  },
  "anything": "Some kind of data you need"
}
```
> Please note: you may add any additional keys you want to this object. This data will be accessible in your method.

When we call your method, we will give it two parameters: 
- the first is the object described above, containing the data you have put into it
- the second is the lang context

**Important**: a `"show"` method must return html. This is what we use to insert your field into the page.

To add styles and scripts for your fields, check out the `config/kabas-admin.php` file, where you can link as many `.css` and `.js` files as you need.