/* Inventory app */

/* Inventories, Inventoryrelations */

-- FUNCTION: public."f_CheckInventory"(character varying);

CREATE OR REPLACE FUNCTION public."f_CheckInventory"(_noteid character varying(50), _partnerid character varying(50))
	RETURNS boolean
	LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM public."cl_Suppliers" WHERE "ID" = _partnerid) AND NOT EXISTS (SELECT 1 FROM public."cl_Customers" WHERE "ID" = _partnerid)
		AND NOT EXISTS (SELECT 1 FROM public."cl_Credentials" WHERE "ID" = _partnerid) THEN
		RETURN FALSE;
	END IF;
	--
	IF NOT EXISTS (SELECT 1 FROM public."cl_DeliveryNotes" WHERE "ID" = _noteid) AND NOT EXISTS (SELECT 1 FROM public."cl_DispatchNotes" WHERE "ID" = _noteid)
		AND NOT EXISTS (SELECT 1 FROM public."cl_ReturnNotes" WHERE "ID" = _noteid) AND NOT EXISTS (SELECT 1 FROM public."cl_WasteNotes" WHERE "ID" = _noteid)
		AND NOT EXISTS (SELECT 1 FROM public."cl_InventNotes" WHERE "ID" = _noteid) AND NOT EXISTS (SELECT 1 FROM public."cl_TransferNotes" WHERE "ID" = _noteid) THEN
		RETURN FALSE;
	END IF;
	RETURN TRUE;
END;
$BODY$;

-- Table: public.cl_Inventories

DROP TABLE IF EXISTS public."cl_Inventories";
CREATE TABLE public."cl_Inventories"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"NoteID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PartnerID" character varying(50) COLLATE pg_catalog."default" NOT NULL, -- Customers, Suppliers, Credentials
	"InventoryType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("InventoryType" IN ('IN', 'OUT', 'RETURN', 'WASTE', 'INVENT', 'TRANSFER')),
	"Quantity" numeric(8,2) NOT NULL,
	"InventDate" timestamp without time zone DEFAULT NOW(),
	"UnitCost" numeric(8,2) NOT NULL CHECK ("UnitCost" >= 0),
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "CT_Inventory" CHECK (public."f_CheckInventory"("NoteID", "PartnerID"))
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInventory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInventory"(
	IN _noteid character varying(50),
	IN _stockid character varying(50),
	IN _unitid character varying(50),
	IN _partnerid character varying(50),
	IN _inventorytype character varying(50),
	IN _quantity numeric(8,2),
	IN _unitcost numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Inventories'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, NOW(), %L, NULL, %L);', _tablename, _id, _noteid, _stockid, _unitid, _partnerid, _inventorytype, _quantity, _unitcost, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'IVT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateInventory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateInventory"(
	IN _id character varying(50),
	IN _quantity numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Inventories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Quantity" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _quantity, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInventory(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInventory"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Inventories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Inventory

CREATE OR REPLACE TRIGGER "Delete_Inventory"
    BEFORE DELETE
    ON public."cl_Inventories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Inventory

CREATE OR REPLACE TRIGGER "Insert_Inventory"
    BEFORE INSERT 
    ON public."cl_Inventories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Inventory

CREATE OR REPLACE TRIGGER "Update_Inventory"
    BEFORE UPDATE 
    ON public."cl_Inventories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_InventoryRelations

DROP TABLE IF EXISTS public."cl_InventoryRelations";
CREATE TABLE public."cl_InventoryRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"InventID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Inventories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"CredentialID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Credentials" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_InventoryRelation" UNIQUE ("InventID", "CredentialID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInventoryRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInventoryRelation"(
	IN _inventid character varying(50),
	IN _credentialid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventoryRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("InventID", "CredentialID") DO NOTHING;', _tablename, _id, _inventid, _credentialid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'IVR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInventoryRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInventoryRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventoryRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_InventoryRelation

CREATE OR REPLACE TRIGGER "Update_InventoryRelation"
	BEFORE UPDATE
	ON public."cl_InventoryRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_InventoryRelation

CREATE OR REPLACE TRIGGER "Insert_InventoryRelation"
    BEFORE INSERT
    ON public."cl_InventoryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_InventoryRelation

CREATE OR REPLACE TRIGGER "Insert_InventoryRelation"
    BEFORE DELETE
    ON public."cl_InventoryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();