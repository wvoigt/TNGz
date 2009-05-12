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

/*********** Common ***********/
define("_TNGZ_PREFIX"   , "index.php?module=TNGz&func=main&show");
define('_USECACHE'      , 'Block zwischenspeichern (wenn pnRender-Zwischenspeicher aktiviert ist)');
define('_PEOPLEDBFERROR', 'Fehler beim Zugriff auf die TNG-Tabellen.');
define('_SELECTYES'     , 'Ja');
define('_SELECTNO'      , 'Nein');

/*********** User ***********/
// common defines
define("_TNGZ_SHOWTOP"     , "Zeige Top ");
define("_TNGZ_BYOCCURRENCE", " nach Häufigkeit sortiert");
define("_TNGZ_GO"          , "Zeige");
define("_TNGZ_SORTEDALPHA" , " alphabetisch sortiert");

// places item
define("_TNGZ_PLACETITLE"    , "Top Orte");
define("_TNGZ_TOPPLACES"     , "Top Orte");
define("_TNGZ_TOTALPLACES"   , "Orte gesamt");
define("_TNGZ_SHOWALLPLACES" , "Zeige alle Orte");
define("_TNGZ_MAINPLACESPAGE", "Hauptansicht der Orte");

// surnames item
define("_TNGZ_SURNAMETITLE"    , "Top Nachnamen");
define("_TNGZ_TOP"             , "Top");
define("_TNGZ_SURNAMES"        , "Nachnamen");
define("_TNGZ_OUTOF"           , "von");
define("_TNGZ_TOTALSURNAMES"   , "eindeutigen Nachnamen gesamt");
define("_TNGZ_SHOWALLSURNAMES" , "Alle Nachnamen anzeigen");
define("_TNGZ_MAINSURNAMESPAGE", "Hauptansicht der Nachnamen");

/*********** RandomPhoto ***********/
define('_SHOWLIVING',     'Zeige Fotos der Personen');
define('_SELECTLIVINGY',  'Ja, zeige alle');
define('_SELECTLIVINGN',  'Nein');
define('_SELECTLIVINGD',  'Ja, aber keine lebenden Personen');
define('_SELECTLIVINGL',  'Ja, aber zeige lebende Personen nur eingeloggten Benutzern');
define('_SHOWPHOTO',      'Welcher Typ von Foto soll benutzt werden');
define('_SELECTPHOTOT',   'Thumbnail der Fotos');
define('_SELECTPHOTOP',   'Aktuelles Foto');
define('_MAXHEIGHT',      'Maximale Höhe der Fotos');
define('_MAXWIDTH',       'Maximale Breite der Fotos');
define('_TNGPHOTO',       'Fotograf');
define('_PEOPLEDBFERROR', 'Fehler im Zugriff auf TNG-Tabelle.');
define('_NOPHOTOFOUND',   'Kein Foto gefunden');
define('_TNGPHOTOLIST',   'Limitiere Fotos auf die folgenden TNG-mediaID-Nummer (standard = leer)');

/*********** ThisDay ***********/
define('_SHOWDATE',       'Zeige heutiges Datum :');
define('_SHOWBIRTH',      'Zeige Geburtstage :');
define('_SHOWMARRIAGE',   'Zeige Hochzeitstage :');
define('_SHOWDEATH',      'Zeige Todestage :');
define('_SHOWORDER',      'Sortiere Einträge nach :');
define('_SHOWWIKI',       'Zeige Wikipedia-Link für aktuellen Tag :');
define('_SELECTBIRTHY',   'Ja, zeige alles');
define('_SELECTBIRTHN',   'Nein');
define('_SELECTBIRTHD',   'Ja, aber keine lebenden Personen');
define('_SELECTBIRTHL',   'Ja, aber lebende Personen nur eingeloggten Benutzern zeigen');
define('_SELECTMARRIAGEY','Ja, zeige alle');
define('_SELECTMARRIAGEN','Nein');
define('_SELECTMARRIAGED','Ja, aber keine lebenden Personen');
define('_SELECTMARRIAGEL','Ja, aber lebende Personen nur eingeloggten Benutzern zeigen');
define('_SELECTDEATHY',   'Ja');
define('_SELECTDEATHN',   'Nein');
define('_SELECTORDERN',   'Nachname, Vorname');
define('_SELECTORDERD',   'Datum des Eintrages - ältester zu neuestem');
define('_SELECTORDERR',   'Datum des Eintrages - neuester zu ältestem');
define('_SELECTYES',      'Ja');
define('_SELECTNO',       'Nein');
define('_PEOPLEDBFERROR', 'Fehler im Zugriff auf TNG-Tabelle. ');
define('_BIRTH',          'Geburtstage');
define('_MARRIAGE',       'Hochzeitstage');
define('_MARRIAGE_AND',   'und');
define('_DIVORCED',       'D');
define('_DEATH',          'Todestage');
define('_ACCESSTNG',      'Ahnenforschungsseite');
define('_NONETNG',        'Niemand für heute gefunden');
define('_WIKITODAY',      'Gehe zu Wikipedia');

