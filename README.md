## ensphere - an alternative Laravel base for creating modular based applications
This was built to bridge the gap of standalone module development without manual configuration or duplicate assets.

1. Add the following to your ~/.bash_profile

```
function _ensphere {
	composer create-project ensphere/ensphere --repository-url=http://pmcom.packagist.wden.co.uk/ --stability=dev "$1"
}
alias ensphere=_ensphere
```

You can then create a new module by entering

```ensphere modelName```

And to rename the module (this does alot more than Laravel's)

```artisan ensphere:rename```

If you are creating a module and not the start of a base application, do not use the app configuration file to register service providers, aliases or middleware, define these in the registration.json file.

When requiring assets (js/css), you must use bower.

Run gulp and get cracking!