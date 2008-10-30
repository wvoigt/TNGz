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



// TNGz Blocks
define('_USECACHE',                  'Block-Caching aktivieren (wenn pnRender-Caching aktiviert ist)');
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

define('_SHOWLIVING',     'Zeige Fotos der Personen');
define('_SELECTLIVINGY',  'Ja, zeige alle');
define('_SELECTLIVINGN',  'Nein');
define('_SELECTLIVINGD',  'Ja, aber keine lebenden Personen');
define('_SELECTLIVINGL',  'Ja, aber zeige lebende Personen nur eingeloggten Benutzern');
define('_SHOWPHOTO',      'Welcher Typ von Foto soll benutzt werden');
define('_SELECTPHOTOT',   'Thumbnail de Fotos');
define('_SELECTPHOTOP',   'Aktuelles Foto');
define('_MAXHEIGHT',      'Maximale Höhe der Fotos');
define('_MAXWIDTH',       'Maximale Breite der Fotos');
define('_TNGPHOTO',       'Fotograf');
define('_PEOPLEDBFERROR', 'Fehler im Zugriff auf TNG-Tabelle.');
define('_NOPHOTOFOUND',   'Kein Foto gefunden');
define('_TNGPHOTOLIST',   'Limitiere Fotos auf die folgenden TNG-mediaID-Nummer (standard = leer)');

define('_ACCESSTNG',      'Ahnenforschungsseite');
define('_NONETNG',        'Niemand für heute gefunden');
define('_PEOPLEDBFERROR', 'Fehler im Zugriff auf TNG-Tabelle. ');
define('_SELECTYES',      'Ja');
define('_SELECTNO',       'Nein');
define('_SHOWORDER',      'Zeige Einträge nach :');
define('_SELECTORDERN',   'Nachname');
define('_SELECTORDERD',   'Neuestem zu Frühestem');
define('_SELECTORDERR',   'Frühestem zu Neuestem');
define('_SELECTORDERE',   'Eingegebene Reihenfolge');
define('_SHOWFAMILYNAME',     'Zeige Familien nach :');
define('_SELECTFAMILYFULL',   'Voller Name');
define('_SELECTFAMILYSHORT',  'nur Nachnamen');
define('_WANTEDINSTRUCTIONS', '<strong>Unten stehende Einträge sind optional.</strong><br />Leere Einträge werden nicht in die Ausgabe übergeben.<br />IDs, welche nicht im richtigen Format sind, nicht in TNG vorhanden sind oder Fehler beinhalten werden in die Ausgabe übernommen.');
define('_WANTEDTEXT',         'Beschreibung im oberen Teil des Blocks (kann HTML enthalten)');
define('_WANTEDPEOPLELABEL',  'Titel der Personenliste (z.B. Personen)');
define('_WANTEDPEOPLELIST',   'Liste der PersonIDs (z.B. I001, I002)');
define('_WANTEDFAMILYLABEL',  'Titel der Familienliste (z.B. Families)');
define('_WANTEDFAMILYLIST',   'Liste der FamilyIDs (z.B. F001, F002)');
define('_WANTEDMENULINK',     'Zeige TNG-Menülink unten?');
define('_MARRIAGE_AND',   'und');
define('_MARRIED_ABR',     'm.');
define('_DIVORCED_ABR',    'D');



