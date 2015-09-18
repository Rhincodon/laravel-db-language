<?php

namespace Rhinodontypicus\DBLanguage\Models;

class Value extends \Eloquent
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function constant()
    {
        return $this->belongsTo(Constant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
