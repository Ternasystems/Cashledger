-- Initial data

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50);
BEGIN

	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	
	-- App registry
	
	CALL public."p_InsertApp"('Payroll');
	
	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Payroll';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Payroll');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Payroll');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paie');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'HR') THEN
		CALL public."p_InsertAppCategory"('HR');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'HR');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'RH');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Payroll';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'HR';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;