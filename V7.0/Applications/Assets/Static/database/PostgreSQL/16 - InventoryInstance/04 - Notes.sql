/* Inventory app */

/* Notes (2) */

/* InventNotes, InventRelations, TransferNotes, TransferRelations */

-- Table: public.cl_InventNotes

DROP TABLE IF EXISTS public."cl_InventNotes";
CREATE TABLE public."cl_InventNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"InventNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"InventDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInventNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInventNote"(
	IN _inventnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _inventdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L) ON CONFLICT ("InventNumber") DO NOTHING;', _tablename, _id, _inventnumber, _reference, _inventdate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'IVN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateInventNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateInventNote"(
	IN _id character varying(50),
	IN _inventnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _inventdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "InventNumber" = %L, "Reference" = %L, "InventDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _inventnumber, _reference, _inventdate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInventNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInventNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_InventNote

CREATE OR REPLACE TRIGGER "Delete_InventNote"
    BEFORE DELETE
    ON public."cl_InventNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_InventNote

CREATE OR REPLACE TRIGGER "Insert_InventNote"
    BEFORE INSERT
    ON public."cl_InventNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_InventNote

CREATE OR REPLACE TRIGGER "Update_InventNote"
    BEFORE UPDATE 
    ON public."cl_InventNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_InventRelations

DROP TABLE IF EXISTS public."cl_InventRelations";
CREATE TABLE public."cl_InventRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"InventID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_InventNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_InventRelation" UNIQUE ("InventID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInventRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInventRelation"(
	IN _stockid character varying(50),
	IN _inventid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("InventID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _inventid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'IVR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInventRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInventRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_InventRelation

CREATE OR REPLACE TRIGGER "Update_InventRelation"
	BEFORE UPDATE
	ON public."cl_InventRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_InventRelation

CREATE OR REPLACE TRIGGER "Insert_InventRelation"
    BEFORE INSERT
    ON public."cl_InventRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_InventRelation

CREATE OR REPLACE TRIGGER "Remove_InventRelation"
    BEFORE DELETE
    ON public."cl_InventRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_TransferNotes

DROP TABLE IF EXISTS public."cl_TransferNotes";
CREATE TABLE public."cl_TransferNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TransferNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"TransferDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTransferNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTransferNote"(
	IN _transfernumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _transferdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TransferNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L) ON CONFLICT ("TransferNumber") DO NOTHING;', _tablename, _id, _transfernumber, _reference, _transferdate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TRN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTransferNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTransferNote"(
	IN _id character varying(50),
	IN _transfernumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _transferdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TransferNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "TransferNumber" = %L, "Reference" = %L, "TransferDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _transfernumber, _reference, _transferdate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTransferNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTransferNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TransferNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_TransferNote

CREATE OR REPLACE TRIGGER "Delete_TransferNote"
    BEFORE DELETE
    ON public."cl_TransferNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TransferNote

CREATE OR REPLACE TRIGGER "Insert_TransferNote"
    BEFORE INSERT
    ON public."cl_TransferNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_TransferNote

CREATE OR REPLACE TRIGGER "Update_TransferNote"
    BEFORE UPDATE 
    ON public."cl_TransferNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_TransferRelations

DROP TABLE IF EXISTS public."cl_TransferRelations";
CREATE TABLE public."cl_TransferRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TransferID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TransferNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_TransferRelation" UNIQUE ("TransferID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTransferRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTransferRelation"(
	IN _stockid character varying(50),
	IN _transferid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TransferRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("TransferID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _transferid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TRR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTransferRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTransferRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TransferRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_TransferRelation

CREATE OR REPLACE TRIGGER "Update_TransferRelation"
	BEFORE UPDATE
	ON public."cl_TransferRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TransferRelation

CREATE OR REPLACE TRIGGER "Insert_TransferRelation"
    BEFORE INSERT
    ON public."cl_TransferRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_TransferRelation

CREATE OR REPLACE TRIGGER "Remove_TransferRelation"
    BEFORE DELETE
    ON public."cl_TransferRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();