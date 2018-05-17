<?php
const queries = [

    'CREATE TABLE tblStop (
        intStopId INT NOT NULL AUTO_INCREMENT,
        strStopName varchar(50) NOT NULL,
        intPointId INT NOT NULL,
        PRIMARY KEY (intStopId)
    );',

    'CREATE TABLE tblPoint (
        intPointId INT NOT NULL AUTO_INCREMENT,
        floatPointLat DECIMAL(15, 9) NOT NULL,
        floatPointLon DECIMAL(15, 9) NOT NULL,
        PRIMARY KEY (intPointId)
    );',

    'CREATE TABLE tblRoute (
        intRouteId INT NOT NULL AUTO_INCREMENT,
        intRouteNum INT NOT NULL,
        strRouteType varchar(50) NOT NULL,
        strRouteMapUrl varchar(256) NOT NULL,
        timeRouteStartWeekOne TIME NOT NULL,
        timeRouteEndWeekOne TIME NOT NULL,
        timeRouteStartWeekTwo TIME NOT NULL,
        timeRouteEndWeekTwo TIME NOT NULL,
        timeRouteStartSatOne TIME NOT NULL,
        timeRouteEndSatOne TIME NOT NULL,
        timeRouteStartSatTwo TIME NOT NULL,
        timeRouteEndSatTwo TIME NOT NULL,
        timeRouteStartSunOne TIME NOT NULL,
        timeRouteEndSunOne TIME NOT NULL,
        timeRouteStartSunTwo TIME NOT NULL,
        timeRouteEndSunTwo TIME NOT NULL,
        intRouteIntervalLeftWeek INT NOT NULL,
        intRouteIntervalRightWeek INT NOT NULL,
        intRouteIntervalLeftSat INT NOT NULL,
        intRouteIntervalRightSat INT NOT NULL,
        intRouteIntervalLeftSun INT NOT NULL,
        intRouteIntervalRightSun INT NOT NULL,
        PRIMARY KEY (intRouteId)
    );',

    'CREATE TABLE tblStopRoute (
        intStopRouteId INT NOT NULL AUTO_INCREMENT,
        intStopId INT NOT NULL,
        intRouteId INT NOT NULL,
        intStopNum INT NOT NULL,
        boolDirection BOOLEAN NOT NULL,
        PRIMARY KEY (intStopRouteId)
    );',

    'CREATE TABLE tblPointRoute (
        intPointRouteId INT NOT NULL AUTO_INCREMENT,
        intPointId INT NOT NULL,
        intRouteId INT NOT NULL,
        intPointNum INT NOT NULL,
        boolDirection BOOLEAN NOT NULL,
        PRIMARY KEY (intPointRouteId)
    );',

    'ALTER TABLE tblStop ADD CONSTRAINT tblStop_fk0 FOREIGN KEY (intPointId) REFERENCES tblPoint(intPointId);',

    'ALTER TABLE tblStopRoute ADD CONSTRAINT tblStopRoute_fk0 FOREIGN KEY (intStopId) REFERENCES tblStop(intStopId);',

    'ALTER TABLE tblStopRoute ADD CONSTRAINT tblStopRoute_fk1 FOREIGN KEY (intRouteId) REFERENCES tblRoute(intRouteId);',

    'ALTER TABLE tblPointRoute ADD CONSTRAINT tblPointRoute_fk0 FOREIGN KEY (intPointId) REFERENCES tblPoint(intPointId);',

    'ALTER TABLE tblPointRoute ADD CONSTRAINT tblPointRoute_fk1 FOREIGN KEY (intRouteId) REFERENCES tblRoute(intRouteId);'

];

for ($i = 0; $i < count (queries); $i++) {
  mysqli_query ($db, queries[$i]);
}

?>
