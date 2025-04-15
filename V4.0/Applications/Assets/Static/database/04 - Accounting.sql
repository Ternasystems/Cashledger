-- Initial data

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50);
BEGIN

	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	
	-- App registry
	
	CALL public."p_InsertApp"('Accounting');
	
	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Accounting';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Accounting');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Accounting');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Comptabilité');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Accounting') THEN
		CALL public."p_InsertAppCategory"('Accounting');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Accounting';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Accounting');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Accounting');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Comptabilité');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Accounting';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Accounting';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;