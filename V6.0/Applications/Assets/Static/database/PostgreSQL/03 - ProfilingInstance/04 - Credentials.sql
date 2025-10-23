/* Profiling app */

/* Credentials */

-- Table: public.cl_Credentials

DROP TABLE IF EXISTS public."cl_Credentials";
CREATE TABLE public."cl_Credentials"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "UserName" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL CHECK ("UserName" ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'),
    "UserPassword" bytea NOT NULL,
    "StartDate" timestamp without time zone NOT NULL,
    "EndDate" timestamp without time zone,
    "SessionID" text COLLATE pg_catalog."default",
    "ConnectionStatus" boolean NOT NULL,
	"LoginStatus" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"CurrentThread" boolean NOT NULL,
	"Threads" integer NOT NULL CHECK ("Threads" >= 0),
	"IP" character varying(50) COLLATE pg_catalog."default",
	"Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- FUNCTION: public."t_LogCredential"()

CREATE OR REPLACE FUNCTION public."t_LogCredential"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF TG_OP = 'INSERT' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), NEW."ID"::character varying(50), row_to_json(NEW)::jsonb);		
	ELSIF TG_OP = 'UPDATE' THEN
		CALL public."p_InsertAudit"(
			CASE
				WHEN NEW."EndDate" IS NOT NULL THEN 'DISABLED'
				WHEN NEW."IsActive" IS NOT NULL THEN 'DEACTIVATE'
				ELSE TG_OP::character varying(50)
			END,
			TG_TABLE_NAME::character varying(50),
			OLD."ID"::character varying(50),
			json_build_object('before: ', row_to_json(OLD), ' after: ', row_to_json(NEW))::jsonb
		);
	ELSIF TG_OP = 'DELETE' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), OLD."ID"::character varying(50), row_to_json(OLD)::jsonb);
	END IF;
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- FUNCTION: public.t_ReleaseThread()

