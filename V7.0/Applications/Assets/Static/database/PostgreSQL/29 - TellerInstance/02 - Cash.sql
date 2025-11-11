/* Teller app */

/* CashFigures, TellerCashCounts, CashRelations */

-- Table: public.cl_CashFigures

DROP TABLE IF EXISTS public."cl_CashFigures";
CREATE TABLE public."cl_CashFigures"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Value" numeric(8,2) NOT NULL,
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCashFigure(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCashFigure"(
	IN _name character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CashFigures'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CFG');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCashFigure(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCashFigure"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CashFigures';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Value" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _value, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCashFigure(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCashFigure"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CashFigures';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_CashFigure

CREATE OR REPLACE TRIGGER "Delete_CashFigure"
    BEFORE DELETE
    ON public."cl_CashFigures"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_CashFigure

CREATE OR REPLACE TRIGGER "Insert_CashFigure"
    BEFORE INSERT 
    ON public."cl_CashFigures"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_CashFigure

CREATE OR REPLACE TRIGGER "Update_CashFigure"
    BEFORE UPDATE 
    ON public."cl_CashFigures"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_TellerCashCounts

DROP TABLE IF EXISTS public."cl_TellerCashCounts";
CREATE TABLE public."cl_TellerCashCounts"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TellerID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"CountDate" timestamp without time zone NOT NULL,
	"Amount" numeric(8,2) NOT NULL CHECK ("Amount" >= 0),
	"ApprovedBy" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Tellers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTellerCashCount(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTellerCashCount"(
	IN _tellerid character varying(50),
	IN _countdate timestamp without time zone DEFAULT NOW(),
	IN _amount numeric(8,2),
	IN _approbator character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TellerCashCounts'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), %L, %L, NULL, %L);', _tablename, _id, _tellerid, _countdate, _amount, _approbator, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TCC');
END;
$BODY$;

-- Trigger: Delete_TellerCashCount

CREATE OR REPLACE TRIGGER "Delete_TellerCashCount"
    BEFORE DELETE OR UPDATE
    ON public."cl_TellerCashCounts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TellerCashCount

CREATE OR REPLACE TRIGGER "Insert_TellerCashCount"
    BEFORE INSERT 
    ON public."cl_TellerCashCounts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_CashRelations

DROP TABLE IF EXISTS public."cl_CashRelations";
CREATE TABLE public."cl_CashRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"CountDate" timestamp without time zone NOT NULL,
	"CountID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TellerCashCounts" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"FigureID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_CashFigures" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"Amount" numeric(8,2) NOT NULL CHECK ("Amount" >= 0),
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_CashRelation" UNIQUE ("CountDate", "CountID", "FigureID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCashRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCashRelation"(
	IN _countid character varying(50),
	IN _figureid character varying(50),
	IN _quantity numeric(8,2),
	IN _amount numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CashRelations'; _id character varying(50) := '%s'; _figure numeric(8,2);
BEGIN
	--
	SELECT "Value" INTO _figure FROM public."cl_CashFigures" WHERE "ID" = _figureid;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, NOW(), %L, %L, %L, %L, NULL, %L);', _tablename, _id, _countid, _figureid, _quantity, (_quantity * _figure), _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CSR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCashRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCashRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CashRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_CashRelation

CREATE OR REPLACE TRIGGER "Update_CashRelation"
	BEFORE UPDATE
	ON public."cl_CashRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_CashRelation

CREATE OR REPLACE TRIGGER "Insert_CashRelation"
    BEFORE INSERT
    ON public."cl_CashRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_CashRelation

CREATE OR REPLACE TRIGGER "Remove_CashRelation"
    BEFORE DELETE
    ON public."cl_CashRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();