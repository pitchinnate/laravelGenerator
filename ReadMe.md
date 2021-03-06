#laravelGenerator
This will be a library of commands that can be used to create php code files for Models, Controllers, etc... with much more than the 
default code than `php artisan make:model` will generate. I started out doing a lot of code in Yii so this is inspired by Yii's gii options.
The commands will connect to your DB you have setup in your .env to pull information and generate more intellegent files.

***THIS HAS ONLY BEEN TESTED WITH MYSQL***

##Installation
GenerateModel.php goes into the /app/Console/Commands directory.
I have also included a simple BaseModel file that I use to base all of my Models off of. This would go in /app/Models, as I store
all my model files in /app/Models instead of /app which is where `php artisan make:model` does.

Modify /app/Console/Kernel.php

```
class Kernel extends ConsoleKernel {

	protected $commands = [
		...
		'App\Console\Commands\GenerateModel',
	];
  ...
```

##Config
I have setup some defaults in the GenerateModel.php file. You can edit these lines to get your desired results:

```
$model_dir = __DIR__ . '/../../Models/';
$model_namespace = 'App\\Models';
$model_base = 'BaseModel';
$model_base_namespace = 'App\\Models\\BaseModel';
```

##generate:models
Currently only the generate models option is created, to use it simply use:

`php artisan generate:models`

This will generate a model file for every single table in your db, other than migrations. It adds commented out properties
for easier use in IDE's. Sets up your `protected $table`, `protected $fillable` and an array `protected $validations` for 
use with the Laravel's Validation class. If a file already exists with the same name (example: User.php) it will skip that table
so you don't accidentally overwrite code.

Example result:

```
<?php
namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $group_id
 * @property string $email
 * @property string $password
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */

class User extends BaseModel {

    use SoftDeletes;

    protected $table = 'user';
    protected $fillable = ['email','password','status'];
    protected $validations = ['group_id' => 'exists:group,id|integer|min:0',
                            'email' => 'max:255|string|required',
                            'password' => 'max:255|string|required',
                            'status' => 'max:255|string|required'];
                            
    public function Group() {
        return $this->belongsTo('App\Models\Group');
    }
    public function UserLogin() {
        return $this->hasMany('App\Models\UserLogin');
    }

}
```

###Update 1.0
- Renamed GenerateCommand to GenerateModel to prepare for a GenerateController and other features
- Added auto-detect of soft deletes and builds it into the model
- Added auto-detect of Forgein keys and automatically creates functions for hasMany and belongsTo
- Added some automatically generated validations based off of the column type, size, etc...
