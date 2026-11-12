# Arrue — plangintza, talde-lana eta Git bidezko trazabilitatea

**Proiektua:** Arrue Hortz Sintetikoak · 2026–2027 ikasturtea · 5. taldea.  
**Dokumentuaren eguneraketa:** 2026-11-12.

Dokumentu honek memoriaren LaTeX/PDF bertsioan jasotako **02. Plangintza, ardurak eta talde-lana** eta **05. Git, berrikuspena eta integrazio jarraitua** atalak azaltzen ditu. Erreferentzia erabilia erabiltzaileak emandako `Erronkako_Dokumentazioa.pdf` da. Plangintza, egindako lana eta egiaztatzeke dauden ekintzak bereizten dira.

## 1. Taldearen antolaketa

Proiektuko lana arlo teknikoen arabera antolatzen da, arduren banaketa eta atalen arteko koordinazioa errazteko. Memorian jasotako banaketa hau da:

| Kidea | Ardura nagusia |
|---|---|
| Ander | Webgunearen konponketa eta birdiseinua |
| Diogo eta Adrian | Zerbitzarien eta sareen konfigurazioa |
| Sandra | Python bidezko scriptaren garapena |
| Talde osoa | Dokumentazioa elkarlanean lantzea |

Banaketa horrek ez du berez lan bakoitza amaituta dagoela frogatzen. Ekarpenak dagokien konfigurazioarekin, dokumentazioarekin, commitarekin edo proba-erregistroarekin lotu behar dira. Atal bakoitzaren arduraduna eta berrikuspenaren egoera ere jasotzen dira.

## 2. GitHub Projects eta Kanban bidezko jarraipena

