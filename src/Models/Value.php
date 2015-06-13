<?php

namespace Rhinodontypicus\DBLanguage\Models;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
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
    protected $fillable = ['constant_id', 'language_id', 'value'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'language_constant_values';

    public function constant()
    {
        return $this->belongsTo('Rhinodontypicus\DBLanguage\Models\Constant');
    }

    public function language()
    {
        return $this->belongsTo('Rhinodontypicus\DBLanguage\Models\Language');
    }
}