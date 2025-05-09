<?php

namespace API_Profiling_Service;

use API_Administration_Contract\ILanguageService;
use API_DTOEntities_Factory\CollectableFactory;
use API_Profiling_Contract\IContactService;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Factory\ContactFactory;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingEntities_Model\ContactType;
use API_ProfilingRepositories\ContactTypeRepository;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Model\ContactRelation;
use ReflectionException;

class ContactService implements IContactService
{
    protected ContactFactory $contactFactory;
    protected ContactTypes $contactTypes;
    protected ContactTypeRepository $contactTypeRepository;
    protected ContactRelationRepository $contactRelationRepository;
    protected ILanguageService $languageService;

    /**
     * @throws ReflectionException
     */
    public function __construct(ContactFactory $_contactFactory, ContactTypeRepository $_contactTypeRepository, LanguageRelationRepository $_relationRepository,
                                ContactRelationRepository $_contactRelationRepository, ILanguageService $_languageService)
    {
        $this->contactFactory = $_contactFactory;
        $this->contactTypeRepository = $_contactTypeRepository;
        $this->contactRelationRepository = $_contactRelationRepository;
        $this->languageService = $_languageService;
        $factory = new CollectableFactory($_contactTypeRepository, $_relationRepository);
        $factory->Create();
        $this->contactTypes = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function GetContacts(callable $predicate = null): Contact|Contacts|null
    {
        $this->contactFactory->Create();
        if (is_null($predicate))
            return $this->contactFactory->Collectable();

        $collection = $this->contactFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetContactTypes(callable $predicate = null): ContactType|ContactTypes|null
    {
        if (is_null($predicate))
            return $this->contactTypes;

        $collection = $this->contactTypes->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetContact(object $model): void
    {
        $repository = $this->contactFactory->Repository();
        $repository->Add(\API_ProfilingRepositories_Model\Contact::class, array($model->contacttypeid, $model->profileid, $model->contactname,
            $model->contactdesc ?? null));
        $id = $repository->GetBy(fn($n) => $n->ProfileId == $model->profileid && $n->Name == $model->contactname)->FirstOrDefault()->Id;
        //
        $languages = $this->languageService->GetLanguages();
        //
        foreach ($languages as $language) {
            $lang = $language->It()->Label;
            $langId = $language->It()->Id;
            if (!key_exists($lang, $model->contacts)) continue;
            $this->contactRelationRepository->Add(ContactRelation::class, array($langId, $id, $model->contacts[$lang], $model->contactphoto, $model->contactdesc ?? null));
        }
        //
        $this->contactFactory->Create();
    }

    public function PutContact(object $model): void
    {
        // TODO: Implement PutContact() method.
    }

    public function DeleteContact(string $id): void
    {
        // TODO: Implement DeleteContact() method.
    }
}