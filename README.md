# vcoemap
Kartentool VCÖ 2020+

Damit du das Tool - so wie es ist - auf einem Server zum Laufen bringen kannst, benötigst du noch Tabellen mit folgender Struktur.... (siehe DLL weiter unten)

Die passenden Daten findest du hier...

https://www.data.gv.at/katalog/dataset/bev_verwaltungsgrenzenstichtagsdaten150000

(Die Verwaltungsgrenzen liegen hier im SHP-Format vor und können mit ogr2ogr (GDAL) bzw. QGIS in mysql-spezifische räumliche Daten umgewandelt werden. (Das räumliche Referenzsystem muss mit dem Referenzsystem übereinstimmen, dass auch Leaflet verwendet.)

zusätzlich: um Koordinaten den jeweiligen Verwaltungseinheiten zuordnen zu können...(Daten auf dem Adressregister)

https://www.bev.gv.at/portal/page?_pageid=713,2601271&_dad=portal&_schema=PORTAL

Beides ist frei verfügbar.

Noch was: Es gibt ein File: config_template.php... Das ist eine Vorlage für ein File das dann in einer funktionierenden Anwendung config.php heißen muss und mit den jeweilig zutreffenden Konfigurationseinstellungen (Mailaccountzugangsdaten, Datenbankzugangsdaten u.sw.) befüllt sein muss. Es gibt jeweils eine Konfiguration für eine Entwicklungsumgebung und eine für eine Produktionsumgebung. Welche von beiden verwendet werden soll wird ebenfalls im config.php-File eingestellt 'dev' bzw. 'prod'.
-- MySQL dump 10.16 Distrib 10.1.30-MariaDB, for Win32 (AMD64)

-- Host: localhost Database: problemstellen

-- Server version 10.1.30-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT /; /!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS /; /!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION /; /!40101 SET NAMES utf8 /; /!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE /; /!40103 SET TIME_ZONE='+00:00' /; /!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 /; /!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 /; /!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' /; /!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
-- -- Table structure for table bezirke

DROP TABLE IF EXISTS bezirke; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE bezirke ( id int(11) NOT NULL AUTO_INCREMENT, bkz varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, bezirk varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; /!40101 SET character_set_client = @saved_cs_client */;
-- -- Table structure for table borders

DROP TABLE IF EXISTS borders; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE borders ( OGR_FID int(11) NOT NULL AUTO_INCREMENT, SHAPE geometry NOT NULL, st_kz decimal(6,0) DEFAULT NULL, fl decimal(12,0) DEFAULT NULL, meridian decimal(6,0) DEFAULT NULL, gkz decimal(10,0) DEFAULT NULL, bkz decimal(6,0) DEFAULT NULL, kg_nr varchar(6) DEFAULT NULL, kg varchar(50) DEFAULT NULL, gb_kz varchar(3) DEFAULT NULL, gb varchar(50) DEFAULT NULL, bl varchar(50) DEFAULT NULL, st varchar(50) DEFAULT NULL, UNIQUE KEY OGR_FID (OGR_FID), SPATIAL KEY SHAPE (SHAPE) ) ENGINE=MyISAM AUTO_INCREMENT=7851 DEFAULT CHARSET=utf8; /!40101 SET character_set_client = @saved_cs_client */;
-- -- Table structure for table entries

DROP TABLE IF EXISTS entries; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE entries ( entryid int(10) unsigned NOT NULL AUTO_INCREMENT, title varchar(250) DEFAULT NULL, body text, email varchar(100) DEFAULT NULL, lon double NOT NULL, lat double NOT NULL, EPSG varchar(40) NOT NULL, notification_type varchar(40) DEFAULT NULL, filepath text NOT NULL, hashed_email varchar(32) DEFAULT NULL, marked_del int(11) NOT NULL DEFAULT '1', created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, plz varchar(6) DEFAULT NULL, terms_of_use tinyint(1) DEFAULT NULL, gemeinde varchar(100) DEFAULT NULL, bezirk varchar(100) NOT NULL, bundesland varchar(100) DEFAULT NULL, vorname varchar(50) NOT NULL, nachname varchar(50) NOT NULL, PRIMARY KEY (entryid) ) ENGINE=InnoDB AUTO_INCREMENT=378 DEFAULT CHARSET=utf8; /!40101 SET character_set_client = @saved_cs_client */;
-- -- Table structure for table gemeinden

DROP TABLE IF EXISTS gemeinden; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE gemeinden ( id int(11) NOT NULL AUTO_INCREMENT, gkz varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, gemeindename varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB AUTO_INCREMENT=2096 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; /!40101 SET character_set_client = @saved_cs_client */;
-- -- Table structure for table katastralgemeinden

DROP TABLE IF EXISTS katastralgemeinden; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE katastralgemeinden ( id int(11) NOT NULL, KG_NR varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, KG_NAME varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, GKZ varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, GEMEINDENAME varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, PB_NUMMER varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, PB_NAME varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, GB_NUMMER varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, GB_NAME varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, BL_NUMMER varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, BL_NAME varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; /!40101 SET character_set_client = @saved_cs_client */;
-- -- Table structure for table kgwien

DROP TABLE IF EXISTS kgwien; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE kgwien ( id int(11) NOT NULL AUTO_INCREMENT, kg varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, kgkz varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL, gembzk_name1 varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, gembzk_name2 varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; /!40101 SET character_set_client = @saved_cs_client */;
-- -- Table structure for table plz

DROP TABLE IF EXISTS plz; /*!40101 SET @saved_cs_client = @@character_set_client /; /!40101 SET character_set_client = utf8 /; CREATE TABLE plz ( id int(11) NOT NULL, plz varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL, bestimmungsort varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL, okz varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL, ortschaft varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL, gemnr varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL, gemname varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; /!40101 SET character_set_client = @saved_cs_client /; /!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE /; /!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS /; /!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS /; /!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT /; /!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS /; /!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION /; /!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-19 10:34:55
