/* Administration app */

/* Parameters */

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
			_s := SUBSTRING(_v, CAST(FLOOR(11 + (random() * 52)) AS integer), 1);
		ELSE
			_s := CONCAT(_s, SUBSTRING(_v, CAST(FLOOR(1 + (random() * 62)) AS integer), 1));
		END IF;
	END LOOP;
	--
	RETURN _s;
END;
$BODY$;

-- EXTENSION: PGCrypto
CREATE EXTENSION IF NOT EXISTS pgcrypto;

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
	IF _dte > _endte THEN
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
	IF _dte > _endte OR _dte < _activedte THEN
		RETURN FALSE;
	END IF;
	--
	RETURN TRUE;
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
			INSERT INTO public."cl_Parameters" ("ParamName", "ParamValue", "OwnerApp", "ParamLock", "Auditable") VALUES
			(DIGEST(_name, 'sha256'), DIGEST(_value, 'sha256'), _owner, _locked, CASE WHEN _locked = TRUE THEN FALSE ELSE _auditable END);
		ELSE
			INSERT INTO public."cl_Parameters" ("ParamName", "ParamUValue", "OwnerApp", "ParamLock", "Auditable") VALUES
			(DIGEST(_name, 'sha256'), _value, _owner, _locked, CASE WHEN _locked = TRUE THEN FALSE ELSE _auditable END);
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

-- PROCEDURE: public.p_DeleteParameter(character varying)

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