/* Invoicing app */

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

	CALL public."p_InsertApp"('Invoicing');
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Invoicing';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Invoicing');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Invoicing');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Facturation');
	CALL public."p_InsertLanguageRelation"(_es, _id, 'Facturación');
	CALL public."p_InsertLanguageRelation"(_ar, _id, 'الفواتير');

	-- Insert References

	CALL public."p_InsertReferenceTable"('cl_Customers');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Customers';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Taxes');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Taxes';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Discounts');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Discounts';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_ProductCategories');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_ProductCategories';
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
	CALL public."p_InsertReferenceTable"('cl_Stocks');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Stocks';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Currencies');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Currencies';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Prices');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Prices';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_PriceRelations');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_PriceRelations';
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
	CALL public."p_InsertReferenceTable"('cl_Discounts');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Discounts';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_InvoiceStatuses');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_InvoiceStatuses';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Invoices');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Invoices';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_PaymentMethods');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_PaymentMethods';
	CALL public."p_InsertReferenceRelation"(_appid, _id);
	--
	CALL public."p_InsertReferenceTable"('cl_Payments');
	SELECT "ID" INTO _appid FROM public."cl_ReferenceTables" WHERE "TableName" = 'cl_Payments';
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
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Invoicing';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);

	-- Profiles

	SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "ISO3" = 'CMR';
	SELECT "ID" INTO _appid FROM public."cl_Cities" WHERE "Name" = 'DLA';
	CALL public."p_InsertProfile"('Anonymous F', LOCALTIMESTAMP, _id, _appid);
	CALL public."p_InsertProfile"('Anonymous M', LOCALTIMESTAMP, _id, _appid);
	CALL public."p_InsertProfile"('Anonymous', LOCALTIMESTAMP, _id, _appid);

	-- Insert TitleRelations

	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertTitleRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertTitleRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertTitleRelation"(_id, _appid);

	-- Insert StatusRelations

	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertStatusRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertStatusRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertStatusRelation"(_id, _appid);

	-- Insert GenderRelations

	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 3;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertGenderRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 2;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertGenderRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertGenderRelation"(_id, _appid);

	-- Insert CivilityRelations

	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 3;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCivilityRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 2;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCivilityRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertCivilityRelation"(_id, _appid);

	-- Insert OccupationRelations

	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertOccupationRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertOccupationRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertOccupationRelation"(_id, _appid);

	-- Insert Customers

	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertCustomer"(_id, 'XXXXXXXXXX', 'XXXXXXXXXX');
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCustomer"(_id, 'XXXXXXXXXX', 'XXXXXXXXXX');
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCustomer"(_id, 'XXXXXXXXXX', 'XXXXXXXXXX');

	-- Insert PaymentMethods

	IF NOT EXISTS(SELECT 1 FROM public."cl_PaymentMethods" WHERE "Code" = 'CASH') THEN
		CALL public."p_InsertPaymentMethod"('Cash', 'CASH');
		SELECT "ID" INTO _id FROM public."cl_PaymentMethods" WHERE "Code" = 'CASH';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Cash');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cash');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Espèces');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Efectivo');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'نقدي');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_PaymentMethods" WHERE "Code" = 'CARD') THEN
		CALL public."p_InsertPaymentMethod"('Banking Card', 'CARD');
		SELECT "ID" INTO _id FROM public."cl_PaymentMethods" WHERE "Code" = 'CARD';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Banking Card');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Banking Card');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Carte Bancaire');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Tarjeta Bancaria');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'بطاقة بنكية');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_PaymentMethods" WHERE "Code" = 'MOBILE') THEN
		CALL public."p_InsertPaymentMethod"('Mobile Money', 'MOBILE');
		SELECT "ID" INTO _id FROM public."cl_PaymentMethods" WHERE "Code" = 'MOBILE';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Mobile Money');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mobile Money');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mobile Money');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Dinero Móvil');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'المال المحمول');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_PaymentMethods" WHERE "Code" = 'CHEQUE') THEN
		CALL public."p_InsertPaymentMethod"('Cheque', 'CHEQUE');
		SELECT "ID" INTO _id FROM public."cl_PaymentMethods" WHERE "Code" = 'CHEQUE';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Cheque');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Cheque');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Chèque');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Cheque');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'شيك');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_PaymentMethods" WHERE "Code" = 'VOUCHER') THEN
		CALL public."p_InsertPaymentMethod"('Voucher', 'VOUCHER');
		SELECT "ID" INTO _id FROM public."cl_PaymentMethods" WHERE "Code" = 'VOUCHER';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Voucher');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Voucher');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bon');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Vale');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'قسيمة');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_PaymentMethods" WHERE "Code" = 'CREDIT') THEN
		CALL public."p_InsertPaymentMethod"('Credit', 'CREDIT');
		SELECT "ID" INTO _id FROM public."cl_PaymentMethods" WHERE "Code" = 'CREDIT';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Credit');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Credit');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Crédit');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Crédito');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'ائتمان');
	END IF;

	-- Insert Currencies

	IF NOT EXISTS(SELECT 1 FROM public."cl_Currencies" WHERE "Code" = 'XAF') THEN
		CALL public."InsertCurrency"('XAF', 'CFAF', 'CFA Franc');
		SELECT "ID" INTO _id FROM public."cl_Currencies" WHERE "Code" = 'XAF';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'CFAF', 'CFA Franc');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'CFAF', 'CFA Franc');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'FCFA', 'Franc CFA');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'CFAF', 'Franco CFA');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'CFAF', 'فرنك سي إف إيه');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Currencies" WHERE "Code" = 'XOF') THEN
		CALL public."InsertCurrency"('XOF', 'CFAF', 'CFA Franc');
		SELECT "ID" INTO _id FROM public."cl_Currencies" WHERE "Code" = 'XOF';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'CFAF', 'CFA Franc');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'CFAF', 'CFA Franc');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'FCFA', 'Franc CFA');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'CFAF', 'Franco CFA');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'CFAF', 'فرنك سي إف إيه');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Currencies" WHERE "Code" = 'EUR') THEN
		CALL public."InsertCurrency"('EUR', '€', 'Euro');
		SELECT "ID" INTO _id FROM public."cl_Currencies" WHERE "Code" = 'EUR';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Euro');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Euro');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Euro');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Euro');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'يورو');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Currencies" WHERE "Code" = 'USD') THEN
		CALL public."InsertCurrency"('USD', '$', 'US Dollar');
		SELECT "ID" INTO _id FROM public."cl_Currencies" WHERE "Code" = 'USD';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Dollar');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Dollar');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dollar');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Dólar');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'دولار');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Currencies" WHERE "Code" = 'GBP') THEN
		CALL public."InsertCurrency"('GBP', '£', 'Sterling Pound');
		SELECT "ID" INTO _id FROM public."cl_Currencies" WHERE "Code" = 'GBP';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sterling Pound');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sterling Pound');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Livre Sterling');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Libra Esterlina');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'الجنيه الإسترليني');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_Currencies" WHERE "Code" = 'CNY') THEN
		CALL public."InsertCurrency"('CNY', '¥', 'Yuan');
		SELECT "ID" INTO _id FROM public."cl_Currencies" WHERE "Code" = 'CNY';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Yuan');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Yuan');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Yuan');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Yuan');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'يوان');
	END IF;

	-- Insert InvoiceStatuses

	IF NOT EXISTS(SELECT 1 FROM public."cl_InvoiceStatuses" WHERE "Code" = 'DRAFT') THEN
		CALL public."p_InsertInvoiceStatus"('Draft', 'DRAFT');
		SELECT "ID" INTO _id FROM public."cl_InvoiceStatuses" WHERE "Code" = 'DRAFT';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Draft');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Draft');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Proforma');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Borrador');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'مسودة');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_InvoiceStatuses" WHERE "Code" = 'SENT') THEN
		CALL public."p_InsertInvoiceStatus"('Sent', 'SENT');
		SELECT "ID" INTO _id FROM public."cl_InvoiceStatuses" WHERE "Code" = 'SENT';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sent');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sent');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Envoyé');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Enviado');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'تم الإرسال');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_InvoiceStatuses" WHERE "Code" = 'PAID') THEN
		CALL public."p_InsertInvoiceStatus"('Paid', 'PAID');
		SELECT "ID" INTO _id FROM public."cl_InvoiceStatuses" WHERE "Code" = 'PAID';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Paid');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Paid');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Payé');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Pagado');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'مدفوع');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_InvoiceStatuses" WHERE "Code" = 'OVERDUE') THEN
		CALL public."p_InsertInvoiceStatus"('Overdue', 'OVERDUE');
		SELECT "ID" INTO _id FROM public."cl_InvoiceStatuses" WHERE "Code" = 'OVERDUE';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Overdue');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Overdue');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Impayé');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Vencido');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'متأخر');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_InvoiceStatuses" WHERE "Code" = 'VOID') THEN
		CALL public."p_InsertInvoiceStatus"('Void', 'VOID');
		SELECT "ID" INTO _id FROM public."cl_InvoiceStatuses" WHERE "Code" = 'VOID';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Void');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Void');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Annulé');
		CALL public."p_InsertLanguageRelation"(_es, _id, 'Anulado');
		CALL public."p_InsertLanguageRelation"(_ar, _id, 'ملغي');
	END IF;
	
