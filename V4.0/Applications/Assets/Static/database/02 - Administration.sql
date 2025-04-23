/* Administration app */

-- View: public.v_Rand

CREATE OR REPLACE VIEW public."v_Rand"
 AS
 SELECT random() AS random;

-- FUNCTION: public.f_PwdGenerator()

CREATE OR REPLACE FUNCTION public."f_PwdGenerator"()
    RETURNS character varying
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
	_v character varying(63) := '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	_s character varying(50) := ''; _l integer := 10; _rd integer;
BEGIN
	WHILE LENGTH(_s) < _l LOOP
		IF LENGTH(_s) = 0 THEN
			_s := SUBSTRING(_v, CAST(FLOOR(11 + ((SELECT "random" FROM public."v_Rand") * 52)) AS integer), 1);
		ELSE
			_s := CONCAT(_s, SUBSTRING(_v, CAST(FLOOR(1 + ((SELECT "random" FROM public."v_Rand") * 62)) AS integer), 1));
		END IF;
	END LOOP;
	--
	RETURN _s;
END;
$BODY$;

-- EXTENSION: PGCrypto
CREATE EXTENSION IF NOT EXISTS pgcrypto;

/* Parameters */

-- FUNCTION: public.f_Activation

CREATE OR REPLACE FUNCTION public."f_Activation"(_date date)
    RETURNS character varying
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _dte character varying(50); _i integer := 1; _str character varying(50) := '';
BEGIN
	_dte := (REPLACE(_date::character varying(50), '-', '')::bigint * 1981)::character varying(50);
	WHILE _i <= LENGTH(_dte) LOOP
		_str = _str || CHR(SUBSTRING(_dte, _i, 1)::integer + 65);
		_i := _i + 1;
	END LOOP;
	--
	RETURN _str;
END;
$BODY$;

-- PROCEDURE: public.p_Activation

CREATE OR REPLACE PROCEDURE public."p_Activation"(IN _activated boolean)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
	IF public."f_IsProc"() = FALSE THEN CALL public."p_IsProc"(TRUE); END IF;
	--
	UPDATE public."cl_Parameters" SET "ParamValue" = DIGEST(CASE WHEN _activated = TRUE THEN 'activated' ELSE 'deactivated' END, 'sha256') WHERE "ParamName" = DIGEST('Activation', 'sha256');
END;
$BODY$;

-- FUNCTION: public.f_ActiveUser

CREATE OR REPLACE FUNCTION public."f_ActiveUser"(_number integer, _year integer)
    RETURNS character varying
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _n character varying(50); _i integer := 1; _str character varying(50) := ''; _str1 character varying(50) := ''; _str2 character varying(50) := '';
BEGIN
	_n := ((_year::character varying(50))::bigint * 1981)::character varying(50);
	WHILE _i <= LENGTH(_n) LOOP
		_str1 = _str1 || CHR(SUBSTRING(_n, _i, 1)::integer + 65);
		_i := _i + 1;
	END LOOP;
	--
	_n := ((_number::character varying(50))::bigint * 1981)::character varying(50);
	_i := 1;
	WHILE _i <= LENGTH(_n) LOOP
		_str2 = _str2 || CHR(SUBSTRING(_n, _i, 1)::integer + 65);
		_i := _i + 1;
	END LOOP;
	--
	_str := RIGHT(TRIM(_str1 || LENGTH(_str2)::character varying(50) || _str2), 11);
	RETURN _str;
END;
$BODY$;

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

-- Table: public.cl_Parameters

CREATE TABLE IF NOT EXISTS public."cl_Parameters"
(
    "ID" serial PRIMARY KEY,
    "ParamName" bytea UNIQUE NOT NULL,
    "ParamUValue" text COLLATE pg_catalog."default",
    "ParamValue" bytea,
	"OwnerApp" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"ParamLock" boolean NOT NULL,
	"Auditable" boolean NOT NULL,
	"IsActive" timestamp without time zone,
    CONSTRAINT "CT_Parameter" CHECK (
        ("ParamValue" IS NULL AND "ParamUValue" IS NOT NULL) OR
        ("ParamValue" IS NOT NULL AND "ParamUValue" IS NULL)
    ),
    CONSTRAINT "CT_ParamLock_Auditable" CHECK (
        ("ParamLock" = true AND "Auditable" = false) OR
        ("ParamLock" = false)
    )
)

TABLESPACE pg_default;

-- Table: public.cl_ParameterRelations

CREATE TABLE IF NOT EXISTS public."cl_ParameterRelations"
(
	"ID" serial PRIMARY KEY,
	"ParamID" integer,
	"UserApp" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_ParameterRelation" UNIQUE ("ParamID", "UserApp")
)

TABLESPACE pg_default;

DO $BODY$
DECLARE 
	_str1 varchar(50) := 'EAAJFGEAAIB'; --2024-01-01
	_str2 varchar(50) := 'EABBHCDCJIB'; --2025-10-01
	_str3 varchar(50) := 'EABBHGIIGBB'; --2025-12-31
	_str4 varchar(50) := 'AAJFEE4HJCE'; --2024-4
	_code1 varchar(50) := '4';
BEGIN
    INSERT INTO "cl_Parameters" ("ParamName", "ParamUValue", "ParamValue", "OwnerApp", "ParamLock", "Auditable", "IsActive") VALUES
    (DIGEST('IsProc', 'sha256'), NULL, DIGEST('0', 'sha256'), 'Administration', FALSE, FALSE, NULL),
    (DIGEST('Serial', 'sha256'), NULL, DIGEST('{60E9AA19-8DDD-41B6-86DB-2D4CA1E2CB32}', 'sha256'), 'Administration', TRUE, FALSE, NULL),
    (DIGEST('Activation', 'sha256'), NULL, DIGEST('activated', 'sha256'), 'Administration', FALSE, TRUE, NULL),
    (DIGEST('Shortname', 'sha256'), 'cashledger', NULL, 'Administration', TRUE, FALSE, NULL),
    (DIGEST('StartDate', 'sha256'), _str1, NULL, 'Administration', FALSE, FALSE, NULL),
    (DIGEST('ActiveDate', 'sha256'), _str2, NULL, 'Administration', FALSE, FALSE, NULL),
    (DIGEST('EndDate', 'sha256'), _str3, NULL, 'Administration', FALSE, FALSE, NULL),
    (DIGEST('Users', 'sha256'), _str4, NULL, 'Administration', FALSE, FALSE, NULL),
	(DIGEST('CodeLength', 'sha256'), _code1, NULL, 'Administration', FALSE, TRUE, NULL),
	(DIGEST('CurrentThread', 'sha256'),null, DIGEST('0', 'sha256'), 'Administration', FALSE, FALSE, NULL),
    (DIGEST('AppVersion', 'sha256'), 'Cashledger Professional Server Edition (SE) build 2025.1.1', NULL, 'Administration', TRUE, FALSE, NULL);
	--
	INSERT INTO "cl_ParameterRelations" ("ParamID", "UserApp", "IsActive") VALUES (1, 'Administration', NULL), (2, 'Administration', NULL), (3, 'Administration', NULL), (4, 'Administration', NULL),
	(5, 'Administration', NULL), (6, 'Administration', NULL), (7, 'Administration', NULL), (8, 'Administration', NULL), (9, 'Administration', NULL), (10, 'Administration', NULL), (11, 'Administration', NULL);
END $BODY$;

-- FUNCTION: public.f_CurrentThread()

CREATE OR REPLACE FUNCTION public."f_CurrentThread"()
	RETURNS boolean
	LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
    IF EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('CurrentThread', 'sha256') AND "ParamValue" = DIGEST('1', 'sha256')) THEN
        RETURN TRUE;
    END IF;
    RETURN FALSE;
END;
$BODY$;

-- PROCEDURE: public.p_CurrentThread(boolean)

CREATE OR REPLACE PROCEDURE public."p_CurrentThread"(IN _threaded boolean)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _isThreaded boolean;
BEGIN
	LOOP
		SELECT public."f_CurrentThread"() INTO _isThreaded;
		EXIT WHEN _isThreaded = FALSE OR _isThreaded != _threaded;
	END LOOP;
	--
	UPDATE public."cl_Parameters" SET "ParamValue" = DIGEST(CASE WHEN _threaded = TRUE THEN '1' ELSE '0' END, 'sha256') WHERE "ParamName" = DIGEST('CurrentThread', 'sha256');
END;
$BODY$;

-- FUNCTION: public.f_IsProc()

CREATE OR REPLACE FUNCTION public."f_IsProc"()
	RETURNS boolean
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('IsProc', 'sha256') AND "ParamValue" = DIGEST('1', 'sha256')) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- PROCEDURE: public.p_IsProc(boolean)

CREATE OR REPLACE PROCEDURE public."p_IsProc"(IN _processed boolean)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
	IF public."f_CurrentThread"() = FALSE THEN CALL public."p_CurrentThread"(TRUE); END IF;
	--
	UPDATE public."cl_Parameters" SET "ParamValue" = DIGEST(CASE WHEN _processed = TRUE THEN '1' ELSE '0' END, 'sha256') WHERE "ParamName" = DIGEST('IsProc', 'sha256');
END;
$BODY$;

-- FUNCTION: public.f_Locked(integer)

CREATE OR REPLACE FUNCTION public."f_Locked"(IN _id integer)
	RETURNS boolean
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ID" = _id AND "ParamLock" = TRUE) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- FUNCTION: public.f_Auditable(integer)

CREATE OR REPLACE FUNCTION public."f_Auditable"(IN _id integer)
	RETURNS boolean
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ID" = _id AND "Auditable" = TRUE) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- FUNCTION: public.t_DeleteParameter()

CREATE OR REPLACE FUNCTION public."t_DeleteParameter"()
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

-- FUNCTION: public.t_InsertParameter()

CREATE OR REPLACE FUNCTION public."t_InsertParameter"()
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

-- FUNCTION: public.t_UpdateParameter()

CREATE OR REPLACE FUNCTION public."t_UpdateParameter"()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF OLD."IsActive" IS NULL AND (NEW."ParamName" = DIGEST('CurrentThread', 'sha256') OR
		(public."f_CurrentThread"() = TRUE AND (NEW."ParamName" = DIGEST('IsProc', 'sha256') OR (public."f_Locked"(NEW."ID") = FALSE AND public."f_IsProc"() = TRUE)))) THEN
		RETURN NEW;
	END IF;
	RAISE EXCEPTION 'Operation not allowed';
END;
$BODY$;

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

-- FUNCTION: public.f_CheckCodeLength(integer)

CREATE OR REPLACE FUNCTION public."f_CheckCodeLength"("_code" integer)
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _n integer;
BEGIN
    SELECT "ParamUValue"::integer INTO _n FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('CodeLength', 'sha256');
	IF _code = _n THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- FUNCTION: public.f_CheckActivation(character varying, character varying)

CREATE OR REPLACE FUNCTION public."f_CheckActivation"(
	_mac character varying(50),
	_date date)
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _endte character varying(50); _dte character varying(50);
BEGIN
	SELECT "ParamUValue" INTO _endte FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('EndDate', 'sha256');
	_dte := public."f_Activation"(_date);
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('Activation', 'sha256')
		AND "ParamValue" = DIGEST('activated', 'sha256')) THEN
		RETURN FALSE;
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('Serial', 'sha256')
		AND "ParamValue" = DIGEST(_mac, 'sha256')) THEN
		RETURN FALSE;
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE _dte <= _endte) THEN
		RETURN FALSE;
	END IF;
	--
	RETURN TRUE;
END;
$BODY$;

-- FUNCTION: public.f_CheckPeriod(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckPeriod"(_date date)
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _activedte character varying(50); _endte character varying(50); _dte character varying(50);
BEGIN
	SELECT "ParamUValue" INTO _activedte FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('ActiveDate', 'sha256');
	--
	SELECT "ParamUValue" INTO _endte FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('EndDate', 'sha256');
	--
	_dte := public."f_Activation"(_date);
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('Activation', 'sha256') AND "ParamValue" = DIGEST('activated', 'sha256')) THEN
		RETURN FALSE;
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Parameters" WHERE _dte <= _endte AND _dte >= _activedte) THEN
		RETURN FALSE;
	END IF;
	--
	RETURN TRUE;
END;
$BODY$;

-- FUNCTION: public.f_Readme()

