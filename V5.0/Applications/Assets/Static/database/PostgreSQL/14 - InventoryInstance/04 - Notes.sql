/* Inventory app */

/* Notes (1) */

-- Table: public.cl_DeliveryNotes

CREATE TABLE IF NOT EXISTS public."cl_DeliveryNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"DeliveryNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"DeliveryDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDeliveryNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDeliveryNote"(
	IN _deliverynumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _deliverydate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _deliverynumber, _reference, _deliverydate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'DLN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateDeliveryNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateDeliveryNote"(
	IN _id character varying(50),
	IN _deliverynumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _deliverydate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "DeliveryNumber" = %L, "Reference" = %L, "DeliveryDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _deliverynumber, _reference, _deliverydate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDeliveryNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDeliveryNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_DeliveryNote

CREATE OR REPLACE TRIGGER "Delete_DeliveryNote"
    BEFORE DELETE
    ON public."cl_DeliveryNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DeliveryNote

CREATE OR REPLACE TRIGGER "Insert_DeliveryNote"
    BEFORE INSERT
    ON public."cl_DeliveryNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_DeliveryNote

CREATE OR REPLACE TRIGGER "Update_DeliveryNote"
    BEFORE UPDATE 
    ON public."cl_DeliveryNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_DeliveryRelations

CREATE TABLE IF NOT EXISTS public."cl_DeliveryRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"DeliveryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_DeliveryNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_DeliveryRelation" UNIQUE ("DeliveryID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDeliveryRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDeliveryRelation"(
	IN _stockid character varying(50),
	IN _deliveryid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("DeliveryID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _deliveryid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SKR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDeliveryRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDeliveryRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_DeliveryRelation

CREATE OR REPLACE TRIGGER "Update_DeliveryRelation"
	BEFORE UPDATE
	ON public."cl_DeliveryRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DeliveryRelation

CREATE OR REPLACE TRIGGER "Insert_DeliveryRelation"
    BEFORE INSERT
    ON public."cl_DeliveryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_DeliveryRelation

CREATE OR REPLACE TRIGGER "Remove_DeliveryRelation"
    BEFORE DELETE
    ON public."cl_DeliveryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_DispatchNotes

CREATE TABLE IF NOT EXISTS public."cl_DispatchNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"DispatchNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"DispatchDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDispatchNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDispatchNote"(
	IN _dispatchnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _dispatchdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _dispatchnumber, _reference, _dispatchdate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'DPN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateDispatchNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateDispatchNote"(
	IN _id character varying(50),
	IN _dispatchnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _dispatchdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "DispatchNumber" = %L, "Reference" = %L, "DispatchDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _dispatchnumber, _reference, _dispatchdate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDispatchNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDispatchNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_DispatchNote

CREATE OR REPLACE TRIGGER "Delete_DispatchNote"
    BEFORE DELETE
    ON public."cl_DispatchNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DispatchNote

CREATE OR REPLACE TRIGGER "Insert_DispatchNote"
    BEFORE INSERT
    ON public."cl_DispatchNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_DispatchNote

CREATE OR REPLACE TRIGGER "Update_DispatchNote"
    BEFORE UPDATE 
    ON public."cl_DispatchNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_DispatchRelations

CREATE TABLE IF NOT EXISTS public."cl_DispatchRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"DispatchID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_DispatchNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_DispatchRelation" UNIQUE ("DispatchID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDispatchRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDispatchRelation"(
	IN _stockid character varying(50),
	IN _dispatchid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("DispatchID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _dispatchid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SKR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDispatchRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDispatchRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_DispatchRelation

CREATE OR REPLACE TRIGGER "Update_DispatchRelation"
	BEFORE UPDATE
	ON public."cl_DispatchRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DispatchRelation

CREATE OR REPLACE TRIGGER "Insert_DispatchRelation"
    BEFORE INSERT
    ON public."cl_DispatchRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_DispatchRelation

CREATE OR REPLACE TRIGGER "Remove_DispatchRelation"
    BEFORE DELETE
    ON public."cl_DispatchRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_ReturnNotes

CREATE TABLE IF NOT EXISTS public."cl_ReturnNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ReturnNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"ReturnDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertReturnNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertReturnNote"(
	IN _returnnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _returndate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReturnNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _returnnumber, _reference, _returndate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'RTN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateReturnNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateReturnNote"(
	IN _id character varying(50),
	IN _returnnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _returndate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReturnNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "ReturnNumber" = %L, "Reference" = %L, "ReturnDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _returnnumber, _reference, _returndate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteReturnNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteReturnNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReturnNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_ReturnNote

CREATE OR REPLACE TRIGGER "Delete_ReturnNote"
    BEFORE DELETE
    ON public."cl_ReturnNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ReturnNote

CREATE OR REPLACE TRIGGER "Insert_ReturnNote"
    BEFORE INSERT
    ON public."cl_ReturnNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ReturnNote

CREATE OR REPLACE TRIGGER "Update_ReturnNote"
    BEFORE UPDATE 
    ON public."cl_ReturnNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_ReturnRelations

CREATE TABLE IF NOT EXISTS public."cl_ReturnRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReturnID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ReturnNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_ReturnRelation" UNIQUE ("ReturnID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertReturnRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertReturnRelation"(
	IN _stockid character varying(50),
	IN _returnid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReturnRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("ReturnID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _returnid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'RTR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteReturnRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteReturnRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReturnRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_ReturnRelation

CREATE OR REPLACE TRIGGER "Update_ReturnRelation"
	BEFORE UPDATE
	ON public."cl_ReturnRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ReturnRelation

CREATE OR REPLACE TRIGGER "Insert_ReturnRelation"
    BEFORE INSERT
    ON public."cl_ReturnRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_ReturnRelation

CREATE OR REPLACE TRIGGER "Remove_ReturnRelation"
    BEFORE DELETE
    ON public."cl_ReturnRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_WasteNotes

CREATE TABLE IF NOT EXISTS public."cl_WasteNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"WasteNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"WasteDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertWasteNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertWasteNote"(
	IN _inventnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _inventdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_WasteNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _inventnumber, _reference, _inventdate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'WSN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateWasteNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateWasteNote"(
	IN _id character varying(50),
	IN _inventnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _inventdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_WasteNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "WasteNumber" = %L, "Reference" = %L, "WasteDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _inventnumber, _reference, _inventdate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteWasteNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteWasteNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_WasteNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_WasteNote

CREATE OR REPLACE TRIGGER "Delete_WasteNote"
    BEFORE DELETE
    ON public."cl_WasteNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_WasteNote

CREATE OR REPLACE TRIGGER "Insert_WasteNote"
    BEFORE INSERT
    ON public."cl_WasteNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_WasteNote

CREATE OR REPLACE TRIGGER "Update_WasteNote"
    BEFORE UPDATE 
    ON public."cl_WasteNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_WasteRelations

CREATE TABLE IF NOT EXISTS public."cl_WasteRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"WasteID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_WasteNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_WasteRelation" UNIQUE ("WasteID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertWasteRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertWasteRelation"(
	IN _stockid character varying(50),
	IN _inventid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_WasteRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("WasteID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _inventid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'WSR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteWasteRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteWasteRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_WasteRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_WasteRelation

CREATE OR REPLACE TRIGGER "Update_WasteRelation"
	BEFORE UPDATE
	ON public."cl_WasteRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_WasteRelation

CREATE OR REPLACE TRIGGER "Insert_WasteRelation"
    BEFORE INSERT
    ON public."cl_WasteRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_WasteRelation

CREATE OR REPLACE TRIGGER "Remove_WasteRelation"
    BEFORE DELETE
    ON public."cl_WasteRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();