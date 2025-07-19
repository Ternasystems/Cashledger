-- Initial data

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50);
BEGIN

	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	
	-- App registry
	
	CALL public."p_InsertApp"('Booking');
	
	-- App
	
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Booking';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Booking');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Booking');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réservations');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Booking') THEN
		CALL public."p_InsertAppCategory"('Booking');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Booking';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Booking');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Booking');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réservations');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Booking';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Booking';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;