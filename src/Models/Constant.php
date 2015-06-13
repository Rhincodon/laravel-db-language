<?php

namespace Rhinodontypicus\DBLanguage\Models;

use Illuminate\Database\Eloquent\Model;

class Constant extends Model
{
    /**
     * Disable updated_at and created_at columns
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'group'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'language_constants';

    public function values()
    {
        return $this->hasMany('Rhinodontypicus\DBLanguage\Models\Value');
    }
}