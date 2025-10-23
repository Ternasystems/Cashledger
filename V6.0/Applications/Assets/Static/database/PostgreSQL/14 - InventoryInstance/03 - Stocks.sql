/* Inventory app */

/* Stocks, StockRelations */

-- Table: public.cl_Stocks

DROP TABLE IF EXISTS public."cl_Stocks";
CREATE TABLE public."cl_Stocks"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ProductID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Products" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"WarehouseID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Warehouses" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PackagingID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Packagings" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StockDate" timestamp without time zone DEFAULT NOW(),
	"BatchNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"LastChecked" timestamp without time zone DEFAULT NOW(),
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"UnitCost" numeric(8,2) NOT NULL CHECK ("UnitCost" >= 0),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_Stock" UNIQUE ("ProductID", "WarehouseID", "PackagingID", "BatchNumber")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStock(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStock"(
	IN _productid character varying(50),
	IN _unitid character varying(50),
	IN _warehouseid character varying(50),
	IN _packagingid character varying(50),
	IN _batchnumber character varying(50),
	IN _quantity numeric(8,2),
	IN _unitcost numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NOW(), %L, NOW(), %L, %L, NULL, %L);', _tablename, _id, _productid, _unitid, _warehouseid, _packagingid, _batchnumber, _quantity,
	_unitcost, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'STK');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateStock(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateStock"(
	IN _id character varying(50),
	IN _warehouseid character varying(50),
	IN _packagingid character varying(50),
	IN _batchnumber character varying(50),
	IN _quantity numeric(8,2),
	IN _unitcost numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "WarehouseID" = %L, "PackagingID" = %L, "BatchNumber" = %L, "Quantity" = %L, "UnitCost" = %L, "LastChecked" = NOW(), "Description" = %L WHERE "ID" = %L;',
	_tablename, _warehouseid, _packagingid, _batchnumber, _quantity, _unitcost, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_UpdateQuantity(character varying, numeric)

CREATE OR REPLACE PROCEDURE public."p_UpdateQuantity"(
	IN _id character varying(50),
	IN _quantity numeric(8,2))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Quantity" = %L, "LastChecked" = NOW() WHERE "ID" = %L;', _tablename, _quantity, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteStock(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteStock"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Stock

CREATE OR REPLACE TRIGGER "Delete_Stock"
    BEFORE DELETE
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Stock

CREATE OR REPLACE TRIGGER "Insert_Stock"
    BEFORE INSERT
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Stock

CREATE OR REPLACE TRIGGER "Update_Stock"
    BEFORE UPDATE 
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_StockRelations

DROP TABLE IF EXISTS public."cl_StockRelations";
CREATE TABLE public."cl_StockRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"AttributeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ProductAttributes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Value" text COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_StockRelation" UNIQUE ("AttributeID", "StockID", "Value")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStockRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStockRelation"(
	IN _attributeid character varying(50),
	IN _stockid character varying(50),
	IN _value text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_StockRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _attributeid, _stockid, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SKR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteStockRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteStockRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_StockRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_StockRelation

CREATE OR REPLACE TRIGGER "Update_StockRelation"
	BEFORE UPDATE
	ON public."cl_StockRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_StockRelation

CREATE OR REPLACE TRIGGER "Insert_StockRelation"
    BEFORE INSERT
    ON public."cl_StockRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_StockRelation

CREATE OR REPLACE TRIGGER "Remove_StockRelation"
    BEFORE DELETE
    ON public."cl_StockRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();