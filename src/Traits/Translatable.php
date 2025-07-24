<?php

namespace MohamedAhmed\LaravelPolyLang\Traits;

use Illuminate\Support\Facades\App;

trait Translatable
{
    public function translations()
    {
        return $this->morphMany(\MohamedAhmed\LaravelPolyLang\Models\Translation::class, 'translatable');
    }

    public function getTranslation(string $field, string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();

        $translation = $this->translations->where('field', $field)->where('locale', $locale)->first();

        if ($translation) {
            return $translation->value;
        }

        // fallback
        $fallbackLocale = config('app.locale');
        if ($locale !== $fallbackLocale) {
            return $this->translations
                    ->where('field', $field)
                    ->where('locale', $fallbackLocale)
                    ->first()
                    ->value ?? null;
        }

        return null;
    }

    public function setTranslation(string $field, string $value, string $locale)
    {
        $this->translations()->updateOrCreate([
            'field' => $field,
            'locale' => $locale,
        ], ['value' => $value]);
    }

    public function getTranslatedAttributes(array $fields, string $locale = null): array
    {
        return collect($fields)->mapWithKeys(function ($field) use ($locale) {
            return [$field => $this->getTranslation($field, $locale)];
        })->toArray();
    }
}
