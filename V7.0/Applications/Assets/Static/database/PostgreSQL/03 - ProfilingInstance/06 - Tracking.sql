/* Profiling app */

/* Tracking */

-- Table: public.cl_Trackings

DROP TABLE IF EXISTS public."cl_Trackings";
CREATE TABLE public."cl_Trackings"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "CredentialID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Credentials" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IP" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "ActionDate" timestamp without time zone NOT NULL
)

TABLESPACE pg_default;

-- FUNCTION: public.t_CreditLog()

CREATE OR REPLACE FUNCTION public."t_CreditLog"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF NEW."Action" = 'LOGIN_ATTEMPT' THEN CALL public."p_InsertTracking"(NEW."ID", NEW."LoginStatus", NEW."IP"); END IF;
	RETURN NEW;
END;
$BODY$;

-- PROCEDURE: public.p_InsertTracking(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTracking"(
	IN _credentialid character varying(50),
	IN _action character varying(50),
	IN _IP character varying(50))
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
	
	-- Insert into the trackings table
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('TRK', 'cl_Trackings');
		-- Insert data to cl_Trakings table
		INSERT INTO public."cl_Trackings" VALUES (_id, _credentialid, _action, _IP, NOW());
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

-- Trigger: Delete_Tracking

CREATE OR REPLACE TRIGGER "Delete_Tracking"
    BEFORE DELETE OR UPDATE
    ON public."cl_Trackings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Tracking

CREATE OR REPLACE TRIGGER "Insert_Tracking"
    BEFORE INSERT 
    ON public."cl_Trackings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();