CREATE OR REPLACE FUNCTION public."f_Readme"()
    RETURNS text
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _str text;
BEGIN
	SELECT "ParamUValue" INTO _str FROM public."cl_Parameters" WHERE "ParamName" = DIGEST('AppVersion', 'sha256');
	RETURN _str;
END;
$BODY$;

-- PROCEDURE: public.p_InsertParameter(character varying, text, boolean, character varying, boolean, boolean)

CREATE OR REPLACE PROCEDURE public."p_InsertParameter"(
	IN _name character varying(50),
	IN _value text,
	IN _encrypted boolean,
	IN _owner character varying(50),
	IN _locked boolean,
	IN _auditable boolean)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Insert into the parameters table
	BEGIN
		IF _encrypted THEN
			INSERT INTO public."cl_Parameters" ("ParamName", "ParamUValue", "ParamValue", "OwnerApp", "ParamLock", "Auditable") VALUES
			(DIGEST(_name, 'sha256'), NULL, DIGEST(_value, 'sha256'), _owner, _locked, CASE WHEN _locked = TRUE THEN FALSE ELSE _auditable END);
		ELSE
			INSERT INTO public."cl_Parameters" ("ParamName", "ParamUValue", "ParamValue", "OwnerApp", "ParamLock", "Auditable") VALUES
			(DIGEST(_name, 'sha256'), _value, NULL, _owner, _locked, CASE WHEN _locked = TRUE THEN FALSE ELSE _auditable END);
		END IF;
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

-- PROCEDURE: public.p_UpdateParameter(character varying, text, boolean)

CREATE OR REPLACE PROCEDURE public."p_UpdateParameter"(
	IN _name character varying(50),
	IN _value text,
	IN _encrypted boolean)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
    -- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Update the parameters table
	BEGIN
		UPDATE public."cl_Parameters" SET "ParamValue" = CASE WHEN _encrypted = TRUE THEN DIGEST(_value, 'sha256') ELSE NULL END, 
		"ParamUValue" = CASE WHEN _encrypted = TRUE THEN NULL ELSE _value END WHERE "ParamName" = DIGEST(_name, 'sha256');
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

-- PROCEDURE: public.p_DeleteParameter(character varying, text, boolean)

CREATE OR REPLACE PROCEDURE public."p_DeleteParameter"(IN _name character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
    -- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);

	-- Update the parameters table
	BEGIN
		UPDATE public."cl_Parameters" SET "IsActive" = NOW() WHERE "ParamName" = DIGEST(_name, 'sha256');
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

-- Trigger: Delete_Parameter

CREATE OR REPLACE TRIGGER "Delete_Parameter"
    BEFORE DELETE
    ON public."cl_Parameters"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteParameter"();

-- Trigger: Insert_Parameter

CREATE OR REPLACE TRIGGER "Insert_Parameter"
    BEFORE INSERT 
    ON public."cl_Parameters"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertParameter"();

-- Trigger: Update_Parameter

CREATE OR REPLACE TRIGGER "Update_Parameter"
    BEFORE UPDATE 
    ON public."cl_Parameters"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateParameter"();

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
		INSERT INTO public."cl_ParameterRelations" ("ParamID", "UserApp", "IsActive") VALUES (_id, _userapp, NULL);
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

-- PROCEDURE: public.p_Query(text)

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

-- Table: public.cl_Languages

