<?php

namespace Rhinodontypicus\DBLanguage\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Constant extends \Eloquent
{
    use SoftDeletes;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(Value::class);
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
