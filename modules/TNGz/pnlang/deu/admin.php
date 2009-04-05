<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 * @package TNGz
 * @url http://code.zikula.org/tngz
 * @license http://www.gnu.org/copyleft/gpl.html
 *
 * @author Wendel Voigt
 * @translator Sven Schomacker aka hilope
 * @version $Id$
 */

 define("_TNGZNAME"          , "TNGz");
 define('_TNGZUPDATECONFIG'  , 'TNGz-Konfiguration aktualisieren');
 define("_TNGZMODULESTATUS"  , "Konfigurationsstatus");
 define("_TNGZCONFIGTIP"     , "Alles in Ordnung bei grün");
 define("_TNGZMODULE"        , "TNG-Installationsverzeichnis");
 define("_TNGZGUESTACCESS"   , "Gastzugriff");
 define("_TNGZGUEST"         , "Sollen unregistrierte Benutzer Zugriff auf TNG bekommen?");
 define("_TNGZGUESTTIP"      , "Wenn aktiviert, sollte der Zugriff in der TNG-Konfiguration überprüft werden. Jeder nicht angemeldete Benutzer wird sonst als aktueller benutzer erkannt.");
 define("_TNGZGUESTNAME"     , "  - Benutzername für den Gastzugriff");
 define("_TNGZGUESTDEFAULT"  , "Gast");
 define("_TNGZUSERS"         , "Erstelle TNG-Benutzer aus den Zikula-Daten");
 define("_TNGZUSERSTIP"      , "Wenn aktiviert, wird bei erstmaligem Zugriff auf TNG dort ein Benutzer mit dem gleichen Namen erstellt.");

 define("_TNGZFOUND"         , "TNG-Dateien gefunden");
 define("_TNGZFOUNDNOT"      , "TNG-Dateien NICHT gefunden");

 define("_TNGZROOTPATHGOOD"  , "TNG-Hauptverzeichnis ist korrekt!");
 define("_TNGZROOTPATHBAD"   , "TNG-Hauptverzeichnis sollte lauten: ");

 define("_TNGZLIVING"        , "  - Erlaube erstelltem Benutzer lebende Personen anzusehen");
 define("_TNGZGEDCOM"        , "  - Erlaube erstelltem Benutzer GEDCOMs herunterzuladen");
 define("_TNGZLDS"           , "  - Erlaube erstelltem Benutzer LDS-Informationen anzusehen");

 define("_TNGZSYNCUSERS"     , "Zikula und TNG Benutzer-Synchronisation");
 define("_TNGZSYNC"          , "  - Halte TNG-Benutzerinformationen aktuell mit veränderten Zikula-Benutzerinformationen");
 define("_TNGZSYNC2"         , "    (inkl. Änderungen an echtem Namen, Website und E-Mail-Adresse)");

 define("_TNGZ_EMAIL"        , "Aktiviere E-Mail-Verschleierung (wenn solche Adressen in den TNG-Dateien vorhanden sind.)");
 define("_TNGZ_EMAIL_NO"     , "Nein (schneller)");
 define("_TNGZ_EMAIL_ENCODE" , "Ja");
 define("_TNGZ_EMAIL_ALL"    , "Ja, und mache die E-Mail-Adressen anklickbar");

 define("_TNGZVERSIONSAME"   , "TNGz ist synchron mit TNG");
 define("_TNGVERSIONUNKNOWN" , "Unbekannt");
 define("_TNGZVERSIONUPDATE" , "Bitte ein Update durchführen. Es wurde eine neue TNG-Version gefunden. Geändert von ");
 define("_TNGZVERSIONNEW"    , "Es ist eine neue Version verfügbar");
 define("_TNGZVERSIONLATEST" , "Dies ist die neueste empfohlene Version");


 define("_TNGZSETTINGS"      , "Einstellungen");
 define("_TNGZOTHERSETTINGS" , "Andere Einstellungen");
 define("_TNGCONFG"          , "TNG-Administration");
 define("_TNGZINSTRUCT"      , "Installationsanweisungen");
 define("_TNGZDEBUGINFO"     , "Informationen/Changelog");

 define("_TNGZDEBUG"         , "Debug-Information");
 define("_TNGZDEBUGMSG"      , "Informationen zum Lösen von Problemen. Könnte bei der Meldung von Fehlern hilfreich sein.");
 define("_TNGZCHANGELOG"     , "Changelog");
 define("_TNGZREADME"        , "Detaillierte Readme-Datei");

 define("_TNGZ_HOMEPAGE"     , "Use TNGz's homepage instead of TNG's index page"); // NEEDS TRANSLATION