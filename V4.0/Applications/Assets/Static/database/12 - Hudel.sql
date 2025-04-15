-- Initial data

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50);
BEGIN

	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	
	-- App registry
	
	CALL public."p_InsertApp"('Hudel');
	
	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Hudel';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Hudel');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Hudel');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Hudel');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Decision making') THEN
		CALL public."p_InsertAppCategory"('Decision making');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Decision making');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Prise de décisions');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Analysis') THEN
		CALL public."p_InsertAppCategory"('Analysis');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Analysis';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Analysis');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Analyses');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'AI') THEN
		CALL public."p_InsertAppCategory"('AI');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'AI';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'AI');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'AI');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'IA');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Hudel';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Decision making';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Analysis';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'AI';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;