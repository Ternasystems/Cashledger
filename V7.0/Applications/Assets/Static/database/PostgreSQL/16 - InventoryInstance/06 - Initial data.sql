/* Inventory app */

/* Initial data */

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';

	-- App registry

	CALL public."p_InsertApp"('Inventory');
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Inventory';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Inventory');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Inventory');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gestion des stocks');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Inventario');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'إدارة المخزون');

	-- Insert References
	
	CALL public."p_InsertReferenceTable"('cl_Customers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Customers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Suppliers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Suppliers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_ProductCategories');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_ProductCategories';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Warehouses');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Warehouses';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Manufacturers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Manufacturers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Units');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Units';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Packagings');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Packagings';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Products');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Products';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_ProductAttributes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_ProductAttributes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Stocks');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Stocks';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_DeliveryNotes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_DeliveryNotes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_DispatchNotes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_DispatchNotes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_ReturnNotes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_ReturnNotes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_WasteNotes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_WasteNotes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_InventNotes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_InventNotes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_TransferNotes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_TransferNotes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Inventories');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Inventories';
	CALL public."p_InsertReferenceRelation"(_appid, _id);

	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Ventas');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'المبيعات');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Purchases') THEN
		CALL public."p_InsertAppCategory"('Purchases');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Purchases';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Compras');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'المشتريات');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Inventory';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
	CALL public."p_InsertAppRelation"(_id, _appid);

	-- Insert Warehouse

	CALL public."p_InsertWarehouse"('Non applicable', 'Non applicable');
	SELECT "ID" INTO _id from public."cl_Warehouses" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	--
	CALL public."p_InsertWarehouse"('Anonymous warehouse', 'Anonymous location');
	SELECT "ID" INTO _id from public."cl_Warehouses" WHERE "Name" = 'nonymous warehouse';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Anonymous warehouse', 'Anonymous location');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Anonymous warehouse', 'Anonymous location');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Magasin anonyme', 'Localisation indéterminée');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Almacén anónimo', 'Ubicación anónima');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'مستودع مجهول', 'موقع مجهول');

	-- Insert Manufacturer

	CALL public."p_InsertManufacturer"('Non applicable');
	SELECT "ID" INTO _id from public."cl_Manufacturers" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	--
	CALL public."p_InsertManufacturer"('Anonymous manufacturer');
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Anonymous manufacturer');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Anonymous manufacturer');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Fabricant anonyme');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Fabricante anónimo');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'شركة تصنيع مجهولة');

	-- Insert Unit

	CALL public."p_InsertUnit"('Non applicable');
	SELECT "ID" INTO _id from public."cl_Units" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');

	-- Insert Packaging

	CALL public."p_InsertPackaging"('Non applicable');
	SELECT "ID" INTO _id from public."cl_Packagings" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'No aplica');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'غير مطبق');
	
END $BODY$;

-- Trigger: Check_ProductAttribute

CREATE OR REPLACE TRIGGER "Check_ProductAttribute"
	BEFORE INSERT OR UPDATE
	ON public."cl_AttributeRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CheckAttribute"();

-- Trigger: Check_StockAttribute

CREATE OR REPLACE TRIGGER "Check_StockAttribute"
	BEFORE INSERT OR UPDATE
	ON public."cl_StockRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CheckAttribute"();

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_ProductCategories', 'cl_Warehouses', 'cl_Manufacturers', 'cl_Units', 'cl_Packagings', 'cl_Products', 'cl_ProductAttributes',
	'cl_AttributeRelations', 'cl_Stocks', 'cl_StockRelations', 'cl_DeliveryNotes', 'cl_DeliveryRelations', 'cl_DispatchNotes', 'cl_DispatchRelations', 'cl_ReturnNotes', 'cl_ReturnRelations', 'cl_WasteNotes',
	'cl_WasteRelations', 'cl_InventNotes', 'cl_InventRelations', 'cl_TransferNotes', 'cl_TransferRelations', 'cl_Inventories', 'cl_InventoryRelations'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername :=
			CASE
				WHEN _tablename = 'cl_ProductCategories' THEN 'ProductCategory'
				WHEN _tablename = 'cl_Inventories' THEN 'Inventory'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
		_triggername := 'Log_' || _triggername;
		IF EXISTS(SELECT 1 FROM information_schema.triggers WHERE trigger_schema = 'public' AND trigger_name = _triggername) THEN CONTINUE; END IF;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_LogAudit"();
		', _triggername, _tablename);
		--
		_triggername :=
			CASE
				WHEN _tablename = 'cl_ProductCategories' THEN 'ProductCategory'
				WHEN _tablename = 'cl_Inventories' THEN 'Inventory'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
			_triggername := 'Release_' || _triggername;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_ReleaseThread"();
		', _triggername, _tablename);
	END LOOP;
END $BODY$;