/*********** WhatsNew ***********/
define('_TNGZ_WHATSNEW_RECENT',      'neu seit ');
define('_TNGZ_WHATSNEW_DAYS',        'Tagen');
define('_TNGZ_WHATSNEW_HEAD_PEOPLE', 'Personen');
define('_TNGZ_WHATSNEW_PEOPLE',      'Zeige neue oder geänderte Personen');
define('_TNGZ_WHATSNEW_HEAD_FAMILY', 'Familien');
define('_TNGZ_WHATSNEW_FAMILY',      'Zeige neue oder geänderte Familien');
define('_TNGZ_WHATSNEW_HEAD_PHOTOS', 'Fotos');
define('_TNGZ_WHATSNEW_PHOTOS',      'Zeige neue oder geänderte Fotos');
define('_TNGZ_WHATSNEW_HEAD_HISTORY','Historien');
define('_TNGZ_WHATSNEW_HISTORY',     'Zeige neue oder geänderte Historien');
define('_TNGZ_WHATSNEW_HOWMANY',     'Maximale Anzahl der Einträge in Darstellung');
define('_TNGZ_WHATSNEW_HOWMANY_NUM', '10');
define('_TNGZ_WHATSNEW_HOWLONG',     'Maximale Anzahl der Tage in Darstellung');
define('_TNGZ_WHATSNEW_HOWLONG_NUM', '30');
define('_TNGZ_WHATSNEW_YES',         'Ja');
define('_TNGZ_WHATSNEW_NO',          'Nein');
define('_TNGZ_WHATSNEW_NOCHANGES',   'Keine neuen oder geänderten Einträge');
define('_TNGZ_WHATSNEW_SQLERROR',    'Fehler im Datenbankzugriff.');
define('_ACCESSTNG',                 'Ahnenforschungsseite');
define('_NONETNG',                   'Niemand für heute gefunden');
define('_PEOPLEDBFERROR',            'Fehler im Zugriff auf TNG-Tabelle. ');
define('_SELECTYES',                 'Ja');
define('_SELECTNO',                  'Nein');
define('_SHOWORDER',                 'Zeige Einträge nach :');
define('_SELECTORDERN',              'Nachname');
define('_SELECTORDERD',              'Neuestem zu Frühestem');
define('_SELECTORDERR',              'Frühestem zu Neuestem');
define('_SELECTORDERE',              'Eingegebene Reihenfolge');
define('_SHOWFAMILYNAME',            'Zeige Familien nach :');
define('_SELECTFAMILYFULL',          'Voller Name');
define('_SELECTFAMILYSHORT',         'nur Nachnamen');

/*********** MostWanted ***********/
define('_WANTEDINSTRUCTIONS', '<strong>Unten stehende Einträge sind optional.</strong><br />Leere Einträge werden nicht in die Ausgabe übergeben.<br />IDs, welche nicht im richtigen Format sind, nicht in TNG vorhanden sind oder Fehler beinhalten werden in die Ausgabe übernommen.');
define('_WANTEDTEXT',         'Beschreibung im oberen Teil des Blocks (kann HTML enthalten)');
define('_WANTEDPEOPLELABEL',  'Titel der Personenliste (z.B. Personen)');
define('_WANTEDPEOPLELIST',   'Liste der PersonIDs (z.B. I001, I002)');
define('_WANTEDFAMILYLABEL',  'Titel der Familienliste (z.B. Families)');
define('_WANTEDFAMILYLIST',   'Liste der FamilyIDs (z.B. F001, F002)');
define('_WANTEDMENULINK',     'Zeige TNG-Menülink unten?');
define('_MARRIAGE_AND',       'und');
define('_MARRIED_ABR',        'm.');
define('_DIVORCED_ABR',       'D');

