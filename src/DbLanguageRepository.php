<?php

namespace Rhinodontypicus\DBLanguage;

use Rhinodontypicus\DBLanguage\Exceptions\LanguageNotFoundException;

class DbLanguageRepository
{
    /**
     * @param $languageId
     * @param $constantGroup
     * @return bool|\Illuminate\Support\Collection
     * @throws LanguageNotFoundException
     */
    public function getValues($languageId, $constantGroup)
    {
        $query = \DB::table('languages')
            ->where('languages.id', $languageId)
            ->leftJoin('language_constant_values', 'languages.id', '=', 'language_constant_values.language_id')
            ->leftJoin('language_constants', 'language_constant_values.constant_id', '=', 'language_constants.id')
            ->select('languages.*', 'language_constant_values.value', 'language_constants.group', 'language_constants.name as group_name');

        if ($constantGroup) {
            $query->where('language_constants.group', $constantGroup);
        }

        $result = $query->get();

        if (!$result) {
            throw new LanguageNotFoundException("Language with specified id does not exists");
        }

        if (is_array($result)) {
            return collect($result);
        }

        return $result;
    }
}
