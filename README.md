# Medusa

## Introduction
Medusa is a easy to use PHP templating engine for PHP.

## Getting Started
Here is a quick example.

*index.php file stored in `/public` directory*

```
<?php

require_once __DIR__.'/../vendor/autoload.php';

use AtlantisPHP\Medusa\Template as Medusa;

$medusa = new Medusa();

$medusa->setCacheDirectory('/../storage/cache');
$medusa->setViewsDirectory('/../views');
$medusa->setViewsExtension('.medusa.php');

$medusa->view('home', ['name' => 'Donald', 'app' => 'Medusa Application']);
```

*home.medusa.php file in `/views` directory*
```
{% isset $app %}

  <h1>{{ $app }}</h1>

{% else %}

  <h1>Unknown application</h1>

{% endisset %}
```