<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class BaseModel extends Model {
    
    protected $validations = array();

    public function validate() {
        $array = $this->toArray();
        $validator = Validator::make($array, $this->validations);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return $messages->all();
        } else {
            return true;
        }
    }
    
}
