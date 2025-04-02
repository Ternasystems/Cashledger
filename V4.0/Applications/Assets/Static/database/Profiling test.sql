-- Check parameters
select public."f_CurrentThread"();
select public."f_IsProc"();
--
call public."p_CurrentThread"(true);
call public."p_IsProc"(true);
--
call public."p_IsProc"(false);
call public."p_CurrentThread"(false);

-- Login with Jéoline
-- IP: 192.168.100.88; ID: 4154220001
select * from public."f_CheckCredential"('infos@jeolinecorporates.com', 'pat1380/*56', '192.168.100.88');
--
select public."f_CheckConnectionStatus"('4154220001');
call public."p_ConnectionStatus"('4154220001', true, 'session_id');
--
select public."f_CheckLoginStatus"('4154220001');
call public."p_LoginStatus"('4154220001', 'LOGIN', '192.168.100.88');
--
select * from public."cl_Credentials" order by "ID";

-- Login with Admin with error
-- IP: 192.168.100.88; ID: 4154220003
select * from public."f_CheckCredential"('admin@cashledger.com', 'pat1380/*56', '192.168.100.88');

-- Login with error
select * from public."f_CheckCredential"('patrikgwet@gmail.com', 'pat1234', '192.168.100.2');

-- Set Credential CurrentThread
-- Threads: 3
select public."f_CheckCurrentThread"();
--
do $$
declare _isThreaded boolean;
begin
	call public."p_SetCurrentThread"(_isThreaded, '4154220001', 3);
end $$;

-- Insert Profile: lastname: Gwét; birthdate: 1981-09-08; firstname: Patrik;
call public."p_InsertProfile"('Gwét', '1981-09-08', 'Patrik');

-- Update Profile: id: 8144220004; lastname: Gwét; birthdate: 1981-09-08; maidenname: null; firstname: Patrik Armand
call public."p_UpdateProfile"('8144220004', 'Gwét', '1981-09-08', null, 'Patrik Armand');

-- Insert Credential: profileid: 8144220004; patrikgwet@gmail.com
do $$
declare _pwd character varying(50);
begin
	call public."p_InsertCredential"(_pwd, '8144220004', 'patrikgwet@gmail.com');
end $$;

-- Logout Jéoline
select public."f_CheckConnectionStatus"('4154220001');
--
call public."p_ConnectionStatus"('4154220001', false);
--
select public."f_CheckLoginStatus"('4154220001');
--
call public."p_LoginStatus"('4154220001', 'LOGOUT', '192.168.100.88');