CREATE OR REPLACE FUNCTION public."t_ReleaseThread"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE _threads integer; _threaded boolean; _processed boolean;
BEGIN
	IF TG_TABLE_NAME = 'cl_Credentials' THEN RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END; END IF;
	--
	IF TG_TABLE_NAME = 'cl_Parameters' THEN
		IF public."f_Auditable"(CASE WHEN TG_OP = 'INSERT' THEN NEW."ID" ELSE OLD."ID" END) = FALSE THEN
			RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
		END IF;
	END IF;
	--
	SELECT "Threads" INTO _threads FROM public."cl_Credentials" WHERE "CurrentThread" = TRUE;
	_threads := _threads - 1;
	
	-- Set CurrentThread parameter
	SELECT public."f_CurrentThread"() INTO _threaded;
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(TRUE); END IF;
	
	-- Set IsProc parameter
	SELECT public."f_IsProc"() INTO _processed;
    IF _processed = FALSE THEN CALL public."p_IsProc"(TRUE); END IF;

	-- Update the credentials table
	BEGIN
		IF _threads = 0 THEN
			UPDATE public."cl_Credentials" SET "Threads" = _threads, "CurrentThread" = FALSE, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
		ELSE
			UPDATE public."cl_Credentials" SET "Threads" = _threads, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
		END IF;
		--
		RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
	END;
	-- Reset IsProc parameter
    IF _processed = FALSE THEN CALL public."p_IsProc"(FALSE); END IF;
	
	-- Reset CurrentThread parameter
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(FALSE); END IF;
	--
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- FUNCTION: public.f_CheckLoginStatus(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckLoginStatus"(_id character varying(50))
	RETURNS character varying
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _status character varying(50);
BEGIN
	SELECT "LoginStatus" INTO _status FROM public."cl_Credentials" WHERE "ID" = _id;
	RETURN _status;
END;
$BODY$;

-- PROCEDURE: public.p_LoginStatus(character varying, character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_LoginStatus"(
	IN _id character varying(50),
	IN _status character varying(50),
	IN _ip character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "LoginStatus" = %L, "IP" = %L, "Action" = ''LOGIN_ATTEMPT'' WHERE "ID" = %L;', _tablename, _status, _ip, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- FUNCTION: public.f_CheckCurrentThread(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckCurrentThread"()
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "CurrentThread" = TRUE) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- PROCEDURE: public.p_CurrentThread(character varying, boolean)

CREATE OR REPLACE PROCEDURE public."p_SetCurrentThread"(OUT _isThreaded boolean, IN _id character varying(50), IN _threads integer)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials'; _threaded boolean;
BEGIN
	LOOP
		SELECT public."f_CheckCurrentThread"() INTO _threaded;
		EXIT WHEN _threaded = FALSE;
	END LOOP;
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "CurrentThread" = TRUE, "Threads" = %L, "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _threads, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	_isThreaded := public."f_CheckCurrentThread"();
END;
$BODY$;

-- FUNCTION: public.f_CheckConnectionStatus(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckConnectionStatus"(_id character varying(50))
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "ID" = _id AND "ConnectionStatus" = TRUE) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- PROCEDURE: public.p_ConnectionStatus(character varying, boolean, text)

CREATE OR REPLACE PROCEDURE public."p_ConnectionStatus"(
	IN _id character varying(50),
	IN _connected boolean,
	IN _sessionid text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('
		UPDATE public.%I SET "SessionID" = CASE WHEN %L THEN %L ELSE NULL END, "ConnectionStatus" = %L, "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _connected, _sessionid, _connected, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- FUNCTION: public.f_CheckCredential(character varying, character varying)

CREATE OR REPLACE FUNCTION public."f_CheckCredential"(
	_username character varying(50),
	_userpassword character varying(50),
	_ip character varying(50))
    RETURNS SETOF "cl_Credentials" 
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
    ROWS 1000
AS $BODY$
DECLARE _id character varying(50);
BEGIN
	-- Check successful
	RETURN QUERY SELECT * FROM public."cl_Credentials" WHERE "UserName" = _username AND "UserPassword" = DIGEST(_userpassword, 'sha256');
	
	-- Check failed
	IF NOT FOUND THEN
		IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "UserName" = _username) THEN
			SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "UserName" = _username;
		ELSE
			SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "UserName" = 'unkown@unkown.com';
		END IF;
		CALL public."p_LoginStatus"(_id, 'LOGIN_FAILED', _ip);
	END IF;
END;
$BODY$;

-- FUNCTION: public."p_ReleaseThread"()

CREATE OR REPLACE PROCEDURE public."p_ReleaseThread"()
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _threads integer;
BEGIN
	SELECT "Threads" INTO _threads FROM public."cl_Credentials" WHERE "CurrentThread" = TRUE;
	--
	_threads := _threads - 1;
	IF _threads = 0 THEN
		UPDATE public."cl_Credentials" SET "Threads" = _threads, "CurrentThread" = FALSE, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
	ELSE
		UPDATE public."cl_Credentials" SET "Threads" = _threads, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
	END IF;
END;
$BODY$;

-- PROCEDURE: public.p_InsertCredential(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCredential"(
	OUT _pwd character varying(50),
	IN _profileid character varying(50),
	IN _username character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50);
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);
	--
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('CRD', 'cl_Credentials');
		-- Set password
		_pwd := public."f_PwdGenerator"();
		-- Insert data to Credentials table
		INSERT INTO public."cl_Credentials" VALUES (_id, _profileid, _username, DIGEST(_pwd, 'sha256'), NOW(), NULL, NULL, FALSE, 'LOGOUT', FALSE, 0, NULL, 'INSERT', NULL, _description);
		--
		CALL public."p_ReleaseThread"();
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

-- PROCEDURE: public.p_UpdateCredential(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCredential"(
	IN _id character varying(50),
	IN _profileid character varying(50),
	IN _username character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "ProfileID" = %L, "UserName" = %L, "Action" = ''UPDATE'', "Description" = %L WHERE "ID" = %L;', _tablename, _profileid, _username, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePassword(character varying, character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_UpdatePassword"(
	IN _id character varying(50),
	IN _oldpwd character varying(50),
	IN _newpwd character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "ID" = _id AND "UserPassword" = DIGEST(_oldpwd, 'sha256')) AND _oldpwd != _newpwd THEN
		_sql := FORMAT('UPDATE public.%I SET "UserPassword" = DIGEST(%L, ''sha256''), "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _newpwd, _id);
	END IF;
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- PROCEDURE: public.p_ResetPassword(character varying)

CREATE OR REPLACE PROCEDURE public."p_ResetPassword"(
	OUT _pwd character varying(50),
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);
	--
	BEGIN
		-- Set password
		_pwd := public."f_PwdGenerator"();
		-- Update password from Credentials table
		UPDATE public."cl_Credentials" SET "UserPassword" = DIGEST(_pwd, 'sha256'), "Action" = 'UPDATE' WHERE "ID" = _id;
		--
		CALL public."p_ReleaseThread"();
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

-- PROCEDURE: public.p_DeleteCredential(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCredential"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW(), "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- PROCEDURE: public.p_DisableCredential(character varying)

CREATE OR REPLACE PROCEDURE public."p_DisableCredential"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "EndDate" = NOW(), "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- Trigger: Delete_Credential

CREATE OR REPLACE TRIGGER "Delete_Credential"
    BEFORE DELETE
    ON public."cl_Credentials"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Credential

CREATE OR REPLACE TRIGGER "Insert_Credential"
    BEFORE INSERT
    ON public."cl_Credentials"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Credential

CREATE OR REPLACE TRIGGER "Update_Credential"
    BEFORE UPDATE 
    ON public."cl_Credentials"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Insert References

CALL public."p_InsertReferenceTable"('cl_Credentials');