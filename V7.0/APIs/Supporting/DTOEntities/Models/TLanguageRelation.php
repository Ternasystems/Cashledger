<?php

namespace API_DTOEntities_Model;

use API_RelationRepositories_Collection\LanguageRelations;

/**
 * A trait that provides any entity with the ability to lazily load its own translations.
 * It uses a static provider to hold the master collection of language relations for the
 * current request, and each entity instance can then filter this collection for its own needs.
 */
trait TLanguageRelation
{
    /**
     * @var LanguageRelations|null A local cache for this entity's specific translations.
     */
    private ?LanguageRelations $languageRelations = null;

    /**
     * @var LanguageRelations|null The static, request-level provider holding all translations.
     */
    private static ?LanguageRelations $languageRelationProvider = null;

    /**
     * Sets the static provider with the master collection of language relations.
     * This is typically called once by a factory before it starts building entities.
     *
     * @param LanguageRelations|null $provider The complete collection of language relations.
     */
    public static function LanguageRelationProvider(?LanguageRelations $provider): void
    {
        self::$languageRelationProvider = $provider;
    }

    /**
     * Gets the language-specific translations for this entity instance.
     *
     * This method implements lazy loading:
     * 1. It first checks if the translations have already been fetched for this instance.
     * 2. If not, it accesses the static provider.
     * 3. It filters the provider's collection to find relations matching its own ID.
     * 4. It caches the result locally for future calls.
     *
     * @return LanguageRelations|null
     */
    public function languageRelations(): ?LanguageRelations
    {
        // If we haven't already figured out the relations for this specific entity...
        if ($this->languageRelations === null && self::$languageRelationProvider !== null) {
            // ...then filter the master list and cache the result locally.
            $this->languageRelations = self::$languageRelationProvider->where(fn($n) => $n->ReferenceId == $this->it()->Id);
        }

        return $this->languageRelations;
    }
}