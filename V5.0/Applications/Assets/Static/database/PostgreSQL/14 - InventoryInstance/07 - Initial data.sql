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

	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Purchases') THEN
		CALL public."p_InsertAppCategory"('Purchases');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Purchases';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Inventory';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
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
	CALL public."p_InsertCustomer"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCustomer"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCustomer"(_id);

	-- Insert Suppliers

	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertSupplier"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertSupplier"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertSupplier"(_id);

	-- Insert Warehouse

	CALL public."p_InsertWarehouse"('Anonymous warehouse', 'Anonymous location');

	-- Insert Manufacturer

	CALL public."p_InsertManufacturer"('Anonymous manufacturer');

	-- Insert Units

	CALL public."p_InsertUnit"('Package', 'pkg');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Package';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Package');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Package');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paquet');
	--
	CALL public."p_InsertUnit"('Meter', 'm');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Meter';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Meter');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Meter');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mètre');
	--
	CALL public."p_InsertUnit"('Liter', 'l');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Liter';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Liter');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Liter');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Litre');
	--
	CALL public."p_InsertUnit"('Second', 's');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Second';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Second');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Second');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Seconde');
	--
	CALL public."p_InsertUnit"('Kilogram', 'kg');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Kilogram';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kilogram');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kilogram');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kilogramme');
	--
	CALL public."p_InsertUnit"('Unit', 'u');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Unit';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Unit');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Unit');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Unité');
	--
	CALL public."p_InsertUnit"('Farenheit', '°F');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Farenheit';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Farenheit');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Farenheit');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Farenheit');
	--
	CALL public."p_InsertUnit"('Celsius', '°C');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Celsius';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Degree Celsius');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Degree Celsius');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Degré Celsius');
	--
	CALL public."p_InsertUnit"('Kelvin', '°K');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Kelvin';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kelvin');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kelvin');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kelvin');
	--
	CALL public."p_InsertUnit"('USD', '$');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'USD';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'USD');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'USD');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dollar US');
	--
	CALL public."p_InsertUnit"('EURO', '€');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'EURO';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Euro');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Euro');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Euro');
	--
	CALL public."p_InsertUnit"('XAF', 'CFAF');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'XAF';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'FCFA');
	--
	CALL public."p_InsertUnit"('XOF', 'CFAF');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'XOF';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'FCFA');
	--
	/*CALL public."p_InsertUnit"('Foot', 'ft');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Foot';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Foot');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Foot');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pied');
	--
	CALL public."p_InsertUnit"('Nautical mile', 'n. mile');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Nautical mile';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nautical mile');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nautical mile');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mile nautique');
	--
	CALL public."p_InsertUnit"('Gallon', 'gal');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Gallon';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Gallon');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Gallon');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gallon');
	--
	CALL public."p_InsertUnit"('Pound', 'p');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Pound';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Pound');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Pound');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Livre');
	--
	CALL public."p_InsertUnit"('Radian', 'rd');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Radian';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Radian');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Radian');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Radian');
	--
	CALL public."p_InsertUnit"('Bar', 'bar');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Bar';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bar');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bar');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bar');
	--
	CALL public."p_InsertUnit"('Pixel', 'px');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Pixel';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Pixel');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Pixel');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pixel');
	--
	CALL public."p_InsertUnit"('Joule', 'j');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Joule';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Joule');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Joule');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Joule');
	--
	CALL public."p_InsertUnit"('Hertz', 'Hz');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Hertz';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Hertz');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Hertz');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Hertz');
	--
	CALL public."p_InsertUnit"('Byte', 'b');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Byte';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Byte');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Byte');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Octet');
	--
	CALL public."p_InsertUnit"('Milli', 'm');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Milli';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Milli');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Milli');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Milli');
	--
	CALL public."p_InsertUnit"('Centi', 'c');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Centi';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Centi');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Centi');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Centi');
	--
	CALL public."p_InsertUnit"('Deci', 'd');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Deci';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Deci');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Deci');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Déci');
	--
	CALL public."p_InsertUnit"('Kilo', 'K');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Kilo';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kilo');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kilo');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kilo');
	--
	CALL public."p_InsertUnit"('Quinta', 'q');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Quinta';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Quinta');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Quinta');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Quinta');
	--
	CALL public."p_InsertUnit"('Giga', 'G');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Giga';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Giga');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Giga');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Giga');
	--
	CALL public."p_InsertUnit"('Mega', 'M');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Mega';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mega');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mega');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mega');
	--
	CALL public."p_InsertUnit"('Tera', 'T');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Tera';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tera');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tera');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tera');*/

	-- Insert Product categories

	CALL public."p_InsertProductCategory"('General pharmaceuticals');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'General pharmaceuticals';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'General pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'General pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Médicaments généraux');
	--
	CALL public."p_InsertProductCategory"('Fertility Pharmaceuticals');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Fertility Pharmaceuticals';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Fertility Pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Fertility Pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Médicaments fertilité');
	--
	CALL public."p_InsertProductCategory"('Medical consumables');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Medical consumables';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Medical consumables');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Medical consumables');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Consommables médicaux');
	--
	CALL public."p_InsertProductCategory"('Fertility consumables');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Fertility consumables';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Fertility consumables');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Fertility consumables');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Consommables fertilité');
	--
	CALL public."p_InsertProductCategory"('Laboratory reagents');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Laboratory reagents';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Laboratory reagents');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Laboratory reagents');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réactifs de laboratoire');
	--
	CALL public."p_InsertProductCategory"('Medical gases');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Medical gases';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Medical gases');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Medical gases');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gaz médicaux');
	--
	CALL public."p_InsertProductCategory"('Culture media');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Culture media';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Culture media');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Culture media');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Milieux de culture');

	-- ProductAttributes

	CALL public."p_InsertProductAttribute"('Commercial denomination', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Commercial denomination';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Commercial denomination');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Commercial denomination');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dénomination commerciale');
	--
	CALL public."p_InsertProductAttribute"('International denomination', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'International denomination';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'International denomination (INN)');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'International denomination (INN)');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dénomination internationale (DCI)');
	--
	CALL public."p_InsertProductAttribute"('Manufacturer', 'table', 'VALUE IN (SELECT "ID" FROM public."cl_Manufacturers")', 'cl_Manufacturers');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Manufacturer';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Manufacturer');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Manufacturer');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Fabricant');
	--
	CALL public."p_InsertProductAttribute"('Dosage', 'number');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Dosage';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Dosage');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Dosage');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dosage');
	--
	/*CALL public."p_InsertProductAttribute"('Drug route', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Drug route';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Drug route');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Drug route');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Voie d''Administration');*/
	--
	CALL public."p_InsertProductAttribute"('Galenics', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Galenics';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Galenics');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Galenics');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Galénique');
	--
	/*CALL public."p_InsertProductAttribute"('Expiration date', 'timestamp');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Expiration date';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Expiration date');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Expiration date');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Date de péremption');*/

	-- Insert Packagings

	CALL public."p_InsertPackaging"('Package of 12');
	SELECT "ID" INTO _id FROM public."cl_Packagings" WHERE "Name" = 'Package of 12';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Package of 12');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Package of 12');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paquet de 12');
	--
	CALL public."p_InsertPackaging"('Box of 8 ampoules');
	SELECT "ID" INTO _id FROM public."cl_Packagings" WHERE "Name" = 'Box of 8 ampoules';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Box of 8 ampoules');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Box of 8 ampoules');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Boîte de 8 ampoules');
	--
	CALL public."p_InsertPackaging"('Bottle of 10ml');
	SELECT "ID" INTO _id FROM public."cl_Packagings" WHERE "Name" = 'Bottle of 10ml';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bottle of 10ml');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bottle of 10ml');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Flacon de 10ml');

	-- Insert Products

	CALL public."p_InsertProduct"('Aspirin', '8154220001', '4634220001', 1, 5);
	SELECT "ID" INTO _id FROM public."cl_Products" WHERE "Name" = 'Aspirin';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Aspirin');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Aspirin');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Aspirine');
	
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
	_tablenames text[] := ARRAY['cl_Customers', 'cl_Suppliers', 'cl_ProductCategories', 'cl_Warehouses', 'cl_Manufacturers', 'cl_Units', 'cl_Packagings', 'cl_Products', 'cl_ProductAttributes',
	'cl_AttributeRelations', 'cl_Stocks', 'cl_StockRelations', 'cl_DeliveryNotes', 'cl_DeliveryRelations', 'cl_DispatchNotes', 'cl_DispatchRelations', 'cl_ReturnNotes', 'cl_ReturnRelations', 'cl_WasteNotes',
	'cl_WasteRelations', 'cl_InventNotes', 'cl_InventRelations', 'cl_TransferNotes', 'cl_TransferRelations', 'cl_Inventories', 'cl_InventoryRelations'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername :=
			CASE
				WHEN _tablename = 'cl_ProductCategories' THEN 'Category'
				WHEN _tablename = 'cl_Inventories' THEN 'Inventory'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
		_triggername := 'Log_' || _triggername;
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
				WHEN _tablename = 'cl_ProductCategories' THEN 'Category'
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