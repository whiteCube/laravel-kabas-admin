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
    "icon": "contact",
    "meta": {
      "description": { 
        "type": "textarea"
        ...
      }
    }
  },
  "introduction": {
    "type": "wysiwyg",
    "label": "Introduction text"
  },
  "phonenumber": {
    "type": "text",
    "readonly": true,
    "label": "Phone number"
  }
}
```

**Let's go through this.**  
The first entry, with the key `"kabas"`, is used to store some general configuration for the page. It must contain a `"name"` which will be displayed in the navigation links, and `"icon"` is used to show a beautiful decorative icon on the page's card. You can find a list of available icons [here](./icons.png).
> Please note: you must reference the name of the icon without the `.svg` extension.
You must also have a "meta" key containing fields that represent the meta tags you need on this page.

The other entries are a list of the fields you wish to have for the page. Each is an object that must contain at least a `"type"` and a `"label"`. The type must refer to one of the available field types, and the label is the text that is displayed above the field.  
Here is a [reference of all the supported field types and their respective configuration json](https://kabas.io/fieldtypes/).

## Models

Models work in a similar way to pages, but their structure files must be placed within a `models` directory inside the `structure` folder. The name of the file does not matter much in this case, as you will define which model this file corresponds to from within the json data.

Here's an example structure for a `App\Post` model:
```json
{
    "kabas": {
        "name": "News",
        "model": "App\\Post",
        "columns": {
            "title": {
                "title": "title",
                "main": true
            },
            "updated_at": {
                "title":  "Last modified"
            }
        },
        "search_query": "SELECT * FROM posts INNER JOIN post_translations ON posts.id = post_translations.post_id WHERE post_translations.title LIKE '%s'"
    },
    "title": {
        "label": "Title",
        "type": "text"
    },
    "content": {
        "label": "Text content",
        "type": "wysiwyg",
    }
}
```

As with pages, general configuration is done within the `kabas` object.

The `name` key will be used in the admin navigation links. 

The `model` key must be a reference to the full class name (including namespace) of your model.  

The `columns` object lets you define which data is displayed when we are listing your models in a table. Say you click on the "News" link in the sidebar, the system will show you a table with your `App\Post` items, but we need to tell it how to display that data.  
The key of each entry within this object must correspond to the name of the corresponding column in the database. Then, we can give it a human-friendly title, and say whether it's the main column or not (the main column will be all the way on the left and take up more space than the other columns).  
If you want to display a value from a relation, you may do it like so:
```json
"user_id": {
    "title": "User",
    "references": "App\\User@id",
    "column": "firstname"
}
```

Lastly, if we want our model to be searchable, we can provide a `search_query` raw sql string, where every `%s` will be replaced with what the user wrote in the search box.
If we do not want a search box on our model, simply do not include the `search_query` key.

And then, define your fields as you would on a page (see above).

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

## Custom pages
You can also have completely custom pages.  
The structure for these must be placed in a `customs`directory within the `structures`folder.

These are really simple:
```json
{
    "kabas": {
        "name": "My custom page",
        "controller": "App\\Http\\Controllers\\Admin\\MyCustomPageController"
    }
}
```

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MyCustomPageController extends Controller
{
    public function __construct()
    {
        // Do any setup stuff you need before render() is called
    }

    public function render()
    {
        return view(...);
    }
}
```

The system will create an instance of your controller and then call a `render` method on it, which must return a view. You can simply use Laravel's `view()` function to do this.  
Please note that your view does not need to contain a header/sidebar, as it will be integrated in the default kabas admin layout automatically.