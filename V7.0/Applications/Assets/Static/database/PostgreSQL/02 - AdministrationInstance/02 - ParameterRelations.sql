/* Administration app */

/* Parameter Relations */

-- FUNCTION: public.t_DeleteTrigger()

CREATE OR REPLACE FUNCTION public."t_DeleteTrigger"()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	RAISE EXCEPTION 'Operations not allowed. DELETE operations attempted.';
	RETURN NULL;
END;
$BODY$;

-- FUNCTION: public.t_RemoveTrigger()

CREATE OR REPLACE FUNCTION public."t_RemoveTrigger"()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF public."f_CurrentThread"() = TRUE AND public."f_IsProc"() = TRUE THEN
		RETURN OLD;
	END IF;
	RAISE EXCEPTION 'Operation not allowed';
END;
$BODY$;

-- FUNCTION: public.t_InsertTrigger()

CREATE OR REPLACE FUNCTION public."t_InsertTrigger"()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF public."f_CurrentThread"() = TRUE AND public."f_IsProc"() = TRUE THEN
		RETURN NEW;
	END IF;
	RAISE EXCEPTION 'Operation not allowed';
END;
$BODY$;

-- FUNCTION: public.t_UpdateTrigger()

CREATE OR REPLACE FUNCTION public."t_UpdateTrigger"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF public."f_CurrentThread"() = TRUE AND public."f_IsProc"() = TRUE AND OLD."IsActive" IS NULL THEN
		RETURN NEW;
	END IF;
	RAISE EXCEPTION 'Operation not allowed';
END;
$BODY$;

-- Table: public.cl_ParameterRelations

DROP TABLE IF EXISTS public."cl_ParameterRelations";
CREATE TABLE public."cl_ParameterRelations"
(
	"ID" serial PRIMARY KEY,
	"ParamID" integer,
	"UserApp" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_ParameterRelation" UNIQUE ("ParamID", "UserApp")
)

TABLESPACE pg_default;

-- FUNCTION: public.f_CheckParameterRelation

CREATE OR REPLACE FUNCTION public."f_CheckParameterRelation"(_name character varying(50))
	RETURNS character varying[]
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _id integer; _result character varying[];
BEGIN
	SELECT "ID" INTO _id FROM public."cl_Parameters" WHERE "ParamName" = DIGEST(_name, 'sha256');
	--
	IF _id IS NULL THEN RETURN ARRAY[]::character varying[]; END IF;
	--
	SELECT ARRAY(SELECT "UserApp" FROM public."cl_ParameterRelations" WHERE "ParamID" = _id) INTO _result;
	RETURN _result;
END;
$BODY$;

DO $BODY$
BEGIN
    INSERT INTO "cl_ParameterRelations" ("ParamID", "UserApp", "IsActive") VALUES
	(1, 'Administration', NULL),
	(2, 'Administration', NULL),
	(3, 'Administration', NULL),
	(4, 'Administration', NULL),
	(5, 'Administration', NULL),
	(6, 'Administration', NULL),
	(7, 'Administration', NULL),
	(8, 'Administration', NULL),
	(9, 'Administration', NULL),
	(10, 'Administration', NULL),
	(11, 'Administration', NULL);
END $BODY$;

-- PROCEDURE: public.p_InsertParameterRelation(character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_InsertParameterRelation"(
	IN _paramname character varying(50),
	IN _userapp character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id integer;
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Insert into the parameters table
	BEGIN
		SELECT "ID" INTO _id FROM public."cl_Parameters" WHERE "ParamName" = DIGEST(_paramname, 'sha256');
		INSERT INTO public."cl_ParameterRelations" ("ParamID", "UserApp") VALUES (_id, _userapp);
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- PROCEDURE: public.p_UpdateParameterRelation(integer, character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_UpdateParameterRelation"(
	IN _id integer,
	IN _paramid character varying(50),
	IN _userapp character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
    -- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Update the parameters table
	BEGIN
		UPDATE public."cl_ParameterRelations" SET "ParamID" = _paramid, "UserApp" = _userapp WHERE "ID" = _id;
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteParameterRelation(integer)

CREATE OR REPLACE PROCEDURE public."p_DeleteParameterRelation"(IN _id integer)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
    -- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Update the parameters table
	BEGIN
		UPDATE public."cl_ParameterRelations" SET "IsActive" = NOW() WHERE "ID" = _id;
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- Trigger: Delete_ParameterRelation

CREATE OR REPLACE TRIGGER "Delete_ParameterRelation"
    BEFORE DELETE
    ON public."cl_ParameterRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ParameterRelation

CREATE OR REPLACE TRIGGER "Insert_ParameterRelation"
    BEFORE INSERT 
    ON public."cl_ParameterRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ParameterRelation

CREATE OR REPLACE TRIGGER "Update_ParameterRelation"
    BEFORE UPDATE 
    ON public."cl_ParameterRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- FUNCTION: public.f_CreateID(character, character varying)

CREATE OR REPLACE FUNCTION public."f_CreateID"(
	_type character(3),
	_tablename character varying(50))
    RETURNS character varying
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
	_id character varying(50) := ''; _shortname character varying(50); _sql text; _c char(1); _x integer; _i integer := 1; _max integer;
BEGIN
	-- Fetch the _shortname parameter
	SELECT "ParamUValue" INTO _shortname FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('Shortname', 'sha256');
	-- Generate type code from ASCII
	WHILE _i <= 3 LOOP
		_c := UPPER(SUBSTRING(_type, _i, 1));
		_x := ASCII(_c);
		--
		WHILE _x >= 10 LOOP
			_x := (_x / 10) + (_x % 10);
		END LOOP;
		--
		_id := _id || _x;
		_i := _i + 1;
	END LOOP;
	-- Generate label code from ASCII
	_i := 1;
	WHILE _i <= 3 LOOP
		_c := UPPER(SUBSTRING(_shortname, _i, 1));
		_x := ASCII(_c);
		--
		WHILE _x >= 10 LOOP
			_x := (_x / 10) + (_x % 10);
		END LOOP;
		--
		_id := _id || _x;
		_i := _i + 1;
	END LOOP;
	-- Fetch the maximum existing ID value for the table
	_sql := format('SELECT COALESCE(MAX(CAST(SUBSTRING("ID", LENGTH("ID") - 3, 4) AS integer)), 0) FROM %I', _tablename);
	EXECUTE _sql INTO _max;
	--
	_max := _max + 1;
	_id := _id || LPAD(_max::text, 4, '0');
	RETURN _id;
END;
$BODY$;

-- PROCEDURE: public.p_Query(text, character varying, character)

CREATE OR REPLACE PROCEDURE public."p_Query"(
	IN _sql text,
	IN _tablename varchar(50) DEFAULT NULL::character varying,
	IN _idcode character(3) DEFAULT NULL::character)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50);
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Execute sql
	BEGIN
		IF _idcode IS NOT NULL THEN
			-- Create ID
			_id := public."f_CreateID"(_idcode, _tablename);
			-- Format sql
			_sql := FORMAT(_sql, _id);
		END IF;
		-- Execute sql
		EXECUTE _sql;
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;