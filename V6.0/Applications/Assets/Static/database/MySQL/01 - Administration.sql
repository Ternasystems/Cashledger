/* Administration app */

DELIMITER $$

CREATE FUNCTION `f_PwdGenerator`()
    RETURNS VARCHAR(50)
    DETERMINISTIC
BEGIN
    DECLARE _v VARCHAR(63) DEFAULT '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    DECLARE _s VARCHAR(50) DEFAULT '';
    DECLARE _l INT DEFAULT 10;

    WHILE LENGTH(_s) < _l DO
        IF LENGTH(_s) = 0 THEN
            SET _s = SUBSTRING(_v, FLOOR(11 + (RAND() * 52)), 1);
        ELSE
            SET _s = CONCAT(_s, SUBSTRING(_v, FLOOR(1 + (RAND() * 62)), 1));
        END IF;
    END WHILE;
    RETURN _s;
END$$

CREATE FUNCTION `f_Activation`(_date DATE)
    RETURNS VARCHAR(50)
    DETERMINISTIC
BEGIN
    DECLARE _dte VARCHAR(50);
    DECLARE _i INT DEFAULT 1;
    DECLARE _str VARCHAR(50) DEFAULT '';
    SET _dte = CAST(CAST(REPLACE(CAST(_date AS CHAR), '-', '') AS UNSIGNED) * 1981 AS CHAR);

    WHILE _i <= LENGTH(_dte) DO
        SET _str = CONCAT(_str, CHAR(CAST(SUBSTRING(_dte, _i, 1) AS UNSIGNED) + 65));
        SET _i = _i + 1;
    END WHILE;
    RETURN _str;
END$$

CREATE FUNCTION `f_ActiveUser`(_number INT, _year INT)
    RETURNS VARCHAR(50)
    DETERMINISTIC
BEGIN
    DECLARE _n VARCHAR(50);
    DECLARE _i INT;
    DECLARE _str VARCHAR(50) DEFAULT '';
    DECLARE _str1 VARCHAR(50) DEFAULT '';
    DECLARE _str2 VARCHAR(50) DEFAULT '';

    SET _n = CAST(CAST(_year AS UNSIGNED) * 1981 AS CHAR);
    SET _i = 1;
    WHILE _i <= LENGTH(_n) DO
        SET _str1 = CONCAT(_str1, CHAR(CAST(SUBSTRING(_n, _i, 1) AS UNSIGNED) + 65));
        SET _i = _i + 1;
    END WHILE;

    SET _n = CAST(CAST(_number AS UNSIGNED) * 1981 AS CHAR);
    SET _i = 1;
    WHILE _i <= LENGTH(_n) DO
        SET _str2 = CONCAT(_str2, CHAR(CAST(SUBSTRING(_n, _i, 1) AS UNSIGNED) + 65));
        SET _i = _i + 1;
    END WHILE;

    SET _str = RIGHT(TRIM(CONCAT(_str1, LENGTH(_str2), _str2)), 11);
    RETURN _str;
END$$

-- ----------------------------
-- Parameters Table
-- ----------------------------

CREATE TABLE `cl_Parameters` (
    `ID` INT AUTO_INCREMENT PRIMARY KEY,
    `ParamName` VARBINARY(64) UNIQUE NOT NULL,
    `ParamUValue` TEXT,
    `ParamValue` VARBINARY(64),
    `OwnerApp` VARCHAR(50) NOT NULL,
    `ParamLock` BOOLEAN NOT NULL,
    `Auditable` BOOLEAN NOT NULL,
    `IsActive` DATETIME
);

-- ----------------------------
-- ParameterRelations Table
-- ----------------------------

CREATE TABLE `cl_ParameterRelations` (
    `ID` INT AUTO_INCREMENT PRIMARY KEY,
    `ParamID` INT,
    `UserApp` VARCHAR(50) NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT,
    CONSTRAINT `UQ_ParameterRelation` UNIQUE (`ParamID`, `UserApp`)
);

-- ----------------------------
-- Functions & Initial Data for Parameters
-- ----------------------------

CREATE FUNCTION `f_CheckParameterRelation`(_name VARCHAR(50))
    RETURNS JSON
    DETERMINISTIC
BEGIN
    DECLARE _id INT;
    DECLARE _result JSON;

    SELECT `ID` INTO _id FROM `cl_Parameters` WHERE `ParamName` = SHA2(_name, 256);

    IF _id IS NULL THEN
        RETURN JSON_ARRAY();
    END IF;

    SELECT JSON_ARRAYAGG(`UserApp`) INTO _result FROM `cl_ParameterRelations` WHERE `ParamID` = _id;
    RETURN _result;
END$$

DELIMITER ;

INSERT INTO `cl_Parameters` (`ParamName`, `ParamUValue`, `ParamValue`, `OwnerApp`, `ParamLock`, `Auditable`, `IsActive`) VALUES
(SHA2('IsProc', 256), NULL, SHA2('0', 256), 'Administration', FALSE, FALSE, NULL),
(SHA2('Serial', 256), NULL, SHA2('{60E9AA19-8DDD-41B6-86DB-2D4CA1E2CB32}', 256), 'Administration', TRUE, FALSE, NULL),
(SHA2('Activation', 256), NULL, SHA2('activated', 256), 'Administration', FALSE, TRUE, NULL),
(SHA2('Shortname', 256), 'cashledger', NULL, 'Administration', TRUE, FALSE, NULL),
(SHA2('StartDate', 256), 'EAAJFGEAAIB', NULL, 'Administration', FALSE, FALSE, NULL),
(SHA2('ActiveDate', 256), 'EABBHCDCJIB', NULL, 'Administration', FALSE, FALSE, NULL),
(SHA2('EndDate', 256), 'EABBHGIIGBB', NULL, 'Administration', FALSE, FALSE, NULL),
(SHA2('Users', 256), 'AAJFEE4HJCE', NULL, 'Administration', FALSE, FALSE, NULL),
(SHA2('CodeLength', 256), '4', NULL, 'Administration', FALSE, TRUE, NULL),
(SHA2('CurrentThread', 256), null, SHA2('0', 256), 'Administration', FALSE, FALSE, NULL),
(SHA2('AppVersion', 256), 'Cashledger Professional Server Edition (SE) build 2025.1.1', NULL, 'Administration', TRUE, FALSE, NULL);

INSERT INTO `cl_ParameterRelations` (`ParamID`, `UserApp`, `IsActive`) VALUES (1, 'Administration', NULL), (2, 'Administration', NULL), (3, 'Administration', NULL), (4, 'Administration', NULL),
(5, 'Administration', NULL), (6, 'Administration', NULL), (7, 'Administration', NULL), (8, 'Administration', NULL), (9, 'Administration', NULL), (10, 'Administration', NULL), (11, 'Administration', NULL);

DELIMITER $$

-- ----------------------------
-- State Management Functions & Procs
-- ----------------------------

CREATE FUNCTION `f_CurrentThread`()
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    IF EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ParamName` = SHA2('CurrentThread', 256) AND `ParamValue` = SHA2('1', 256)) THEN
        RETURN TRUE;
    END IF;
    RETURN FALSE;
END$$

CREATE PROCEDURE `p_CurrentThread`(IN _threaded BOOLEAN)
BEGIN
    DECLARE _isThreaded BOOLEAN;
    thread_loop: LOOP
        SET _isThreaded = `f_CurrentThread`();
        IF _isThreaded = FALSE OR _isThreaded != _threaded THEN
            LEAVE thread_loop;
        END IF;
    END LOOP thread_loop;
    UPDATE `cl_Parameters` SET `ParamValue` = SHA2(CASE WHEN _threaded = TRUE THEN '1' ELSE '0' END, 256) WHERE `ParamName` = SHA2('CurrentThread', 256);
END$$

CREATE FUNCTION `f_IsProc`()
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    IF EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ParamName` = SHA2('IsProc', 256) AND `ParamValue` = SHA2('1', 256)) THEN
        RETURN TRUE;
    END IF;
    RETURN FALSE;
END$$

CREATE PROCEDURE `p_IsProc`(IN _processed BOOLEAN)
BEGIN
    IF `f_CurrentThread`() = FALSE THEN
        CALL `p_CurrentThread`(TRUE);
    END IF;
    UPDATE `cl_Parameters` SET `ParamValue` = SHA2(CASE WHEN _processed = TRUE THEN '1' ELSE '0' END, 256) WHERE `ParamName` = SHA2('IsProc', 256);
END$$

CREATE PROCEDURE `p_Activation`(IN _activated BOOLEAN)
BEGIN
    IF `f_IsProc`() = FALSE THEN
        CALL `p_IsProc`(TRUE);
    END IF;
    UPDATE `cl_Parameters` SET `ParamValue` = SHA2(CASE WHEN _activated = TRUE THEN 'activated' ELSE 'deactivated' END, 256) WHERE `ParamName` = SHA2('Activation', 256);
END$$

CREATE FUNCTION `f_Locked`(_id INT)
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    IF EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ID` = _id AND `ParamLock` = TRUE) THEN
        RETURN TRUE;
    END IF;
    RETURN FALSE;
END$$

CREATE FUNCTION `f_Auditable`(_id INT)
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    IF EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ID` = _id AND `Auditable` = TRUE) THEN
        RETURN TRUE;
    END IF;
    RETURN FALSE;
END$$

CREATE FUNCTION `f_CheckActivation`(_mac VARCHAR(50), _date DATE)
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    DECLARE _endte VARCHAR(50);
    DECLARE _dte VARCHAR(50);

    SELECT `ParamUValue` INTO _endte FROM `cl_Parameters` WHERE `ParamName` = SHA2('EndDate', 256);
    SET _dte = `f_Activation`(_date);

    IF NOT EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ParamName` = SHA2('Activation', 256) AND `ParamValue` = SHA2('activated', 256)) THEN
        RETURN FALSE;
    END IF;

    IF NOT EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ParamName` = SHA2('Serial', 256) AND `ParamValue` = SHA2(_mac, 256)) THEN
        RETURN FALSE;
    END IF;

    IF _dte > _endte THEN
        RETURN FALSE;
    END IF;

    RETURN TRUE;
END$$

CREATE FUNCTION `f_CheckPeriod`(_date DATE)
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    DECLARE _activedte VARCHAR(50);
    DECLARE _endte VARCHAR(50);
    DECLARE _dte VARCHAR(50);

    SELECT `ParamUValue` INTO _activedte FROM `cl_Parameters` WHERE `ParamName` = SHA2('ActiveDate', 256);
    SELECT `ParamUValue` INTO _endte FROM `cl_Parameters` WHERE `ParamName` = SHA2('EndDate', 256);
    SET _dte = `f_Activation`(_date);

    IF NOT EXISTS(SELECT 1 FROM `cl_Parameters` WHERE `ParamName` = SHA2('Activation', 256) AND `ParamValue` = SHA2('activated', 256)) THEN
        RETURN FALSE;
    END IF;

    IF _dte > _endte OR _dte < _activedte THEN
        RETURN FALSE;
    END IF;

    RETURN TRUE;
END$$

CREATE FUNCTION `f_CheckCodeLength`(_code INT)
    RETURNS BOOLEAN
    DETERMINISTIC
BEGIN
    DECLARE _n INT;
    SELECT CAST(`ParamUValue` AS SIGNED) INTO _n FROM `cl_Parameters` WHERE `ParamName` = SHA2('CodeLength', 256);
    IF _code = _n THEN
        RETURN TRUE;
    END IF;
    RETURN FALSE;
END$$

CREATE FUNCTION `f_Readme`()
    RETURNS TEXT
    DETERMINISTIC
BEGIN
    DECLARE _str TEXT;
    SELECT `ParamUValue` INTO _str FROM `cl_Parameters` WHERE `ParamName` = SHA2('AppVersion', 256);
    RETURN _str;
END$$

-- ----------------------------
-- Generic Trigger Logic Procedures
-- ----------------------------

CREATE PROCEDURE `t_DeleteTrigger`()
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Operations not allowed. DELETE operations attempted.';
END$$

CREATE PROCEDURE `t_InsertTrigger`()
BEGIN
    IF NOT (`f_CurrentThread`() = TRUE AND `f_IsProc`() = TRUE) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Operation not allowed';
    END IF;
END$$