END $BODY$;

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_Customers', 'cl_Taxes', 'cl_PartnerTaxRelations', 'cl_ProductCategories', 'cl_Units', 'cl_Packagings', 'cl_Products', 'cl_Stocks', 'cl_DeliveryNotes', 'cl_DeliveryRelations',
	'cl_DispatchNotes', 'cl_DispatchRelations', 'cl_Discounts', 'cl_Currencies', 'cl_Prices', 'cl_PriceRelations', 'cl_InvoiceStatuses', 'cl_Invoices', 'cl_InvoiceRelations', 'cl_PaymentMethods', 'cl_Payments'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername :=
			CASE
				WHEN _tablename = 'cl_Taxes' THEN 'Tax'
				WHEN _tablename = 'cl_ProductCategories' THEN 'ProductCategory'
				WHEN _tablename = 'cl_InvoiceStatuses' THEN 'InvoiceStatus'
				WHEN _tablename = 'cl_Currencies' THEN 'Currency'
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
				WHEN _tablename = 'cl_Taxes' THEN 'Tax'
				WHEN _tablename = 'cl_ProductCategories' THEN 'ProductCategory'
				WHEN _tablename = 'cl_InvoiceStatuses' THEN 'InvoiceStatus'
				WHEN _tablename = 'cl_Currencies' THEN 'Currency'
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