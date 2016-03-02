<?php

namespace Rhinodontypicus\DBLanguage;

use Illuminate\Database\Eloquent\Collection;
use Psy\Exception\ErrorException;
use Rhinodontypicus\DBLanguage\Models\Constant;
use Rhinodontypicus\DBLanguage\Models\Value;

class DbLanguage
{
    /**
     * @var
     */
    protected $language;

    /**
     * @var Collection
     */
    protected $values;

    /**
     * Load language constants with values by languageId and group
     *
     * @param      $languageId
     * @param null $constantGroup
     * @return bool
     */
    public function load($languageId, $constantGroup = null)
    {
        $query = \DB::table('languages')
            ->where('languages.id', $languageId)
            ->leftJoin('language_constant_values', 'languages.id', '=', 'language_constant_values.language_id')
            ->leftJoin('language_constants', 'language_constant_values.constant_id', '=', 'language_constants.id')
            ->select('languages.*', 'language_constant_values.value', 'language_constants.group', 'language_constants.name as group_name');

        if ($constantGroup) {
            $query->where('language_constants.group', $constantGroup);
        }

        $data = $query->get();

        if (!$data) {
            return false;
        }

        $this->language = $data[0];
        $this->values = collect($data);
        return true;
    }

    /**
     * @param null $field
     * @return mixed
     */
    public function language($field = null)
    {
        if ($field) {
            return $this->language->{$field};
        }

        return $this->language;
    }

    /**
     * @param $constantName
     * @return mixed
     */
    public function get($constantName)
    {
        list($group, $name) = $this->splitName($constantName);

        $value = $this->findByGroupAndName($group, $name);

        return $value->value;
    }

    /**
     * @param $constantName
     * @param $constantValue
     * @return mixed
     */
    public function getAndSetForFirstLanguage($constantName, $constantValue)
    {
        if (! $this->language) {
            $this->load(config('laravel-db-language.defaultLanguageId'));
        }

        list($group, $name) = $this->splitName($constantName);

        $value = $this->findByGroupAndName($group, $name);

        if ($value->value === "$group::$name" && $this->language->id == config('laravel-db-language.defaultLanguageId')) {
            $this->createConstantForFirstLanguage($group, $name, $constantValue);

            return $constantValue;
        }

        return $value->value;
    }

    /**
     * @param $name
     * @return array
     */
    private function splitName($name)
    {
        if (strpos($name, '::') !== false) {
            list($group, $name) = explode('::', $name, 2);

            return [$group, $name];
        }

        return [null, $name];
    }

    /**
     * @param $group
     * @param $name
     * @return string
     */
    private function findByGroupAndName($group, $name)
    {
        if (!$this->values) {
            return (object)['value' => "$group::$name"];
        }

        $value = $this->values->search(function ($item) use ($group, $name) {
            return $item->group == $group && $item->group_name == $name;
        });

        if ($value === false) {
            return (object)['value' => "$group::$name"];
        }

        return $this->values[$value];
    }

    /**
     * @param $group
     * @param $name
     * @param $constantValue
     * @return bool|static
     */
    private function createConstantForFirstLanguage($group, $name, $constantValue)
    {
        if (!$this->language || $this->language->id != config('laravel-db-language.defaultLanguageId')) {
            return false;
        }

        $constant = Constant::firstOrCreate(['group' => $group, 'name' => $name]);
        $value = Value::create(
            [
                'constant_id' => $constant->id,
                'language_id' => $this->language->id,
                'value' => $constantValue
            ]
        );

        $this->values->push((object)['group' => $group, 'group_name' => $name, 'value' => $constantValue]);

        return $value;
    }
}
