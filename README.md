# Tradecast Wordpress Plugin

## Description

A plugin to easily add Tradecast content to your WordPress site. The plugin lets you add videos and galleries in WordPress. The provided Gutenberg blocks make it easy to add videos and/or galleries to posts and pages.

## Development

This repository is a mono repo, meaning that this repository contains all components required for the WordPress plugin (including Gutenberg blocks and Vue components libraries) in one repository.

### Setup

#### Prerequisites

When you checked out the repository from Bitbucket, first you have to install the npm packages through yarn. Before you do, please make sure you have yarn installed on your system (through Corepack), see [https://yarnpkg.com/getting-started/install](https://yarnpkg.com/getting-started/install) for more information. **Note:** all packages are created using Node v16.13.2 LTS.

> **The mentioned shell commands have to be run from the root of your local repository!**

#### Install all modules

To install the npm packages for all modules, run the following command:

```shell
yarn install:all
```

#### Install single modules

You can also install npm packages per module.

**Development tools in the root**
```shell
yarn install
```

**Gutenberg block for galleries**
```shell
yarn install:gallery-block
```

**Gutenberg block for videos**
```shell
yarn install:video-block`
```

**Vue admin components library**
```shell
yarn install:admin`
``` 

**Vue front-end components library**
```shell
yarn install:front-end`
```

### Link library

The Vue library (in `src/vue-library`) is symlinked to the Vue admin components library and the Vue front-end components library through the command stated below. This command is also part of the command `yarn install:all`. So if you already used `yarn install:all`, you should already be good to go.

```shell
yarn link:library
```

### Linting

To lint the entire project for code styling errors in JSON/JavaScript/TypeScript/Vue and PHP, use the following command: 

```shell
yarn lint
```

### Build

The Gutenberg blocks and Vue component libraries need to be build in order to be useable in the plugin. There are some commands to help you in the build process. The build works automatically and built files will be placed in the corresponding directories inside `src/wp-admin/src`.

#### Build all modules

To build all the modules (that require JavaScript/TypeScript/CSS/SCSS to be processed) with one command, you can use:

```shell
yarn build
```  

#### Build single modules

**Gutenberg block for galleries**
```shell
yarn build:gallery-block`
```

**Gutenberg block for videos**
```shell
yarn build:video-block`
```

**Vue admin component library**
```shell
yarn build:admin`
```

**Vue front-end component library**
```shell
yarn build:front-end`
```

### Translations

Translations for the Vue applications are stored in the `locales` folder of the Vue application. If any translations are present, two JSON files are found in the directory (`en.json` and `nl.json`, for English and Dutch).

You can use a translated string in the Vue application by including `const { t } = useI18n();` in the `setup()`.

Translated strings are retrieved by `t('path.to.field')`, for instance `t('buttons.generic.cancel.label')` retrieves the label from:
```json
{
    buttons: {
        generic: {
            cancel: {
                label: 'Cancel'
            }
        }
    }
}
```

### Styling and images

All stylesheets and images are present in the `src/assets` folder. The stylesheets are created using SCSS (SaSS) and are automatically compiled when you run `yarn build` or when you use the watcher for a specific Vue application, for instance `watch:front-end`.

## Elements

### WordPress Plugin

The WordPress plugin is based on wppb.me and structured accordingly. In the admin class (`src/wp-plugin/src/class-tradecast-admin.php`) the dependencies for the admin are loaded and the Vue component is enqueued. In the public class (`src/wp-plugin/src/public/class-tradecast-public.php`) the same is done for the front-facing elements of the plugin.

### Gutenberg block for galleries

The gallery block is found in `src/wp-gutenberg-block-gallery` and can be edited from there. See the [Build](#build) topic to learn more about building the Gutenberg block.

### Gutenberg block for videos

The video block is found in `src/wp-gutenberg-block-video` and can be edited from there. See the [Build](#build) topic to learn more about building the Gutenberg block.

### Vue admin components library

The video block is found in `src/vue-admin` and can be edited from there. See the [Build](#build) topic to learn more about building the Vue component library.

### Vue front-end components library

The video block is found in `src/vue-front-end` and can be edited from there. See the [Build](#build) topic to learn more about building the Vue component library.

## Creating a release

Start by building very element through `yarn build`. This builds every JavaScript package and puts the builds in the right directory within the WordPress plugin (`src/wp-plugin`). Once all builds are done, the WordPress plugin directory (`src/wp-plugin`) contains everything that is required for the plugin te function.

You could either upload the contents of that directory to the `wp-plugins` folder of a WordPress installation (e.g. in `wp-plugins/tradecast`), or you could create a zip file containing the contents of the `wp-plugin` folder and upload it as a plugin in the plugins page of the WordPress admin.

## Contributors

- Reinder van Bochove ([Kiener Digital Commerce](https://www.kiener.nl))