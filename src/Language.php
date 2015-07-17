<?php

namespace Rhinodontypicus\DBLanguage;

use Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Psy\Exception\ErrorException;
use Rhinodontypicus\DBLanguage\Models\Constant;
use Rhinodontypicus\DBLanguage\Models\Language as LanguageModel;
use Rhinodontypicus\DBLanguage\Models\Value;

class Language
{
    /**
     * @var
     */
    protected $language;
    /**
     * @var
     */
    protected $values;

    /**
     * @var bool
     */
    protected $dontLoad = false;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (strpos($request->path(), 'stock-photos') !== false) {
            $this->dontLoad = true;
        };
    }

    /**
     * Load language constants with values by languageId and group
     *
     * @param $languageId
     * @param null $constantGroup
     * @throws ErrorException
     */
    public function load($languageId, $constantGroup = null)
    {
        $this->language = LanguageModel::find($languageId);

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
        if (!$this->language) {
            $this->load(1);
        }

        list($group, $name) = $this->splitName($constantName);

        $value = $this->findByGroupAndName($group, $name);

        if ($value['value'] === "$group::$name" && $this->language->id == 1) {
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
    private function loadConstants($languageId, $constantGroup)
    {
        $where = [];
        if ($constantGroup) {
            $where = ['group' => $constantGroup];
        }

        $constantsIds = Constant::where($where)->lists('id')->all();

        $constantValues = Cache::remember('constant_values', 60, function () use ($languageId, $constantsIds) {
            return Value::with('constant')
                ->where('language_id', $languageId)
                ->whereIn('constant_id', $constantsIds)
                ->get();
        });

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
     * @return Collection
     */
    private function parseValuesCollection($constantValues)
    {
        $result = new Collection();
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


    /**
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            if (!$this->dontLoad) {
                call_user_func_array(array($this, $method), $arguments);
            }
        }
    }
}
