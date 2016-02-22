# Ensphere

I've writen this to build a process for creating modular based applications. It is basically Laravel 5.2 with a few commands and wrappers to sync everything automatically without having to manually add middleware, service providers or aliases and to manage frontend dependencies better without duplication libraries.

## Test case preperation:

Firstly make sure you have bower installed, run `bower` to check.

```
npm install -g bower
```

and also make sure node is installed (https://nodejs.org/en/), run `npm` in terminal to check.


Create a local database called `l5` with credentials `root` `root` (make sure it's empty).

Open up `~/.bash_profile` and add the following function and then restart terminal :
(for those with hidden files not visible and using sublimetext2 run `/Applications/SublimeText2.app/Contents/SharedSupport/bin/subl ~/.bash_profile` in terminal)

[running `source ~/.bash_profile` from terminal will refresh without having to restart it]

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

This will run the standard `composer create` command along with some extra installation.

Hopefully this has all installed correctly so cd into baseapp and start a server up:

```
cd baseapp
```

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

### The MIT License (MIT)

Copyright (c) 2016 ensphere

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.