CREATE PROCEDURE `t_UpdateTrigger`(IN _is_active_old DATETIME)
BEGIN
    IF NOT (`f_CurrentThread`() = TRUE AND `f_IsProc`() = TRUE AND _is_active_old IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Operation not allowed';
    END IF;
END$$

CREATE PROCEDURE `t_RemoveTrigger`()
BEGIN
    IF NOT (`f_CurrentThread`() = TRUE AND `f_IsProc`() = TRUE) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Operation not allowed';
    END IF;
END$$

-- ----------------------------
-- Parameter Procedures & Triggers
-- ----------------------------

CREATE PROCEDURE `p_InsertParameter`(
    IN _name VARCHAR(50),
    IN _value TEXT,
    IN _encrypted BOOLEAN,
    IN _owner VARCHAR(50),
    IN _locked BOOLEAN,
    IN _auditable BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    IF _encrypted THEN
        INSERT INTO `cl_Parameters` (`ParamName`, `ParamValue`, `OwnerApp`, `ParamLock`, `Auditable`) VALUES
        (SHA2(_name, 256), SHA2(_value, 256), _owner, _locked, CASE WHEN _locked = TRUE THEN FALSE ELSE _auditable END);
    ELSE
        INSERT INTO `cl_Parameters` (`ParamName`, `ParamUValue`, `OwnerApp`, `ParamLock`, `Auditable`) VALUES
        (SHA2(_name, 256), _value, _owner, _locked, CASE WHEN _locked = TRUE THEN FALSE ELSE _auditable END);
    END IF;

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

CREATE PROCEDURE `p_UpdateParameter`(
    IN _name VARCHAR(50),
    IN _value TEXT,
    IN _encrypted BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    UPDATE `cl_Parameters` SET
        `ParamValue` = CASE WHEN _encrypted = TRUE THEN SHA2(_value, 256) ELSE NULL END,
        `ParamUValue` = CASE WHEN _encrypted = TRUE THEN NULL ELSE _value END
    WHERE `ParamName` = SHA2(_name, 256);

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

CREATE PROCEDURE `p_DeleteParameter`(IN _name VARCHAR(50))
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    UPDATE `cl_Parameters` SET `IsActive` = NOW() WHERE `ParamName` = SHA2(_name, 256);

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

CREATE TRIGGER `Delete_Parameter`
BEFORE DELETE ON `cl_Parameters`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_Parameter`
BEFORE INSERT ON `cl_Parameters`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_Parameter`
BEFORE UPDATE ON `cl_Parameters`
FOR EACH ROW
BEGIN
    IF NOT (OLD.`IsActive` IS NULL AND (NEW.`ParamName` = SHA2('CurrentThread', 256) OR
        (`f_CurrentThread`() = TRUE AND (NEW.`ParamName` = SHA2('IsProc', 256) OR (`f_Locked`(NEW.`ID`) = FALSE AND `f_IsProc`() = TRUE))))) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Operation not allowed';
    END IF;
END;

DELIMITER $$

-- ----------------------------
-- Parameter Relation Procedures & Triggers
-- ----------------------------

CREATE PROCEDURE `p_InsertParameterRelation`(
    IN _paramname VARCHAR(50),
    IN _userapp VARCHAR(50)
)
BEGIN
    DECLARE _id INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    SELECT `ID` INTO _id FROM `cl_Parameters` WHERE `ParamName` = SHA2(_paramname, 256);
    INSERT INTO `cl_ParameterRelations` (`ParamID`, `UserApp`) VALUES (_id, _userapp);

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

CREATE PROCEDURE `p_UpdateParameterRelation`(
    IN _id INT,
    IN _paramid VARCHAR(50),
    IN _userapp VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    UPDATE `cl_ParameterRelations` SET `ParamID` = _paramid, `UserApp` = _userapp WHERE `ID` = _id;

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

CREATE PROCEDURE `p_DeleteParameterRelation`(IN _id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    UPDATE `cl_ParameterRelations` SET `IsActive` = NOW() WHERE `ID` = _id;

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_ParameterRelation`
BEFORE DELETE ON `cl_ParameterRelations`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_ParameterRelation`
BEFORE INSERT ON `cl_ParameterRelations`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_ParameterRelation`
BEFORE UPDATE ON `cl_ParameterRelations`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

DELIMITER $$

-- ----------------------------
-- ID Generation & Generic Query
-- ----------------------------

CREATE FUNCTION `f_CreateID`(_type CHAR(3), _tablename VARCHAR(50))
    RETURNS VARCHAR(50)
    DETERMINISTIC
BEGIN
    DECLARE _id VARCHAR(50) DEFAULT '';
    DECLARE _shortname VARCHAR(50);
    DECLARE _c CHAR(1);
    DECLARE _x INT;
    DECLARE _i INT DEFAULT 1;
    DECLARE _max INT;

    SELECT `ParamUValue` INTO _shortname FROM `cl_Parameters` WHERE `ParamName` = SHA2('Shortname', 256);

    WHILE _i <= 3 DO
        SET _c = UPPER(SUBSTRING(_type, _i, 1));
        SET _x = ASCII(_c);
        WHILE _x >= 10 DO
            SET _x = FLOOR(_x / 10) + (_x % 10);
        END WHILE;
        SET _id = CONCAT(_id, _x);
        SET _i = _i + 1;
    END WHILE;

    SET _i = 1;
    WHILE _i <= 3 DO
        SET _c = UPPER(SUBSTRING(_shortname, _i, 1));
        SET _x = ASCII(_c);
        WHILE _x >= 10 DO
            SET _x = FLOOR(_x / 10) + (_x % 10);
        END WHILE;
        SET _id = CONCAT(_id, _x);
        SET _i = _i + 1;
    END WHILE;

    SET @sql = CONCAT('SELECT COALESCE(MAX(CAST(SUBSTRING(`ID`, LENGTH(`ID`) - 3, 4) AS SIGNED)), 0) INTO @max_id FROM `', _tablename, '`');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET _max = @max_id;

    SET _max = _max + 1;
    SET _id = CONCAT(_id, LPAD(_max, 4, '0'));
    RETURN _id;
END$$

CREATE PROCEDURE `p_Query`(
    IN _sql TEXT,
    IN _tablename VARCHAR(50),
    IN _idcode CHAR(3)
)
BEGIN
    DECLARE _id VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        CALL `p_IsProc`(FALSE);
        CALL `p_CurrentThread`(FALSE);
        RESIGNAL;
    END;

    CALL `p_CurrentThread`(TRUE);
    CALL `p_IsProc`(TRUE);

    IF _idcode IS NOT NULL THEN
        SET _id = `f_CreateID`(_idcode, _tablename);
        SET _sql = REPLACE(_sql, '%s', QUOTE(_id));
    END IF;

    SET @final_sql = _sql;
    PREPARE stmt FROM @final_sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    CALL `p_IsProc`(FALSE);
    CALL `p_CurrentThread`(FALSE);
END$$

-- ----------------------------
-- Languages Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_Languages` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Code` INT UNIQUE NOT NULL,
    `Label` VARCHAR(50) NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT
);

DELIMITER $$

CREATE PROCEDURE `p_InsertLanguage`(IN _label VARCHAR(50), IN _description TEXT)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Languages';
    DECLARE _code INT;

    SET @s = CONCAT('SELECT COALESCE(MAX(`Code`), 0) + 1 FROM `', _tablename, '` INTO @code');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET _code = @code;

    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', _code, ', ', QUOTE(_label), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'LNG');
END$$

CREATE PROCEDURE `p_UpdateLanguage`(
    IN _id VARCHAR(50),
    IN _label VARCHAR(50),
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Languages';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `Label` = ', QUOTE(_label), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteLanguage`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Languages';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_Language`
BEFORE DELETE ON `cl_Languages`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_Language`
BEFORE INSERT ON `cl_Languages`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_Language`
BEFORE UPDATE ON `cl_Languages`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- AppCategories Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_AppCategories` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Code` INT UNIQUE NOT NULL,
    `Name` VARCHAR(50) NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT
);

DELIMITER $$

CREATE PROCEDURE `p_InsertAppCategory`(IN _name VARCHAR(50), IN _description TEXT)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_AppCategories';
    DECLARE _code INT;

    SET @s = CONCAT('SELECT COALESCE(MAX(`Code`), 0) + 1 FROM `', _tablename, '` INTO @code');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET _code = @code;

    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', _code, ', ', QUOTE(_name), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'ACT');
END$$

CREATE PROCEDURE `p_UpdateAppCategory`(
    IN _id VARCHAR(50),
    IN _name VARCHAR(50),
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_AppCategories';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `Name` = ', QUOTE(_name), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteAppCategory`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_AppCategories';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_AppCategory`
BEFORE DELETE ON `cl_AppCategories`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_AppCategory`
BEFORE INSERT ON `cl_AppCategories`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_AppCategory`
BEFORE UPDATE ON `cl_AppCategories`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- Apps Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_Apps` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Code` INT UNIQUE NOT NULL,
    `Name` VARCHAR(50) NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT
);

DELIMITER $$

CREATE PROCEDURE `p_InsertApp`(IN _name VARCHAR(50), IN _description TEXT)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Apps';
    DECLARE _code INT;

    SET @s = CONCAT('SELECT COALESCE(MAX(`Code`), 0) + 1 FROM `', _tablename, '` INTO @code');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET _code = @code;

    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', _code, ', ', QUOTE(_name), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'APP');
END$$

CREATE PROCEDURE `p_UpdateApp`(
    IN _id VARCHAR(50),
    IN _name VARCHAR(50),
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Apps';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `Name` = ', QUOTE(_name), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteApp`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Apps';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_App`
BEFORE DELETE ON `cl_Apps`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_App`
BEFORE INSERT ON `cl_Apps`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_App`
BEFORE UPDATE ON `cl_Apps`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- AppRelations Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_AppRelations` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `AppID` VARCHAR(50) NOT NULL,
    `AppCategoryID` VARCHAR(50) NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT,
    CONSTRAINT `FK_AppRelations_App` FOREIGN KEY (`AppID`) REFERENCES `cl_Apps` (`ID`),
    CONSTRAINT `FK_AppRelations_Category` FOREIGN KEY (`AppCategoryID`) REFERENCES `cl_AppCategories` (`ID`),
    CONSTRAINT `UQ_AppRelation` UNIQUE (`AppID`, `AppCategoryID`)
);

DELIMITER $$

CREATE PROCEDURE `p_InsertAppRelation`(
    IN _appid VARCHAR(50),
    IN _categoryid VARCHAR(50),
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_AppRelations';
    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', QUOTE(_appid), ', ', QUOTE(_categoryid), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'APR');
END$$

CREATE PROCEDURE `p_DeleteAppRelation`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_AppRelations';
    SET _sql = CONCAT('DELETE FROM `', _tablename, '` WHERE `ID` = ', QUOTE(_id));
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Update_AppRelation`
BEFORE UPDATE ON `cl_AppRelations`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_AppRelation`
BEFORE INSERT ON `cl_AppRelations`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Remove_AppRelation`
BEFORE DELETE ON `cl_AppRelations`
FOR EACH ROW
    CALL `t_RemoveTrigger`();

-- ----------------------------
-- Continents Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_Continents` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Code` INT UNIQUE NOT NULL,
    `Name` VARCHAR(50) NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT
);

DELIMITER $$

CREATE PROCEDURE `p_InsertContinent`(IN _name VARCHAR(50), IN _description TEXT)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Continents';
    DECLARE _code INT;

    SET @s = CONCAT('SELECT COALESCE(MAX(`Code`), 0) + 1 FROM `', _tablename, '` INTO @code');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET _code = @code;

    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', _code, ', ', QUOTE(_name), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'CTN');
END$$

CREATE PROCEDURE `p_UpdateContinent`(
    IN _id VARCHAR(50),
    IN _name VARCHAR(50),
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Continents';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `Name` = ', QUOTE(_name), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteContinent`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Continents';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_Continent`
BEFORE DELETE ON `cl_Continents`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_Continent`
BEFORE INSERT ON `cl_Continents`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_Continent`
BEFORE UPDATE ON `cl_Continents`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- Countries Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_Countries` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Code` INT NOT NULL,
    `ISO2` CHAR(2) UNIQUE NOT NULL,
    `ISO3` CHAR(3) UNIQUE NOT NULL,
    `ContinentID` VARCHAR(50) NOT NULL,
    `Name` TEXT NOT NULL,
    `Flag` TEXT NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT,
    CONSTRAINT `FK_Countries_Continent` FOREIGN KEY (`ContinentID`) REFERENCES `cl_Continents` (`ID`)
);

DELIMITER $$

CREATE PROCEDURE `p_InsertCountry`(
    IN _code INT,
    IN _iso2 CHAR(2),
    IN _iso3 CHAR(3),
    IN _continent VARCHAR(50),
    IN _name TEXT,
    IN _flag TEXT,
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Countries';
    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', _code, ', ', QUOTE(_iso2), ', ', QUOTE(_iso3), ', ', QUOTE(_continent), ', ', QUOTE(_name), ', ', QUOTE(_flag), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'CTY');
END$$

CREATE PROCEDURE `p_UpdateCountry`(
    IN _id VARCHAR(50),
    IN _code INT,
    IN _iso2 CHAR(2),
    IN _iso3 CHAR(3),
    IN _continent VARCHAR(50),
    IN _name TEXT,
    IN _flag TEXT,
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Countries';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `Code` = ', _code, ', `ISO2` = ', QUOTE(_iso2), ', `ISO3` = ', QUOTE(_iso3), ', `ContinentID` = ', QUOTE(_continent), ', `Name` = ', QUOTE(_name), ', `Flag` = ', QUOTE(_flag), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteCountry`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Countries';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_Country`
BEFORE DELETE ON `cl_Countries`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_Country`
BEFORE INSERT ON `cl_Countries`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_Country`
BEFORE UPDATE ON `cl_Countries`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- Cities Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_Cities` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Code` INT UNIQUE NOT NULL,
    `CountryID` VARCHAR(50) NOT NULL,
    `Name` TEXT NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT,
    CONSTRAINT `FK_Cities_Country` FOREIGN KEY (`CountryID`) REFERENCES `cl_Countries` (`ID`)
);

DELIMITER $$

CREATE PROCEDURE `p_InsertCity`(
    IN _country VARCHAR(50),
    IN _name TEXT,
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Cities';
    DECLARE _code INT;

    SET @s = CONCAT('SELECT COALESCE(MAX(`Code`), 0) + 1 FROM `', _tablename, '` INTO @code');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET _code = @code;

    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', _code, ', ', QUOTE(_country), ', ', QUOTE(_name), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'CIT');
END$$

CREATE PROCEDURE `p_UpdateCity`(
    IN _id VARCHAR(50),
    IN _country VARCHAR(50),
    IN _name TEXT,
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Cities';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `CountryID` = ', QUOTE(_country), ', `Name` = ', QUOTE(_name), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteCity`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_Cities';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_City`
BEFORE DELETE ON `cl_Cities`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_City`
BEFORE INSERT ON `cl_Cities`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_City`
BEFORE UPDATE ON `cl_Cities`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- LanguageRelations Table, Procs, Triggers
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_LanguageRelations` (
    `ID` VARCHAR(50) NOT NULL PRIMARY KEY,
    `LangID` VARCHAR(50) NOT NULL,
    `ReferenceID` VARCHAR(50) NOT NULL,
    `Label` TEXT NOT NULL,
    `IsActive` DATETIME,
    `Description` TEXT,
    CONSTRAINT `FK_LangRelations_Lang` FOREIGN KEY (`LangID`) REFERENCES `cl_Languages` (`ID`),
    CONSTRAINT `UQ_LanguageRelation` UNIQUE (`ReferenceID`, `LangID`)
);

DELIMITER $$

CREATE PROCEDURE `p_InsertLanguageRelation`(
    IN _langid VARCHAR(50),
    IN _referenceid VARCHAR(50),
    IN _label TEXT,
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_LanguageRelations';
    SET _sql = CONCAT('INSERT INTO `', _tablename, '` VALUES (%s, ', QUOTE(_langid), ', ', QUOTE(_referenceid), ', ', QUOTE(_label), ', NULL, ', QUOTE(IFNULL(_description, '')), ');');
    CALL `p_Query`(_sql, _tablename, 'LGR');
END$$

CREATE PROCEDURE `p_UpdateLanguageRelation`(
    IN _id VARCHAR(50),
    IN _label TEXT,
    IN _description TEXT
)
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_LanguageRelations';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `Label` = ', QUOTE(_label), ', `Description` = ', QUOTE(IFNULL(_description, '')), ' WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

CREATE PROCEDURE `p_DeleteLanguageRelation`(IN _id VARCHAR(50))
BEGIN
    DECLARE _sql TEXT;
    DECLARE _tablename VARCHAR(50) DEFAULT 'cl_LanguageRelations';
    SET _sql = CONCAT('UPDATE `', _tablename, '` SET `IsActive` = NOW() WHERE `ID` = ', QUOTE(_id), ';');
    CALL `p_Query`(_sql, NULL, NULL);
END$$

DELIMITER ;

CREATE TRIGGER `Delete_LanguageRelation`
BEFORE DELETE ON `cl_LanguageRelations`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_LanguageRelation`
BEFORE INSERT ON `cl_LanguageRelations`
FOR EACH ROW
    CALL `t_InsertTrigger`();

CREATE TRIGGER `Update_LanguageRelation`
BEFORE UPDATE ON `cl_LanguageRelations`
FOR EACH ROW
    CALL `t_UpdateTrigger`(OLD.`IsActive`);

-- ----------------------------
-- Auditing Section
-- ----------------------------

DELIMITER ;

CREATE TABLE `cl_Audits` (
    `ID` VARCHAR(50) PRIMARY KEY,
    `Action` VARCHAR(50) NOT NULL,
    `TableName` VARCHAR(50) NOT NULL,
    `RecordID` VARCHAR(50) NOT NULL,
    `ActionDate` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `Description` JSON NOT NULL
);

DELIMITER $$

CREATE PROCEDURE `p_InsertAudit`(
    IN _action VARCHAR(50),
    IN _tablename VARCHAR(50),
    IN _recordid VARCHAR(50),
    IN _description JSON
)
BEGIN
    DECLARE _id VARCHAR(50);
    DECLARE _threaded BOOLEAN;
    DECLARE _processed BOOLEAN;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        IF _processed = FALSE THEN CALL `p_IsProc`(FALSE); END IF;
        IF _threaded = FALSE THEN CALL `p_CurrentThread`(FALSE); END IF;
        RESIGNAL;
    END;

    SET _threaded = `f_CurrentThread`();
    IF _threaded = FALSE THEN CALL `p_CurrentThread`(TRUE); END IF;

    SET _processed = `f_IsProc`();
    IF _processed = FALSE THEN CALL `p_IsProc`(TRUE); END IF;

    SET _id = `f_CreateID`('AUD', 'cl_Audits');
    INSERT INTO `cl_Audits` VALUES (_id, _action, _tablename, _recordid, NOW(), _description);

    IF _processed = FALSE THEN CALL `p_IsProc`(FALSE); END IF;
    IF _threaded = FALSE THEN CALL `p_CurrentThread`(FALSE); END IF;
END$$

DELIMITER ;

-- ----------------------------
-- Initial Data Insertion
-- ----------------------------

CALL `p_InsertLanguage`('US', 'English (US)');
CALL `p_InsertLanguage`('GB', 'English (GB)');
CALL `p_InsertLanguage`('FR', 'Français');
CALL `p_InsertLanguage`('ES', 'Español');
CALL `p_InsertLanguage`('AR', 'عربي');

CALL `p_InsertContinent`('Africa', NULL);
CALL `p_InsertContinent`('Asia', NULL);
CALL `p_InsertContinent`('America', NULL);
CALL `p_InsertContinent`('Europe', NULL);
CALL `p_InsertContinent`('Oceania', NULL);

CALL `p_InsertCountry`(93, 'AF', 'AFG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Afghanistan', 'afghanistan.png', NULL);
CALL `p_InsertCountry`(355, 'AL', 'ALB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Albania', 'albania.png', NULL);
CALL `p_InsertCountry`(213, 'DZ', 'DZA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Algeria', 'algeria.png', NULL);
CALL `p_InsertCountry`(358, 'AX', 'ALA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Åland Islands', 'aland-islands.png', NULL);
CALL `p_InsertCountry`(1, 'AS', 'ASM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'American Samoa', 'american-samoa.png', NULL);
CALL `p_InsertCountry`(376, 'AD', 'AND', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Andorra', 'andorra.png', NULL);
CALL `p_InsertCountry`(244, 'AO', 'AGO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Angola', 'angola.png', NULL);
CALL `p_InsertCountry`(1, 'AI', 'AIA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Anguilla', 'anguilla.png', NULL);
CALL `p_InsertCountry`(1, 'AG', 'ATG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Antigua and Barbuda', 'antigua-and-barbuda.png', NULL);
CALL `p_InsertCountry`(54, 'AR', 'ARG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Argentina', 'argentina.png', NULL);
CALL `p_InsertCountry`(374, 'AM', 'ARM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Armenia', 'armenia.png', NULL);
CALL `p_InsertCountry`(297, 'AW', 'ABW', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Aruba', 'aruba.png', NULL);
CALL `p_InsertCountry`(61, 'AU', 'AUS', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Australia', 'australia.png', NULL);
CALL `p_InsertCountry`(43, 'AT', 'AUT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Austria', 'austria.png', NULL);
CALL `p_InsertCountry`(994, 'AZ', 'AZE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Azerbaijan', 'azerbaijan.png', NULL);
CALL `p_InsertCountry`(1, 'BS', 'BHS', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Bahamas', 'bahamas.png', NULL);
CALL `p_InsertCountry`(973, 'BH', 'BHR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Bahrain', 'bahrain.png', NULL);
CALL `p_InsertCountry`(880, 'BD', 'BGD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Bangladesh', 'bangladesh.png', NULL);
CALL `p_InsertCountry`(1, 'BB', 'BRB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Barbados', 'barbados.png', NULL);
CALL `p_InsertCountry`(375, 'BY', 'BLR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Belarus', 'belarus.png', NULL);
CALL `p_InsertCountry`(32, 'BE', 'BEL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Belgium', 'belgium.png', NULL);
CALL `p_InsertCountry`(501, 'BZ', 'BLZ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Belize', 'belize.png', NULL);
CALL `p_InsertCountry`(229, 'BJ', 'BEN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Benin', 'benin.png', NULL);
CALL `p_InsertCountry`(1, 'BM', 'BMU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Bermuda', 'bermuda.png', NULL);
CALL `p_InsertCountry`(975, 'BT', 'BTN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Bhutan', 'bhutan.png', NULL);
CALL `p_InsertCountry`(591, 'BO', 'BOL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Bolivia', 'bolivia.png', NULL);
CALL `p_InsertCountry`(387, 'BA', 'BIH', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Bosnia and Herzegovina', 'bosnia-and-herzegovina.png', NULL);
CALL `p_InsertCountry`(267, 'BW', 'BWA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Botswana', 'botswana.png', NULL);
CALL `p_InsertCountry`(55, 'BR', 'BRA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Brazil', 'brazil.png', NULL);
CALL `p_InsertCountry`(246, 'IO', 'IOT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'British Indian Ocean Territory', 'british-indian-ocean-territory.png', NULL);
CALL `p_InsertCountry`(1, 'VG', 'VGB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'British Virgin Islands', 'british-virgin-islands.png', NULL);
CALL `p_InsertCountry`(673, 'BN', 'BRN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Brunei', 'brunei.png', NULL);
CALL `p_InsertCountry`(359, 'BG', 'BGR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Bulgaria', 'bulgaria.png', NULL);
CALL `p_InsertCountry`(226, 'BF', 'BFA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Burkina Faso', 'burkina-faso.png', NULL);
CALL `p_InsertCountry`(257, 'BI', 'BDI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Burundi', 'burundi.png', NULL);
CALL `p_InsertCountry`(855, 'KH', 'KHM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Cambodia', 'cambodia.png', NULL);
CALL `p_InsertCountry`(237, 'CM', 'CMR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Cameroon', 'cameroon.png', NULL);
CALL `p_InsertCountry`(1, 'CA', 'CAN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Canada', 'canada.png', NULL);
CALL `p_InsertCountry`(238, 'CV', 'CPV', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Cape Verde', 'cape-verde.png', NULL);
CALL `p_InsertCountry`(599, 'BQ', 'BES', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Caribbean Netherlands', 'caribbean-netherlands.png', NULL);
CALL `p_InsertCountry`(1, 'KY', 'CYM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Cayman Islands', 'cayman-islands.png', NULL);
CALL `p_InsertCountry`(236, 'CF', 'CAF', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Central African Republic', 'central-african-republic.png', NULL);
CALL `p_InsertCountry`(235, 'TD', 'TCD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Chad', 'chad.png', NULL);
CALL `p_InsertCountry`(56, 'CL', 'CHL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Chile', 'chile.png', NULL);
CALL `p_InsertCountry`(86, 'CN', 'CHN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'China', 'china.png', NULL);
CALL `p_InsertCountry`(61, 'CX', 'CXR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Christmas Island', 'christmas-island.png', NULL);
CALL `p_InsertCountry`(61, 'CC', 'CCK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Cocos Islands', 'cocos-islands.png', NULL);
CALL `p_InsertCountry`(57, 'CO', 'COL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Colombia', 'colombia.png', NULL);
CALL `p_InsertCountry`(269, 'KM', 'COM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Comoros', 'comoros.png', NULL);
CALL `p_InsertCountry`(682, 'CK', 'COK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Cook Islands', 'cook-islands.png', NULL);
CALL `p_InsertCountry`(506, 'CR', 'CRI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Costa Rica', 'costa-rica.png', NULL);
CALL `p_InsertCountry`(385, 'HR', 'HRV', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Croatia', 'croatia.png', NULL);
CALL `p_InsertCountry`(53, 'CU', 'CUB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Cuba', 'cuba.png', NULL);
CALL `p_InsertCountry`(599, 'CW', 'CUW', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Curacao', 'curacao.png', NULL);
CALL `p_InsertCountry`(357, 'CY', 'CYP', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Cyprus', 'cyprus.png', NULL);
CALL `p_InsertCountry`(420, 'CZ', 'CZE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Czech Republic', 'czech-republic.png', NULL);
CALL `p_InsertCountry`(243, 'CD', 'COD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Democratic Republic of the Congo', 'democratic-republic-of-congo.png', NULL);
CALL `p_InsertCountry`(45, 'DK', 'DNK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Denmark', 'denmark.png', NULL);
CALL `p_InsertCountry`(253, 'DJ', 'DJI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Djibouti', 'djibouti.png', NULL);
CALL `p_InsertCountry`(1, 'DM', 'DMA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Dominica', 'dominica.png', NULL);
CALL `p_InsertCountry`(1, 'DO', 'DOM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Dominican Republic', 'dominican-republic.png', NULL);
CALL `p_InsertCountry`(670, 'TL', 'TLS', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'East Timor', 'east-timor.png', NULL);
CALL `p_InsertCountry`(593, 'EC', 'ECU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Ecuador', 'ecuador.png', NULL);
CALL `p_InsertCountry`(20, 'EG', 'EGY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Egypt', 'egypt.png', NULL);
CALL `p_InsertCountry`(503, 'SV', 'SLV', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'El Salvador', 'el-salvador.png', NULL);
CALL `p_InsertCountry`(240, 'GQ', 'GNQ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Equatorial Guinea', 'equatorial-guinea.png', NULL);
CALL `p_InsertCountry`(291, 'ER', 'ERI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Eritrea', 'eritrea.png', NULL);
CALL `p_InsertCountry`(372, 'EE', 'EST', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Estonia', 'estonia.png', NULL);
CALL `p_InsertCountry`(251, 'ET', 'ETH', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Ethiopia', 'ethiopia.png', NULL);
CALL `p_InsertCountry`(500, 'FK', 'FLK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Falkland Islands', 'falkland-islands.png', NULL);
CALL `p_InsertCountry`(298, 'FO', 'FRO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Faroe Islands', 'faroe-islands.png', NULL);
CALL `p_InsertCountry`(679, 'FJ', 'FJI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Fiji', 'fiji.png', NULL);
CALL `p_InsertCountry`(358, 'FI', 'FIN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Finland', 'finland.png', NULL);
CALL `p_InsertCountry`(33, 'FR', 'FRA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'France', 'france.png', NULL);
CALL `p_InsertCountry`(594, 'GF', 'GUF', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'French Guiana', 'france.png', NULL);
CALL `p_InsertCountry`(689, 'PF', 'PYF', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'French Polynesia', 'french-polynesia.png', NULL);
CALL `p_InsertCountry`(241, 'GA', 'GAB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Gabon', 'gabon.png', NULL);
CALL `p_InsertCountry`(220, 'GM', 'GMB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Gambia', 'gambia.png', NULL);
CALL `p_InsertCountry`(995, 'GE', 'GEO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Georgia', 'georgia.png', NULL);
CALL `p_InsertCountry`(49, 'DE', 'DEU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Germany', 'germany.png', NULL);
CALL `p_InsertCountry`(233, 'GH', 'GHA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Ghana', 'ghana.png', NULL);
CALL `p_InsertCountry`(350, 'GI', 'GIB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Gibraltar', 'gibraltar.png', NULL);
CALL `p_InsertCountry`(30, 'GR', 'GRC', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Greece', 'greece.png', NULL);
CALL `p_InsertCountry`(299, 'GL', 'GRL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Greenland', 'greenland.png', NULL);
CALL `p_InsertCountry`(1, 'GD', 'GRD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Grenada', 'grenada.png', NULL);
CALL `p_InsertCountry`(590, 'GP', 'GLP', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Guadeloupe', 'france.png', NULL);
CALL `p_InsertCountry`(1, 'GU', 'GUM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Guam', 'guam.png', NULL);
CALL `p_InsertCountry`(502, 'GT', 'GTM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Guatemala', 'guatemala.png', NULL);
CALL `p_InsertCountry`(44, 'GG', 'GGY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Guernsey', 'guernsey.png', NULL);
CALL `p_InsertCountry`(224, 'GN', 'GIN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Guinea', 'guinea.png', NULL);
CALL `p_InsertCountry`(245, 'GW', 'GNB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Guinea-Bissau', 'guinea-bissau.png', NULL);
CALL `p_InsertCountry`(592, 'GY', 'GUY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Guyana', 'guyana.png', NULL);
CALL `p_InsertCountry`(509, 'HT', 'HTI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Haiti', 'haiti.png', NULL);
CALL `p_InsertCountry`(504, 'HN', 'HND', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Honduras', 'honduras.png', NULL);
CALL `p_InsertCountry`(852, 'HK', 'HKG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Hong Kong', 'hong-kong.png', NULL);
CALL `p_InsertCountry`(36, 'HU', 'HUN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Hungary', 'hungary.png', NULL);
CALL `p_InsertCountry`(354, 'IS', 'ISL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Iceland', 'iceland.png', NULL);
CALL `p_InsertCountry`(91, 'IN', 'IND', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'India', 'india.png', NULL);
CALL `p_InsertCountry`(62, 'ID', 'IDN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Indonesia', 'indonesia.png', NULL);
CALL `p_InsertCountry`(98, 'IR', 'IRN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Iran', 'iran.png', NULL);
CALL `p_InsertCountry`(964, 'IQ', 'IRQ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Iraq', 'iraq.png', NULL);
CALL `p_InsertCountry`(353, 'IE', 'IRL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Ireland', 'ireland.png', NULL);
CALL `p_InsertCountry`(44, 'IM', 'IMN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Isle of Man', 'isle-of-man.png', NULL);
CALL `p_InsertCountry`(972, 'IL', 'ISR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Israel', 'israel.png', NULL);
CALL `p_InsertCountry`(39, 'IT', 'ITA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Italy', 'italy.png', NULL);
CALL `p_InsertCountry`(225, 'CI', 'CIV', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Ivory Coast', 'ivory-coast.png', NULL);
CALL `p_InsertCountry`(1, 'JM', 'JAM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Jamaica', 'jamaica.png', NULL);
CALL `p_InsertCountry`(81, 'JP', 'JPN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Japan', 'japan.png', NULL);
CALL `p_InsertCountry`(44, 'JE', 'JEY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Jersey', 'jersey.png', NULL);
CALL `p_InsertCountry`(962, 'JO', 'JOR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Jordan', 'jordan.png', NULL);
CALL `p_InsertCountry`(7, 'KZ', 'KAZ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Kazakhstan', 'kazakhstan.png', NULL);
CALL `p_InsertCountry`(254, 'KE', 'KEN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Kenya', 'kenya.png', NULL);
CALL `p_InsertCountry`(686, 'KI', 'KIR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Kiribati', 'kiribati.png', NULL);
CALL `p_InsertCountry`(383, 'XK', 'XKX', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Kosovo', 'kosovo.png', NULL);
CALL `p_InsertCountry`(965, 'KW', 'KWT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Kuwait', 'kuwait.png', NULL);
CALL `p_InsertCountry`(996, 'KG', 'KGZ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Kyrgyzstan', 'kyrgyzstan.png', NULL);
CALL `p_InsertCountry`(856, 'LA', 'LAO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Laos', 'laos.png', NULL);
CALL `p_InsertCountry`(371, 'LV', 'LVA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Latvia', 'latvia.png', NULL);
CALL `p_InsertCountry`(961, 'LB', 'LBN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Lebanon', 'lebanon.png', NULL);
CALL `p_InsertCountry`(266, 'LS', 'LSO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Lesotho', 'lesotho.png', NULL);
CALL `p_InsertCountry`(231, 'LR', 'LBR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Liberia', 'liberia.png', NULL);
CALL `p_InsertCountry`(218, 'LY', 'LBY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Libya', 'libya.png', NULL);
CALL `p_InsertCountry`(423, 'LI', 'LIE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Liechtenstein', 'liechtenstein.png', NULL);
CALL `p_InsertCountry`(370, 'LT', 'LTU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Lithuania', 'lithuania.png', NULL);
CALL `p_InsertCountry`(352, 'LU', 'LUX', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Luxembourg', 'luxembourg.png', NULL);
CALL `p_InsertCountry`(853, 'MO', 'MAC', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Macau', 'macau.png', NULL);
CALL `p_InsertCountry`(389, 'MK', 'MKD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Macedonia', 'republic-of-macedonia.png', NULL);
CALL `p_InsertCountry`(261, 'MG', 'MDG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Madagascar', 'madagascar.png', NULL);
CALL `p_InsertCountry`(265, 'MW', 'MWI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Malawi', 'malawi.png', NULL);
CALL `p_InsertCountry`(60, 'MY', 'MYS', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Malaysia', 'malaysia.png', NULL);
CALL `p_InsertCountry`(960, 'MV', 'MDV', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Maldives', 'maldives.png', NULL);
CALL `p_InsertCountry`(223, 'ML', 'MLI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Mali', 'mali.png', NULL);
CALL `p_InsertCountry`(356, 'MT', 'MLT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Malta', 'malta.png', NULL);
CALL `p_InsertCountry`(692, 'MH', 'MHL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Marshall Islands', 'marshall-islands.png', NULL);
CALL `p_InsertCountry`(596, 'MQ', 'MTQ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Martinique', 'martinique.png', NULL);
CALL `p_InsertCountry`(222, 'MR', 'MRT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Mauritania', 'mauritania.png', NULL);
CALL `p_InsertCountry`(230, 'MU', 'MUS', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Mauritius', 'mauritius.png', NULL);
CALL `p_InsertCountry`(262, 'YT', 'MYT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Mayotte', 'mayotte.png', NULL);
CALL `p_InsertCountry`(52, 'MX', 'MEX', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Mexico', 'mexico.png', NULL);
CALL `p_InsertCountry`(691, 'FM', 'FSM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Micronesia', 'micronesia.png', NULL);
CALL `p_InsertCountry`(373, 'MD', 'MDA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Moldova', 'moldova.png', NULL);
CALL `p_InsertCountry`(377, 'MC', 'MCO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Monaco', 'monaco.png', NULL);
CALL `p_InsertCountry`(976, 'MN', 'MNG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Mongolia', 'mongolia.png', NULL);
CALL `p_InsertCountry`(382, 'ME', 'MNE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Montenegro', 'montenegro.png', NULL);
CALL `p_InsertCountry`(1, 'MS', 'MSR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Montserrat', 'montserrat.png', NULL);
CALL `p_InsertCountry`(212, 'MA', 'MAR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Morocco', 'morocco.png', NULL);
CALL `p_InsertCountry`(258, 'MZ', 'MOZ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Mozambique', 'mozambique.png', NULL);
CALL `p_InsertCountry`(95, 'MM', 'MMR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Myanmar', 'myanmar.png', NULL);
CALL `p_InsertCountry`(264, 'NA', 'NAM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Namibia', 'namibia.png', NULL);
CALL `p_InsertCountry`(674, 'NR', 'NRU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Nauru', 'nauru.png', NULL);
CALL `p_InsertCountry`(977, 'NP', 'NPL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Nepal', 'nepal.png', NULL);
CALL `p_InsertCountry`(31, 'NL', 'NLD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Netherlands', 'netherlands.png', NULL);
CALL `p_InsertCountry`(687, 'NC', 'NCL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'New Caledonia', 'new-caledonia.png', NULL);
CALL `p_InsertCountry`(64, 'NZ', 'NZL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'New Zealand', 'new-zealand.png', NULL);
CALL `p_InsertCountry`(505, 'NI', 'NIC', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Nicaragua', 'nicaragua.png', NULL);
CALL `p_InsertCountry`(227, 'NE', 'NER', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Niger', 'niger.png', NULL);
CALL `p_InsertCountry`(234, 'NG', 'NGA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Nigeria', 'nigeria.png', NULL);
CALL `p_InsertCountry`(683, 'NU', 'NIU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Niue', 'niue.png', NULL);
CALL `p_InsertCountry`(672, 'NF', 'NFK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Norfolk Island', 'norfolk-island.png', NULL);
CALL `p_InsertCountry`(850, 'KP', 'PRK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'North Korea', 'north-korea.png', NULL);
CALL `p_InsertCountry`(1, 'MP', 'MNP', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Northern Mariana Islands', 'northern-mariana-islands.png', NULL);
CALL `p_InsertCountry`(47, 'NO', 'NOR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Norway', 'norway.png', NULL);
CALL `p_InsertCountry`(968, 'OM', 'OMN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Oman', 'oman.png', NULL);
CALL `p_InsertCountry`(92, 'PK', 'PAK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Pakistan', 'pakistan.png', NULL);
CALL `p_InsertCountry`(680, 'PW', 'PLW', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Palau', 'palau.png', NULL);
CALL `p_InsertCountry`(970, 'PS', 'PSE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Palestine', 'palestine.png', NULL);
CALL `p_InsertCountry`(507, 'PA', 'PAN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Panama', 'panama.png', NULL);
CALL `p_InsertCountry`(675, 'PG', 'PNG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Papua New Guinea', 'papua-new-guinea.png', NULL);
CALL `p_InsertCountry`(595, 'PY', 'PRY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Paraguay', 'paraguay.png', NULL);
CALL `p_InsertCountry`(51, 'PE', 'PER', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Peru', 'peru.png', NULL);
CALL `p_InsertCountry`(63, 'PH', 'PHL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Philippines', 'philippines.png', NULL);
CALL `p_InsertCountry`(64, 'PN', 'PCN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Pitcairn', 'pitcairn-islands.png', NULL);
CALL `p_InsertCountry`(48, 'PL', 'POL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Poland', 'poland.png', NULL);
CALL `p_InsertCountry`(351, 'PT', 'PRT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Portugal', 'portugal.png', NULL);
CALL `p_InsertCountry`(1, 'PR', 'PRI', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Puerto Rico', 'puerto-rico.png', NULL);
CALL `p_InsertCountry`(974, 'QA', 'QAT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Qatar', 'qatar.png', NULL);
CALL `p_InsertCountry`(242, 'CG', 'COG', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Republic of the Congo', 'republic-of-the-congo.png', NULL);
CALL `p_InsertCountry`(262, 'RE', 'REU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Reunion', 'reunion.png', NULL);
CALL `p_InsertCountry`(40, 'RO', 'ROU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Romania', 'romania.png', NULL);
CALL `p_InsertCountry`(7, 'RU', 'RUS', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Russia', 'russia.png', NULL);
CALL `p_InsertCountry`(250, 'RW', 'RWA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Rwanda', 'rwanda.png', NULL);
CALL `p_InsertCountry`(590, 'BL', 'BLM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Saint Barthelemy', 'saint-barthelemy.png', NULL);
CALL `p_InsertCountry`(290, 'SH', 'SHN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Saint Helena', 'saint-helena.png', NULL);
CALL `p_InsertCountry`(1, 'KN', 'KNA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Saint Kitts and Nevis', 'saint-kitts-and-nevis.png', NULL);
CALL `p_InsertCountry`(1, 'LC', 'LCA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Saint Lucia', 'saint-lucia.png', NULL);
CALL `p_InsertCountry`(590, 'MF', 'MAF', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Saint Martin', 'france.png', NULL);
CALL `p_InsertCountry`(508, 'PM', 'SPM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Saint Pierre and Miquelon', 'saint-pierre-and-miquelon.png', NULL);
CALL `p_InsertCountry`(1, 'VC', 'VCT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Saint Vincent and the Grenadines', 'saint-vincent-and-the-grenadines.png', NULL);
CALL `p_InsertCountry`(685, 'WS', 'WSM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Samoa', 'samoa.png', NULL);
CALL `p_InsertCountry`(378, 'SM', 'SMR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'San Marino', 'san-marino.png', NULL);
CALL `p_InsertCountry`(239, 'ST', 'STP', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Sao Tome and Principe', 'sao-tome-and-principe.png', NULL);
CALL `p_InsertCountry`(966, 'SA', 'SAU', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Saudi Arabia', 'saudi-arabia.png', NULL);
CALL `p_InsertCountry`(221, 'SN', 'SEN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Senegal', 'senegal.png', NULL);
CALL `p_InsertCountry`(381, 'RS', 'SRB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Serbia', 'serbia.png', NULL);
CALL `p_InsertCountry`(248, 'SC', 'SYC', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Seychelles', 'seychelles.png', NULL);
CALL `p_InsertCountry`(232, 'SL', 'SLE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Sierra Leone', 'sierra-leone.png', NULL);
CALL `p_InsertCountry`(65, 'SG', 'SGP', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Singapore', 'singapore.png', NULL);
CALL `p_InsertCountry`(1, 'SX', 'SXM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Sint Maarten', 'sint-maarten.png', NULL);
CALL `p_InsertCountry`(421, 'SK', 'SVK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Slovakia', 'slovakia.png', NULL);
CALL `p_InsertCountry`(386, 'SI', 'SVN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Slovenia', 'slovenia.png', NULL);
CALL `p_InsertCountry`(677, 'SB', 'SLB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Solomon Islands', 'solomon-islands.png', NULL);
CALL `p_InsertCountry`(252, 'SO', 'SOM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Somalia', 'somalia.png', NULL);
CALL `p_InsertCountry`(27, 'ZA', 'ZAF', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'South Africa', 'south-africa.png', NULL);
CALL `p_InsertCountry`(82, 'KR', 'KOR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'South Korea', 'south-korea.png', NULL);
CALL `p_InsertCountry`(211, 'SS', 'SSD', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'South Sudan', 'south-sudan.png', NULL);
CALL `p_InsertCountry`(34, 'ES', 'ESP', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Spain', 'spain.png', NULL);
CALL `p_InsertCountry`(94, 'LK', 'LKA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Sri Lanka', 'sri-lanka.png', NULL);
CALL `p_InsertCountry`(249, 'SD', 'SDN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Sudan', 'sudan.png', NULL);
CALL `p_InsertCountry`(597, 'SR', 'SUR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Suriname', 'suriname.png', NULL);
CALL `p_InsertCountry`(47, 'SJ', 'SJM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Svalbard and Jan Mayen', 'norway.png', NULL);
CALL `p_InsertCountry`(268, 'SZ', 'SWZ', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Swaziland', 'swaziland.png', NULL);
CALL `p_InsertCountry`(46, 'SE', 'SWE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Sweden', 'sweden.png', NULL);
CALL `p_InsertCountry`(41, 'CH', 'CHE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Switzerland', 'switzerland.png', NULL);
CALL `p_InsertCountry`(963, 'SY', 'SYR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Syria', 'syria.png', NULL);
CALL `p_InsertCountry`(886, 'TW', 'TWN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Taiwan', 'taiwan.png', NULL);
CALL `p_InsertCountry`(992, 'TJ', 'TJK', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Tajikistan', 'tajikistan.png', NULL);
CALL `p_InsertCountry`(255, 'TZ', 'TZA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Tanzania', 'tanzania.png', NULL);
CALL `p_InsertCountry`(66, 'TH', 'THA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Thailand', 'thailand.png', NULL);
CALL `p_InsertCountry`(228, 'TG', 'TGO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Togo', 'togo.png', NULL);
CALL `p_InsertCountry`(690, 'TK', 'TKL', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Tokelau', 'tokelau.png', NULL);
CALL `p_InsertCountry`(676, 'TO', 'TON', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Tonga', 'tonga.png', NULL);
CALL `p_InsertCountry`(1, 'TT', 'TTO', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Trinidad and Tobago', 'trinidad-and-tobago.png', NULL);
CALL `p_InsertCountry`(216, 'TN', 'TUN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Tunisia', 'tunisia.png', NULL);
CALL `p_InsertCountry`(90, 'TR', 'TUR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Turkey', 'turkey.png', NULL);
CALL `p_InsertCountry`(993, 'TM', 'TKM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Turkmenistan', 'turkmenistan.png', NULL);
CALL `p_InsertCountry`(1, 'TC', 'TCA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Turks and Caicos Islands', 'turks-and-caicos-islands.png', NULL);
CALL `p_InsertCountry`(688, 'TV', 'TUV', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Tuvalu', 'tuvalu.png', NULL);
CALL `p_InsertCountry`(1, 'VI', 'VIR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'U.S. Virgin Islands', 'virgin-islands.png', NULL);
CALL `p_InsertCountry`(256, 'UG', 'UGA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Uganda', 'uganda.png', NULL);
CALL `p_InsertCountry`(380, 'UA', 'UKR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Ukraine', 'ukraine.png', NULL);
CALL `p_InsertCountry`(971, 'AE', 'ARE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'United Arab Emirates', 'united-arab-emirates.png', NULL);
CALL `p_InsertCountry`(44, 'GB', 'GBR', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'United Kingdom', 'united-kingdom.png', NULL);
CALL `p_InsertCountry`(1, 'US', 'USA', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'United States', 'united-states.png', NULL);
CALL `p_InsertCountry`(598, 'UY', 'URY', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Uruguay', 'uruguay.png', NULL);
CALL `p_InsertCountry`(998, 'UZ', 'UZB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Uzbekistan', 'uzbekistan.png', NULL);
CALL `p_InsertCountry`(678, 'VU', 'VUT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Vanuatu', 'vanuatu.png', NULL);
CALL `p_InsertCountry`(379, 'VA', 'VAT', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Vatican', 'vatican.png', NULL);
CALL `p_InsertCountry`(58, 'VE', 'VEN', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Venezuela', 'venezuela.png', NULL);
CALL `p_InsertCountry`(84, 'VN', 'VNM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Vietnam', 'vietnam.png', NULL);
CALL `p_InsertCountry`(681, 'WF', 'WLF', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Wallis and Futuna', 'wallis-and-futuna.png', NULL);
CALL `p_InsertCountry`(212, 'EH', 'ESH', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Western Sahara', 'western-sahara.png', NULL);
CALL `p_InsertCountry`(967, 'YE', 'YEM', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Yemen', 'yemen.png', NULL);
CALL `p_InsertCountry`(260, 'ZM', 'ZMB', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Zambia', 'zambia.png', NULL);
CALL `p_InsertCountry`(263, 'ZW', 'ZWE', (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Zimbabwe', 'zimbabwe.png', NULL);

CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Maroua', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Garoua', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Ngaoundéré', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Yaoundé', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Douala', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Ebolowa', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Bafoussam', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Buéa', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Bamenda', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CMR'), 'Bertoua', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'CAF'), 'Bangui', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'COG'), 'Brazzaville', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'GAB'), 'Libreville', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'GNQ'), 'Malabo', NULL);
CALL `p_InsertCity`((SELECT `ID` FROM `cl_Countries` WHERE `ISO3` = 'TCD'), 'N''Djamena', NULL);

CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), 'en-US');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), 'en-US');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), 'en-US');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'ES'), (SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'ES'), 'en-US');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'AR'), (SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'AR'), 'en-US');

CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Africa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Africa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Africa'), 'Afrique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'America');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'America');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'America'), 'Amérique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Asia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Asia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Asia'), 'Asie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Europe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Europe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Europe'), 'Europe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Oceania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Oceania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oceania'), 'Oceanie');

CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Afghanistan'), 'Afghanistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Afghanistan'), 'Afghanistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Afghanistan'), 'Afghanistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Albania'), 'Albania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Albania'), 'Albania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Albania'), 'Albanie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Algeria'), 'Algeria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Algeria'), 'Algeria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Algeria'), 'Algérie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Åland Islands'), 'Åland Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Åland Islands'), 'Åland Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Åland Islands'), 'Les Iles Åland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'American Samoa'), 'American Samoa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'American Samoa'), 'American Samoa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'American Samoa'), 'Samoa Américaines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Andorra'), 'Andorra');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Andorra'), 'Andorra');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Andorra'), 'Andorres');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Angola'), 'Angola');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Angola'), 'Angola');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Angola'), 'Angola');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Anguilla'), 'Anguilla');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Anguilla'), 'Anguilla');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Anguilla'), 'Anguilla');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Antigua and Barbuda'), 'Antigua and Barbuda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Antigua and Barbuda'), 'Antigua and Barbuda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Antigua and Barbuda'), 'Antigua et Barbuda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Argentina'), 'Argentina');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Argentina'), 'Argentina');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Argentina'), 'Argentine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Armenia'), 'Armenia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Armenia'), 'Armenia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Armenia'), 'Arménie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Aruba'), 'Aruba');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Aruba'), 'Aruba');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Aruba'), 'Aruba');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Australia'), 'Australia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Australia'), 'Australia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Australia'), 'Australie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Austria'), 'Austria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Austria'), 'Austria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Austria'), 'Autriche');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Azerbaijan'), 'Azerbaijan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Azerbaijan'), 'Azerbaijan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Azerbaijan'), 'Azerbaijan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bahamas'), 'Bahamas');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bahamas'), 'Bahamas');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bahamas'), 'Bahamas');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bahrain'), 'Bahrain');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bahrain'), 'Bahrain');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bahrain'), 'Bahrein');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bangladesh'), 'Bangladesh');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bangladesh'), 'Bangladesh');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bangladesh'), 'Bangladesh');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Barbados'), 'Barbados');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Barbados'), 'Barbados');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Barbados'), 'Barbades');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belarus'), 'Belarus');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belarus'), 'Belarus');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belarus'), 'Biélorussie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belgium'), 'Belgium');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belgium'), 'Belgium');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belgium'), 'Belgique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belize'), 'Belize');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belize'), 'Belize');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Belize'), 'Bélize');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Benin'), 'Benin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Benin'), 'Benin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Benin'), 'Bénin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bermuda'), 'Bermuda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bermuda'), 'Bermuda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bermuda'), 'Bermudes');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bhutan'), 'Bhutan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bhutan'), 'Bhutan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bhutan'), 'Bhoutan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bolivia'), 'Bolivia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bolivia'), 'Bolivia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bolivia'), 'Bolivie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bosnia and Herzegovina'), 'Bosnia and Herzegovina');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bosnia and Herzegovina'), 'Bosnia and Herzegovina');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bosnia and Herzegovina'), 'Bosnie Herzégovine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Botswana'), 'Botswana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Botswana'), 'Botswana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Botswana'), 'Botswana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Brazil'), 'Brazil');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Brazil'), 'Brazil');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Brazil'), 'Brésil');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'British Indian Ocean Territory'), 'British Indian Ocean Territory');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'British Indian Ocean Territory'), 'British Indian Ocean Territory');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'British Indian Ocean Territory'), 'Territoire britannique de l''océan Indien');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'British Virgin Islands'), 'British Virgin Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'British Virgin Islands'), 'British Virgin Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'British Virgin Islands'), 'Îles Vierges britanniques');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Brunei'), 'Brunei');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Brunei'), 'Brunei');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Brunei'), 'Brunei');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bulgaria'), 'Bulgaria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bulgaria'), 'Bulgaria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Bulgaria'), 'Bulgarie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Burkina Faso'), 'Burkina Faso');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Burkina Faso'), 'Burkina Faso');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Burkina Faso'), 'Burkina Faso');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Burundi'), 'Burundi');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Burundi'), 'Burundi');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Burundi'), 'Burundi');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cambodia'), 'Cambodia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cambodia'), 'Cambodia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cambodia'), 'Cambodge');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cameroon'), 'Cameroon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cameroon'), 'Cameroon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cameroon'), 'Cameroun');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Canada'), 'Canada');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Canada'), 'Canada');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Canada'), 'Canada');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cape Verde'), 'Cape Verde');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cape Verde'), 'Cape Verde');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cape Verde'), 'Cap Vert');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Caribbean Netherlands'), 'Caribbean Netherlands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Caribbean Netherlands'), 'Caribbean Netherlands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Caribbean Netherlands'), 'Pays-Bas caribéens');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cayman Islands'), 'Cayman Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cayman Islands'), 'Cayman Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cayman Islands'), 'Îles Caïmans');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Central African Republic'), 'Central African Republic');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Central African Republic'), 'Central African Republic');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Central African Republic'), 'République Centrafricaine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Chad'), 'Chad');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Chad'), 'Chad');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Chad'), 'Tchad');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Chile'), 'Chile');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Chile'), 'Chile');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Chile'), 'Chili');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'China'), 'China');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'China'), 'China');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'China'), 'Chine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Christmas Island'), 'Christmas Island');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Christmas Island'), 'Christmas Island');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Christmas Island'), 'Ile Christmas');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cocos Islands'), 'Cocos Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cocos Islands'), 'Cocos Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cocos Islands'), 'Îles Cocos');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Colombia'), 'Colombia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Colombia'), 'Colombia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Colombia'), 'Colombie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Comoros'), 'Comoros');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Comoros'), 'Comoros');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Comoros'), 'Comores');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cook Islands'), 'Cook Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cook Islands'), 'Cook Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cook Islands'), 'Iles Cook');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Costa Rica'), 'Costa Rica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Costa Rica'), 'Costa Rica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Costa Rica'), 'Costa Rica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Croatia'), 'Croatia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Croatia'), 'Croatia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Croatia'), 'Croatie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cuba'), 'Cuba');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cuba'), 'Cuba');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cuba'), 'Cuba');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Curacao'), 'Curacao');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Curacao'), 'Curacao');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Curacao'), 'Curacao');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cyprus'), 'Cyprus');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cyprus'), 'Cyprus');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Cyprus'), 'Chypre');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Czech Republic'), 'Czech Republic');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Czech Republic'), 'Czech Republic');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Czech Republic'), 'République Tchèque');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Democratic Republic of the Congo'), 'Democratic Republic of the Congo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Democratic Republic of the Congo'), 'Democratic Republic of the Congo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Democratic Republic of the Congo'), 'République Démocratique du Congo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Denmark'), 'Denmark');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Denmark'), 'Denmark');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Denmark'), 'Danemark');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Djibouti'), 'Djibouti');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Djibouti'), 'Djibouti');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Djibouti'), 'Djibouti');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Dominica'), 'Dominica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Dominica'), 'Dominica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Dominica'), 'Dominique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Dominican Republic'), 'Dominican Republic');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Dominican Republic'), 'Dominican Republic');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Dominican Republic'), 'République Dominicaine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'East Timor'), 'East Timor');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'East Timor'), 'East Timor');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'East Timor'), 'Timor Oriental');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ecuador'), 'Ecuador');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ecuador'), 'Ecuador');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ecuador'), 'Equateur');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Egypt'), 'Egypt');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Egypt'), 'Egypt');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Egypt'), 'Egypte');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'El Salvador'), 'El Salvador');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'El Salvador'), 'El Salvador');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'El Salvador'), 'Le Salvador');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Equatorial Guinea'), 'Equatorial Guinea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Equatorial Guinea'), 'Equatorial Guinea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Equatorial Guinea'), 'Guinée Équatoriale');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Eritrea'), 'Eritrea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Eritrea'), 'Eritrea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Eritrea'), 'Érythrée');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Estonia'), 'Estonia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Estonia'), 'Estonia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Estonia'), 'Estonie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ethiopia'), 'Ethiopia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ethiopia'), 'Ethiopia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ethiopia'), 'Ethiopie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Falkland Islands'), 'Falkland Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Falkland Islands'), 'Falkland Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Falkland Islands'), 'Iles Falkland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Faroe Islands'), 'Faroe Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Faroe Islands'), 'Faroe Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Faroe Islands'), 'Îles Féroé');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Fiji'), 'Fiji');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Fiji'), 'Fiji');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Fiji'), 'Fiji');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Finland'), 'Finland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Finland'), 'Finland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Finland'), 'Finlande');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'France'), 'France');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'France'), 'France');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'France'), 'France');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'French Guiana'), 'French Guiana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'French Guiana'), 'French Guiana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'French Guiana'), 'Guyane Française');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'French Polynesia'), 'French Polynesia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'French Polynesia'), 'French Polynesia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'French Polynesia'), 'Polynésie française');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gabon'), 'Gabon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gabon'), 'Gabon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gabon'), 'Gabon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gambia'), 'Gambia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gambia'), 'Gambia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gambia'), 'Gambie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Georgia'), 'Georgia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Georgia'), 'Georgia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Georgia'), 'Géorgie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Germany'), 'Germany');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Germany'), 'Germany');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Germany'), 'Allemagne');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ghana'), 'Ghana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ghana'), 'Ghana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ghana'), 'Ghana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gibraltar'), 'Gibraltar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gibraltar'), 'Gibraltar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Gibraltar'), 'Gibraltar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Greece'), 'Greece');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Greece'), 'Greece');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Greece'), 'Grèce');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Greenland'), 'Greenland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Greenland'), 'Greenland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Greenland'), 'Groenland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Grenada'), 'Grenada');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Grenada'), 'Grenada');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Grenada'), 'Grenade');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guadeloupe'), 'Guadeloupe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guadeloupe'), 'Guadeloupe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guadeloupe'), 'Guadeloupe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guam'), 'Guam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guam'), 'Guam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guam'), 'Guam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guatemala'), 'Guatemala');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guatemala'), 'Guatemala');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guatemala'), 'Guatemala');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guernsey'), 'Guernsey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guernsey'), 'Guernsey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guernsey'), 'Guernesey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guinea'), 'Guinea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guinea'), 'Guinea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guinea'), 'Guinée');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guinea-Bissau'), 'Guinea-Bissau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guinea-Bissau'), 'Guinea-Bissau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guinea-Bissau'), 'Guinée-Bissau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guyana'), 'Guyana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guyana'), 'Guyana');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Guyana'), 'Guyane');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Haiti'), 'Haiti');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Haiti'), 'Haiti');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Haiti'), 'Haiti');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Honduras'), 'Honduras');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Honduras'), 'Honduras');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Honduras'), 'Honduras');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Hong Kong'), 'Hong Kong');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Hong Kong'), 'Hong Kong');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Hong Kong'), 'Hong Kong');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Hungary'), 'Hungary');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Hungary'), 'Hungary');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Hungary'), 'Hongrie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iceland'), 'Iceland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iceland'), 'Iceland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iceland'), 'Islande');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'India'), 'India');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'India'), 'India');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'India'), 'Indie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Indonesia'), 'Indonesia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Indonesia'), 'Indonesia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Indonesia'), 'Indonésie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iran'), 'Iran');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iran'), 'Iran');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iran'), 'Iran');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iraq'), 'Iraq');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iraq'), 'Iraq');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Iraq'), 'Iraq');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ireland'), 'Ireland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ireland'), 'Ireland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ireland'), 'Irelande');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Isle of Man'), 'Isle of Man');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Isle of Man'), 'Isle of Man');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Isle of Man'), 'île de Man');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Israel'), 'Israel');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Israel'), 'Israel');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Israel'), 'Israël');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Italy'), 'Italy');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Italy'), 'Italy');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Italy'), 'Italie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ivory Coast'), 'Ivory Coast');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ivory Coast'), 'Ivory Coast');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ivory Coast'), 'Côte d''Ivoire');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jamaica'), 'Jamaica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jamaica'), 'Jamaica');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jamaica'), 'Jamaique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Japan'), 'Japan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Japan'), 'Japan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Japan'), 'Japan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jersey'), 'Jersey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jersey'), 'Jersey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jersey'), 'Jersey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jordan'), 'Jordan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jordan'), 'Jordan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Jordan'), 'Jordanie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kazakhstan'), 'Kazakhstan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kazakhstan'), 'Kazakhstan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kazakhstan'), 'Kazakhstan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kenya'), 'Kenya');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kenya'), 'Kenya');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kenya'), 'Kenya');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kiribati'), 'Kiribati');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kiribati'), 'Kiribati');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kiribati'), 'Kiribati');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kosovo'), 'Kosovo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kosovo'), 'Kosovo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kosovo'), 'Kosovo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kuwait'), 'Kuwait');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kuwait'), 'Kuwait');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kuwait'), 'Koweit');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kyrgyzstan'), 'Kyrgyzstan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kyrgyzstan'), 'Kyrgyzstan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Kyrgyzstan'), 'Kirghizistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Laos'), 'Laos');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Laos'), 'Laos');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Laos'), 'Laos');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Latvia'), 'Latvia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Latvia'), 'Latvia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Latvia'), 'Lettonie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lebanon'), 'Lebanon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lebanon'), 'Lebanon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lebanon'), 'Liban');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lesotho'), 'Lesotho');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lesotho'), 'Lesotho');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lesotho'), 'Lesotho');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Liberia'), 'Liberia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Liberia'), 'Liberia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Liberia'), 'Libéria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Libya'), 'Libya');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Libya'), 'Libya');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Libya'), 'Libye');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Liechtenstein'), 'Liechtenstein');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Liechtenstein'), 'Liechtenstein');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Liechtenstein'), 'Liechtenstein');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lithuania'), 'Lithuania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lithuania'), 'Lithuania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Lithuania'), 'Lithuanie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Luxembourg'), 'Luxembourg');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Luxembourg'), 'Luxembourg');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Luxembourg'), 'Luxembourg');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Macau'), 'Macau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Macau'), 'Macau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Macau'), 'Macau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Macedonia'), 'Macedonia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Macedonia'), 'Macedonia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Macedonia'), 'Macédoine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Madagascar'), 'Madagascar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Madagascar'), 'Madagascar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Madagascar'), 'Madagascar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malawi'), 'Malawi');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malawi'), 'Malawi');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malawi'), 'Malawi');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malaysia'), 'Malaysia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malaysia'), 'Malaysia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malaysia'), 'Malaisie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Maldives'), 'Maldives');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Maldives'), 'Maldives');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Maldives'), 'Maldives');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mali'), 'Mali');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mali'), 'Mali');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mali'), 'Mali');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malta'), 'Malta');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malta'), 'Malta');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Malta'), 'Malte');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Marshall Islands'), 'Marshall Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Marshall Islands'), 'Marshall Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Marshall Islands'), 'Iles Marshall');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Martinique'), 'Martinique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Martinique'), 'Martinique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Martinique'), 'Martinique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mauritania'), 'Mauritania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mauritania'), 'Mauritania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mauritania'), 'Mauritanie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mauritius'), 'Mauritius');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mauritius'), 'Mauritius');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mauritius'), 'Ile Maurice');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mayotte'), 'Mayotte');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mayotte'), 'Mayotte');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mayotte'), 'Mayotte');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mexico'), 'Mexico');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mexico'), 'Mexico');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mexico'), 'Mexique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Micronesia'), 'Micronesia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Micronesia'), 'Micronesia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Micronesia'), 'Micronésie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Moldova'), 'Moldova');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Moldova'), 'Moldova');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Moldova'), 'Moldavie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Monaco'), 'Monaco');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Monaco'), 'Monaco');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Monaco'), 'Monaco');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mongolia'), 'Mongolia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mongolia'), 'Mongolia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mongolia'), 'Mongolie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Montenegro'), 'Montenegro');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Montenegro'), 'Montenegro');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Montenegro'), 'Monténégro');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Montserrat'), 'Montserrat');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Montserrat'), 'Montserrat');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Montserrat'), 'Montserrat');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Morocco'), 'Morocco');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Morocco'), 'Morocco');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Morocco'), 'Maroc');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mozambique'), 'Mozambique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mozambique'), 'Mozambique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Mozambique'), 'Mozambique');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Myanmar'), 'Myanmar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Myanmar'), 'Myanmar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Myanmar'), 'Myanmar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Namibia'), 'Namibia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Namibia'), 'Namibia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Namibia'), 'Namibie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nauru'), 'Nauru');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nauru'), 'Nauru');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nauru'), 'Nauru');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nepal'), 'Nepal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nepal'), 'Nepal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nepal'), 'Népal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Netherlands'), 'Netherlands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Netherlands'), 'Netherlands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Netherlands'), 'Pays-Bas');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'New Caledonia'), 'New Caledonia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'New Caledonia'), 'New Caledonia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'New Caledonia'), 'Nouvelle Calédonie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'ew Zealand'), 'New Zealand');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'ew Zealand'), 'New Zealand');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'ew Zealand'), 'Nouvelle-Zélande');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nicaragua'), 'Nicaragua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nicaragua'), 'Nicaragua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nicaragua'), 'Nicaragua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Niger'), 'Niger');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Niger'), 'Niger');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Niger'), 'Niger');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nigeria'), 'Nigeria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nigeria'), 'Nigeria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Nigeria'), 'Nigéria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Niue'), 'Niue');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Niue'), 'Niue');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Niue'), 'Niué');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Norfolk Island'), 'Norfolk Island');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Norfolk Island'), 'Norfolk Island');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Norfolk Island'), 'Ile Norfolk');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'North Korea'), 'North Korea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'North Korea'), 'North Korea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'North Korea'), 'Corée du Nord');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Northern Mariana Islands'), 'Northern Mariana Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Northern Mariana Islands'), 'Northern Mariana Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Northern Mariana Islands'), 'Îles Mariannes du Nord');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Norway'), 'Norway');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Norway'), 'Norway');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Norway'), 'Norvège');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oman'), 'Oman');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oman'), 'Oman');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Oman'), 'Oman');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Pakistan'), 'Pakistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Pakistan'), 'Pakistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Pakistan'), 'Pakistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Palau'), 'Palau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Palau'), 'Palau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Palau'), 'Palau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Palestine'), 'Palestine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Palestine'), 'Palestine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Palestine'), 'Palestine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Panama'), 'Panama');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Panama'), 'Panama');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Panama'), 'Panama');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Papua New Guinea'), 'Papua New Guinea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Papua New Guinea'), 'Papua New Guinea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Papua New Guinea'), 'Papouasie Nouvelle Guinée');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Paraguay'), 'Paraguay');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Paraguay'), 'Paraguay');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Paraguay'), 'Paraguay');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Peru'), 'Peru');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Peru'), 'Peru');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Peru'), 'Peru');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Philippines'), 'Philippines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Philippines'), 'Philippines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Philippines'), 'Philippines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Pitcairn'), 'Pitcairn');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Pitcairn'), 'Pitcairn');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Pitcairn'), 'Pitcairn');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Poland'), 'Poland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Poland'), 'Poland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Poland'), 'Pologne');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Portugal'), 'Portugal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Portugal'), 'Portugal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Portugal'), 'Portugal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Puerto Rico'), 'Puerto Rico');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Puerto Rico'), 'Puerto Rico');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Puerto Rico'), 'Porto Rico');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Qatar'), 'Qatar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Qatar'), 'Qatar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Qatar'), 'Qatar');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Republic of the Congo'), 'Republic of the Congo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Republic of the Congo'), 'Republic of the Congo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Republic of the Congo'), 'République du Congo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Reunion'), 'Reunion');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Reunion'), 'Reunion');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Reunion'), 'Réunion');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Romania'), 'Romania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Romania'), 'Romania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Romania'), 'Roumanie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Russia'), 'Russia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Russia'), 'Russia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Russia'), 'Russie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Rwanda'), 'Rwanda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Rwanda'), 'Rwanda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Rwanda'), 'Rwanda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Barthelemy'), 'Saint Barthelemy');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Barthelemy'), 'Saint Barthelemy');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Barthelemy'), 'Saint Barthélemy');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Helena'), 'Saint Helena');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Helena'), 'Saint Helena');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Helena'), 'Sainte Hélène');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Kitts and Nevis'), 'Saint Kitts and Nevis');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Kitts and Nevis'), 'Saint Kitts and Nevis');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Kitts and Nevis'), 'Saint-Christophe-et-Niévès');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Lucia'), 'Saint Lucia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Lucia'), 'Saint Lucia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Lucia'), 'Sainte Lucie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Martin'), 'Saint Martin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Martin'), 'Saint Martin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Martin'), 'Saint Martin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Pierre and Miquelon'), 'Saint Pierre and Miquelon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Pierre and Miquelon'), 'Saint Pierre and Miquelon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Pierre and Miquelon'), 'Saint Pierre et Miquélon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Vincent and the Grenadines'), 'Saint Vincent and the Grenadines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Vincent and the Grenadines'), 'Saint Vincent and the Grenadines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saint Vincent and the Grenadines'), 'Saint Vincent et les Grenadines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Samoa'), 'Samoa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Samoa'), 'Samoa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Samoa'), 'Samoa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'San Marino'), 'San Marino');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'San Marino'), 'San Marino');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'San Marino'), 'Saint Marin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sao Tome and Principe'), 'Sao Tome and Principe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sao Tome and Principe'), 'Sao Tome and Principe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sao Tome and Principe'), 'Sao Tomé et Principe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saudi Arabia'), 'Saudi Arabia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saudi Arabia'), 'Saudi Arabia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Saudi Arabia'), 'Arabie Saoudite');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Senegal'), 'Senegal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Senegal'), 'Senegal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Senegal'), 'Sénégal');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Serbia'), 'Serbia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Serbia'), 'Serbia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Serbia'), 'Serbie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Seychelles'), 'Seychelles');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Seychelles'), 'Seychelles');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Seychelles'), 'Seychelles');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sierra Leone'), 'Sierra Leone');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sierra Leone'), 'Sierra Leone');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sierra Leone'), 'Sierra Leone');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Singapore'), 'Singapore');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Singapore'), 'Singapore');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Singapore'), 'Singapour');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sint Maarten'), 'Sint Maarten');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sint Maarten'), 'Sint Maarten');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sint Maarten'), 'Saint-Martin');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Slovakia'), 'Slovakia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Slovakia'), 'Slovakia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Slovakia'), 'Slovaquie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Slovenia'), 'Slovenia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Slovenia'), 'Slovenia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Slovenia'), 'Slovénie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Solomon Islands'), 'Solomon Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Solomon Islands'), 'Solomon Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Solomon Islands'), 'Les îles Salomon');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Somalia'), 'Somalia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Somalia'), 'Somalia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Somalia'), 'Somalie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Africa'), 'South Africa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Africa'), 'South Africa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Africa'), 'Afrique du Sud');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Korea'), 'South Korea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Korea'), 'South Korea');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Korea'), 'Corée du Sud');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Sudan'), 'South Sudan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Sudan'), 'South Sudan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'South Sudan'), 'Soudan du sud');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Spain'), 'Spain');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Spain'), 'Spain');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Spain'), 'Espagne');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sri Lanka'), 'Sri Lanka');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sri Lanka'), 'Sri Lanka');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sri Lanka'), 'Sri Lanka');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sudan'), 'Sudan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sudan'), 'Sudan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sudan'), 'Soudan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Suriname'), 'Suriname');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Suriname'), 'Suriname');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Suriname'), 'Suriname');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Svalbard and Jan Mayen'), 'Svalbard and Jan Mayen');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Svalbard and Jan Mayen'), 'Svalbard and Jan Mayen');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Svalbard and Jan Mayen'), 'Svalbard et Jan Mayen');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Swaziland'), 'Swaziland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Swaziland'), 'Swaziland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Swaziland'), 'Swaziland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sweden'), 'Sweden');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sweden'), 'Sweden');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Sweden'), 'Suède');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Switzerland'), 'Switzerland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Switzerland'), 'Switzerland');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Switzerland'), 'Suisse');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Syria'), 'Syria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Syria'), 'Syria');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Syria'), 'Syrie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Taiwan'), 'Taiwan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Taiwan'), 'Taiwan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Taiwan'), 'Taiwan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tajikistan'), 'Tajikistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tajikistan'), 'Tajikistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tajikistan'), 'Tajikistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tanzania'), 'Tanzania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tanzania'), 'Tanzania');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tanzania'), 'Tanzanie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Thailand'), 'Thailand');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Thailand'), 'Thailand');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Thailand'), 'Thaïlande');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Togo'), 'Togo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Togo'), 'Togo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Togo'), 'Togo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tokelau'), 'Tokelau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tokelau'), 'Tokelau');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tokelau'), 'Tokélaou');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tonga'), 'Tonga');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tonga'), 'Tonga');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tonga'), 'Tonga');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Trinidad and Tobago'), 'Trinidad and Tobago');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Trinidad and Tobago'), 'Trinidad and Tobago');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Trinidad and Tobago'), 'Trinité-et-Tobago');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tunisia'), 'Tunisia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tunisia'), 'Tunisia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tunisia'), 'Tunisie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turkey'), 'Turkey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turkey'), 'Turkey');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turkey'), 'Turquie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turkmenistan'), 'Turkmenistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turkmenistan'), 'Turkmenistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turkmenistan'), 'Turkmenistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turks and Caicos Islands'), 'Turks and Caicos Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turks and Caicos Islands'), 'Turks and Caicos Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Turks and Caicos Islands'), 'îles Turques-et-Caïques');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tuvalu'), 'Tuvalu');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tuvalu'), 'Tuvalu');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Tuvalu'), 'Tuvalu');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'U.S. Virgin Islands'), 'U.S. Virgin Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'U.S. Virgin Islands'), 'U.S. Virgin Islands');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'U.S. Virgin Islands'), 'Îles Vierges Américaines');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uganda'), 'Uganda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uganda'), 'Uganda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uganda'), 'Ouganda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ukraine'), 'Ukraine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ukraine'), 'Ukraine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Ukraine'), 'Ukraine');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United Arab Emirates'), 'United Arab Emirates');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United Arab Emirates'), 'United Arab Emirates');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United Arab Emirates'), 'Emirats Arabes Unis');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United Kingdom'), 'United Kingdom');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United Kingdom'), 'United Kingdom');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United Kingdom'), 'Royaume-Uni');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United States'), 'United States');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United States'), 'United States');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'United States'), 'États-Unis');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uruguay'), 'Uruguay');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uruguay'), 'Uruguay');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uruguay'), 'Uruguay');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uzbekistan'), 'Uzbekistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uzbekistan'), 'Uzbekistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Uzbekistan'), 'Uzbekistan');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vanuatu'), 'Vanuatu');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vanuatu'), 'Vanuatu');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vanuatu'), 'Vanuatu');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vatican'), 'Vatican');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vatican'), 'Vatican');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vatican'), 'Vatican');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Venezuela'), 'Venezuela');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Venezuela'), 'Venezuela');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Venezuela'), 'Vénézuéla');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vietnam'), 'Vietnam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vietnam'), 'Vietnam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Vietnam'), 'Vietnam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Wallis and Futuna'), 'Wallis and Futuna');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Wallis and Futuna'), 'Wallis and Futuna');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Wallis and Futuna'), 'Wallis et Futuna');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Western Sahara'), 'Western Sahara');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Western Sahara'), 'Western Sahara');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Western Sahara'), 'Sahara Occidental');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Yemen'), 'Yemen');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Yemen'), 'Yemen');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Yemen'), 'Yémen');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Zambia'), 'Zambia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Zambia'), 'Zambia');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Zambia'), 'Zambie');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Zimbabwe'), 'Zimbabwe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Zimbabwe'), 'Zimbabwe');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Continents` WHERE `Name` = 'Zimbabwe'), 'Zimbabwé');

CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'YDE'), 'Yaounde');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'YDE'), 'Yaounde');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'YDE'), 'Yaoundé');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'DLA'), 'Douala');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'DLA'), 'Douala');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'DLA'), 'Douala');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'MVR'), 'Maroua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'MVR'), 'Maroua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'MVR'), 'Maroua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'GOU'), 'Garoua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'GOU'), 'Garoua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'GOU'), 'Garoua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'NGE'), 'Ngaoundéré');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'NGE'), 'Ngaoundéré');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'NGE'), 'Ngaoundéré');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'EBA'), 'Ebolowa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'EBA'), 'Ebolowa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'EBA'), 'Ebolowa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BFM'), 'Bafoussam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BFM'), 'Bafoussam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BFM'), 'Bafoussam');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BUA'), 'Buéa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BUA'), 'Buéa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BUA'), 'Buéa');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BDA'), 'Bamenda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BDA'), 'Bamenda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BDA'), 'Bamenda');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BTA'), 'Bertoua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BTA'), 'Bertoua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BTA'), 'Bertoua');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BGF'), 'Bangui');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BGF'), 'Bangui');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BGF'), 'Bangui');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BZV'), 'Brazzaville');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BZV'), 'Brazzaville');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'BZV'), 'Brazzaville');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'LBV'), 'Libreville');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'LBV'), 'Libreville');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'LBV'), 'Libreville');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'SSG'), 'Malabo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'SSG'), 'Malabo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'SSG'), 'Malabo');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'US'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'NDJ'), 'N''Djamena');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'GB'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'NDJ'), 'N''Djamena');
CALL `p_InsertLanguageRelation`((SELECT `ID` FROM `cl_Languages` WHERE `Label` = 'FR'), (SELECT `ID` FROM `cl_Cities` WHERE `Name` = 'NDJ'), 'N''Djamena');

-- ----------------------------
-- Final Triggers
-- ----------------------------
DELIMITER $$

CREATE TRIGGER `BeforeDelete_Audit`
BEFORE DELETE ON `cl_Audits`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `BeforeUpdate_Audit`
BEFORE UPDATE ON `cl_Audits`
FOR EACH ROW
    CALL `t_DeleteTrigger`();

CREATE TRIGGER `Insert_Audit`
BEFORE INSERT ON `cl_Audits`
FOR EACH ROW
    CALL `t_InsertTrigger`();

-- ----------------------------
-- Audit Log Triggers
-- ----------------------------

-- Log Parameters

CREATE TRIGGER `Log_Parameters_INSERT` AFTER INSERT ON `cl_Parameters` FOR EACH ROW
BEGIN
    IF (`f_Auditable`(NEW.`ID`) = TRUE) THEN
        CALL `p_InsertAudit`('INSERT', 'cl_Parameters', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'ParamName', NEW.ParamName, 'ParamUValue', NEW.ParamUValue, 'ParamValue', NEW.ParamValue, 'OwnerApp', NEW.OwnerApp, 'ParamLock', NEW.ParamLock, 'Auditable', NEW.Auditable, 'IsActive', NEW.IsActive));
    END IF;
END$$

CREATE TRIGGER `Log_Parameters_UPDATE` AFTER UPDATE ON `cl_Parameters` FOR EACH ROW
BEGIN
    IF (`f_Auditable`(NEW.`ID`) = TRUE) THEN
        CALL `p_InsertAudit`(
            CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
            'cl_Parameters',
            OLD.`ID`,
            JSON_OBJECT(
                'before', JSON_OBJECT('ID', OLD.ID, 'ParamName', OLD.ParamName, 'ParamUValue', OLD.ParamUValue, 'ParamValue', OLD.ParamValue, 'OwnerApp', OLD.OwnerApp, 'ParamLock', OLD.ParamLock, 'Auditable', OLD.Auditable, 'IsActive', OLD.IsActive),
                'after', JSON_OBJECT('ID', NEW.ID, 'ParamName', NEW.ParamName, 'ParamUValue', NEW.ParamUValue, 'ParamValue', NEW.ParamValue, 'OwnerApp', NEW.OwnerApp, 'ParamLock', NEW.ParamLock, 'Auditable', NEW.Auditable, 'IsActive', NEW.IsActive)
            )
        );
    END IF;
END$$

CREATE TRIGGER `Log_Parameters_DELETE` AFTER DELETE ON `cl_Parameters` FOR EACH ROW
BEGIN
    IF (`f_Auditable`(OLD.`ID`) = TRUE) THEN
        CALL `p_InsertAudit`('DELETE', 'cl_Parameters', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'ParamName', OLD.ParamName, 'ParamUValue', OLD.ParamUValue, 'ParamValue', OLD.ParamValue, 'OwnerApp', OLD.OwnerApp, 'ParamLock', OLD.ParamLock, 'Auditable', OLD.Auditable, 'IsActive', OLD.IsActive));
    END IF;
END$$

-- Log Languages

CREATE TRIGGER `Log_Languages_INSERT` AFTER INSERT ON `cl_Languages` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_Languages', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Label', NEW.Label, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_Languages_UPDATE` AFTER UPDATE ON `cl_Languages` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_Languages',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Label', OLD.Label, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Label', NEW.Label, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_Languages_DELETE` AFTER DELETE ON `cl_Languages` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_Languages', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Label', OLD.Label, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log AppCategories

