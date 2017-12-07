<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    {{-- kabas-admin styles: https://github.com/whiteCube/kabas-admin --}}
    <link href="/vendor/kabas-admin/app.css" rel="stylesheet" type="text/css">

    {{-- custom styles --}}
    <link href="/vendor/kabas-admin/styles.css" rel="stylesheet" type="text/css">

    {{-- User styles --}}
    @foreach(Admin::stylesheets() as $stylesheet)
        <link href="{{ asset($stylesheet) }}" rel="stylesheet" type="text/css">
    @endforeach
</head>
<body>
    <div id="app" translations='{
    "fields": {
        "color": {
            "hue": "Hue",
            "sat": "Saturation",
            "lum": "Luminosity",
            "red": "Red",
            "green": "Green",
            "blue": "Blue",
            "cancel": "Cancel",
            "finish": "Finish"
        },
        "group": {
            "backlink": "Back to «&nbsp;%s&nbsp;»",
            "edit": {
                "group": "Edit group",
                "repeater": "Edit repeater",
                "flexible": "Edit flexible",
                "gallery": "Edit gallery"
            },
            "sub": {
                "group": "%s field|%s fields",
                "repeater": "%s item|%s items",
                "gallery": "%s image|%s images"
            }
        },
        "date": {
            "placeholder": "Click to pick a date"
        },
        "select": {
            "prompt": "Pick one",
            "search": "Type here to search...",
            "clearsearch": "Clear search field",
            "noresults": "No matches",
            "focus": "[ENTER]"
        },
        "url": {
            "external": "External",
            "internal": "Internal"
        },
        "file": {
            "choose": "Choose a file",
            "prompt": "Click here to pick a file on your computer, or drag and drop your file into this box directly.",
            "formats": "Accepted format|Accepted formats",
            "errors": {
                "notsupported": "This file type is not allowed for this field",
                "size": "This file exceeds the size limit for this field"
            }
        },
        "image": {
            "choose": "Choose an image",
            "prompt": "Click here to pick a file on your computer, or drag and drop your file into this box directly.",
            "alt": "Alt text",
            "sizes": "Size constraints",
            "height": "Height",
            "width": "Width",
            "min": "min",
            "max": "max",
            "errors": {
                "notsupported": "This file type is not allowed for this field",
                "size": "This image exceeds the size limit for this field",
                "dimensions": "The dimensions of this image do not conform to the limits of this field"
            }
        },
        "repeater": {
            "tip": "Click on an element to edit it.",
            "add": "Add",
            "cancel": "Cancel",
            "editTip": "Click finish to save this element.",
            "finish": "Finish",
            "elements": "%s element|%s elements",
            "subEdit": "Edit this repeater",
            "empty": "Click the add button to get started.",
            "delete": "Delete",
            "confirmdelete": "I am sure I want to delete this item",
            "edit": "Edit",
            "nopreview": "No preview available"
        },
        "gallery": {
            "image": "Image",
            "alt": "Describe your image"
        },
        "submit": {
            "submit": "Save changes",
            "tip": "There are no automatic saves. Do not forget to save manually."
        }
    }
}'>
        @include('admin::nav')
        
        <main class="main">
            @yield('main')
        </main>
        
    </div>

    {{-- kabas-admin js: https://github.com/whiteCube/kabas-admin  --}}
    <script src="/vendor/kabas-admin/app.js" type="text/javascript"></script>

    {{-- Additionnal scripts --}}
    <script src="/vendor/kabas-admin/script.js" type="text/javascript"></script>

    {{-- User scripts --}}
    @foreach(Admin::scripts() as $script)
        <script src="{{ asset($script) }}" type="text/javascript"></script>
    @endforeach
</body>
</html>