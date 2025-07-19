/* Administration app */

/* Audit (2) */

-- Trigger: Delete_Audit

CREATE OR REPLACE TRIGGER "Delete_Audit"
    BEFORE DELETE OR UPDATE
    ON public."cl_Audits"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Audit

CREATE OR REPLACE TRIGGER "Insert_Audit"
    BEFORE INSERT 
    ON public."cl_Audits"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text; _specialnames text[] := ARRAY['cl_AppCategories', 'cl_Cities', 'cl_Countries'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename = 'cl_Audits' THEN CONTINUE; END IF;
		_triggername := 'Log_' ||
			CASE
				WHEN _tablename = ANY(_specialnames) THEN REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 3), 'ies$', 'y')
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_LogAudit"();
		', _triggername, _tablename);
	END LOOP;
END $BODY$;