CREATE TRIGGER `Log_AppCategories_INSERT` AFTER INSERT ON `cl_AppCategories` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_AppCategories', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_AppCategories_UPDATE` AFTER UPDATE ON `cl_AppCategories` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_AppCategories',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_AppCategories_DELETE` AFTER DELETE ON `cl_AppCategories` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_AppCategories', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log Apps

CREATE TRIGGER `Log_Apps_INSERT` AFTER INSERT ON `cl_Apps` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_Apps', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_Apps_UPDATE` AFTER UPDATE ON `cl_Apps` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_Apps',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_Apps_DELETE` AFTER DELETE ON `cl_Apps` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_Apps', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log AppRelations

CREATE TRIGGER `Log_AppRelations_INSERT` AFTER INSERT ON `cl_AppRelations` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_AppRelations', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'AppID', NEW.AppID, 'AppCategoryID', NEW.AppCategoryID, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_AppRelations_UPDATE` AFTER UPDATE ON `cl_AppRelations` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_AppRelations',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'AppID', OLD.AppID, 'AppCategoryID', OLD.AppCategoryID, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'AppID', NEW.AppID, 'AppCategoryID', NEW.AppCategoryID, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_AppRelations_DELETE` AFTER DELETE ON `cl_AppRelations` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_AppRelations', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'AppID', OLD.AppID, 'AppCategoryID', OLD.AppCategoryID, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log Continents

CREATE TRIGGER `Log_Continents_INSERT` AFTER INSERT ON `cl_Continents` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_Continents', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_Continents_UPDATE` AFTER UPDATE ON `cl_Continents` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_Continents',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_Continents_DELETE` AFTER DELETE ON `cl_Continents` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_Continents', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log Countries

