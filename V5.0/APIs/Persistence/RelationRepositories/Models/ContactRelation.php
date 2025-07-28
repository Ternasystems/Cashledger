<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class ContactRelation extends DTOBase
{
    public string $LangId;
    public string $ContactId;
    public string $Contact;
    public ?string $Photo;
}