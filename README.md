# Ensphere

I've writen this to build a process for creating modular based applications. It is basically Laravel 5.2 with a few commands and wrappers to sync everything automatically without having to manually add middleware, service providers or aliases and to manage frontend dependencies better without duplication libraries.

## Test case preperation:

Firstly make sure you have bower installed (run `bower` to check)
```
npm install -g bower
```

Create a local database called `l5` with credentials `root` `root` (make sure it's empty).

Open up `~/.bash_profile` and add the following function and then restart terminal:

```
alias composer="php ~/composer.phar"
alias artisan="php artisan"
function _ensphere {
	composer create-project ensphere/ensphere --repository-url=http://pmcom.packagist.wden.co.uk/ --stability=dev "$1"
}
alias ensphere=_ensphere
```

if you haven't got composer installed globally, go to `https://getcomposer.org/composer.phar` and save it in `~/composer.phar`

## Test case

Navigate to your documents folder
```
cd ~/Documents/
```

Create a testcase folder

```
mkdir testcase
```

and then cd into it

```
cd testcase
```

Create a default application or module by running
```
ensphere baseapp
```

This will run the standard `composer create` command along with some extra installation (It will also run npm commands so make sure node is installed on your machine https://nodejs.org/en/).

Hopefully this has all installed correctly so cd into baseapp `cd baseapp` and start a server up.

```
artisan serve
```

(if you already have the default port in operation use `artisan serve --port=8888`)

Navigate to `http://localhost:8000/admin/login` in your browser and you should receive an error (404 Exception).

Now back to terminal and create a new tab in terminal (command + t) and then require the authentication module.

```
composer require ensphere/authentication:dev-master
```

Once installed, go back to your browser and hit refresh, boom!
