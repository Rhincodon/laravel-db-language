<?php

namespace Rhinodontypicus\DBLanguage;

use Illuminate\Database\Eloquent\Collection;
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
     *
     */
    public function __construct()
    {

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

        if (!$this->language) {
            throw new ErrorException('No language provided');
        }

        $this->values = $this->loadConstants($languageId, $constantGroup);
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

    public function getAndSetForFirstLanguage($constantName, $constantValue)
    {
        list($group, $name) = $this->splitName($constantName);

        $value = $this->findByGroupAndName($group, $name);

        if ($value['value'] === "$group::$name") {
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
        $value = $this->values->search(function ($item) use ($group, $name) {
            return $item['group'] == $group && $item['name'] == $name;
        });

        if ($value === false) {
            return ['value' => "$group::$name"];
        }

        return $this->values[$value];
    }

    private function createConstantForFirstLanguage($group, $name, $constantValue)
    {
        // Find first language
        $language = LanguageModel::find(1);
        if ($language === null) {
            return false;
        }

        $constant = Constant::create(['group' => $group, 'name' => $name]);
        $value = Value::create(['constant_id' => $constant->id, 'language_id' => $language->id, 'value' => $constantValue]);
        return true;
    }
}