CREATE TRIGGER `Log_Countries_INSERT` AFTER INSERT ON `cl_Countries` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_Countries', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'ISO2', NEW.ISO2, 'ISO3', NEW.ISO3, 'ContinentID', NEW.ContinentID, 'Name', NEW.Name, 'Flag', NEW.Flag, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_Countries_UPDATE` AFTER UPDATE ON `cl_Countries` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_Countries',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'ISO2', OLD.ISO2, 'ISO3', OLD.ISO3, 'ContinentID', OLD.ContinentID, 'Name', OLD.Name, 'Flag', OLD.Flag, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'ISO2', NEW.ISO2, 'ISO3', NEW.ISO3, 'ContinentID', NEW.ContinentID, 'Name', NEW.Name, 'Flag', NEW.Flag, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_Countries_DELETE` AFTER DELETE ON `cl_Countries` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_Countries', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'ISO2', OLD.ISO2, 'ISO3', OLD.ISO3, 'ContinentID', OLD.ContinentID, 'Name', OLD.Name, 'Flag', OLD.Flag, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log Cities

CREATE TRIGGER `Log_Cities_INSERT` AFTER INSERT ON `cl_Cities` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_Cities', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'CountryID', NEW.CountryID, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_Cities_UPDATE` AFTER UPDATE ON `cl_Cities` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_Cities',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'CountryID', OLD.CountryID, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'Code', NEW.Code, 'CountryID', NEW.CountryID, 'Name', NEW.Name, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_Cities_DELETE` AFTER DELETE ON `cl_Cities` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_Cities', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'Code', OLD.Code, 'CountryID', OLD.CountryID, 'Name', OLD.Name, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

