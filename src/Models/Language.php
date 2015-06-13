<?php

namespace Rhinodontypicus\DBLanguage\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
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
    protected $fillable = ['name', 'code', 'uri_prefix', 'description', 'datetime_format'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'languages';

    public function values()
    {
        return $this->hasMany('Rhinodontypicus\DBLanguage\Models\Value');
    }
}