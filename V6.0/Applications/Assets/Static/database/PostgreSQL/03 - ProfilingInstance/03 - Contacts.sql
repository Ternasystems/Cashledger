/* Profiling app */

/* Contacts */

-- Table: public.cl_ContactTypes

CREATE TABLE IF NOT EXISTS public."cl_ContactTypes"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContactType(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContactType"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactTypes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateContactType(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateContactType"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactTypes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContactType(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteContactType"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactTypes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_ContactType

CREATE OR REPLACE TRIGGER "Delete_ContactType"
    BEFORE DELETE
    ON public."cl_ContactTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ContactType

CREATE OR REPLACE TRIGGER "Insert_ContactType"
    BEFORE INSERT
    ON public."cl_ContactTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ContactType

CREATE OR REPLACE TRIGGER "Update_ContactType"
    BEFORE UPDATE 
    ON public."cl_ContactTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Contacts

CREATE TABLE IF NOT EXISTS public."cl_Contacts"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "ContactTypeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ContactTypes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "ContactNo" integer NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_Contact" UNIQUE ("ProfileID", "ContactNo")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContact(character varying, character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContact"(
	IN _contacttypeid character varying(50),
	IN _profileid character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Contacts'; _id character varying(50) := '%s'; _no integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("ContactNo"), 0) + 1 FROM public.%I', _tablename) INTO _no;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _contacttypeid, _profileid, _no, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTC');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateContact(character varying, character varying, character varying, character varying, integer, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateContact"(
	IN _id character varying(50),
	IN _contacttype character varying(50),
	IN _profileid character varying(50),
	IN _name character varying(50),
	IN _contactno integer DEFAULT 0,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Contacts'; _no integer; _tempno integer;
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "ContactTypeID" = %L, "ProfileID" = %L, "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _contacttype, _profileid, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	-- Check contact no
	EXECUTE FORMAT('SELECT "ContactNo" FROM public.%I WHERE "ID" = %L;', _tablename, _id) INTO _no;
	IF _contactno != 0 AND _contactno != _no THEN
		-- Use a temporary value to swap ContactNo
        _tempno := -1;  -- Assuming -1 is not a valid ContactNo
        
        -- First, set the ContactNo to a temporary value
        _sql := FORMAT('UPDATE public.%I SET "ContactNo" = %L WHERE "ID" = %L;', _tablename, _tempno, _id);
        CALL public."p_Query"(_sql);
        
        -- Then, update the other record
        _sql := FORMAT('UPDATE public.%I SET "ContactNo" = %L WHERE "ContactNo" = %L;', _tablename, _no, _contactno);
        CALL public."p_Query"(_sql);
        
        -- Finally, set the ContactNo to the desired value
        _sql := FORMAT('UPDATE public.%I SET "ContactNo" = %L WHERE "ID" = %L;', _tablename, _contactno, _id);
        CALL public."p_Query"(_sql);
	END IF;
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContact(character varying, boolean)

CREATE OR REPLACE PROCEDURE public."p_DeleteContact"(IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Contacts';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger Delete_Contact
CREATE OR REPLACE TRIGGER "Delete_Contact"
	BEFORE DELETE
	ON public."cl_Contacts"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Contact

CREATE OR REPLACE TRIGGER "Insert_Contact"
    BEFORE INSERT 
    ON public."cl_Contacts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Contact

CREATE OR REPLACE TRIGGER "Update_Contact"
    BEFORE UPDATE 
    ON public."cl_Contacts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_ContactRelations

CREATE TABLE IF NOT EXISTS public."cl_ContactRelations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"LangID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Languages" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "ContactID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Contacts" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Contact" text COLLATE pg_catalog."default" NOT NULL,
    "Photo" text COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" character varying(50) COLLATE pg_catalog."default",
    CONSTRAINT "UQ_ContactRelation" UNIQUE ("LangID", "ContactID", "Contact")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContactRelation(character varying, character varying, text, text, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContactRelation"(
	IN _langid character varying(50),
	IN _contactid character varying(50),
	IN _contact text,
	IN _photo text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _langid, _contactid, _contact, _photo, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContactRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteContactRelation"(
	IN _id character varying(50),
	IN _deactivate boolean DEFAULT true)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactRelations';
BEGIN
	-- Format sql
	IF _deactivate = TRUE THEN
		_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	ELSE
		_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L;', _tablename, _id);
	END IF;
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Update_ContactRelation

CREATE OR REPLACE TRIGGER "Update_ContactRelation"
	BEFORE UPDATE
	ON public."cl_ContactRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ContactRelation

CREATE OR REPLACE TRIGGER "Insert_ContactRelation"
    BEFORE INSERT
    ON public."cl_ContactRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_ContactRelation

CREATE OR REPLACE TRIGGER "Remove_ContactRelation"
    BEFORE DELETE 
    ON public."cl_ContactRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();