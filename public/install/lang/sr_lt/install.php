<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Automatically generated strings for Moodle installer
 *
 * Do not edit this file manually! It contains just a subset of strings
 * needed during the very first steps of installation. This file was
 * generated automatically by export-installer.php (which is part of AMOS
 * {@link https://moodledev.io/general/projects/api/amos}) using the
 * list of strings defined in public/install/stringnames.txt file.
 *
 * @package   installer
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admindirname'] = 'Administratorski direktorijum';
$string['availablelangs'] = 'Dostupni jezički paketi';
$string['chooselanguagehead'] = 'Izaberite jezik';
$string['chooselanguagesub'] = 'Izaberite jezik koji će se koristiti tokom instalacije. Ovaj jezik će, takođe, biti korišćen na nivou sajta kao podrazumevani, iako to naknadno može da se promeni.';
$string['clialreadyconfigured'] = 'Datoteka config.php već postoji. Molimo, koristite admin/cli/install_database.php ako želite da instalirate Moodle na ovom serveru.';
$string['clialreadyinstalled'] = 'Datoteka config.php već postoji. Molimo koristite admin/cli/upgrade.php ako želite da nadogradite Moodle na ovom serveru.';
$string['cliinstallheader'] = 'Moodle {$a} program za instalaciju iz komandne linije';
$string['clitablesexist'] = 'Baza podataka već postoji. Nije moguće nastaviti instalaciju iz komandne linije.';
$string['databasehost'] = 'Server baze podataka';
$string['databasename'] = 'Ime baze podataka';
$string['databasetypehead'] = 'Izaberite drajver baze podataka';
$string['dataroot'] = 'Direktorijum podataka';
$string['datarootpermission'] = 'Ovlašćenja nad direktorijumom podataka';
$string['dbprefix'] = 'Prefiks tabele';
$string['dirroot'] = 'Moodle direktorijum';
$string['environmenthead'] = 'Proveravanje Vašeg okruženja...';
$string['environmentsub2'] = 'Svako izdanje Moodlea ima minimum zahteva po pitanju odgovarajuće PHP verzije i nekoliko obaveznih PHP ekstenzija.
Kompletna provera okruženja se vrši pre svake instalacije i nadogradnje postojeće verzije. Ukoliko ne znate kako da instalirate novu verziju ili omogućite PHP ekstenzije kontaktirajte svog administratora servera.';
$string['errorsinenvironment'] = 'Provera okruženja nije prošla!';
$string['installation'] = 'Instalacija';
$string['langdownloaderror'] = 'Nažalost, jezik "{$a}" se ne može preuzeti. Proces instalacije biće nastavljen na engleskom jeziku.';
$string['paths'] = 'Putanje';
$string['pathserrcreatedataroot'] = 'Instalaciona procedura ne može da kreira direktorijum baze podataka ({$a->dataroot}).';
$string['pathshead'] = 'Potvrdi putanje';
$string['pathsrodataroot'] = 'U direktorijum za podatke nije moguć upis';
$string['pathsroparentdataroot'] = 'Nije moguć upis u nadređeni direktorijum ({$a->parent}).  Instalacioni program ne može da kreira direktorijum za podatke ({$a->dataroot}).';
$string['pathssubadmindir'] = 'Vrlo mali broj veb servera koristi /admin kao specijalni URL za pristup raznim podešavanjima (kontrolni panel i sl.). Nažalost, to dovodi do konflikta sa standardnom lokacijom za administratorske stranice u Moodleu. Ovaj problem možete rešiti tako što ćete promeniti ime administratorskog direktorijuma u vašoj instalaciji, i ovde upisati to novo ime. Na primer <em>moodleadmin</em>. Ovo podešavanje će prepraviti administratorske linkove u Moodle sistemu.';
$string['pathssubdataroot'] = '<p>Direktorijum gde će Moodle čuvati datoteke i sadržaj koji su postavili korisnici. </p>
<p>Ovaj direktorijum treba da bude podešen tako da korisnik veb servera (obično \'nobody\' ili \'apache\') može da ga čita i u njega upisuje.</p>
<p>Direktorijum ne sme biti dostupan direktno preko veba. </p>
<p>Ukoliko ovaj direktorijum ne postoji proces instalacije će pokušati da ga kreira.</p>';
$string['pathssubdirroot'] = '<p>Puna putanja do direktorijuma koji sadrži kôd Moodlea.</p>';
$string['pathssubwwwroot'] = '<p>Puna adresa putem koje će se pristupati Moodleu, tj. adresa koju će korisnici uneti u adresnu traku svojih veb čitača kako bi pristupili Moodleu.</p> <p>Nije moguće pristupati Moodleu korišćenjem više adresa. Ako se vašem sajtu može pristupiti sa više adresa, onda izaberite najlakšu, a za sve ostale adrese podesite permanentnu redirekciju.</p> <p>Ako se vašem sajtu može pristupiti kako sa interneta, tako i iz interne mreže (koja se ponekad naziv intranet), onda ovde upotrebite javnu adresu.</p> <p>Ako je tekuća adresa netačna, molimo vas, promenite URL adresu u adresnoj traci svog veb čitača i ponovo pokrenite instalaciju.</p>';
$string['pathsunsecuredataroot'] = 'Lokacija direktorijuma sa podacima nije bezbedna';
$string['pathswrongadmindir'] = 'Admin direktorijum ne postoji';
$string['phpextension'] = '{$a} PHP ekstenѕija';
$string['phpversion'] = 'PHP verzija';
$string['welcomep10'] = '{$a->installername} ({$a->installerversion})';
$string['welcomep20'] = 'Ovu stranicu vidite zato što ste uspešno instalirali i pokrenuli <strong>{$a->packname} {$a->packversion}</strong> paket na svom serveru. Čestitamo!';
$string['welcomep30'] = 'Ovo izdanje <strong>{$a->installername}</strong> uključuje aplikacije za kreiranje okruženja u kojem će <strong>Moodle</strong> uspešno funkcionisati, konkretno:';
$string['welcomep40'] = 'Ovaj paket obuhvata i <strong>Moodle {$a->moodlerelease} ({$a->moodleversion})</strong>.';
$string['welcomep50'] = 'Korišćenje svih aplikacija ovog paketa je uređeno njihovim licencama. Kompletan<strong>{$a->installername}</strong> paket je <a href="https://www.opensource.org/docs/definition_plain.html">otvorenog koda</a> i distribuira se pod <a href="https://www.gnu.org/copyleft/gpl.html">GPL</a> licencom.';
$string['welcomep60'] = 'Naredne stranice će vas provesti kroz nekoliko jednostavnih koraka tokom kojih ćete konfigurisati i podesiti <strong>Moodle</strong> na svom računaru. Možete prihvatiti podrazumevana podešavanja ili ih, opciono, prilagoditi sopstvenim potrebama.';
$string['welcomep70'] = 'Kliknite na dugme za nastavak da biste dalje podešavali <strong>Moodle</strong>.';
$string['wwwroot'] = 'Web adresa';
