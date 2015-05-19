#laravelGenerator
This will be a library of commands that can be used to create php code files for Models, Controllers, etc... with much more than the 
default code than `php artisan make:model` will generate. I started out doing a lot of code in Yii so this is inspired by Yii's gii options.

##Installation
Modify /app/Console/Kernel.php

```
class Kernel extends ConsoleKernel {

	protected $commands = [
		...
		'App\Console\Commands\GenerateCommand',
	];
  ...
```

##Usage
Currently only the generate models option is created, to use it simply use:

`php artisan generate:models`