Zereginak GitHubeko issueen bidez antolatzen dira, eta jarraipena [Kanban taulan](https://github.com/users/91Adrian91/projects/9) egiten da. Memoriak lau egoera bereizten ditu:

| Egoera | Jarraipenean duen esanahia |
|---|---|
| To Do | Hasteko edo egiteko dagoen lana |
| Doing | Garatzen ari den lana |
| Testing | Egiaztatzen edo berrikusten ari den lana |
| Done | Taulan amaitutzat edo dagokion amaierako egoeratzat markatutako lana; issuearen egoera eta ebidentziak ere egiaztatu behar dira |

**Memorian aztertutako unean**, 28 zeregin agertzen ziren: 14 To Do, 5 Doing, 1 Testing eta 8 Done. Kopuru horiek memoriako irudiaren erregistro historikoa dira, **ez 2026-11-12ko taularen zenbaketa berri bat**.

Memoriaren deskribapenean Doing egoeran dokumentazioa, VPN, araudia, compliance plana eta arrisku-mapa agertzen dira. Testing egoeran Git eta adarren bidezko talde-lanaren berrikuspena dago. Done egoerako adibideen artean webaren azterketa, XSS/SQLi zuzenketak, pasahitzen eta saioen kontrolak, email scripta eta GitHub Actions pipelinea daude. Egoerak aldakorrak direnez, uneko informazioa proiektuko taulan eta issueetan kontsultatzen da.

Done zutabean egotea eta issuea itxita egotea ez dira gauza bera. Lan bat amaitutzat justifikatzeko, haren onarpen-irizpideak, emaitzak eta berrikuspena ere aztertu behar dira.

## 3. Egutegia eta mugarriak

Memoriak GitHub Projects tresnaren Gantt ikuspegian oinarritutako plangintza jasotzen du. Ondoko epeak **aurreikusitakoak dira**, eta batzuk diagramaren irakurketatik gutxi gorabehera hartuta daude. Ez dira benetako hasiera- edo amaiera-data egiaztatu gisa aurkezten. Data guztiak 2026koak dira.

| Zeregina | Aurreikusitako epea | Issuea |
|---|---|---|
| Webgunearen prestaketa eta azterketa | Urriak 29 – azaroak 2 | #10 |
| XSS ahuleziak zuzentzea | Urriak 29 – azaroak 2 | #11 |
| SQLi ahuleziak zuzentzea | Urriak 29 – azaroak 2 | #12 |
| Pasahitzen kudeaketa zuzentzea | Urriak 29 – azaroak 2 | #13 |
| Email scripta egitea | Urriak 29 – azaroak 2 | #25 |
| Saioak babestea | Azaroak 1–4 | #14 |
| Webgunea edukiontzietan hedatzea | Azaroak 1–4 | #15 |
| GitHub Actions pipelinea sortzea | Urriak 29–31 | #17 |
| Compliance plana | Azaroak 4–9 | #29 |
| Arrisku-mapa | Azaroak 4–9 | #28 |
| Araudiak, ISO | Azaroak 4–9 | #30 |
| VPN konfiguratzea | Azaroak 4–11 | #27 |
| Egoitzen arteko VPN | Azaroak 1–4 | #3 |
| Komunikazio-politikak aplikatzea eta egiaztatzea | Azaroak 7–11 | #5 |
| IP helbideratzea, rutak eta NAT | Azaroak 9–11 | #1 |
| SSH eta WebGUI sarbideak FQDN bidez ziurtatzea | Azaroak 9–11 | #7 |
| P2B VPN | Azaroak 3–11 | #4 |
| Pasahitzen kudeaketa ezartzea | Urriak 29 – azaroak 11 | #6 |
| MFA ezartzea | Azaroak 10–12 | #31 |

## 4. Eskakizunen eta ekarpenen trazabilitatea

Errubrikako eskakizunak dagokien zereginarekin, memoriako atalarekin eta ebidentziekin lotzen dira. Issueak lanaren helburua, arduraduna eta egoera identifikatzeko erabiltzen dira. Kode-aldaketak commit eta pull request bidez erregistratzen dira; kodearekin loturarik ez duten lanak konfigurazio-fitxategien, kapturen, proba-erregistroen eta txosten teknikoen bidez justifikatzen dira.

Memoriaren eranskinetako trazabilitate-matrizeak eskakizuna, atala, arduraduna, proba, ebidentzia eta berrikuspena elkartzeko balio du. Bete gabeko eremuak osatzeke daude; ez dira benetako emaitzekin ordezkatzen frogarik gabe.

Adibidez, #16 issueak Git bidezko talde-lana eta berrikuspen-prozedura jasotzen ditu; #17 issueak integrazio jarraituaren pipelinea. Horien emaitzak ez dira beste zeregin guztien amaieraren edo kide guztien ekarpenaren froga automatikoa.

## 5. Biltegia eta adarren eginkizuna

Biltegi publikoa: [91Adrian91/ERRONKA2_TALDE5](https://github.com/91Adrian91/ERRONKA2_TALDE5).

| Adarra | Dokumentatutako erabilera |
|---|---|
| `main` | Berrikusitako lana integratzeko helmuga eta dokumentu honen argitalpen-adarra |
| `web/desarrollo` | Web-aplikazioaren kodea, Docker konfigurazioa, probak eta dokumentazio teknikoa |
| Zeregin-adarrak | Aldaketa zehatzak prestatu eta berrikuspena proposatzeko adarrak |

Dokumentu hau `main` adarrean argitaratzeak **ez du web-aplikazio osoaren integrazioa edo #24 PRaren bateratzea esan nahi**. Webaren lan-kopia prestatzeko `web/desarrollo` erabiltzen da:

```bash
git clone --branch web/desarrollo https://github.com/91Adrian91/ERRONKA2_TALDE5.git
cd ERRONKA2_TALDE5
composer install
composer check
```

Azken bi komandoek PHP, Composer eta proiektuak eskatutako luzapenak instalatuta egotea eskatzen dute. Aplikazio osoaren Docker bidezko abiaraztea [lan-adarreko READMEan](https://github.com/91Adrian91/ERRONKA2_TALDE5/blob/web/desarrollo/README.md) azaltzen da.

## 6. Aldaketak egiteko lan-prozedura

Zeregin bat hastean, oinarrizko adarra eguneratu eta lan-adar bereizia sortzen da. Aldaketak egin ondoren, dagozkion probak eta dokumentazioa egiaztatzen dira. Bertsionatu beharreko fitxategiak soilik hautatzen dira, diff-a berrikusten da, commit ulergarria egiten da eta adarra GitHubera igotzen da.

Pull requestean issuea, aldaketaren helburua eta egiaztapenak lotzen dira. Beste taldekide batek kodea eta emaitzak aztertzea da prestatutako prozedura. Zuzenketak eskatzen badira, lan-adarrean osatu eta CI berriz egiaztatzen da. Prozedura aurreikusita egoteak ez du esan nahi berrikuspen guztiak eginda daudenik.

Kide bakoitzak bere GitHub kontua eta benetako Git identitatea erabiltzen ditu. Ez dira `.env`, benetako pasahitzak, datuen migrazio-kopiak edo mendekotasun lokalak argitaratzen.

Komandoen azalpen osatua: [CONTRIBUTING.md — web/desarrollo](https://github.com/91Adrian91/ERRONKA2_TALDE5/blob/web/desarrollo/CONTRIBUTING.md).

## 7. Pull requesta eta berrikuspenaren egoera

Webaren integraziorako [#24 pull requesta](https://github.com/91Adrian91/ERRONKA2_TALDE5/pull/24) sortu da, `web/desarrollo` jatorritik `main` helmugara. **2026-11-12an kontsultatutako egoeran, irekita, zirriborroan eta bateratu gabe dago**.

Memoriak 2026-11-03ko egoera jasotzen du: une hartan ez zegoen beste taldekide baten berrikuspenik edo onarpenik erregistratuta, eta berrikuspena eskatzea gerorako utzita zegoen. Giza berrikuspenaren azken egoera dagokion GitHub review-erregistroarekin justifikatu behar da. CI zuzena ez da pertsona baten onarpenaren ordezkoa.

`main` babesteko [.github/rulesets/main.json proposamena](https://github.com/91Adrian91/ERRONKA2_TALDE5/blob/web/desarrollo/.github/rulesets/main.json) prestatuta dago lan-adarrean. Pull requesta, beste pertsona baten onarpena eta CIko kontrolak eskatzea aurreikusten du. Memoriako azken konfigurazio-egiaztapena 2026-10-30ekoa da, babesa aktibatzeke zegoenean. Dokumentu honetan ez da gaurko babesaren aktibazioa egiaztatu, eta JSON fitxategia gordetzeak ez ditu arauak automatikoki gaitzen.

## 8. Integrazio jarraitua eta emaitzak

GitHub Actions pipelinea [ci.yml](https://github.com/91Adrian91/ERRONKA2_TALDE5/blob/web/desarrollo/.github/workflows/ci.yml) fitxategian definitzen da. Dokumentatutako konfigurazioak `push`, `pull_request` eta eskuzko exekuzioak jasotzen ditu. Composer konfigurazioa, PHP sintaxia, PHPStan analisi estatikoa eta PHPUnit unitate-probak egiaztatzen ditu PHP 8.3 eta PHP 8.5 bertsioetan. HTTP/MySQL probek Docker ingurune isolatua erabiltzen dute.

Memorian jasotako proba-multzoak 11 unitate-proba, 30 baieztapen eta 76 HTTP/MySQL egiaztapen ditu. Kopuru horiek erregistro historikoei dagozkie; ez dira dokumentu hau argitaratzean berriz exekutatu. JUnit txostenak eta HTTP emaitzen JSONa dagokien exekuzioaren artefaktu gisa gordetzen dira.

2026-11-03an egiaztatutako [41037257367 exekuzioa](https://github.com/91Adrian91/ERRONKA2_TALDE5/actions/runs/41037257367) `00653dd` commitari lotuta dago:

| Lana | Erregistro historikoko emaitza |
|---|---|
| PHP checks (8.3) | Success |
| PHP checks (8.5) | Success |
| HTTP security and Docker | Success |

Pipelineak integrazio jarraitua egiten du; ez du aplikazioa automatikoki ekoizpenera hedatzen. CIko proben arrakasta eta Ubuntu Serverreko hedapenaren balidazioa bi egiaztapen bereizi dira. Lan-adarreko azalpena: [PIPELINEA.md](https://github.com/91Adrian91/ERRONKA2_TALDE5/blob/web/desarrollo/docs/PIPELINEA.md).

## 9. Dokumentazioa eta ebidentziak

| Baliabidea | Zertarako erabiltzen den |
|---|---|
| Memoriaren 02. atala | Ardurak, Kanban, egutegia eta trazabilitatea |
| Memoriaren 05. atala | Git, adarrak, berrikuspena eta CI |
| [GitHub Projects](https://github.com/users/91Adrian91/projects/9) | Zereginen jarraipena eta aurreikusitako egutegia |
| [#16 issuea](https://github.com/91Adrian91/ERRONKA2_TALDE5/issues/16) | Git bidezko talde-lanaren jarraipena |
| [#24 pull requesta](https://github.com/91Adrian91/ERRONKA2_TALDE5/pull/24) | Webaren integrazio-proposamena |
| [WEB-SEC-001/validation.json](https://github.com/91Adrian91/ERRONKA2_TALDE5/blob/web/desarrollo/docs/auditoria/ebidentziak/WEB-SEC-001/validation.json) | Proba historikoen laburpena eta iturburu-hashak |

Dokumentu honetako estekek adarra esplizituki adierazten dute, `main` adarrean argitaratutako azalpena eta oraindik `web/desarrollo` adarrean dagoen material teknikoa ez nahasteko.