/*********** Surnames and Places ***********/
define('_TNGZ_SURNAMES_INSTRUCTIONS', 'Bitte Anzahl der Nachnamen, Anzeigetyp und Sortierung angeben. Sortierung nach Rang oder alphabetisch möglich.');
define('_TNGZ_PLACES_INSTRUCTIONS',   'Bitte Anzahl der Orte, Anzeigetyp und Sortierung angeben. Sortierung nach Rang oder alphabetisch möglich.');
define('_TNGZ_TEXT',                  'Optionaler Text im oberen Bereich des Blocks (darf HTML enthalten)');
define('_TNGZ_TOP',                   'Anzahl der Top-Einträge');
define('_TNGZ_SORTED',                'Zeige Einträge');
define('_TNGZ_COLS',                  'Anzahl der Spalten (bei Tabellenbenutzung)');
define('_TNGZ_TYPE',                  'Zeige als');
define('_TNGZ_CLOUD',                 'Nachnamen-Wolke');
define('_TNGZ_TABLE',                 'Tabelle');
define('_TNGZ_LIST',                  'Liste');
define('_TNGZ_FORM_SHOWTOP',           'Zeige Top');
define('_TNGZ_FORM_ORDERBYOCC',        'sortiert nach Vorkommen');
define('_TNGZ_FORM_GO',                'Start');
define('_TNGZ_SURNAMES_LINK_SURNAMES_ALL', 'Zeige Nachnamen alphabetisch');
define('_TNGZ_SURNAMES_LINK_SURNAMES', 'Hauptseite der Nachnamen');
define('_TNGZ_PLACES_LINK_PLACES_ALL', 'Zeige Orte alphabetisch');
define('_TNGZ_PLACES_LINK_PLACES',     'Hauptseite der Orte');

/*********** NameSearch ***********/
define('_TNGZ_NAMESEARCH_SEARCH',        'Suche');
define('_TNGZ_NAMESEARCH_LASTNAME',      'Nachname');
define('_TNGZ_NAMESEARCH_FIRSTNAME',     'Vorname');
define('_TNGZ_NAMESEARCH_ADVANCED',      'Erweiterte Suche');

/*********** Search ***********/
define('_TNGZ_SEARCH_LIVING',    'Die folgenden Personen sind als lebend gekennzeichnet und wurden deshalb ausgeblendet.');
define('_TNGZ_SEARCH',           'Ahnenforschungssuche');
define('_TNGZ_SEARCH_BORN',      'geb.');
define('_TNGZ_SEARCH_DIED',      'gest.');
define('_TNGZ_SEARCH_RESULTS',   'Personen gefunden');
define('_TNGZ_SEARCH_NONEFOUND', 'Niemand gefunden');

/*********** admin ***********/
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

define("_TNGZPWHASHOK"      , "Password-Hashmethode von Zikula ist MD5.");
define("_TNGZPWHASHNOTOK"   , "Password-Hashmethode von Zikula sollte auf MD5 gesetzt werden, um eine korrekte Synchronisation mit TNG zu ermöglichen!");

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
define("_TNGZUSERLOG"       , "Benutzerlog");
define("_TNGZADMINLOG"      , "Administrationslog");
define("_TNGZREADME"        , "Detaillierte Readme-Datei");

define("_TNGZ_HOMEPAGE"     , "Benutze Hauptseite von TNGz anstatt der TNG-Index-Seite");

define("_TNGZ_ID_TITLE"     , "Primärperson angeben");
define("_TNGZ_ID_TREE"      , "Zweig, im dem die Person gespeichert ist.");
define("_TNGZ_ID"           , "TNG-ID der Primärperson (muss im Zweig vorhanden sein)");
define("_TNGZ_ID_INFO"      , "Diese Einstellungen sind optional. Wenn aktiv kann eine persönlichere Ansicht benutzt werden. Bitte leerlassen, wenn nicht gewünscht.");

define("_TNGZ_CACHE"        , "Zwischenspeicher");
define("_TNGZ_CACHE_INFO"   , "Das Nutzen des Zwischenspeichers kann den Zugriff auf Blöcke und Plugins beschleunigen. Anstatt jedes Mal die Abfragen zu starten wird eine zwischengespeicherte Version benutzt.");
define("_TNGZ_CACHEDB"      , "TNG-Datenbankänderungen löschen den Zwischenspeicher");
define("_TNGZ_CACHEDB_INFO" , "Das Überprüfen der Datenbankupdates funktioniert nicht auf jedem System. Bei Zugriffsfehlern bitte den Zwischenspeicher deaktivieren.");
define("_TNGZ_CACHESEC"     , "Aktualisierungsintervall");
define("_TNGZ_CACHE_DISABLE", "Beide Einstellungen müssen für eine Deaktivierung des Zwischenspeichers aus sein.");
define("_TNGZ_CACHE_DELETE" , "Zwischenspeicher löschen");
define("_TNGZ_CACHE_DELETED", "Zwischenspeicher gelöscht");
define("_TNGZ_CACHE_ERROR"  , "Fehler beim Leeren des Zwischenspeichers");
define("_TNGZ_CACHE_NONE"   , "Kein Zwischenspeicher");
define("_TNGZ_HOUR"         , "Stunde");
define("_TNGZ_HOURS"        , "Stunden");