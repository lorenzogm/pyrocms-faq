# FAQ module for PyroCMS

FAQ module built with streams.

## Installation

* Add the [streams library](https://github.com/LorenzoGarcia/pyrocms-streams) to your shared_addons: `addons/shared_addons/libraries/streamas/streams_details.php`
* Install the PyroCMS module.
* Add this route: `$route['faq/(:any)'] = 'faq/category/$1';`
* Add Twitter Bootstrap 3 or redesign the views.
