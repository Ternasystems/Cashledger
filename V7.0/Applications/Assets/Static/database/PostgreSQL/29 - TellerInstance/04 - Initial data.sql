/* Teller app */

/* Initial data */

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50);
BEGIN

	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	
	-- App registry
	
	CALL public."p_InsertApp"('Teller');
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Teller';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Teller');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Teller');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Caisses');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Cajero');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'أمين الصندوق');

	-- Insert References

	CALL public."p_InsertReferenceTable"('cl_Tellers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Tellers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerSessions');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerSessions';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerTransactions');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerTransactions';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerPayments');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerPayments';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerReceipts');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerReceipts';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerTransfers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerTransfers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerReversals');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerReversals';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_CashFigures');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_CashFigures';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TellerCashCounts');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TellerCashCounts';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_CashRelations');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_CashRelations';
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Finance') THEN
		CALL public."p_InsertAppCategory"('Finance');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Finance';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Finance');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Finanzas');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'التمويل');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Teller';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Finance';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;

-- Trigger: Log_Teller

CREATE OR REPLACE TRIGGER "Log_Teller"
	AFTER INSERT OR UPDATE OR DELETE
	ON public."cl_Tellers"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_LogTeller"();

-- Trigger: Teller_Log
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_TellerSessions', 'cl_TellerTransactions', 'cl_TellerPayments', 'cl_TellerReceipts', 'cl_TellerTransfers', 'cl_TellerReversals', 'cl_TellerCashCounts'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername := REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '');
		_triggername := 'Teller' || _triggername || '_Log';
		IF EXISTS(SELECT 1 FROM information_schema.triggers WHERE trigger_schema = 'public' AND trigger_name = _triggername) THEN CONTINUE; END IF;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_TellerLog"();
		', _triggername, _tablename);
	END LOOP;
END $BODY$;

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_Tellers', 'cl_TellerSessions', 'cl_TellerTransactions', 'cl_TellerPayments', 'cl_TellerReceipts', 'cl_TellerTransfers', 'cl_TellerReversals', 'cl_CashFigures',
	'cl_TellerCashCounts', 'cl_CashRelations', 'cl_TellerAudits'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername := REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '');
		_triggername := 'Log_' || _triggername;
		IF EXISTS(SELECT 1 FROM information_schema.triggers WHERE trigger_schema = 'public' AND trigger_name = _triggername) THEN CONTINUE; END IF;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_LogAudit"();
		', _triggername, _tablename);
		--
		_triggername := REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '');
			_triggername := 'Release_' || _triggername;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_ReleaseThread"();
		', _triggername, _tablename);
	END LOOP;
END $BODY$;