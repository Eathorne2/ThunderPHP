---
title: "Validate Form Data"
slug: "validate-form-data"
description: "Use the newer Validate class properly in plugin forms."
published: true
order: 10
source_id: 249
keywords: ["validate", "form", "data", "newer", "class", "properly", "plugin", "forms", "dev", "tutorials", "build", "first"]
---

When saving posts, validate the input before inserting or updating.

```php
$req = new \Core\Request;
$validate = new \Core\Validate($req->post());

$validate->setRules([
    'title' => [
        'name' => 'Title',
        'rules' => ['required', 'min:3', 'max:255']
    ],
    'slug' => [
        'name' => 'Slug',
        'rules' => [
            'required',
            [
                'rule' => 'unique',
                'table' => 'basic_blog_posts',
                'column' => 'slug',
                'where' => [
                    'deleted' => 0
                ]
            ]
        ]
    ]
]);

if($validate->fails())
{
    set_value('errors', $validate->getErrors());
}
```

The updated validator supports richer unique logic, including `where` and `whereNot`, which is useful for soft deletes and edit forms.
