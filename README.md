# eZ (platform) Siteaccess Matchers Bundle

 master | [![Build Status](https://travis-ci.org/Novactive/ezsiteaccessmatchers-bundle.svg?branch=master)](https://travis-ci.org/Novactive/ezsiteaccessmatchers-bundle)|
--------|---------|

## About

This eZ publish bundle provides additional siteaccess matchers, usefull for mapping siteacesses with Platform.sh dynamic environments urls.

The new matchers are :

* ExtendedHostElement : use a host element for siteaccess identification and replace the provided patterns in the string ( ex: "-" by "_" )
* SuffixedHostElement : use a host element for siteaccess identification and suffix it with the provided string
* PrefixedHostElement : use a host element for siteaccess identification and prefix it with the provided string

## Installation

The recommended way to install this bundle is through [Composer](http://getcomposer.org/). Just run :

```bash
composer require novactive/ezsiteaccessmatcher-bundle
```

Register the bundle in the kernel of your application :

```php
// ezpublish/EzPublishKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Novactive\EzSiteaccessMatchersBundle\NovaEzSiteaccessMatchersBundle(),
    );

    return $bundles;
}
```

## Usage

### ExtendedHostElement matcher:

In your ezpublish/config/ezpublish.yml / app/config/ezplatform.yml config file :

```yml
ezpublish:
    ...
    siteaccess:
        ...
        match:
            \Novactive\EzSiteaccessMatchersBundle\Matcher\ExtendedHostElement:
                elementNumber: 1
# Replacements config is not mandatory as following config is default one
#                replacements:
#                    pattern: '-'
#                    replacement: '_'
# You could also provide array of patterns / replacements strings
#                replacements:
#                    pattern: ['-']
#                    replacement: ['_']
```

### SuffixedHostElement matcher:

In your ezpublish/config/ezpublish.yml / app/config/ezplatform.yml config file :

```yml
ezpublish:
    ...
    siteaccess:
        ...
        match:
            \Novactive\EzSiteaccessMatchersBundle\Matcher\SuffixedHostElement:
                elementNumber: 1
                suffix: test
```

### PrefixedHostElement matcher:

In your ezpublish/config/ezpublish.yml / app/config/ezplatform.yml config file :

```yml
ezpublish:
    ...
    siteaccess:
        ...
        match:
            \Novactive\EzSiteaccessMatchersBundle\Matcher\PrefixedHostElement:
                elementNumber: 1
                prefix: test
```

## License

This bundle is released under the MIT license. See the complete license in the bundle:

```bash
LICENSE
```
