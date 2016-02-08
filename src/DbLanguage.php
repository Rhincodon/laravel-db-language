<?php

namespace Rhinodontypicus\DBLanguage;

use Illuminate\Database\Eloquent\Collection;
use Psy\Exception\ErrorException;
use Rhinodontypicus\DBLanguage\Models\Constant;
use Rhinodontypicus\DBLanguage\Models\Language as LanguageModel;
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
     * @param $languageId
     * @param null $constantGroup
     * @throws ErrorException
     */
    public function load($languageId, $constantGroup = null)
    {
        $this->language = LanguageModel::findOrFail($languageId);

        $this->values = $this->loadConstants($languageId, $constantGroup);
    }

    /**
     * @param null $field
     * @return mixed
     */
    public function language($field = null)
    {
        if ($field) {
            return $this->language[$field];
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

        return $value['value'];
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

        if ($value['value'] === "$group::$name" && $this->language->id == config('laravel-db-language.defaultLanguageId')) {
            $this->createConstantForFirstLanguage($group, $name, $constantValue);

            return $constantValue;
        }

        return $value['value'];
    }


    /**
     * @param $languageId
     * @param $constantGroup
     * @return mixed
     */
    private function loadConstants($languageId, $constantGroup = null)
    {
        $where = [];
        if ($constantGroup) {
            $where = ['group' => $constantGroup];
        }

        $constantsIds = Constant::where($where)->pluck('id')->all();

        $constantValues = Value::with('constant')
            ->where('language_id', $languageId)
            ->whereIn('constant_id', $constantsIds)
            ->get();

        $constantValues = $this->parseValuesCollection($constantValues);

        return $constantValues;
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
     * @param $constantValues
     * @return Collection $result
     */
    private function parseValuesCollection($constantValues)
    {
        $result = collect([]);
        foreach ($constantValues as $value) {
            $value = [
                'name' => $value->constant->name,
                'group' => $value->constant->group,
                'value' => $value->value
            ];
            $result->push($value);
        }

        return $result;
    }

    /**
     * @param $group
     * @param $name
     * @return string
     */
    private function findByGroupAndName($group, $name)
    {
        if (!$this->values) {
            return ['value' => "$group::$name"];
        }

        $value = $this->values->search(function ($item) use ($group, $name) {
            return $item['group'] == $group && $item['name'] == $name;
        });

        if ($value === false) {
            return ['value' => "$group::$name"];
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
        if (!$this->language || $this->language->id != 1) {
            return false;
        }
        $constant = Constant::create(['group' => $group, 'name' => $name]);
        $value = Value::create(
            [
                'constant_id' => $constant->id,
                'language_id' => $this->language->id,
                'value' => $constantValue
            ]
        );

        $this->values->push(['group' => $group, 'name' => $name, 'value' => $constantValue]);

        return $value;
    }
}
