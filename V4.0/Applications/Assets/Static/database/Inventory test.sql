-- Check parameters
select public."f_CurrentThread"();
select public."f_IsProc"();

-- Insert new profiles
call public."p_InsertProfile"('Medi Pharma', '2025-03-25');
call public."p_InsertProfile"('GWET', '1975-05-01', 'Marie Louise', 'IDIBWANI ABAYI');

-- Insert supplier
call public."p_InsertSupplier"('8144220004');

-- Insert customer
call public."p_InsertCustomer"('8144220005');

-- Insert product categories
call public."p_InsertProductCategory"('Medical Supplies');
call public."p_InsertProductCategory"('Phamarceuticals');
call public."p_InsertProductCategory"('Medical gaz');
call public."p_InsertProductCategory"('Laboratory supplies');
call public."p_InsertProductCategory"('Cleaning supplies');

-- Insert warehouses
call public."p_InsertWarehouse"('Little pharmacy', '4th floor');
call public."p_InsertWarehouse"('Pharmacy', '2nd floor');
call public."p_InsertWarehouse"('Cleaning closet', '1st floor');
call public."p_InsertWarehouse"('Laboratory', '1st floor');
call public."p_InsertWarehouse"('Gaz tank', 'Basement');

-- Insert manufacturers
call public."p_InsertManufacturer"('Pfizer');

-- Insert units
call public."p_InsertUnit"('Meter', 'm');
call public."p_InsertUnit"('Liter', 'l');
call public."p_InsertUnit"('Unit', 'u');
call public."p_InsertUnit"('Package', 'pck');

-- 