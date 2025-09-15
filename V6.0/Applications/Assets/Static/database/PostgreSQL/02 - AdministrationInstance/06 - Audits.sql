/* Administration app */

/* Audits (1) */

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