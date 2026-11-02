# ERRONKA2_TALDE5

Proiektu honen helburua enpresako datu-basean (`bdweb`) argazkirik gabe dauden erabiltzaileak aurkitzea eta automatikoki mezu bat bidaltzea da, profileko irudia igo dezaten eskatzeko.

## Nola funtzionatzen du kodeak?

JupyterLab-en sortutako notebook-a lau zati nagusitan dago banatuta:

1. **Datu-basera konexioa:**
   MySQL/MariaDB datu-basera konektatzen gara. Ondoren, `users` taula aztertu eta `irudia` eremua hutsik edo `NULL` duten erabiltzaileak bakarrik hautatzen ditugu, beraien izena eta e-maila lortuz.

2. **Postak bidaltzea:**
   Aurreko pausoan lortutako erabiltzaile bakoitzari abisu-mezu bat prestatzen zaio. Mezu hauek tokiko SMTP zerbitzari batera bidaltzen dira (`smtp4dev`), benetako posta helbideetara mezurik bidali gabe probak modu seguruan egin ahal izateko.

## Atazaren automatizazioa eta zergatia

Script-a astelehenero goizeko 09:00etan exekutatzeko konfiguratu dugu. Aukera hau honako arrazoi hauengatik egin dugu:

* **Zergatik astean behin eta ez egunero?** Egunero abisuak bidaltzea astuna izango litzateke erabiltzaileentzat eta mezuak spam gisa ignoratzeko arriskua egongo litzateke. Astelehen goizetan bidaltzea erabaki dugu, lan-astea hasten duten unean oharra ikus dezaten.
* **Zenbat aldiz bidaltzen da mezua?** Script-ak ez du muga finkorik (adibidez 2 aste). Exekuzio bakoitzean datu-basa unean bertan kontsultatzen duenez, erabiltzaileak argazkia igotzen ez duen bitartean astelehenero jasoko du abisua. Erabiltzaileak bere profileko argazkia kargatzen duen unean, hurrengo asteleheneko kontsultan jada ez da agertuko eta automatikoki utziko dio mezuak jasotzeari.

Konfigurazio hau egiteko tresna hauek erabili ditugu:

* **Linux-en (`cron`):** `crontab -e` komandoaren bidez astelehenero 09:00etan exekutatzeko araua ezarri dugu.
* **Windows-en:** Windows-eko Ataza Programatzailea (Task Scheduler) erabili dugu script-a astero ordu berean abiarazteko.

## Nola jarri martxan eta probatu

1. **SMTP zerbitzaria piztu Docker-ekin:**
   Terminalean komando hau exekutatu behar da mezuak jaso ahal izateko:

   ```bash
   docker run --rm -it -p 3000:80 -p 2525:25 rnwood/smtp4dev
   ```

2. **Script-a exekutatu:**
   JupyterLab-en `scripta.ipynb` ireki eta gelaxka guztiak exekutatu.

3. **Mezuak egiaztatu:**
   Nabigatzailean `http://localhost:3000` helbidean sartu. Bertan agertuko dira bidalitako posta guztiak eta ikus daiteke zein erabiltzaileri iritsi zaion abisua.