CREATE TABLE IF NOT EXISTS public."cl_Languages"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Label" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertLanguage(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertLanguage"(
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Languages'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _label, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'LNG');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateLanguage(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateLanguage"(
	IN _id character varying(50),
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Languages';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Label" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _label, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteLanguage(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteLanguage"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Languages';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Language

CREATE OR REPLACE TRIGGER "Delete_Language"
    BEFORE DELETE
    ON public."cl_Languages"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Language

CREATE OR REPLACE TRIGGER "Insert_Language"
    BEFORE INSERT 
    ON public."cl_Languages"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Language

CREATE OR REPLACE TRIGGER "Update_Language"
	BEFORE UPDATE
	ON public."cl_Languages"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_AppCategories

CREATE TABLE IF NOT EXISTS public."cl_AppCategories"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertAppCategory(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertAppCategory"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppCategories'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'ACT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateAppCategory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateAppCategory"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppCategories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteAppCategory(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteAppCategory"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppCategories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_AppCategory

CREATE OR REPLACE TRIGGER "Delete_AppCategory"
    BEFORE DELETE
    ON public."cl_AppCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_AppCategory

CREATE OR REPLACE TRIGGER "Insert_AppCategory"
    BEFORE INSERT 
    ON public."cl_AppCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_AppCategory

CREATE OR REPLACE TRIGGER "Update_AppCategory"
	BEFORE UPDATE
	ON public."cl_AppCategories"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Apps

CREATE TABLE IF NOT EXISTS public."cl_Apps"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertApp(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertApp"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Apps'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'APP');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateApp(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateApp"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Apps';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteApp(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteApp"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Apps';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_App

CREATE OR REPLACE TRIGGER "Delete_App"
    BEFORE DELETE
    ON public."cl_Apps"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_App

CREATE OR REPLACE TRIGGER "Insert_App"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Apps"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_App

CREATE OR REPLACE TRIGGER "Update_App"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Apps"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_AppRelations

CREATE TABLE IF NOT EXISTS public."cl_AppRelations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "AppCategoryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_AppCategories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
    CONSTRAINT "UQ_AppRelation" UNIQUE ("AppID", "AppCategoryID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertAppRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertAppRelation"(
	IN _appid character varying(50),
	IN _categoryid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _appid, _categoryid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'APR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteAppRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteAppRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_AppRelation

CREATE OR REPLACE TRIGGER "Update_AppRelation"
	BEFORE UPDATE
	ON public."cl_AppRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_AppRelation

CREATE OR REPLACE TRIGGER "Insert_AppRelation"
    BEFORE INSERT
    ON public."cl_AppRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_AppRelation

CREATE OR REPLACE TRIGGER "Remove_AppRelation"
	BEFORE DELETE
	ON public."cl_AppRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Continents

CREATE TABLE IF NOT EXISTS public."cl_Continents"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContinent(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContinent"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Continents'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateContinent(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateContinent"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Continents';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContinent(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteContinent"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Continents';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Continent

CREATE OR REPLACE TRIGGER "Delete_Continent"
    BEFORE DELETE
    ON public."cl_Continents"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Continent

CREATE OR REPLACE TRIGGER "Insert_Continent"
    BEFORE INSERT 
    ON public."cl_Continents"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Continent

CREATE OR REPLACE TRIGGER "Update_Continent"
	BEFORE UPDATE
	ON public."cl_Continents"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table public.Countries

-- Table: public.cl_Countries

CREATE TABLE IF NOT EXISTS public."cl_Countries"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer NOT NULL,
	"ISO2" character(2) UNIQUE NOT NULL,
	"ISO3" character(3) UNIQUE NOT NULL,
    "ContinentID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Continents" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Name" text COLLATE pg_catalog."default" NOT NULL,
    "Flag" text COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCountry(integer, character, character, character varying, text, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCountry"(
	IN _code integer,
	IN _iso2 character(2),
	IN _iso3 character(3),
	IN _continent character varying(50),
	IN _name text,
	IN _flag text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Countries'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _iso2, _iso3, _continent, _name, _flag, NULL, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTY');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCountry(character varying, integer, character, character, character varying, text, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCountry"(
	IN _id character varying(50),
	IN _code integer,
	IN _iso2 character(2),
	IN _iso3 character(3),
	IN _continent character varying(50),
	IN _name text,
	IN _flag text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Countries';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Code" = %L, "ISO2" = %L, "ISO3" = %L, "Continent" = %L, "Name" = %L, "Flag" = %L, "Description" = %L WHERE "ID" = %L;',
		_tablename, _code, _iso2, _iso3, _continent, _name, _flag, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCountry(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCountry"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Countries';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Country

CREATE OR REPLACE TRIGGER "Delete_Country"
    BEFORE DELETE
    ON public."cl_Countries"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Country

CREATE OR REPLACE TRIGGER "Insert_Country"
    BEFORE INSERT 
    ON public."cl_Countries"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Country

CREATE OR REPLACE TRIGGER "Update_Country"
	BEFORE UPDATE
	ON public."cl_Countries"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Cities

CREATE TABLE IF NOT EXISTS public."cl_Cities"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"CountryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Countries" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Name" text COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCity(character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCity"(
	IN _country character varying(50),
	IN _name text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Cities'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _country, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CIT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCity(character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCity"(
	IN _id character varying(50),
	IN _country character varying(50),
	IN _name text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Cities';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "CountryID" = %L, "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _country, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCity(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCity"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Cities';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_City

CREATE OR REPLACE TRIGGER "Delete_City"
    BEFORE DELETE
    ON public."cl_Cities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_City

CREATE OR REPLACE TRIGGER "Insert_City"
    BEFORE INSERT 
    ON public."cl_Cities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_City

CREATE OR REPLACE TRIGGER "Update_City"
	BEFORE UPDATE
	ON public."cl_Cities"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- FUNCTION: public."f_CheckReference"(character varying);

CREATE OR REPLACE FUNCTION public."f_CheckReference"(_referenceid character varying(50))
RETURNS boolean
LANGUAGE plpgsql
COST 100
VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    tbl record;
    found boolean := false;
BEGIN
    FOR tbl IN 
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name LIKE 'cl\\_%' ESCAPE '\\'
    LOOP
        EXECUTE format('SELECT EXISTS(SELECT 1 FROM %I WHERE "ID" = $1)', tbl.table_name) 
        INTO found 
        USING _referenceid;
        
        IF found THEN
            RETURN true;
        END IF;
    END LOOP;
    
    RETURN false;
END;
$BODY$;

-- Table: public.LanguageRelations

CREATE TABLE IF NOT EXISTS public."cl_LanguageRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" NOT NULL PRIMARY KEY,
	"LangID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Languages" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"Label" text COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_LanguageRelation" UNIQUE ("ReferenceID", "LangID"),
	CONSTRAINT "CT_LanguageRelation" CHECK (public."f_CheckReference"("ReferenceID"))
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertLanguageRelation(character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertLanguageRelation"(
	IN _langid character varying(50),
	IN _referenceid character varying(50),
	IN _label text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_LanguageRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _langid, _referenceid, _label, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'LGR');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateLanguageRelation(character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateLanguageRelation"(
	IN _id character varying(50),
	IN _label text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_LanguageRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Label" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _label, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteLanguageRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteLanguageRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_LanguageRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_LanguageRelation

CREATE OR REPLACE TRIGGER "Delete_LanguageRelation"
    BEFORE DELETE
    ON public."cl_LanguageRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_LanguageRelation

CREATE OR REPLACE TRIGGER "Insert_LanguageRelation"
    BEFORE INSERT 
    ON public."cl_LanguageRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_LanguageRelation

CREATE OR REPLACE TRIGGER "Update_LanguageRelation"
	BEFORE UPDATE
	ON public."cl_LanguageRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Audits

CREATE TABLE IF NOT EXISTS public."cl_Audits"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"TableName" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"RecordID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "ActionDate" timestamp without time zone DEFAULT NOW(),
	"Description" jsonb NOT NULL
)

TABLESPACE pg_default;

-- FUNCTION: public.t_LogAudit()

CREATE OR REPLACE FUNCTION public."t_LogAudit"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF TG_TABLE_NAME = 'cl_Audits' THEN RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END; END IF;
	--
	IF TG_TABLE_NAME = 'cl_Parameters' THEN
		IF public."f_Auditable"(CASE WHEN TG_OP = 'INSERT' THEN NEW."ID" ELSE OLD."ID" END) = FALSE THEN
			RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
		END IF;
	END IF;
	--
	IF TG_OP = 'INSERT' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), NEW."ID"::character varying(50), row_to_json(NEW)::jsonb);
	ELSIF TG_OP = 'UPDATE' THEN
		CALL public."p_InsertAudit"(CASE WHEN NEW."IsActive" IS NULL THEN TG_OP::character varying(50) ELSE 'DEACTIVATE' END, TG_TABLE_NAME::character varying(50), OLD."ID"::character varying(50),
		json_build_object('before: ', row_to_json(OLD), ' after: ', row_to_json(NEW))::jsonb);
	ELSIF TG_OP = 'DELETE' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), OLD."ID"::character varying(50), row_to_json(OLD)::jsonb);
	END IF;
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- PROCEDURE: public.p_InsertAudit(character varying, character varying, jsonb)

CREATE OR REPLACE PROCEDURE public."p_InsertAudit"(
	IN _action character varying(50),
	IN _tablename character varying(50),
	IN _recordid character varying(50) DEFAULT NULL::character varying(50),
	IN _description jsonb DEFAULT NULL::jsonb)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50); _threaded boolean; _processed boolean;
BEGIN
	-- Set CurrentThread parameter
	SELECT public."f_CurrentThread"() INTO _threaded;
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(TRUE); END IF;
	
	-- Set IsProc parameter
	SELECT public."f_IsProc"() INTO _processed;
    IF _processed = FALSE THEN CALL public."p_IsProc"(TRUE); END IF;
	
	-- Insert into the audits table
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('AUD', 'cl_Audits');
		-- Insert data to cl_Audits table
		INSERT INTO public."cl_Audits" VALUES (_id, _action, _tablename, _recordid, NOW(), _description);
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    IF _processed = FALSE THEN CALL public."p_IsProc"(FALSE); END IF;
	
	-- Reset CurrentThread parameter
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(FALSE); END IF;
END;
$BODY$;
	
-- Initial data

-- Insert Languages

CALL public."p_InsertLanguage"('US', 'English (US)');
CALL public."p_InsertLanguage"('GB', 'English (GB)');
CALL public."p_InsertLanguage"('FR', 'Français');
CALL public."p_InsertLanguage"('ES', 'Español');
CALL public."p_InsertLanguage"('AR', 'عربي');

-- Insert Continents

CALL public."p_InsertContinent"('Africa');
CALL public."p_InsertContinent"('Asia');
CALL public."p_InsertContinent"('America');
CALL public."p_InsertContinent"('Europe');
CALL public."p_InsertContinent"('Oceania');

-- Insert Countries

DO
$BODY$
DECLARE _africa character varying(50); _america character varying(50); _asia character varying(50); _europe character varying(50); _oceania character varying(50);

BEGIN
SELECT "ID" INTO _africa FROM public."cl_Continents" WHERE "Name" = 'Africa';
SELECT "ID" INTO _asia FROM public."cl_Continents" WHERE "Name" = 'Asia';
SELECT "ID" INTO _america FROM public."cl_Continents" WHERE "Name" = 'America';
SELECT "ID" INTO _oceania FROM public."cl_Continents" WHERE "Name" = 'Oceania';
SELECT "ID" INTO _europe FROM public."cl_Continents" WHERE "Name" = 'Europe';

--Countries data

CALL public."p_InsertCountry"(93::integer, 'AF'::character(2), 'AFG'::character(3), _asia, 'Afghanistan'::text, 'Chronocare.Application.Items\Pictures\Flags\afghanistan.png'::text);
CALL public."p_InsertCountry"(355::integer, 'AL'::character(2), 'ALB'::character(3), _europe, 'Albania'::text, 'Chronocare.Application.Items\Pictures\Flags\albania.png'::text);
CALL public."p_InsertCountry"(213::integer, 'DZ'::character(2), 'DZA'::character(3), _africa, 'Algeria'::text, 'Chronocare.Application.Items\Pictures\Flags\algeria.png'::text);
CALL public."p_InsertCountry"(358::integer, 'AX'::character(2), 'ALA'::character(3), _europe, 'Åland Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\aland-islands.png'::text);
CALL public."p_InsertCountry"(1::integer, 'AS'::character(2), 'ASM'::character(3), _america, 'American Samoa'::text, 'Chronocare.Application.Items\Pictures\Flags\american-samoa.png'::text);
CALL public."p_InsertCountry"(376::integer, 'AD'::character(2), 'AND'::character(3), _america, 'Andorra'::text, 'Chronocare.Application.Items\Pictures\Flags\andorra.png'::text);
CALL public."p_InsertCountry"(244::integer, 'AO'::character(2), 'AGO'::character(3), _africa, 'Angola'::text, 'Chronocare.Application.Items\Pictures\Flags\angola.png'::text);
CALL public."p_InsertCountry"(1::integer, 'AI'::character(2), 'AIA'::character(3), _oceania, 'Anguilla'::text, 'Chronocare.Application.Items\Pictures\Flags\anguilla.png'::text);
CALL public."p_InsertCountry"(1::integer, 'AG'::character(2), 'ATG'::character(3), _oceania, 'Antigua and Barbuda'::text, 'Chronocare.Application.Items\Pictures\Flags\antigua-and-barbuda.png'::text);
CALL public."p_InsertCountry"(54::integer, 'AR'::character(2), 'ARG'::character(3), _america, 'Argentina'::text, 'Chronocare.Application.Items\Pictures\Flags\argentina.png'::text);
CALL public."p_InsertCountry"(374::integer, 'AM'::character(2), 'ARM'::character(3), _europe, 'Armenia'::text, 'Chronocare.Application.Items\Pictures\Flags\armenia.png'::text);
CALL public."p_InsertCountry"(297::integer, 'AW'::character(2), 'ABW'::character(3), _oceania, 'Aruba'::text, 'Chronocare.Application.Items\Pictures\Flags\aruba.png'::text);
CALL public."p_InsertCountry"(61::integer, 'AU'::character(2), 'AUS'::character(3), _oceania, 'Australia'::text, 'Chronocare.Application.Items\Pictures\Flags\australia.png'::text);
CALL public."p_InsertCountry"(43::integer, 'AT'::character(2), 'AUT'::character(3), _europe, 'Austria'::text, 'Chronocare.Application.Items\Pictures\Flags\austria.png'::text);
CALL public."p_InsertCountry"(994::integer, 'AZ'::character(2), 'AZE'::character(3), _asia, 'Azerbaijan'::text, 'Chronocare.Application.Items\Pictures\Flags\azerbaijan.png'::text);
CALL public."p_InsertCountry"(1::integer, 'BS'::character(2), 'BHS'::character(3), _america, 'Bahamas'::text, 'Chronocare.Application.Items\Pictures\Flags\bahamas.png'::text);
CALL public."p_InsertCountry"(973::integer, 'BH'::character(2), 'BHR'::character(3), _asia, 'Bahrain'::text, 'Chronocare.Application.Items\Pictures\Flags\bahrain.png'::text);
CALL public."p_InsertCountry"(880::integer, 'BD'::character(2), 'BGD'::character(3), _asia, 'Bangladesh'::text, 'Chronocare.Application.Items\Pictures\Flags\bangladesh.png'::text);
CALL public."p_InsertCountry"(1::integer, 'BB'::character(2), 'BRB'::character(3), _oceania, 'Barbados'::text, 'Chronocare.Application.Items\Pictures\Flags\barbados.png'::text);
CALL public."p_InsertCountry"(375::integer, 'BY'::character(2), 'BLR'::character(3), _europe, 'Belarus'::text, 'Chronocare.Application.Items\Pictures\Flags\belarus.png'::text);
CALL public."p_InsertCountry"(32::integer, 'BE'::character(2), 'BEL'::character(3), _europe, 'Belgium'::text, 'Chronocare.Application.Items\Pictures\Flags\belgium.png'::text);
CALL public."p_InsertCountry"(501::integer, 'BZ'::character(2), 'BLZ'::character(3), _oceania, 'Belize'::text, 'Chronocare.Application.Items\Pictures\Flags\belize.png'::text);
CALL public."p_InsertCountry"(229::integer, 'BJ'::character(2), 'BEN'::character(3), _africa, 'Benin'::text, 'Chronocare.Application.Items\Pictures\Flags\benin.png'::text);
CALL public."p_InsertCountry"(1::integer, 'BM'::character(2), 'BMU'::character(3), _america, 'Bermuda'::text, 'Chronocare.Application.Items\Pictures\Flags\bermuda.png'::text);
CALL public."p_InsertCountry"(975::integer, 'BT'::character(2), 'BTN'::character(3), _asia, 'Bhutan'::text, 'Chronocare.Application.Items\Pictures\Flags\bhutan.png'::text);
CALL public."p_InsertCountry"(591::integer, 'BO'::character(2), 'BOL'::character(3), _america, 'Bolivia'::text, 'Chronocare.Application.Items\Pictures\Flags\bolivia.png'::text);
CALL public."p_InsertCountry"(387::integer, 'BA'::character(2), 'BIH'::character(3), _europe, 'Bosnia and Herzegovina'::text, 'Chronocare.Application.Items\Pictures\Flags\bosnia-and-herzegovina.png'::text);
CALL public."p_InsertCountry"(267::integer, 'BW'::character(2), 'BWA'::character(3), _africa, 'Botswana'::text, 'Chronocare.Application.Items\Pictures\Flags\botswana.png'::text);
CALL public."p_InsertCountry"(55::integer, 'BR'::character(2), 'BRA'::character(3), _america, 'Brazil'::text, 'Chronocare.Application.Items\Pictures\Flags\brazil.png'::text);
CALL public."p_InsertCountry"(246::integer, 'IO'::character(2), 'IOT'::character(3), _asia, 'British Indian Ocean Territory'::text, 'Chronocare.Application.Items\Pictures\Flags\british-indian-ocean-territory.png'::text);
CALL public."p_InsertCountry"(1::integer, 'VG'::character(2), 'VGB'::character(3), _oceania, 'British Virgin Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\british-virgin-islands.png'::text);
CALL public."p_InsertCountry"(673::integer, 'BN'::character(2), 'BRN'::character(3), _asia, 'Brunei'::text, 'Chronocare.Application.Items\Pictures\Flags\brunei.png'::text);
CALL public."p_InsertCountry"(359::integer, 'BG'::character(2), 'BGR'::character(3), _europe, 'Bulgaria'::text, 'Chronocare.Application.Items\Pictures\Flags\bulgaria.png'::text);
CALL public."p_InsertCountry"(226::integer, 'BF'::character(2), 'BFA'::character(3), _africa, 'Burkina Faso'::text, 'Chronocare.Application.Items\Pictures\Flags\burkina-faso.png'::text);
CALL public."p_InsertCountry"(257::integer, 'BI'::character(2), 'BDI'::character(3), _africa, 'Burundi'::text, 'Chronocare.Application.Items\Pictures\Flags\burundi.png'::text);
CALL public."p_InsertCountry"(855::integer, 'KH'::character(2), 'KHM'::character(3), _asia, 'Cambodia'::text, 'Chronocare.Application.Items\Pictures\Flags\cambodia.png'::text);
CALL public."p_InsertCountry"(237::integer, 'CM'::character(2), 'CMR'::character(3), _africa, 'Cameroon'::text, 'Chronocare.Application.Items\Pictures\Flags\cameroon.png'::text);
CALL public."p_InsertCountry"(1::integer, 'CA'::character(2), 'CAN'::character(3), _america, 'Canada'::text, 'Chronocare.Application.Items\Pictures\Flags\canada.png'::text);
CALL public."p_InsertCountry"(238::integer, 'CV'::character(2), 'CPV'::character(3), _africa, 'Cape Verde'::text, 'Chronocare.Application.Items\Pictures\Flags\cape-verde.png'::text);
CALL public."p_InsertCountry"(599::integer, 'BQ'::character(2), 'BES'::character(3), _america, 'Caribbean Netherlands'::text, 'Chronocare.Application.Items\Pictures\Flags\caribbean-netherlands.png'::text);
CALL public."p_InsertCountry"(1::integer, 'KY'::character(2), 'CYM'::character(3), _oceania, 'Cayman Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\cayman-islands.png'::text);
CALL public."p_InsertCountry"(236::integer, 'CF'::character(2), 'CAF'::character(3), _africa, 'Central African Republic'::text, 'Chronocare.Application.Items\Pictures\Flags\central-african-republic.png'::text);
CALL public."p_InsertCountry"(235::integer, 'TD'::character(2), 'TCD'::character(3), _africa, 'Chad'::text, 'Chronocare.Application.Items\Pictures\Flags\chad.png'::text);
CALL public."p_InsertCountry"(56::integer, 'CL'::character(2), 'CHL'::character(3), _america, 'Chile'::text, 'Chronocare.Application.Items\Pictures\Flags\chile.png'::text);
CALL public."p_InsertCountry"(86::integer, 'CN'::character(2), 'CHN'::character(3), _asia, 'China'::text, 'Chronocare.Application.Items\Pictures\Flags\china.png'::text);
CALL public."p_InsertCountry"(61::integer, 'CX'::character(2), 'CXR'::character(3), _oceania, 'Christmas Island'::text, 'Chronocare.Application.Items\Pictures\Flags\christmas-island.png'::text);
CALL public."p_InsertCountry"(61::integer, 'CC'::character(2), 'CCK'::character(3), _oceania, 'Cocos Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\cocos-islands.png'::text);
CALL public."p_InsertCountry"(57::integer, 'CO'::character(2), 'COL'::character(3), _america, 'Colombia'::text, 'Chronocare.Application.Items\Pictures\Flags\colombia.png'::text);
CALL public."p_InsertCountry"(269::integer, 'KM'::character(2), 'COM'::character(3), _africa, 'Comoros'::text, 'Chronocare.Application.Items\Pictures\Flags\comoros.png'::text);
CALL public."p_InsertCountry"(682::integer, 'CK'::character(2), 'COK'::character(3), _oceania, 'Cook Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\cook-islands.png'::text);
CALL public."p_InsertCountry"(506::integer, 'CR'::character(2), 'CRI'::character(3), _america, 'Costa Rica'::text, 'Chronocare.Application.Items\Pictures\Flags\costa-rica.png'::text);
CALL public."p_InsertCountry"(385::integer, 'HR'::character(2), 'HRV'::character(3), _europe, 'Croatia'::text, 'Chronocare.Application.Items\Pictures\Flags\croatia.png'::text);
CALL public."p_InsertCountry"(53::integer, 'CU'::character(2), 'CUB'::character(3), _america, 'Cuba'::text, 'Chronocare.Application.Items\Pictures\Flags\cuba.png'::text);
CALL public."p_InsertCountry"(599::integer, 'CW'::character(2), 'CUW'::character(3), _oceania, 'Curacao'::text, 'Chronocare.Application.Items\Pictures\Flags\curacao.png'::text);
CALL public."p_InsertCountry"(357::integer, 'CY'::character(2), 'CYP'::character(3), _europe, 'Cyprus'::text, 'Chronocare.Application.Items\Pictures\Flags\cyprus.png'::text);
CALL public."p_InsertCountry"(420::integer, 'CZ'::character(2), 'CZE'::character(3), _europe, 'Czech Republic'::text, 'Chronocare.Application.Items\Pictures\Flags\czech-republic.png'::text);
CALL public."p_InsertCountry"(243::integer, 'CD'::character(2), 'COD'::character(3), _africa, 'Democratic Republic of the Congo'::text, 'Chronocare.Application.Items\Pictures\Flags\democratic-republic-of-congo.png'::text);
CALL public."p_InsertCountry"(45::integer, 'DK'::character(2), 'DNK'::character(3), _europe, 'Denmark'::text, 'Chronocare.Application.Items\Pictures\Flags\denmark.png'::text);
CALL public."p_InsertCountry"(253::integer, 'DJ'::character(2), 'DJI'::character(3), _africa, 'Djibouti'::text, 'Chronocare.Application.Items\Pictures\Flags\djibouti.png'::text);
CALL public."p_InsertCountry"(1::integer, 'DM'::character(2), 'DMA'::character(3), _america, 'Dominica'::text, 'Chronocare.Application.Items\Pictures\Flags\dominica.png'::text);
CALL public."p_InsertCountry"(1::integer, 'DO'::character(2), 'DOM'::character(3), _america, 'Dominican Republic'::text, 'Chronocare.Application.Items\Pictures\Flags\dominican-republic.png'::text);
CALL public."p_InsertCountry"(670::integer, 'TL'::character(2), 'TLS'::character(3), _oceania, 'East Timor'::text, 'Chronocare.Application.Items\Pictures\Flags\east-timor.png'::text);
CALL public."p_InsertCountry"(593::integer, 'EC'::character(2), 'ECU'::character(3), _america, 'Ecuador'::text, 'Chronocare.Application.Items\Pictures\Flags\ecuador.png'::text);
CALL public."p_InsertCountry"(20::integer, 'EG'::character(2), 'EGY'::character(3), _africa, 'Egypt'::text, 'Chronocare.Application.Items\Pictures\Flags\egypt.png'::text);
CALL public."p_InsertCountry"(503::integer, 'SV'::character(2), 'SLV'::character(3), _america, 'El Salvador'::text, 'Chronocare.Application.Items\Pictures\Flags\el-salvador.png'::text);
CALL public."p_InsertCountry"(240::integer, 'GQ'::character(2), 'GNQ'::character(3), _africa, 'Equatorial Guinea'::text, 'Chronocare.Application.Items\Pictures\Flags\equatorial-guinea.png'::text);
CALL public."p_InsertCountry"(291::integer, 'ER'::character(2), 'ERI'::character(3), _africa, 'Eritrea'::text, 'Chronocare.Application.Items\Pictures\Flags\eritrea.png'::text);
CALL public."p_InsertCountry"(372::integer, 'EE'::character(2), 'EST'::character(3), _europe, 'Estonia'::text, 'Chronocare.Application.Items\Pictures\Flags\estonia.png'::text);
CALL public."p_InsertCountry"(251::integer, 'ET'::character(2), 'ETH'::character(3), _africa, 'Ethiopia'::text, 'Chronocare.Application.Items\Pictures\Flags\ethiopia.png'::text);
CALL public."p_InsertCountry"(500::integer, 'FK'::character(2), 'FLK'::character(3), _oceania, 'Falkland Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\falkland-islands.png'::text);
CALL public."p_InsertCountry"(298::integer, 'FO'::character(2), 'FRO'::character(3), _oceania, 'Faroe Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\faroe-islands.png'::text);
CALL public."p_InsertCountry"(679::integer, 'FJ'::character(2), 'FJI'::character(3), _oceania, 'Fiji'::text, 'Chronocare.Application.Items\Pictures\Flags\fiji.png'::text);
CALL public."p_InsertCountry"(358::integer, 'FI'::character(2), 'FIN'::character(3), _europe, 'Finland'::text, 'Chronocare.Application.Items\Pictures\Flags\finland.png'::text);
CALL public."p_InsertCountry"(33::integer, 'FR'::character(2), 'FRA'::character(3), _europe, 'France'::text, 'Chronocare.Application.Items\Pictures\Flags\france.png'::text);
CALL public."p_InsertCountry"(594::integer, 'GF'::character(2), 'GUF'::character(3), _america, 'French Guiana'::text, 'Chronocare.Application.Items\Pictures\Flags\france.png'::text);
CALL public."p_InsertCountry"(689::integer, 'PF'::character(2), 'PYF'::character(3), _asia, 'French Polynesia'::text, 'Chronocare.Application.Items\Pictures\Flags\french-polynesia.png'::text);
CALL public."p_InsertCountry"(241::integer, 'GA'::character(2), 'GAB'::character(3), _africa, 'Gabon'::text, 'Chronocare.Application.Items\Pictures\Flags\gabon.png'::text);
CALL public."p_InsertCountry"(220::integer, 'GM'::character(2), 'GMB'::character(3), _africa, 'Gambia'::text, 'Chronocare.Application.Items\Pictures\Flags\gambia.png'::text);
CALL public."p_InsertCountry"(995::integer, 'GE'::character(2), 'GEO'::character(3), _europe, 'Georgia'::text, 'Chronocare.Application.Items\Pictures\Flags\georgia.png'::text);
CALL public."p_InsertCountry"(49::integer, 'DE'::character(2), 'DEU'::character(3), _europe, 'Germany'::text, 'Chronocare.Application.Items\Pictures\Flags\germany.png'::text);
CALL public."p_InsertCountry"(233::integer, 'GH'::character(2), 'GHA'::character(3), _africa, 'Ghana'::text, 'Chronocare.Application.Items\Pictures\Flags\ghana.png'::text);
CALL public."p_InsertCountry"(350::integer, 'GI'::character(2), 'GIB'::character(3), _europe, 'Gibraltar'::text, 'Chronocare.Application.Items\Pictures\Flags\gibraltar.png'::text);
CALL public."p_InsertCountry"(30::integer, 'GR'::character(2), 'GRC'::character(3), _europe, 'Greece'::text, 'Chronocare.Application.Items\Pictures\Flags\greece.png'::text);
CALL public."p_InsertCountry"(299::integer, 'GL'::character(2), 'GRL'::character(3), _europe, 'Greenland'::text, 'Chronocare.Application.Items\Pictures\Flags\greenland.png'::text);
CALL public."p_InsertCountry"(1::integer, 'GD'::character(2), 'GRD'::character(3), _america, 'Grenada'::text, 'Chronocare.Application.Items\Pictures\Flags\grenada.png'::text);
CALL public."p_InsertCountry"(590::integer, 'GP'::character(2), 'GLP'::character(3), _america, 'Guadeloupe'::text, 'Chronocare.Application.Items\Pictures\Flags\france.png'::text);
CALL public."p_InsertCountry"(1::integer, 'GU'::character(2), 'GUM'::character(3), _america, 'Guam'::text, 'Chronocare.Application.Items\Pictures\Flags\guam.png'::text);
CALL public."p_InsertCountry"(502::integer, 'GT'::character(2), 'GTM'::character(3), _america, 'Guatemala'::text, 'Chronocare.Application.Items\Pictures\Flags\guatemala.png'::text);
CALL public."p_InsertCountry"(44::integer, 'GG'::character(2), 'GGY'::character(3), _oceania, 'Guernsey'::text, 'Chronocare.Application.Items\Pictures\Flags\guernsey.png'::text);
CALL public."p_InsertCountry"(224::integer, 'GN'::character(2), 'GIN'::character(3), _africa, 'Guinea'::text, 'Chronocare.Application.Items\Pictures\Flags\guinea.png'::text);
CALL public."p_InsertCountry"(245::integer, 'GW'::character(2), 'GNB'::character(3), _africa, 'Guinea-Bissau'::text, 'Chronocare.Application.Items\Pictures\Flags\guinea-bissau.png'::text);
CALL public."p_InsertCountry"(592::integer, 'GY'::character(2), 'GUY'::character(3), _america, 'Guyana'::text, 'Chronocare.Application.Items\Pictures\Flags\guyana.png'::text);
CALL public."p_InsertCountry"(509::integer, 'HT'::character(2), 'HTI'::character(3), _america, 'Haiti'::text, 'Chronocare.Application.Items\Pictures\Flags\haiti.png'::text);
CALL public."p_InsertCountry"(504::integer, 'HN'::character(2), 'HND'::character(3), _america, 'Honduras'::text, 'Chronocare.Application.Items\Pictures\Flags\honduras.png'::text);
CALL public."p_InsertCountry"(852::integer, 'HK'::character(2), 'HKG'::character(3), _asia, 'Hong Kong'::text, 'Chronocare.Application.Items\Pictures\Flags\hong-kong.png'::text);
CALL public."p_InsertCountry"(36::integer, 'HU'::character(2), 'HUN'::character(3), _europe, 'Hungary'::text, 'Chronocare.Application.Items\Pictures\Flags\hungary.png'::text);
CALL public."p_InsertCountry"(354::integer, 'IS'::character(2), 'ISL'::character(3), _europe, 'Iceland'::text, 'Chronocare.Application.Items\Pictures\Flags\iceland.png'::text);
CALL public."p_InsertCountry"(91::integer, 'IN'::character(2), 'IND'::character(3), _asia, 'India'::text, 'Chronocare.Application.Items\Pictures\Flags\india.png'::text);
CALL public."p_InsertCountry"(62::integer, 'ID'::character(2), 'IDN'::character(3), _asia, 'Indonesia'::text, 'Chronocare.Application.Items\Pictures\Flags\indonesia.png'::text);
CALL public."p_InsertCountry"(98::integer, 'IR'::character(2), 'IRN'::character(3), _asia, 'Iran'::text, 'Chronocare.Application.Items\Pictures\Flags\iran.png'::text);
CALL public."p_InsertCountry"(964::integer, 'IQ'::character(2), 'IRQ'::character(3), _asia, 'Iraq'::text, 'Chronocare.Application.Items\Pictures\Flags\iraq.png'::text);
CALL public."p_InsertCountry"(353::integer, 'IE'::character(2), 'IRL'::character(3), _europe, 'Ireland'::text, 'Chronocare.Application.Items\Pictures\Flags\ireland.png'::text);
CALL public."p_InsertCountry"(44::integer, 'IM'::character(2), 'IMN'::character(3), _oceania, 'Isle of Man'::text, 'Chronocare.Application.Items\Pictures\Flags\isle-of-man.png'::text);
CALL public."p_InsertCountry"(972::integer, 'IL'::character(2), 'ISR'::character(3), _asia, 'Israel'::text, 'Chronocare.Application.Items\Pictures\Flags\israel.png'::text);
CALL public."p_InsertCountry"(39::integer, 'IT'::character(2), 'ITA'::character(3), _europe, 'Italy'::text, 'Chronocare.Application.Items\Pictures\Flags\italy.png'::text);
CALL public."p_InsertCountry"(225::integer, 'CI'::character(2), 'CIV'::character(3), _africa, 'Ivory Coast'::text, 'Chronocare.Application.Items\Pictures\Flags\ivory-coast.png'::text);
CALL public."p_InsertCountry"(1::integer, 'JM'::character(2), 'JAM'::character(3), _america, 'Jamaica'::text, 'Chronocare.Application.Items\Pictures\Flags\jamaica.png'::text);
CALL public."p_InsertCountry"(81::integer, 'JP'::character(2), 'JPN'::character(3), _asia, 'Japan'::text, 'Chronocare.Application.Items\Pictures\Flags\japan.png'::text);
CALL public."p_InsertCountry"(44::integer, 'JE'::character(2), 'JEY'::character(3), _europe, 'Jersey'::text, 'Chronocare.Application.Items\Pictures\Flags\jersey.png'::text);
CALL public."p_InsertCountry"(962::integer, 'JO'::character(2), 'JOR'::character(3), _asia, 'Jordan'::text, 'Chronocare.Application.Items\Pictures\Flags\jordan.png'::text);
CALL public."p_InsertCountry"(7::integer, 'KZ'::character(2), 'KAZ'::character(3), _asia, 'Kazakhstan'::text, 'Chronocare.Application.Items\Pictures\Flags\kazakhstan.png'::text);
CALL public."p_InsertCountry"(254::integer, 'KE'::character(2), 'KEN'::character(3), _africa, 'Kenya'::text, 'Chronocare.Application.Items\Pictures\Flags\kenya.png'::text);
CALL public."p_InsertCountry"(686::integer, 'KI'::character(2), 'KIR'::character(3), _oceania, 'Kiribati'::text, 'Chronocare.Application.Items\Pictures\Flags\kiribati.png'::text);
CALL public."p_InsertCountry"(383::integer, 'XK'::character(2), 'XKX'::character(3), _europe, 'Kosovo'::text, 'Chronocare.Application.Items\Pictures\Flags\kosovo.png'::text);
CALL public."p_InsertCountry"(965::integer, 'KW'::character(2), 'KWT'::character(3), _asia, 'Kuwait'::text, 'Chronocare.Application.Items\Pictures\Flags\kuwait.png'::text);
CALL public."p_InsertCountry"(996::integer, 'KG'::character(2), 'KGZ'::character(3), _asia, 'Kyrgyzstan'::text, 'Chronocare.Application.Items\Pictures\Flags\kyrgyzstan.png'::text);
CALL public."p_InsertCountry"(856::integer, 'LA'::character(2), 'LAO'::character(3), _asia, 'Laos'::text, 'Chronocare.Application.Items\Pictures\Flags\laos.png'::text);
CALL public."p_InsertCountry"(371::integer, 'LV'::character(2), 'LVA'::character(3), _europe, 'Latvia'::text, 'Chronocare.Application.Items\Pictures\Flags\latvia.png'::text);
CALL public."p_InsertCountry"(961::integer, 'LB'::character(2), 'LBN'::character(3), _asia, 'Lebanon'::text, 'Chronocare.Application.Items\Pictures\Flags\lebanon.png'::text);
CALL public."p_InsertCountry"(266::integer, 'LS'::character(2), 'LSO'::character(3), _africa, 'Lesotho'::text, 'Chronocare.Application.Items\Pictures\Flags\lesotho.png'::text);
CALL public."p_InsertCountry"(231::integer, 'LR'::character(2), 'LBR'::character(3), _africa, 'Liberia'::text, 'Chronocare.Application.Items\Pictures\Flags\liberia.png'::text);
CALL public."p_InsertCountry"(218::integer, 'LY'::character(2), 'LBY'::character(3), _africa, 'Libya'::text, 'Chronocare.Application.Items\Pictures\Flags\libya.png'::text);
CALL public."p_InsertCountry"(423::integer, 'LI'::character(2), 'LIE'::character(3), _europe, 'Liechtenstein'::text, 'Chronocare.Application.Items\Pictures\Flags\liechtenstein.png'::text);
CALL public."p_InsertCountry"(370::integer, 'LT'::character(2), 'LTU'::character(3), _europe, 'Lithuania'::text, 'Chronocare.Application.Items\Pictures\Flags\lithuania.png'::text);
CALL public."p_InsertCountry"(352::integer, 'LU'::character(2), 'LUX'::character(3), _europe, 'Luxembourg'::text, 'Chronocare.Application.Items\Pictures\Flags\luxembourg.png'::text);
CALL public."p_InsertCountry"(853::integer, 'MO'::character(2), 'MAC'::character(3), _asia, 'Macau'::text, 'Chronocare.Application.Items\Pictures\Flags\macau.png'::text);
CALL public."p_InsertCountry"(389::integer, 'MK'::character(2), 'MKD'::character(3), _europe, 'Macedonia'::text, 'Chronocare.Application.Items\Pictures\Flags\republic-of-macedonia.png'::text);
CALL public."p_InsertCountry"(261::integer, 'MG'::character(2), 'MDG'::character(3), _africa, 'Madagascar'::text, 'Chronocare.Application.Items\Pictures\Flags\madagascar.png'::text);
CALL public."p_InsertCountry"(265::integer, 'MW'::character(2), 'MWI'::character(3), _africa, 'Malawi'::text, 'Chronocare.Application.Items\Pictures\Flags\malawi.png'::text);
CALL public."p_InsertCountry"(60::integer, 'MY'::character(2), 'MYS'::character(3), _asia, 'Malaysia'::text, 'Chronocare.Application.Items\Pictures\Flags\malaysia.png'::text);
CALL public."p_InsertCountry"(960::integer, 'MV'::character(2), 'MDV'::character(3), _america, 'Maldives'::text, 'Chronocare.Application.Items\Pictures\Flags\maldives.png'::text);
CALL public."p_InsertCountry"(223::integer, 'ML'::character(2), 'MLI'::character(3), _africa, 'Mali'::text, 'Chronocare.Application.Items\Pictures\Flags\mali.png'::text);
CALL public."p_InsertCountry"(356::integer, 'MT'::character(2), 'MLT'::character(3), _america, 'Malta'::text, 'Chronocare.Application.Items\Pictures\Flags\malta.png'::text);
CALL public."p_InsertCountry"(692::integer, 'MH'::character(2), 'MHL'::character(3), _oceania, 'Marshall Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\marshall-islands.png'::text);
CALL public."p_InsertCountry"(596::integer, 'MQ'::character(2), 'MTQ'::character(3), _america, 'Martinique'::text, 'Chronocare.Application.Items\Pictures\Flags\martinique.png'::text);
CALL public."p_InsertCountry"(222::integer, 'MR'::character(2), 'MRT'::character(3), _africa, 'Mauritania'::text, 'Chronocare.Application.Items\Pictures\Flags\mauritania.png'::text);
CALL public."p_InsertCountry"(230::integer, 'MU'::character(2), 'MUS'::character(3), _africa, 'Mauritius'::text, 'Chronocare.Application.Items\Pictures\Flags\mauritius.png'::text);
CALL public."p_InsertCountry"(262::integer, 'YT'::character(2), 'MYT'::character(3), _africa, 'Mayotte'::text, 'Chronocare.Application.Items\Pictures\Flags\mayotte.png'::text);
CALL public."p_InsertCountry"(52::integer, 'MX'::character(2), 'MEX'::character(3), _america, 'Mexico'::text, 'Chronocare.Application.Items\Pictures\Flags\mexico.png'::text);
CALL public."p_InsertCountry"(691::integer, 'FM'::character(2), 'FSM'::character(3), _oceania, 'Micronesia'::text, 'Chronocare.Application.Items\Pictures\Flags\micronesia.png'::text);
CALL public."p_InsertCountry"(373::integer, 'MD'::character(2), 'MDA'::character(3), _europe, 'Moldova'::text, 'Chronocare.Application.Items\Pictures\Flags\moldova.png'::text);
CALL public."p_InsertCountry"(377::integer, 'MC'::character(2), 'MCO'::character(3), _europe, 'Monaco'::text, 'Chronocare.Application.Items\Pictures\Flags\monaco.png'::text);
CALL public."p_InsertCountry"(976::integer, 'MN'::character(2), 'MNG'::character(3), _america, 'Mongolia'::text, 'Chronocare.Application.Items\Pictures\Flags\mongolia.png'::text);
CALL public."p_InsertCountry"(382::integer, 'ME'::character(2), 'MNE'::character(3), _europe, 'Montenegro'::text, 'Chronocare.Application.Items\Pictures\Flags\montenegro.png'::text);
CALL public."p_InsertCountry"(1::integer, 'MS'::character(2), 'MSR'::character(3), _europe, 'Montserrat'::text, 'Chronocare.Application.Items\Pictures\Flags\montserrat.png'::text);
CALL public."p_InsertCountry"(212::integer, 'MA'::character(2), 'MAR'::character(3), _africa, 'Morocco'::text, 'Chronocare.Application.Items\Pictures\Flags\morocco.png'::text);
CALL public."p_InsertCountry"(258::integer, 'MZ'::character(2), 'MOZ'::character(3), _africa, 'Mozambique'::text, 'Chronocare.Application.Items\Pictures\Flags\mozambique.png'::text);
CALL public."p_InsertCountry"(95::integer, 'MM'::character(2), 'MMR'::character(3), _asia, 'Myanmar'::text, 'Chronocare.Application.Items\Pictures\Flags\myanmar.png'::text);
CALL public."p_InsertCountry"(264::integer, 'NA'::character(2), 'NAM'::character(3), _africa, 'Namibia'::text, 'Chronocare.Application.Items\Pictures\Flags\namibia.png'::text);
CALL public."p_InsertCountry"(674::integer, 'NR'::character(2), 'NRU'::character(3), _oceania, 'Nauru'::text, 'Chronocare.Application.Items\Pictures\Flags\nauru.png'::text);
CALL public."p_InsertCountry"(977::integer, 'NP'::character(2), 'NPL'::character(3), _asia, 'Nepal'::text, 'Chronocare.Application.Items\Pictures\Flags\nepal.png'::text);
CALL public."p_InsertCountry"(31::integer, 'NL'::character(2), 'NLD'::character(3), _europe, 'Netherlands'::text, 'Chronocare.Application.Items\Pictures\Flags\netherlands.png'::text);
CALL public."p_InsertCountry"(687::integer, 'NC'::character(2), 'NCL'::character(3), _asia, 'New Caledonia'::text, 'Chronocare.Application.Items\Pictures\Flags\new-caledonia.png'::text);
CALL public."p_InsertCountry"(64::integer, 'NZ'::character(2), 'NZL'::character(3), _oceania, 'New Zealand'::text, 'Chronocare.Application.Items\Pictures\Flags\new-zealand.png'::text);
CALL public."p_InsertCountry"(505::integer, 'NI'::character(2), 'NIC'::character(3), _america, 'Nicaragua'::text, 'Chronocare.Application.Items\Pictures\Flags\nicaragua.png'::text);
CALL public."p_InsertCountry"(227::integer, 'NE'::character(2), 'NER'::character(3), _africa, 'Niger'::text, 'Chronocare.Application.Items\Pictures\Flags\niger.png'::text);
CALL public."p_InsertCountry"(234::integer, 'NG'::character(2), 'NGA'::character(3), _africa, 'Nigeria'::text, 'Chronocare.Application.Items\Pictures\Flags\nigeria.png'::text);
CALL public."p_InsertCountry"(683::integer, 'NU'::character(2), 'NIU'::character(3), _oceania, 'Niue'::text, 'Chronocare.Application.Items\Pictures\Flags\niue.png'::text);
CALL public."p_InsertCountry"(672::integer, 'NF'::character(2), 'NFK'::character(3), _oceania, 'Norfolk Island'::text, 'Chronocare.Application.Items\Pictures\Flags\norfolk-island.png'::text);
CALL public."p_InsertCountry"(850::integer, 'KP'::character(2), 'PRK'::character(3), _asia, 'North Korea'::text, 'Chronocare.Application.Items\Pictures\Flags\north-korea.png'::text);
CALL public."p_InsertCountry"(1::integer, 'MP'::character(2), 'MNP'::character(3), _oceania, 'Northern Mariana Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\northern-mariana-islands.png'::text);
CALL public."p_InsertCountry"(47::integer, 'NO'::character(2), 'NOR'::character(3), _europe, 'Norway'::text, 'Chronocare.Application.Items\Pictures\Flags\norway.png'::text);
CALL public."p_InsertCountry"(968::integer, 'OM'::character(2), 'OMN'::character(3), _asia, 'Oman'::text, 'Chronocare.Application.Items\Pictures\Flags\oman.png'::text);
CALL public."p_InsertCountry"(92::integer, 'PK'::character(2), 'PAK'::character(3), _asia, 'Pakistan'::text, 'Chronocare.Application.Items\Pictures\Flags\pakistan.png'::text);
CALL public."p_InsertCountry"(680::integer, 'PW'::character(2), 'PLW'::character(3), _oceania, 'Palau'::text, 'Chronocare.Application.Items\Pictures\Flags\palau.png'::text);
CALL public."p_InsertCountry"(970::integer, 'PS'::character(2), 'PSE'::character(3), _asia, 'Palestine'::text, 'Chronocare.Application.Items\Pictures\Flags\palestine.png'::text);
CALL public."p_InsertCountry"(507::integer, 'PA'::character(2), 'PAN'::character(3), _america, 'Panama'::text, 'Chronocare.Application.Items\Pictures\Flags\panama.png'::text);
CALL public."p_InsertCountry"(675::integer, 'PG'::character(2), 'PNG'::character(3), _oceania, 'Papua New Guinea'::text, 'Chronocare.Application.Items\Pictures\Flags\papua-new-guinea.png'::text);
CALL public."p_InsertCountry"(595::integer, 'PY'::character(2), 'PRY'::character(3), _america, 'Paraguay'::text, 'Chronocare.Application.Items\Pictures\Flags\paraguay.png'::text);
CALL public."p_InsertCountry"(51::integer, 'PE'::character(2), 'PER'::character(3), _america, 'Peru'::text, 'Chronocare.Application.Items\Pictures\Flags\peru.png'::text);
CALL public."p_InsertCountry"(63::integer, 'PH'::character(2), 'PHL'::character(3), _asia, 'Philippines'::text, 'Chronocare.Application.Items\Pictures\Flags\philippines.png'::text);
CALL public."p_InsertCountry"(64::integer, 'PN'::character(2), 'PCN'::character(3), _oceania, 'Pitcairn'::text, 'Chronocare.Application.Items\Pictures\Flags\pitcairn-islands.png'::text);
CALL public."p_InsertCountry"(48::integer, 'PL'::character(2), 'POL'::character(3), _europe, 'Poland'::text, 'Chronocare.Application.Items\Pictures\Flags\poland.png'::text);
CALL public."p_InsertCountry"(351::integer, 'PT'::character(2), 'PRT'::character(3), _europe, 'Portugal'::text, 'Chronocare.Application.Items\Pictures\Flags\portugal.png'::text);
CALL public."p_InsertCountry"(1::integer, 'PR'::character(2), 'PRI'::character(3), _america, 'Puerto Rico'::text, 'Chronocare.Application.Items\Pictures\Flags\puerto-rico.png'::text);
CALL public."p_InsertCountry"(974::integer, 'QA'::character(2), 'QAT'::character(3), _asia, 'Qatar'::text, 'Chronocare.Application.Items\Pictures\Flags\qatar.png'::text);
CALL public."p_InsertCountry"(242::integer, 'CG'::character(2), 'COG'::character(3), _africa, 'Republic of the Congo'::text, 'Chronocare.Application.Items\Pictures\Flags\republic-of-the-congo.png'::text);
CALL public."p_InsertCountry"(262::integer, 'RE'::character(2), 'REU'::character(3), _africa, 'Reunion'::text, 'Chronocare.Application.Items\Pictures\Flags\reunion.png'::text);
CALL public."p_InsertCountry"(40::integer, 'RO'::character(2), 'ROU'::character(3), _europe, 'Romania'::text, 'Chronocare.Application.Items\Pictures\Flags\romania.png'::text);
CALL public."p_InsertCountry"(7::integer, 'RU'::character(2), 'RUS'::character(3), _asia, 'Russia'::text, 'Chronocare.Application.Items\Pictures\Flags\russia.png'::text);
CALL public."p_InsertCountry"(250::integer, 'RW'::character(2), 'RWA'::character(3), _africa, 'Rwanda'::text, 'Chronocare.Application.Items\Pictures\Flags\rwanda.png'::text);
CALL public."p_InsertCountry"(590::integer, 'BL'::character(2), 'BLM'::character(3), _america, 'Saint Barthelemy'::text, 'Chronocare.Application.Items\Pictures\Flags\saint-barthelemy.png'::text);
CALL public."p_InsertCountry"(290::integer, 'SH'::character(2), 'SHN'::character(3), _america, 'Saint Helena'::text, 'Chronocare.Application.Items\Pictures\Flags\saint-helena.png'::text);
CALL public."p_InsertCountry"(1::integer, 'KN'::character(2), 'KNA'::character(3), _oceania, 'Saint Kitts and Nevis'::text, 'Chronocare.Application.Items\Pictures\Flags\saint-kitts-and-nevis.png'::text);
CALL public."p_InsertCountry"(1::integer, 'LC'::character(2), 'LCA'::character(3), _oceania, 'Saint Lucia'::text, 'Chronocare.Application.Items\Pictures\Flags\saint-lucia.png'::text);
CALL public."p_InsertCountry"(590::integer, 'MF'::character(2), 'MAF'::character(3), _oceania, 'Saint Martin'::text, 'Chronocare.Application.Items\Pictures\Flags\france.png'::text);
CALL public."p_InsertCountry"(508::integer, 'PM'::character(2), 'SPM'::character(3), _oceania, 'Saint Pierre and Miquelon'::text, 'Chronocare.Application.Items\Pictures\Flags\saint-pierre-and-miquelon.png'::text);
CALL public."p_InsertCountry"(1::integer, 'VC'::character(2), 'VCT'::character(3), _oceania, 'Saint Vincent and the Grenadines'::text, 'Chronocare.Application.Items\Pictures\Flags\saint-vincent-and-the-grenadines.png'::text);
CALL public."p_InsertCountry"(685::integer, 'WS'::character(2), 'WSM'::character(3), _oceania, 'Samoa'::text, 'Chronocare.Application.Items\Pictures\Flags\samoa.png'::text);
CALL public."p_InsertCountry"(378::integer, 'SM'::character(2), 'SMR'::character(3), _oceania, 'San Marino'::text, 'Chronocare.Application.Items\Pictures\Flags\san-marino.png'::text);
CALL public."p_InsertCountry"(239::integer, 'ST'::character(2), 'STP'::character(3), _africa, 'Sao Tome and Principe'::text, 'Chronocare.Application.Items\Pictures\Flags\sao-tome-and-principe.png'::text);
CALL public."p_InsertCountry"(966::integer, 'SA'::character(2), 'SAU'::character(3), _asia, 'Saudi Arabia'::text, 'Chronocare.Application.Items\Pictures\Flags\saudi-arabia.png'::text);
CALL public."p_InsertCountry"(221::integer, 'SN'::character(2), 'SEN'::character(3), _africa, 'Senegal'::text, 'Chronocare.Application.Items\Pictures\Flags\senegal.png'::text);
CALL public."p_InsertCountry"(381::integer, 'RS'::character(2), 'SRB'::character(3), _europe, 'Serbia'::text, 'Chronocare.Application.Items\Pictures\Flags\serbia.png'::text);
CALL public."p_InsertCountry"(248::integer, 'SC'::character(2), 'SYC'::character(3), _africa, 'Seychelles'::text, 'Chronocare.Application.Items\Pictures\Flags\seychelles.png'::text);
CALL public."p_InsertCountry"(232::integer, 'SL'::character(2), 'SLE'::character(3), _africa, 'Sierra Leone'::text, 'Chronocare.Application.Items\Pictures\Flags\sierra-leone.png'::text);
CALL public."p_InsertCountry"(65::integer, 'SG'::character(2), 'SGP'::character(3), _asia, 'Singapore'::text, 'Chronocare.Application.Items\Pictures\Flags\singapore.png'::text);
CALL public."p_InsertCountry"(1::integer, 'SX'::character(2), 'SXM'::character(3), _oceania, 'Sint Maarten'::text, 'Chronocare.Application.Items\Pictures\Flags\sint-maarten.png'::text);
CALL public."p_InsertCountry"(421::integer, 'SK'::character(2), 'SVK'::character(3), _europe, 'Slovakia'::text, 'Chronocare.Application.Items\Pictures\Flags\slovakia.png'::text);
CALL public."p_InsertCountry"(386::integer, 'SI'::character(2), 'SVN'::character(3), _europe, 'Slovenia'::text, 'Chronocare.Application.Items\Pictures\Flags\slovenia.png'::text);
CALL public."p_InsertCountry"(677::integer, 'SB'::character(2), 'SLB'::character(3), _oceania, 'Solomon Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\solomon-islands.png'::text);
CALL public."p_InsertCountry"(252::integer, 'SO'::character(2), 'SOM'::character(3), _africa, 'Somalia'::text, 'Chronocare.Application.Items\Pictures\Flags\somalia.png'::text);
CALL public."p_InsertCountry"(27::integer, 'ZA'::character(2), 'ZAF'::character(3), _africa, 'South Africa'::text, 'Chronocare.Application.Items\Pictures\Flags\south-africa.png'::text);
CALL public."p_InsertCountry"(82::integer, 'KR'::character(2), 'KOR'::character(3), _asia, 'South Korea'::text, 'Chronocare.Application.Items\Pictures\Flags\south-korea.png'::text);
CALL public."p_InsertCountry"(211::integer, 'SS'::character(2), 'SSD'::character(3), _africa, 'South Sudan'::text, 'Chronocare.Application.Items\Pictures\Flags\south-sudan.png'::text);
CALL public."p_InsertCountry"(34::integer, 'ES'::character(2), 'ESP'::character(3), _europe, 'Spain'::text, 'Chronocare.Application.Items\Pictures\Flags\spain.png'::text);
CALL public."p_InsertCountry"(94::integer, 'LK'::character(2), 'LKA'::character(3), _asia, 'Sri Lanka'::text, 'Chronocare.Application.Items\Pictures\Flags\sri-lanka.png'::text);
CALL public."p_InsertCountry"(249::integer, 'SD'::character(2), 'SDN'::character(3), _africa, 'Sudan'::text, 'Chronocare.Application.Items\Pictures\Flags\sudan.png'::text);
CALL public."p_InsertCountry"(597::integer, 'SR'::character(2), 'SUR'::character(3), _asia, 'Suriname'::text, 'Chronocare.Application.Items\Pictures\Flags\suriname.png'::text);
CALL public."p_InsertCountry"(47::integer, 'SJ'::character(2), 'SJM'::character(3), _oceania, 'Svalbard and Jan Mayen'::text, 'Chronocare.Application.Items\Pictures\Flags\norway.png'::text);
CALL public."p_InsertCountry"(268::integer, 'SZ'::character(2), 'SWZ'::character(3), _africa, 'Swaziland'::text, 'Chronocare.Application.Items\Pictures\Flags\swaziland.png'::text);
CALL public."p_InsertCountry"(46::integer, 'SE'::character(2), 'SWE'::character(3), _europe, 'Sweden'::text, 'Chronocare.Application.Items\Pictures\Flags\sweden.png'::text);
CALL public."p_InsertCountry"(41::integer, 'CH'::character(2), 'CHE'::character(3), _europe, 'Switzerland'::text, 'Chronocare.Application.Items\Pictures\Flags\switzerland.png'::text);
CALL public."p_InsertCountry"(963::integer, 'SY'::character(2), 'SYR'::character(3), _asia, 'Syria'::text, 'Chronocare.Application.Items\Pictures\Flags\syria.png'::text);
CALL public."p_InsertCountry"(886::integer, 'TW'::character(2), 'TWN'::character(3), _asia, 'Taiwan'::text, 'Chronocare.Application.Items\Pictures\Flags\taiwan.png'::text);
CALL public."p_InsertCountry"(992::integer, 'TJ'::character(2), 'TJK'::character(3), _asia, 'Tajikistan'::text, 'Chronocare.Application.Items\Pictures\Flags\tajikistan.png'::text);
CALL public."p_InsertCountry"(255::integer, 'TZ'::character(2), 'TZA'::character(3), _africa, 'Tanzania'::text, 'Chronocare.Application.Items\Pictures\Flags\tanzania.png'::text);
CALL public."p_InsertCountry"(66::integer, 'TH'::character(2), 'THA'::character(3), _asia, 'Thailand'::text, 'Chronocare.Application.Items\Pictures\Flags\thailand.png'::text);
CALL public."p_InsertCountry"(228::integer, 'TG'::character(2), 'TGO'::character(3), _africa, 'Togo'::text, 'Chronocare.Application.Items\Pictures\Flags\togo.png'::text);
CALL public."p_InsertCountry"(690::integer, 'TK'::character(2), 'TKL'::character(3), _oceania, 'Tokelau'::text, 'Chronocare.Application.Items\Pictures\Flags\tokelau.png'::text);
CALL public."p_InsertCountry"(676::integer, 'TO'::character(2), 'TON'::character(3), _oceania, 'Tonga'::text, 'Chronocare.Application.Items\Pictures\Flags\tonga.png'::text);
CALL public."p_InsertCountry"(1::integer, 'TT'::character(2), 'TTO'::character(3), _oceania, 'Trinidad and Tobago'::text, 'Chronocare.Application.Items\Pictures\Flags\trinidad-and-tobago.png'::text);
CALL public."p_InsertCountry"(216::integer, 'TN'::character(2), 'TUN'::character(3), _africa, 'Tunisia'::text, 'Chronocare.Application.Items\Pictures\Flags\tunisia.png'::text);
CALL public."p_InsertCountry"(90::integer, 'TR'::character(2), 'TUR'::character(3), _europe, 'Turkey'::text, 'Chronocare.Application.Items\Pictures\Flags\turkey.png'::text);
CALL public."p_InsertCountry"(993::integer, 'TM'::character(2), 'TKM'::character(3), _asia, 'Turkmenistan'::text, 'Chronocare.Application.Items\Pictures\Flags\turkmenistan.png'::text);
CALL public."p_InsertCountry"(1::integer, 'TC'::character(2), 'TCA'::character(3), _oceania, 'Turks and Caicos Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\turks-and-caicos-islands.png'::text);
CALL public."p_InsertCountry"(688::integer, 'TV'::character(2), 'TUV'::character(3), _oceania, 'Tuvalu'::text, 'Chronocare.Application.Items\Pictures\Flags\tuvalu.png'::text);
CALL public."p_InsertCountry"(1::integer, 'VI'::character(2), 'VIR'::character(3), _oceania, 'U.S. Virgin Islands'::text, 'Chronocare.Application.Items\Pictures\Flags\virgin-islands.png'::text);
CALL public."p_InsertCountry"(256::integer, 'UG'::character(2), 'UGA'::character(3), _africa, 'Uganda'::text, 'Chronocare.Application.Items\Pictures\Flags\uganda.png'::text);
CALL public."p_InsertCountry"(380::integer, 'UA'::character(2), 'UKR'::character(3), _europe, 'Ukraine'::text, 'Chronocare.Application.Items\Pictures\Flags\ukraine.png'::text);
CALL public."p_InsertCountry"(971::integer, 'AE'::character(2), 'ARE'::character(3), _asia, 'United Arab Emirates'::text, 'Chronocare.Application.Items\Pictures\Flags\united-arab-emirates.png'::text);
CALL public."p_InsertCountry"(44::integer, 'GB'::character(2), 'GBR'::character(3), _europe, 'United Kingdom'::text, 'Chronocare.Application.Items\Pictures\Flags\united-kingdom.png'::text);
CALL public."p_InsertCountry"(1::integer, 'US'::character(2), 'USA'::character(3), _america, 'United States'::text, 'Chronocare.Application.Items\Pictures\Flags\united-states.png'::text);
CALL public."p_InsertCountry"(598::integer, 'UY'::character(2), 'URY'::character(3), _america, 'Uruguay'::text, 'Chronocare.Application.Items\Pictures\Flags\uruguay.png'::text);
CALL public."p_InsertCountry"(998::integer, 'UZ'::character(2), 'UZB'::character(3), _asia, 'Uzbekistan'::text, 'Chronocare.Application.Items\Pictures\Flags\uzbekistan.png'::text);
CALL public."p_InsertCountry"(678::integer, 'VU'::character(2), 'VUT'::character(3), _oceania, 'Vanuatu'::text, 'Chronocare.Application.Items\Pictures\Flags\vanuatu.png'::text);
CALL public."p_InsertCountry"(379::integer, 'VA'::character(2), 'VAT'::character(3), _europe, 'Vatican'::text, 'Chronocare.Application.Items\Pictures\Flags\vatican.png'::text);
CALL public."p_InsertCountry"(58::integer, 'VE'::character(2), 'VEN'::character(3), _america, 'Venezuela'::text, 'Chronocare.Application.Items\Pictures\Flags\venezuela.png'::text);
CALL public."p_InsertCountry"(84::integer, 'VN'::character(2), 'VNM'::character(3), _asia, 'Vietnam'::text, 'Chronocare.Application.Items\Pictures\Flags\vietnam.png'::text);
CALL public."p_InsertCountry"(681::integer, 'WF'::character(2), 'WLF'::character(3), _oceania, 'Wallis and Futuna'::text, 'Chronocare.Application.Items\Pictures\Flags\wallis-and-futuna.png'::text);
CALL public."p_InsertCountry"(212::integer, 'EH'::character(2), 'ESH'::character(3), _africa, 'Western Sahara'::text, 'Chronocare.Application.Items\Pictures\Flags\western-sahara.png'::text);
CALL public."p_InsertCountry"(967::integer, 'YE'::character(2), 'YEM'::character(3), _asia, 'Yemen'::text, 'Chronocare.Application.Items\Pictures\Flags\yemen.png'::text);
CALL public."p_InsertCountry"(260::integer, 'ZM'::character(2), 'ZMB'::character(3), _africa, 'Zambia'::text, 'Chronocare.Application.Items\Pictures\Flags\zambia.png'::text);
CALL public."p_InsertCountry"(263::integer, 'ZW'::character(2), 'ZWE'::character(3), _africa, 'Zimbabwe'::text, 'Chronocare.Application.Items\Pictures\Flags\zimbabwe.png'::text);
END $BODY$;

-- Insert Cities

DO
$BODY$
DECLARE _id character varying(50);

BEGIN
SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "ISO3" = 'CMR';
CALL public."p_InsertCity"(_id, 'YDE', 'Yaoundé');
CALL public."p_InsertCity"(_id, 'DLA', 'Douala');
END $BODY$;

-- Insert LanguageRelations

DO
$BODY$
DECLARE _id character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';
	
	-- Languages
	
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'US';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'en-US');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'GB';
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'en-GB');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'FR';
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'fr-FR');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'ES';
	CALL public."p_InsertLanguageRelation"(_es, _id, 'es-ES');
	SELECT "ID" INTO _id FROM public."cl_Languages" WHERE "Label" = 'AR';
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'ar-SA');
	
	-- Continents
	
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Africa';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Africa');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Africa');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Afrique');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'America';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'America');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'America');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Amérique');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Asia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Asia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Asia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Asie');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Europe';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Europe');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Europe');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Europe');
	SELECT "ID" INTO _id FROM public."cl_Continents" WHERE "Name" = 'Oceania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Oceania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Oceania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Oceanie');
	
	-- Countries
	
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Afghanistan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Afghanistan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Afghanistan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Afghanistan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Albania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Albania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Albania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Albanie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Algeria';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Algeria');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Algeria');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Algérie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Åland Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Åland Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Åland Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Les Iles Åland');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'American Samoa';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'American Samoa');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'American Samoa');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Samoa Américaines');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Andorra';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Andorra');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Andorra');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Andorres');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Angola';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Angola');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Angola');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Angola');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Anguilla';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Anguilla');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Anguilla');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Anguilla');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Antigua and Barbuda';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Antigua and Barbuda');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Antigua and Barbuda');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Antigua et Barbuda');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Argentina';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Argentina');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Argentina');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Argentine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Armenia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Armenia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Armenia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Arménie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Aruba';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Aruba');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Aruba');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Aruba');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Australia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Australia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Australia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Australie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Austria';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Austria');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Austria');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Autriche');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Azerbaijan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Azerbaijan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Azerbaijan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Azerbaijan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bahamas';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bahamas');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bahamas');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bahamas');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bahrain';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bahrain');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bahrain');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bahrein');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bangladesh';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bangladesh');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bangladesh');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bangladesh');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Barbados';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Barbados');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Barbados');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Barbades');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Belarus';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Belarus');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Belarus');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Biélorussie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Belgium';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Belgium');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Belgium');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Belgique');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Belize';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Belize');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Belize');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bélize');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Benin';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Benin');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Benin');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bénin');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bermuda';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bermuda');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bermuda');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bermudes');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bhutan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bhutan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bhutan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bhoutan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bolivia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bolivia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bolivia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bolivie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bosnia and Herzegovina';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bosnia and Herzegovina');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bosnia and Herzegovina');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bosnie Herzégovine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Botswana';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Botswana');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Botswana');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Botswana');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Brazil';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Brazil');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Brazil');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Brésil');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'British Indian Ocean Territory';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'British Indian Ocean Territory');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'British Indian Ocean Territory');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Territoire britannique de l''océan Indien');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'British Virgin Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'British Virgin Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'British Virgin Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Îles Vierges britanniques');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Brunei';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Brunei');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Brunei');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Brunei');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Bulgaria';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bulgaria');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bulgaria');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bulgarie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Burkina Faso';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Burkina Faso');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Burkina Faso');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Burkina Faso');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Burundi';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Burundi');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Burundi');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Burundi');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cambodia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cambodia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cambodia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Cambodge');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cameroon';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cameroon');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cameroon');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Cameroun');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Canada';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Canada');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Canada');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Canada');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cape Verde';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cape Verde');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cape Verde');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Cap Vert');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Caribbean Netherlands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Caribbean Netherlands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Caribbean Netherlands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pays-Bas caribéens');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cayman Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cayman Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cayman Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Îles Caïmans');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Central African Republic';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Central African Republic');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Central African Republic');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'République Centrafricaine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Chad';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Chad');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Chad');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tchad');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Chile';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Chile');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Chile');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Chili');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'China';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'China');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'China');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Chine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Christmas Island';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Christmas Island');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Christmas Island');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ile Christmas');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cocos Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cocos Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cocos Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Îles Cocos');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Colombia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Colombia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Colombia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Colombie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Comoros';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Comoros');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Comoros');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Comores');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cook Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cook Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cook Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Iles Cook');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Costa Rica';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Costa Rica');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Costa Rica');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Costa Rica');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Croatia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Croatia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Croatia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Croatie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cuba';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cuba');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cuba');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Cuba');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Curacao';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Curacao');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Curacao');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Curacao');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Cyprus';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Cyprus');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cyprus');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Chypre');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Czech Republic';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Czech Republic');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Czech Republic');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'République Tchèque');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Democratic Republic of the Congo';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Democratic Republic of the Congo');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Democratic Republic of the Congo');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'République Démocratique du Congo');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Denmark';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Denmark');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Denmark');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Danemark');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Djibouti';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Djibouti');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Djibouti');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Djibouti');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Dominica';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Dominica');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Dominica');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dominique');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Dominican Republic';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Dominican Republic');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Dominican Republic');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'République Dominicaine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'East Timor';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'East Timor');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'East Timor');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Timor Oriental');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Ecuador';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Ecuador');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Ecuador');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Equateur');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Egypt';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Egypt');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Egypt');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Egypte');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'El Salvador';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'El Salvador');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'El Salvador');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Le Salvador');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Equatorial Guinea';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Equatorial Guinea');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Equatorial Guinea');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guinée Équatoriale');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Eritrea';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Eritrea');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Eritrea');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Érythrée');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Estonia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Estonia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Estonia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Estonie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Ethiopia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Ethiopia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Ethiopia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ethiopie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Falkland Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Falkland Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Falkland Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Iles Falkland');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Faroe Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Faroe Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Faroe Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Îles Féroé');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Fiji';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Fiji');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Fiji');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Fiji');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Finland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Finland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Finland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Finlande');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'France';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'France');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'France');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'France');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'French Guiana';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'French Guiana');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'French Guiana');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guyane Française');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'French Polynesia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'French Polynesia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'French Polynesia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Polynésie française');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Gabon';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Gabon');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Gabon');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gabon');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Gambia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Gambia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Gambia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gambie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Georgia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Georgia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Georgia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Géorgie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Germany';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Germany');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Germany');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Allemagne');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Ghana';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Ghana');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Ghana');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ghana');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Gibraltar';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Gibraltar');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Gibraltar');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gibraltar');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Greece';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Greece');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Greece');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Grèce');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Greenland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Greenland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Greenland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Groenland');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Grenada';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Grenada');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Grenada');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Grenade');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guadeloupe';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guadeloupe');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guadeloupe');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guadeloupe');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guam';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guam');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guam');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guam');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guatemala';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guatemala');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guatemala');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guatemala');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guernsey';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guernsey');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guernsey');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guernesey');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guinea';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guinea');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guinea');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guinée');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guinea-Bissau';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guinea-Bissau');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guinea-Bissau');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guinée-Bissau');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Guyana';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Guyana');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Guyana');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Guyane');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Haiti';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Haiti');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Haiti');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Haiti');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Honduras';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Honduras');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Honduras');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Honduras');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Hong Kong';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Hong Kong');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Hong Kong');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Hong Kong');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Hungary';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Hungary');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Hungary');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Hongrie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Iceland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Iceland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Iceland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Islande');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'India';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'India');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'India');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Indie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Indonesia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Indonesia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Indonesia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Indonésie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Iran';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Iran');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Iran');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Iran');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Iraq';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Iraq');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Iraq');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Iraq');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Ireland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Ireland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Ireland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Irelande');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Isle of Man';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Isle of Man');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Isle of Man');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'île de Man');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Israel';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Israel');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Israel');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Israël');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Italy';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Italy');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Italy');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Italie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Ivory Coast';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Ivory Coast');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Ivory Coast');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Côte d''Ivoire');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Jamaica';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Jamaica');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Jamaica');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Jamaique');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Japan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Japan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Japan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Japan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Jersey';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Jersey');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Jersey');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Jersey');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Jordan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Jordan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Jordan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Jordanie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Kazakhstan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kazakhstan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kazakhstan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kazakhstan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Kenya';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kenya');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kenya');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kenya');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Kiribati';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kiribati');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kiribati');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kiribati');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Kosovo';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kosovo');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kosovo');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kosovo');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Kuwait';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kuwait');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kuwait');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Koweit');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Kyrgyzstan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kyrgyzstan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kyrgyzstan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kirghizistan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Laos';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Laos');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Laos');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Laos');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Latvia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Latvia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Latvia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Lettonie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Lebanon';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Lebanon');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Lebanon');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Liban');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Lesotho';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Lesotho');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Lesotho');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Lesotho');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Liberia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Liberia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Liberia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Libéria');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Libya';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Libya');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Libya');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Libye');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Liechtenstein';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Liechtenstein');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Liechtenstein');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Liechtenstein');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Lithuania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Lithuania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Lithuania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Lithuanie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Luxembourg';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Luxembourg');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Luxembourg');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Luxembourg');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Macau';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Macau');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Macau');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Macau');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Macedonia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Macedonia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Macedonia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Macédoine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Madagascar';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Madagascar');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Madagascar');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Madagascar');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Malawi';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Malawi');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Malawi');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Malawi');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Malaysia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Malaysia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Malaysia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Malaisie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Maldives';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Maldives');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Maldives');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Maldives');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mali';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mali');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mali');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mali');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Malta';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Malta');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Malta');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Malte');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Marshall Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Marshall Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Marshall Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Iles Marshall');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Martinique';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Martinique');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Martinique');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Martinique');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mauritania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mauritania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mauritania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mauritanie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mauritius';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mauritius');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mauritius');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ile Maurice');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mayotte';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mayotte');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mayotte');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mayotte');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mexico';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mexico');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mexico');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mexique');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Micronesia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Micronesia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Micronesia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Micronésie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Moldova';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Moldova');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Moldova');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Moldavie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Monaco';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Monaco');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Monaco');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Monaco');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mongolia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mongolia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mongolia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mongolie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Montenegro';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Montenegro');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Montenegro');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Monténégro');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Montserrat';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Montserrat');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Montserrat');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Montserrat');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Morocco';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Morocco');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Morocco');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Maroc');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Mozambique';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mozambique');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mozambique');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mozambique');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Myanmar';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Myanmar');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Myanmar');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Myanmar');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Namibia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Namibia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Namibia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Namibie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Nauru';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nauru');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nauru');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Nauru');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Nepal';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nepal');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nepal');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Népal');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Netherlands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Netherlands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Netherlands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pays-Bas');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'New Caledonia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'New Caledonia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'New Caledonia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Nouvelle Calédonie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'New Zealand';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'New Zealand');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'New Zealand');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Nouvelle-Zélande');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Nicaragua';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nicaragua');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nicaragua');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Nicaragua');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Niger';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Niger');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Niger');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Niger');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Nigeria';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nigeria');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nigeria');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Nigéria');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Niue';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Niue');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Niue');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Niué');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Norfolk Island';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Norfolk Island');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Norfolk Island');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ile Norfolk');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'North Korea';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'North Korea');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'North Korea');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Corée du Nord');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Northern Mariana Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Northern Mariana Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Northern Mariana Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Îles Mariannes du Nord');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Norway';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Norway');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Norway');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Norvège');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Oman';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Oman');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Oman');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Oman');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Pakistan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Pakistan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Pakistan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pakistan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Palau';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Palau');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Palau');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Palau');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Palestine';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Palestine');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Palestine');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Palestine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Panama';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Panama');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Panama');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Panama');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Papua New Guinea';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Papua New Guinea');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Papua New Guinea');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Papouasie Nouvelle Guinée');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Paraguay';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Paraguay');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Paraguay');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paraguay');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Peru';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Peru');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Peru');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Peru');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Philippines';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Philippines');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Philippines');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Philippines');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Pitcairn';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Pitcairn');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Pitcairn');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pitcairn');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Poland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Poland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Poland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pologne');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Portugal';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Portugal');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Portugal');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Portugal');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Puerto Rico';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Puerto Rico');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Puerto Rico');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Porto Rico');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Qatar';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Qatar');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Qatar');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Qatar');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Republic of the Congo';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Republic of the Congo');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Republic of the Congo');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'République du Congo');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Reunion';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Reunion');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Reunion');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réunion');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Romania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Romania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Romania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Roumanie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Russia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Russia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Russia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Russie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Rwanda';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Rwanda');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Rwanda');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Rwanda');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Barthelemy';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Barthelemy');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Barthelemy');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint Barthélemy');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Helena';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Helena');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Helena');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sainte Hélène');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Kitts and Nevis';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Kitts and Nevis');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Kitts and Nevis');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint-Christophe-et-Niévès');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Lucia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Lucia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Lucia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sainte Lucie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Martin';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Martin');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Martin');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint Martin');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Pierre and Miquelon';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Pierre and Miquelon');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Pierre and Miquelon');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint Pierre et Miquélon');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saint Vincent and the Grenadines';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saint Vincent and the Grenadines');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saint Vincent and the Grenadines');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint Vincent et les Grenadines');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Samoa';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Samoa');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Samoa');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Samoa');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'San Marino';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'San Marino');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'San Marino');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint Marin');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Sao Tome and Principe';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Sao Tome and Principe');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sao Tome and Principe');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sao Tomé et Principe');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Saudi Arabia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Saudi Arabia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Saudi Arabia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Arabie Saoudite');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Senegal';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Senegal');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Senegal');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sénégal');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Serbia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Serbia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Serbia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Serbie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Seychelles';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Seychelles');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Seychelles');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Seychelles');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Sierra Leone';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Sierra Leone');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sierra Leone');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sierra Leone');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Singapore';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Singapore');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Singapore');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Singapour');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Sint Maarten';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Sint Maarten');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sint Maarten');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Saint-Martin');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Slovakia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Slovakia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Slovakia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Slovaquie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Slovenia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Slovenia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Slovenia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Slovénie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Solomon Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Solomon Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Solomon Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Les îles Salomon');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Somalia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Somalia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Somalia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Somalie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'South Africa';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'South Africa');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'South Africa');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Afrique du Sud');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'South Korea';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'South Korea');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'South Korea');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Corée du Sud');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'South Sudan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'South Sudan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'South Sudan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Soudan du sud');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Spain';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Spain');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Spain');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Espagne');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Sri Lanka';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Sri Lanka');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sri Lanka');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sri Lanka');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Sudan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Sudan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sudan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Soudan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Suriname';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Suriname');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Suriname');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Suriname');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Svalbard and Jan Mayen';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Svalbard and Jan Mayen');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Svalbard and Jan Mayen');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Svalbard et Jan Mayen');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Swaziland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Swaziland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Swaziland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Swaziland');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Sweden';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Sweden');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sweden');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Suède');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Switzerland';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Switzerland');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Switzerland');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Suisse');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Syria';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Syria');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Syria');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Syrie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Taiwan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Taiwan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Taiwan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Taiwan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Tajikistan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tajikistan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tajikistan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tajikistan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Tanzania';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tanzania');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tanzania');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tanzanie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Thailand';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Thailand');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Thailand');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Thaïlande');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Togo';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Togo');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Togo');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Togo');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Tokelau';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tokelau');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tokelau');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tokélaou');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Tonga';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tonga');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tonga');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tonga');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Trinidad and Tobago';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Trinidad and Tobago');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Trinidad and Tobago');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Trinité-et-Tobago');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Tunisia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tunisia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tunisia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tunisie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Turkey';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Turkey');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Turkey');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Turquie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Turkmenistan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Turkmenistan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Turkmenistan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Turkmenistan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Turks and Caicos Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Turks and Caicos Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Turks and Caicos Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'îles Turques-et-Caïques');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Tuvalu';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tuvalu');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tuvalu');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tuvalu');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'U.S. Virgin Islands';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'U.S. Virgin Islands');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'U.S. Virgin Islands');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Îles Vierges Américaines');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Uganda';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Uganda');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Uganda');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ouganda');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Ukraine';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Ukraine');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Ukraine');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ukraine');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'United Arab Emirates';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'United Arab Emirates');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'United Arab Emirates');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Emirats Arabes Unis');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'United Kingdom';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'United Kingdom');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'United Kingdom');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Royaume-Uni');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'United States';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'United States');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'United States');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'États-Unis');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Uruguay';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Uruguay');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Uruguay');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Uruguay');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Uzbekistan';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Uzbekistan');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Uzbekistan');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Uzbekistan');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Vanuatu';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Vanuatu');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Vanuatu');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Vanuatu');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Vatican';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Vatican');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Vatican');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Vatican');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Venezuela';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Venezuela');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Venezuela');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Vénézuéla');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Vietnam';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Vietnam');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Vietnam');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Vietnam');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Wallis and Futuna';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Wallis and Futuna');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Wallis and Futuna');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Wallis et Futuna');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Western Sahara';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Western Sahara');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Western Sahara');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Sahara Occidental');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Yemen';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Yemen');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Yemen');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Yémen');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Zambia';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Zambia');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Zambia');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Zambie');
	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "Name" = 'Zimbabwe';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Zimbabwe');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Zimbabwe');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Zimbabwé');
	-- Cities
	SELECT "ID" INTO _id FROM public."cl_Cities" WHERE "Name" = 'YDE';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Yaounde');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Yaounde');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Yaoundé');
	SELECT "ID" INTO _id FROM public."cl_Cities" WHERE "Name" = 'DLA';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Douala');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Douala');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Douala');
END $BODY$;

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
