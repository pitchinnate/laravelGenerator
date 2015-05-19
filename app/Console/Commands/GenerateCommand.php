<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB;

class GenerateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:models';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create Models based off of the db';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
            $model_dir = __DIR__ . '/../../Models/';
            $model_namespace = 'App\\Models';
            $model_base = 'BaseModel';
            $model_base_namespace = 'App\\Models\\BaseModel';
            
            $all_tables = [];
            $tables = DB::select('SHOW tables');
            foreach($tables as $table) {
                foreach($table as $key => $val) {
                    $all_tables[] = $val;
                }
            }
            
            $migrate_key = array_search('migrations',$all_tables);
            if($migrate_key) {
                unset($all_tables[$migrate_key]);
            }
            
            foreach($all_tables as $table) {
                $pieces = explode('_',$table);
                $newName = '';
                foreach($pieces as $piece) {
                    $newName .= ucfirst($piece);
                }
                
                $modelFile = $model_dir . $newName . '.php';
                if(!is_file($modelFile)) {
                    $columns = DB::select("DESCRIBE `{$table}`");
                    $this->info($modelFile);
                    
$content = "<?php
namespace {$model_namespace};

use {$model_base_namespace};

/**
";

$column_names = [];
$validations = "";
foreach($columns as $column) {
    if($column->Field != 'created_at' && $column->Field != 'updated_at' && $column->Field != 'id') {
        $column_names[] = $column->Field;
        if(count($column_names) > 1) {
            $validations .= "\n                            ";
        }
        $validations .= "'{$column->Field}' => '',";
    }
    if(substr($column->Type,0,3) == 'int') {
         $content .= " * @property integer \$" . $column->Field . "\n";
    } else {
        $content .= " * @property string \$" . $column->Field . "\n";
    }
}
$fillable = implode("','",$column_names);
$validations = substr($validations,0,-1);

$content .= " */

class {$newName} extends {$model_base} {

    protected \$table = '{$table}';
    protected \$fillable = ['{$fillable}'];
    protected \$validations = [{$validations}];

}
";
                    $file = fopen($modelFile,'w');
                    fwrite($file, $content);
                    fclose($file);
                }
            }
	}

}
