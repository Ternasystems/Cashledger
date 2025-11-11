/* Taxes app */

/* Initial data */

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';

	-- App registry

	CALL public."p_InsertApp"('Taxes');
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Taxes';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Taxes');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Taxes');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Taxes');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Impuestos');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'الضرائب');

	-- Insert References

	CALL public."p_InsertReferenceTable"('cl_TaxTypes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TaxTypes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Taxes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Taxes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TaxAttributes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TaxAttributes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TaxProfiles');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TaxProfiles';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TaxProfiles');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TaxProfiles';
	CALL public."p_InsertReferenceRelation"(_appid, _id);

	-- App category

	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Ventas');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'المبيعات');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Purchases') THEN
		CALL public."p_InsertAppCategory"('Purchases');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Purchases';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Compras');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'المشتريات');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Payroll') THEN
		CALL public."p_InsertAppCategory"('Payroll');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Payroll';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Payroll');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Payroll');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paie');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Nómina');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'كشوف المرتبات');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Accounting') THEN
		CALL public."p_InsertAppCategory"('Accounting');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Accounting';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Accounting');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Accounting');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Comptabilité');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Contabilidad');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'المحاسبة');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Taxes';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Payroll';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Accounting';
	CALL public."p_InsertAppRelation"(_id, _appid);

END $BODY$;

-- Trigger: Log_Audit

DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_TaxTypes', 'cl_Taxes', 'cl_TaxAttributes', 'cl_TaxProfiles', 'cl_TaxRelations', 'cl_PartnerTaxRelations'];
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