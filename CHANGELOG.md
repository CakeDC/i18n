Changelog
=========

Release 1.2.0
-------------

https://github.com/CakeDC/i18n/tree/1.2.0

 * [576c2ae](https://github.com/cakedc/i18n/commit/576c2ae) Adding semver and travis
 * [0a2c5e2](https://github.com/cakedc/i18n/commit/0a2c5e2) Working on making the Search plugin an optional requirement, some CS fixes as well
 * [53ecd3a](https://github.com/cakedc/i18n/commit/53ecd3a) Fixing the tests, coding standards and broken dependencies, solved by adding the dependent route to the plugin
 * [91eac4c](https://github.com/cakedc/i18n/commit/91eac4c) Fixed strict warning in flagImage method
 * [0830b22](https://github.com/cakedc/i18n/commit/0830b22) Making the unnecessary private method I18nRoute::__shouldStripDefaultLanguageOnMatch() protected I18nRoute::_shouldStripDefaultLanguageOnMatch()
 * [bd7960f](https://github.com/cakedc/i18n/commit/bd7960f) Fix code formatting. Represent new I18nRoute method to get default language
 * [67ef04c](https://github.com/cakedc/i18n/commit/67ef04c) Fixed an issue where lang code was removed from generated routes unexpectedly
 * [d304313](https://github.com/cakedc/i18n/commit/d304313) Added composer.json
 * [0d66524](https://github.com/cakedc/i18n/commit/0d66524) Adding a clarification that the plugin / i18nroute is for handling of setting the language based on the url NOT for translating the url itself to something. This is related to https://github.com/CakeDC/i18n/issues/18
 * [e6de0eb](https://github.com/cakedc/i18n/commit/e6de0eb) Fixing #13 #14 fixing url function in readme file and adding default language to Config.languages array
 * [e945542](https://github.com/cakedc/i18n/commit/e945542) Closes #15
 * [2fe3e61](https://github.com/cakedc/i18n/commit/2fe3e61) Fixing readme, closes #13
 * [58a2ea2](https://github.com/cakedc/i18n/commit/58a2ea2) Check languages for default before adding default.
 * [95a9b4a](https://github.com/cakedc/i18n/commit/95a9b4a) Some tests expect return value of false.
 * [bd927bc](https://github.com/cakedc/i18n/commit/bd927bc) Fixing problems with reverse routing and default language, fixes #7
 * [99020ec](https://github.com/cakedc/i18n/commit/99020ec) Update element call.
 * [ebf1cbb](https://github.com/cakedc/i18n/commit/ebf1cbb) Update from old paginator format.
 * [9aa0463](https://github.com/cakedc/i18n/commit/9aa0463) Fix parse error.
 * [e76c53d](https://github.com/cakedc/i18n/commit/e76c53d) Reverse routing gives unexpected results.
 * [14fba08](https://github.com/cakedc/i18n/commit/14fba08) Removing use of ife()
 * [8514fcd](https://github.com/cakedc/i18n/commit/8514fcd) Migrating rest of the code to 2.0
 * [4eda048](https://github.com/cakedc/i18n/commit/4eda048) Fixing some bugs and making all custom routing tests pass
 * [7f40aa5](https://github.com/cakedc/i18n/commit/7f40aa5) Removing google translate lib, it was deprecated by google
 * [efc6339](https://github.com/cakedc/i18n/commit/efc6339) Adding by default the same I18n route using the default language, simplifying Sluggable route
 * [919fbac](https://github.com/cakedc/i18n/commit/919fbac) Dropped support for localized plugin short route
 * [e6fa1de](https://github.com/cakedc/i18n/commit/e6fa1de) Making default routes work again
 * [d8cef78](https://github.com/cakedc/i18n/commit/d8cef78) Fixing a bug and removing unneded code
 * [1074f55](https://github.com/cakedc/i18n/commit/1074f55) Moving routes to their correct location, bringin sluggable route up to date
 * [9573902](https://github.com/cakedc/i18n/commit/9573902) Simplifying I18nRoute, dropping support for localized short plugin routes
 * [9ca37d6](https://github.com/cakedc/i18n/commit/9ca37d6) Renaming files and folders to match 2.0 conventions
 * [321c8f5](https://github.com/cakedc/i18n/commit/321c8f5) Renaming files and folders to match 2.0 conventions
 * [eb4a2d5](https://github.com/cakedc/i18n/commit/eb4a2d5) Renaming more files and folders, adding App::uses(), adding docblocks
 * [fede5ef](https://github.com/cakedc/i18n/commit/fede5ef) Renaming files and folders to match 2.0 conventions
 * [1518840](https://github.com/cakedc/i18n/commit/1518840) Renaming folders to match 2.0 conventions
 * [12ca1ea](https://github.com/cakedc/i18n/commit/12ca1ea) Refs https://github.com/CakeDC/i18n/issues/5 Testing pagination urls with admin and language