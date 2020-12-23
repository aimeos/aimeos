<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

# Aimeos shop system

[![Total Downloads](https://poser.pugx.org/aimeos/aimeos/d/total.svg)](https://packagist.org/packages/aimeos/aimeos)
[![License](https://poser.pugx.org/aimeos/aimeos/license.svg)](https://packagist.org/packages/aimeos/aimeos)

:star: Star us on GitHub — it helps!

[Aimeos](https://aimeos.org/Laravel) is THE professional, full-featured and
high performance e-commerce platform! You can install it within 5 minutes
and can adapt, extend, overwrite and customize anything to your needs.

[![Aimeos Laravel demo](https://aimeos.org/fileadmin/aimeos.org/images/aimeos-github.png)](http://laravel.demo.aimeos.org/)

## Features

Aimeos is a full-featured e-commerce package:

* Multi vendor, multi channel and multi warehouse
* From one to 1,000,000,000+ items
* Extremly fast down to  20ms
* For multi-tentant e-commerce SaaS solutions
* Bundles, vouchers, virtual, configurable, custom and event products
* Subscriptions with recurring payments
* 100+ payment gateways
* Block/tier pricing out of the box
* Extension for customer/group based prices
* Discount and voucher support
* Flexible basket rule system
* Full-featured admin backend
* Beautiful admin dashboard
* Configurable product data sets
* JSON REST API based on jsonapi.org
* Completly modular structure
* Extremely configurable and extensible
* Extension for market places with millions of vendors
* Fully SEO optimized including rich snippets
* Translated to 30+ languages
* AI-based text translation
* Optimized for smart phones and tablets
* Secure and reviewed implementation
* High quality source code

... and [more Aimeos features](https://aimeos.org/features)

Check out the demos:

* [Aimeos frontend demo](http://laravel.demo.aimeos.org/)
* [Aimeos admin demo](http://admin.demo.aimeos.org/)

## Package only

Want to **integrate Aimeos** into your **existing application**?

Use the [Aimeos Laravel package](https://github.com/aimeos/aimeos-laravel) directly!

## Table of content

- [Requirements](#requirements)
- [Installation](#installation)
- [Frontend](#frontend)
- [Backend](#backend)
- [Customize](#customize)
- [Multi-vendor](#multi-vendor)
- [License](#license)
- [Links](#links)

## Requirements

The Aimeos shop distribution requires:
- Linux/Unix, WAMP/XAMP or MacOS environment
- PHP >= 7.2
- MySQL >= 5.7.8, MariaDB >= 10.2.2
- Web server (Apache, Nginx or integrated PHP web server for testing)

If required PHP extensions are missing, `composer` will tell you about the missing
dependencies.

## Installation

To install the Aimeos shop application, you need [composer](https://getcomposer.org).
On the CLI, execute this command for a complete installation including a working setup:

`composer create-project aimeos/aimeos myshop`

You will be asked for the parameters of your database and mail server as well as an
e-mail and password used for creating the administration account.

In a local environment, you can use the integrated PHP web server to test your new Aimeos
installation. Simply execute the following command to start the web server:

```
cd myshop
php artisan serve
```

**Note:** In an hosting environment, the document root of your virtual host must point to
the **/.../myshop/public/** directory and you have to change the `APP_URL` setting in your `.env`
file to your domain without port, e.g.:

```
APP_URL=http://myhostingdomain.com
```

## Frontend

After the installation, you can test the Aimeos shop frontend by calling the URL of your
VHost in your browser. If you use the integrated PHP web server, you should browse
this URL: [http://127.0.0.1:8000](http://127.0.0.1:8000)

[![Aimeos frontend](https://aimeos.org/fileadmin/aimeos.org/images/aimeos-frontend.png)](http://laravel.demo.aimeos.org/)

## Backend

The Aimeos administration interface will be available at `/admin` in your VHost. When using
the integrated PHP web server, call this URL: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

[![Aimeos admin backend](https://aimeos.org/fileadmin/aimeos.org/images/aimeos-backend.png)](http://admin.demo.aimeos.org/)

## Customize

Laravel and the Aimeos e-commerce package are extremely flexible and highly customizable.
A lot of documentation for the [Laravel framework](https://laravel.com) and the
[Aimeos e-commerce framework](https://aimeos.org/docs/latest/laravel) exists. If you have questions
about Aimeos, don't hesitate to ask in our [Aimeos forum](https://aimeos.org/help/).

For more details about Aimeos Laravel integration, please have a look at its
[repository](https://github.com/aimeos/aimeos-laravel).

## Multi-vendor

To enable multi-vendor features including self-registration for new sellers, add this
settings to the `./myshop/.env` file:

```
SHOP_MULTISHOP=true
SHOP_REGISTRATION=true
```

By default, newly registered sellers have administrator privileges in the backend for
their own site. For a more limited access to the backend, you can change the permission
level to "editor":

```
SHOP_PERMISSION=editor
```

You can change the permissions associated to "admin" or "editor" by adding your own version
of the [JQAdm resource configuration](https://github.com/aimeos/ai-admin-jqadm/blob/master/config/admin/jqadm/resource.php)
to the "admin" section of your `./config/shop.php` file.

## License

The Aimeos shop system is licensed under the terms of the MIT and LGPLv3 license and
is available for free.

## Links

* [Web site](https://aimeos.org/Laravel)
* [Documentation](https://aimeos.org/docs/latest/laravel)
* [Forum](https://aimeos.org/help/laravel-package-f18/)
* [Issue tracker](https://github.com/aimeos/aimeos/issues)
* [Composer packages](https://packagist.org/packages/aimeos/aimeos)
* [Source code](https://github.com/aimeos/aimeos)
