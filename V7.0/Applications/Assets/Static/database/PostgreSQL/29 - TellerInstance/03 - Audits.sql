/* Teller app */

/* TellerReversals, TellerAudits */

-- Table: public.cl_TellerReversals

DROP TABLE IF EXISTS public."cl_TellerReversals";
CREATE TABLE public."cl_TellerReversals"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TransactionID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TellerTransactions" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Reference" text COLLATE pg_catalog."default" NOT NULL,
	"ReversedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ApprovedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.InsertTellerReversal

CREATE OR REPLACE PROCEDURE public."InsertTellerReversal"(
	IN _tellerid character varying(50),
	IN _transactionid character varying(50),
	IN _reference text,
	IN _approbator character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerReversals'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _transactionid, _reference, _tellerid, _approbator, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TVS');
END;
$BODY$;

-- Trigger: Delete_TellerReversal

CREATE OR REPLACE TRIGGER "Delete_TellerReversal"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerReversals"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerReversal

CREATE OR REPLACE TRIGGER "Insert_TellerReversal"
    BEFORE INSERT 
    ON public."cl_TellerReversals"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_TellerAudits

DROP TABLE IF EXISTS public."cl_TellerAudits";
CREATE TABLE public."cl_TellerAudits"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"TableName" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"RecordID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "ActionDate" timestamp without time zone DEFAULT NOW(),
	"Description" jsonb NOT NULL
)

TABLESPACE pg_default;

-- FUNCTION: public.t_TellerLog()

CREATE OR REPLACE FUNCTION public."t_TellerLog"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF TG_OP = 'INSERT' THEN
		CALL public."p_InsertTellerAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), NEW."ID"::character varying(50), row_to_json(NEW)::jsonb);
	ELSIF TG_OP = 'UPDATE' THEN
		CALL public."p_InsertTellerAudit"(CASE WHEN NEW."IsActive" IS NULL THEN TG_OP::character varying(50) ELSE 'DEACTIVATE' END, TG_TABLE_NAME::character varying(50),
		OLD."ID"::character varying(50), json_build_object('before: ', row_to_json(OLD), ' after: ', row_to_json(NEW))::jsonb);
	ELSIF TG_OP = 'DELETE' THEN
		CALL public."p_InsertTellerAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), OLD."ID"::character varying(50), row_to_json(OLD)::jsonb);
	END IF;
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- PROCEDURE: public.p_InsertTellerAudit(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerAudit"(
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
	
	-- Insert into the TellerAudits table
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('TAU', 'cl_TellerAudits');
		-- Insert data to cl_TellerAudits table
		INSERT INTO public."cl_TellerAudits" VALUES (_id, _action, _tablename, _recordid, NOW(), _description);
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

-- Trigger: Delete_TellerAudit

CREATE OR REPLACE TRIGGER "Delete_TellerAudit"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerAudits"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerAudit

CREATE OR REPLACE TRIGGER "Insert_TellerAudit"
    BEFORE INSERT 
    ON public."cl_TellerAudits"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();