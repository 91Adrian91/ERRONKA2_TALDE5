# WEB-SEC-001 — Webaren segurtasuna eta edukiontzien hedapena

Data: 2026-10-29. Lan-adarra: `web/desarrollo`. Esparrua: irakasleen weba,
PHP/Apache, MySQL eta CI. Errubrikako ESJ-5, ESJ-6 eta ESJ-8 atalak lantzen
dira. Android eta horiz markatutako bigarren zatiko entregak ez dira aldatu.

## Metodoa eta ebidentziak

1. Uneko iturburuaren sarrera, SQL, HTML irteera, autentifikazio eta saio-fluxuak berrikusi.
2. Aurretik zeuden zuzenketak mantendu eta falta ziren kontrolak gehitu.
3. Unitate-probak eta benetako HTTP/MySQL proba isolatuak exekutatu.
4. Emaitzak gorde, erabilitako proba-datuak garbitu eta CI-n erreprodukzioa konfiguratu.

- [HTTP emaitzen JSONa](auditoria/ebidentziak/WEB-SEC-001/http-security.json): **76 egiaztapen gaindituta**.
- [Exekuzioaren laburpena](auditoria/ebidentziak/WEB-SEC-001/validation.json).
- PHPUnit: **11 proba / 30 baieztapen**; PHPStan eta PHP sintaxia gaindituta.
- [HTTP tresna](../tools/http-security-test.php) eta [unitate-probak](../tests/SecurityHelpersTest.php).
- Aurreko ahulezien ebidentziak: [AUD-001](auditoria/AUD-001.md), SONAR-001 eta SONAR-002. Ez dira berridatzi.

Ez da jatorrizko bertsio ahula berriro argitaratu. Egungo erregresioek
zuzenketaren ondorengo emaitza frogatzen dute; aurreko egoeraren frogak
historialean daude. Probek ez dute cookie, token edo pasahitzik gordetzen
ebidentzia-fitxategietan.

## Aurkikuntzen eta kontrolen inbentarioa

Lehentasuna aplikazio honen testuinguruan egindako balorazioa da, ez CVSS
ziurtatua. Aurretik konpondutako arazoak eta orain gehitutako kontrolak
bereizten dira. Errenkada bateko probak gainditzeak ez du esan nahi eraso
posible guztiak estaltzen direnik.

| IDa / lehentasuna | Puntua eta arriskua | Zuzenketa / uneko kontrola | Berriz egiaztatutakoa |
|---|---|---|---|
| XSS-01 / handia | Bilaketaren testua HTML bihurtzea | Aurretik zegoen `htmlText` mantendu; CSP gehitu | Script sarrerak testu kodetu gisa agertzen dira |
| XSS-02 / handia | Produktuen izena/deskripzioa, administrazio-taula eta formularioetako balioak | Testuinguruaren araberako HTML/atributu kodetzea eta URL segmentuen kodetzea | Biltegiratutako script/HTMLak katalogoan, administrazioan, textarea eta erregistroaren berrerakustaldian kodetuta |
| XSS-03 / handia | Login `action` atributuko `PHP_SELF` | AUD-001eko helmuga finkoa mantendu | PHPUnit-ek ez du scriptik aurkitzen eta helmuga finkoa egiaztatzen du |
| SQLI-01 / kritikoa | Login eta bilaketatik SQL aldatzea | Aurretik zeuden kontsulta prestatuak mantendu | SQLi sarrerek ez dute saioa edo katalogo osoa ematen |
| SQLI-02 / handia | Deskripzio/produktu/kontu administrazioko parametroak | Kontsulta prestatuak, ID numerikoak eta sarrera mota/luzeren baliozkotzea | IDan SQLi baztertuta; ezabatze-helburuko kateak ez ditu kontuak ezabatzen; komatxoak datu gisa gordeta |
| AUTH-01 / handia | Pasahitzak modu ahulean gordetzea | Aurretik zeuden `password_hash`/`password_verify` mantendu; 8–72 byteko muga | Erregistro, admin-alta eta aldaketako hashak; pasahitz zaharraren porrota eta berriaren arrakasta |
| AUTH-02 / handia | Saioa duen norbaitek pasahitza aldatu ahal izatea berriz autentifikatu gabe | Oraingo pasahitza eskatu eta egiaztatu | Pasahitz okerrarekin ez da aldatzen |
| CSRF-01 / handia | Cookiea automatikoki bidaltzen den POST eskaera faltsuak | Ausazko 32 byteko tokena saioan; POST guztietan egiaztapen zentralizatua | Login, erregistro, pasahitz, kontu/produktu alta/ezabatze, edizio eta logout eskaerek tokenik gabe huts egiten dute |
| SESSION-01 / handia | ID finkatzea eta iraungitze-kontrolik eza | Strict mode, cookie bidezko IDa, HttpOnly, SameSite=Lax, HTTPSrako Secure, idle 30 min eta gehienez 8 ordu | ID arrotza baztertuta, ID/token berritzea; HTTP bidezko idle/absolute iraungitze-probak |
| SESSION-02 / handia | Pasahitza aldatu edo kontua ezabatu ondoren saio zaharrak erabiltzea | Eskaera bakoitzean kontua eta pasahitz-hasharen bertsioa egiaztatu | Bigarren saioa eta cookie zaharren replay-a baztertuta |
| SESSION-03 / ertaina | GET bidezko logout eta cookie/saio hondarrak | Logout POST+CSRF; zerbitzariko saioa eta cookiea ezabatu | GET ez da onartzen; logout osteko cookie zaharra ez da autentifikatzen |
| ACCESS-01 / handia | Erabiltzaile arruntak administrazio-fluxuak erabiltzea | `updel` eta `description` adminarentzat, `account` autentifikatuentzat; ekintza bakoitzean kontrola | Gonbidatu eta erabiltzaile arrunten GET/POST eskaerak baztertuta |
| DATA-01 / ertaina | Erabiltzaile-izen bikoiztuak eta datu-formatu okerrak | Izen bakarreko indizea, alta aurreko kontrola eta migrazio segurua | Alta bikoiztua 409; array moduko pasahitz/prezioak eta luzeegiak baztertuta |
| UPLOAD-01 / handia | Irudi faltsuak, izen/bide arriskutsuak eta fitxategi handiak | MIME+irudi-neurriak, 2 MB, ausazko izena, Apache-n PHP exekuzioa blokeatu eta nosniff | Profil/produktu GIF errealak igo; PHP irudi faltsua eta tamaina handiegia baztertu; izenak berriz sortu |
| DEPLOY-01 / handia | BD root erabiltzailea edo kanpora irekitako BD | Compose-n erabiltzaile propioa, ingurune-aldagaiak eta BD barneko sarea | Webak BD errealarekin funtzionatzen du; DBk ez du host-porturik |
| ERROR-01 / ertaina | Salbuespenek SQL/bide/xehetasun teknikoak erakustea | Errore-orri generikoa, display_errors off eta log mugatua | Sarrera okerrak 400/403/409; probek ez dute SQL/PHP errore-orririk behar |

