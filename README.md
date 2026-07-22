# Jumi

Jumi is a set of Joomla! extensions that let you include custom code (HTML, PHP,
JavaScript, CSS, ...) into Joomla! pages.

This repository has been **migrated to Joomla! 6** (Joomla 4/5/6 compatible
architecture: namespaced MVC, dependency-injection service providers,
`SubscriberInterface` plugins and a component-based SEF router).

## Contents

The repository is a Joomla! **package** (`pkg_jumi`) that bundles three
extensions, found under `packages/`:

| Extension | Folder | Purpose |
| --- | --- | --- |
| `com_jumi` | `packages/com_jumi` | Component. Manages Jumi applications and renders them on the front-end. Includes the SEF router. |
| `mod_jumi` | `packages/mod_jumi` | Module. Includes custom code into a module position. |
| `plg_system_jumi` | `packages/plg_system_jumi` | System plugin. Replaces `{jumi [source] [arg1] ... [argN]}` tags in content. |

> The legacy standalone *Jumi Router* system plugin has been removed; SEF
> routing is now handled by the component's `Site\Service\Router` class, which
> is the Joomla 4+ way of doing it.

## Installation

Zip the repository (or the individual extension folders) and install through
**System → Install → Extensions** in the Joomla! administrator. Installing the
package installs the component, module and plugin, and enables the plugin
automatically.

## Requirements

- Joomla! 4.4 / 5.x / 6.x
- PHP 8.1+

## License

GNU General Public License version 2 or later; see `LICENSE`.
Copyright (C) 2026 LennartMart. All rights reserved.