-- Log LanguageRelations

CREATE TRIGGER `Log_LanguageRelations_INSERT` AFTER INSERT ON `cl_LanguageRelations` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('INSERT', 'cl_LanguageRelations', NEW.`ID`, JSON_OBJECT('ID', NEW.ID, 'LangID', NEW.LangID, 'ReferenceID', NEW.ReferenceID, 'Label', NEW.Label, 'IsActive', NEW.IsActive, 'Description', NEW.Description));
END$$

CREATE TRIGGER `Log_LanguageRelations_UPDATE` AFTER UPDATE ON `cl_LanguageRelations` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`(
        CASE WHEN NEW.`IsActive` IS NOT NULL THEN 'DEACTIVATE' ELSE 'UPDATE' END,
        'cl_LanguageRelations',
        OLD.`ID`,
        JSON_OBJECT(
            'before', JSON_OBJECT('ID', OLD.ID, 'LangID', OLD.LangID, 'ReferenceID', OLD.ReferenceID, 'Label', OLD.Label, 'IsActive', OLD.IsActive, 'Description', OLD.Description),
            'after', JSON_OBJECT('ID', NEW.ID, 'LangID', NEW.LangID, 'ReferenceID', NEW.ReferenceID, 'Label', NEW.Label, 'IsActive', NEW.IsActive, 'Description', NEW.Description)
        )
    );
END$$

CREATE TRIGGER `Log_LanguageRelations_DELETE` AFTER DELETE ON `cl_LanguageRelations` FOR EACH ROW
BEGIN
    CALL `p_InsertAudit`('DELETE', 'cl_LanguageRelations', OLD.`ID`, JSON_OBJECT('ID', OLD.ID, 'LangID', OLD.LangID, 'ReferenceID', OLD.ReferenceID, 'Label', OLD.Label, 'IsActive', OLD.IsActive, 'Description', OLD.Description));
END$$

DELIMITER ;