## SQL eta HTTP fluxuen estaldura

| Fluxua | Sarrera | Babesa / proba |
|---|---|---|
| Katalogoa/bilaketa | `keyword` | Kontsulta prestatua + HTML kodetzea; SQLi eta XSS |
| Login | `username`, `password`, `csrf_token` | Kontsulta prestatua, hash verify, ID/token berria |
| Erregistroa | `data[...]`, `imagen` | Mota/luzerak, email/zenbakiak, indize bakarra, hash eta irudi-kontrola |
| Pasahitz-aldaketa | `currentpass`, `newpass`, `confnewpass` | CSRF, oraingo hash verify, hash berria, saio guztiak baliogabetu |
| Admin erabiltzaile-alta/ezabatzea | `newuser`, `newuserpass`, `deleteuser` | Admin, CSRF, kontsulta prestatua eta hash |
| Produktu-alta/ezabatzea | `izena`, `deskripzioa`, `salneurria`, `stock`, `pic_id`, `upfile` | Admin, CSRF, zenbaki/luzerak, SQL prestatua eta irudi-kontrola |
| Deskripzio/prezio edizioa | `pic_id`, `deskripzioa`, `salneurria` | Admin, CSRF, ID numerikoa, SQL prestatua, textarea kodetua |
| Logout | POST + tokena | Saioa eta cookiea ezabatu; GET debekatu |

Beste SQL kontsultak konstanteak dira; ez dute bezeroaren baliorik SQL
katean txertatzen. `main_orig.php`-k egungo katalogo bakarra kargatzen du.
`conf.php` eta `includes/` HTTP bidez blokeatuta daude Apache-n; tokiko PHP
zerbitzarian `router.php` erabili.

## Hedapena eta erreprodukzioa

[README](../README.md)-ko komandoek web+BD multzoa abiarazten dute. Datuak,
produktu-irudiak eta profilak bolumen bereizietan gordetzen dira.
`depends_on: service_healthy`-k BD prest egotea eskatzen du weba abiatu aurretik.

Probek `APP_TESTING=1` eta `_test` amaierako BD izena eskatzen dituzte.
Ohiko `arrue-mysql` edukiontzia ez da probetan erabiltzen. Saio-proben
zerbitzaria test profilean bakarrik sortzen da; ez du host-porturik.

Secure cookiearen testak konfigurazioaren flag-a egiaztatzen du HTTP bezero
kontrolatuarekin; **ez da TLS garraioaren proba**. Ohiko tokiko weba HTTP da.
HTTPS amaierako benetako hedapenean `SESSION_COOKIE_SECURE=1` ezarri.

## Errubrika eta geratzen diren egiaztapenak

- ESJ-5: goiko inbentarioak aurkikuntza, eragina, zuzenketa eta proba lotzen ditu.
- ESJ-6: XSS/SQLi, pasahitz eta saio-fluxuen erregresioak eta HTTP ebidentziak daude.
- ESJ-8: web+BD edukiontzietan; CI-k PHP 8.3/8.5 eta HTTP suitea exekutatzen ditu, JUnit/JSON artefaktuekin.
- Git talde-lanaren ebidentzia pertsonalak eta beste kide baten berrikuspena taldeak gehitu behar ditu; ezin dira agenteak asmatu.
- ESJ-4ko descriptor hutsa irakaslearekin zehaztu behar da. Honek ez du nota 10 automatikoki ematen.
- Ez da SonarQube analisi berririk egin: ez erabili lehengo zero abisuak aldaketa hauen emaitza gisa.
- Estaldura hau ez da pentest independente osoa. Kanpoko zerbitzu publiko baten hardeninga, TLS eta sarbide-maiztasunaren kontrola beste hedapen-esparru batean aztertu behar dira.

## Erreferentziak

- [PHP: saioen konfigurazio segurua](https://www.php.net/manual/en/session.security.ini.php).
- [OWASP: CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html).
- [Docker: Compose startup order](https://docs.docker.com/compose/how-tos/startup-order).
