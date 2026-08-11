# melis-tool-creator

MelisToolCreator generates ready-to-use Melis Platform modules from the back-office. In a few steps and a little typing, it writes a complete module — `Module.php`, configuration, controllers, models, services, forms, views, translations and JavaScript — so a new tool is up and running without writing boilerplate.

## Getting Started

These instructions will get you a copy of the project up and running on your machine.
This Melis Platform module is made to work with the MelisCore.

### Prerequisites

- PHP 8.3 or 8.5
- `melisplatform/melis-core` `^6.0`
- `melisplatform/melis-cms` `^6.0`

This will automatically be done when using composer.

### Installing

Run the composer command:
```
composer require melisplatform/melis-tool-creator
```

## Where to find it

Back-office menu: **Tool creation & Design > Tools > Tool Creator**
(melisKey: `melistoolcreator_tool`)

## Tools & Elements provided

* Tool Creator wizard (8 steps, from module naming to final generation)
* DB Tool generation: a list + edition tool built on one database table
* Dual table support: a second, linked table for translations
* iFrame Tool generation: loads an external URL inside a Melis tool
* Blank Tool generation: the module structure only, no tool logic
* Framework tool generation for Laravel, Symfony, Lumen and Silex
* Modal or tab edition mode for the generated tool
* Column selection, editable / mandatory fields, field titles, tooltips and translations (en_EN, fr_FR)
* Database table list caching, with manual refresh

## Running the code

### Generated module

The generated module is written to the platform's `module/` directory:

```
<document_root>/../module/<ModuleName>
```

Once generated, activate it in **Modules** so it is loaded by the platform.

### MelisToolCreator Services

MelisToolCreator provides services to be used in other modules:

* MelisToolCreatorService
Builds the module: generates the directory tree, `Module.php`, configuration, code and assets from the wizard's session data. It also exposes the database introspection helpers used by the wizard.
File: /melis-tool-creator/src/Service/MelisToolCreatorService.php
```
// Get the service
$toolCreatorSrv = $this->getServiceManager()->get('MelisToolCreatorService');
// Generate the module from the current wizard steps
$toolCreatorSrv->createTool();
// Introspect a table
$columns = $toolCreatorSrv->getTableColumns($table);
$primaryKey = $toolCreatorSrv->getTablePK($table);
```

* MelisToolCreatorCacheSystemService
Caches the database table list so the wizard does not describe the whole schema on every step.
File: /melis-tool-creator/src/Service/MelisToolCreatorCacheSystemService.php
```
// Get the service
$cacheSrv = $this->getServiceManager()->get('MelisToolCreatorCacheSystemService');
// Read a cached key
$results = $cacheSrv->getCacheByKey($cacheKey, $confCache);
// Invalidate a set of keys
$cacheSrv->deleteCacheByPrefix($prefix, $confName);
```

### Code templates

All generated code comes from plain-text templates shipped with the module:

* `/melis-tool-creator/template/Module` — the generated `Module.php`
* `/melis-tool-creator/template/Code` — reusable PHP code blocks injected into the generated files
* `/melis-tool-creator/template/Controller`, `Model`, `Service`, `Form`, `Listener`, `Config`, `View`, `Language` — one template per generated file type
* `/melis-tool-creator/template/Asset` — JavaScript for the generated tool, per edition mode (`modal-*`, `tab-*`)

Placeholders in these files (`#TCMODULE`, `#TCCONFIG`, …) are replaced during generation, so the output can be customised by editing the templates.

### Listening to services and update behavior with custom code

The generation process triggers events so the behavior can be modified:

```
public function attach(EventManagerInterface $events)
{
    $sharedEvents = $events->getSharedManager();

    $callBackHandler = $sharedEvents->attach(
    	'MelisToolCreator',
    	array(
    		'melis_tool_creator_generate_tool_start',
    		'melis_tool_creator_generate_tool_end',
    	),
    	function($e){

    		$sm = $e->getTarget()->getEvent()->getApplication()->getServiceManager();

    		// Custom Code here
    	},
    100);

    $this->listeners[] = $callBackHandler;
}
```

Two more events let a module extend the wizard's dropdowns:

* `melis_toolcreator_col_display_options` — add display types for a list column
* `melis_toolcreator_input_edition_type_options` — add input types for an editable field

## Authors

* **Melis Technology** - [www.melisplatform.com](https://www.melisplatform.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-tool-creator/contributors) who participated in this project.


## License

This project is licensed under the OSL-3.0 License.
