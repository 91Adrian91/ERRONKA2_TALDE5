# Talde-lana Git eta GitHub bidez

Biltegia: https://github.com/91Adrian91/ERRONKA2_TALDE5

`main` adarrean berrikusitako lana mantendu. Zeregin bakoitzeko adar bat sortu,
adibidez `web/WEB-02-xss` edo `dev/DEV-03-pipeline`. Issuearen arduradunak
aldaketa egiten du eta beste taldekide batek pull requesta berrikusten du.

## Lehenengo aldia

```sh
git clone --branch web/desarrollo https://github.com/91Adrian91/ERRONKA2_TALDE5.git
cd ERRONKA2_TALDE5
composer install
composer check
```

PHP 8.3 edo berriagoa, Composer 2 eta PHPUnit-ek behar dituen `dom`, `mbstring`,
`xml` eta `xmlwriter` luzapenak behar dira. Webgunearen benetako exekuzioak
MySQL eta `mysqli` ere behar ditu; unitate-probek ez dute datu-basea erabiltzen.

## Zeregin bakoitza

```sh
git switch web/desarrollo
git pull --ff-only origin web/desarrollo
git switch -c web/WEB-02-xss
# Egin aldaketak eta gehitu dagozkien probak.
composer check
git add erronka2_web/includes/login.php tests/AuthenticationViewsTest.php
git diff --cached
git commit -m "fix: WEB-02 saio-hasierako irteera babestu"
git push -u origin web/WEB-02-xss
```

Komandoetako fitxategiak eta mezua adibideak dira: egokitu benetako aldaketari.
Ez partekatu erabiltzaile-identitaterik; bakoitzak bere Git identitatea erabili.
Ez igo `.env`, benetako kredentzialak, benetako datu pertsonalak edo `vendor/`.
Irakasleen SQL fitxategia laborategiko material gisa soilik erabili.

Uneko integrazio-adarra `web/desarrollo` da. Lehen web osoaren integrazioa
`web/desarrollo` → `main` pull requestean berrikusten da; bitartean `main`
adarreko hasierako READMEak ez du aplikazio osoa. Horregatik, lehen klonazioak
goiko `--branch web/desarrollo` erabiltzen du.

Eguneroko zeregin-adarretarako, ireki PR bat `web/desarrollo` helmugara.
Lotu issuea `Refs #ZENBAKIA` erabiliz integrazio partzialetan. Erabili
`Closes #ZENBAKIA` onarpen-irizpide guztiak bete direnean eta helmuga-adarrera
bateratzeak issuea ixtea dagokionean. Azaldu aldaketa eta gehitu ebidentziak.
Beste taldekide batek kodea, probak eta CI aztertu behar ditu. Ez egin force
push adar partekatuetan, eta ez bateratu norberaren lana berrikuspenik gabe.

PR bat probatzeko, berrikusleak GitHub-eko bere kontua eta checkout bereizia
erabiliko ditu; ez du egilearen identitatea erabiliko. Aldaketak eskatzen
baditu, egileak lan-adar berean konpondu eta CI berriz egiaztatuko du.

## Administratzailearen konfigurazioa

Settings > Rules > Rulesets atalean babestu `main`: pull requesta eskatu,
gutxienez beste pertsona baten onarpena, elkarrizketak ebatzita izatea eta
`PHP checks (8.3)`, `PHP checks (8.5)` eta `HTTP security and Docker` kontrolak
gainditzea. Blokeatu force
push eta adarraren ezabaketa. Kontrolak lehenengo CI exekuzioaren ondoren
hautatu ahal izango dira. Fitxategi honek ez ditu GitHub-eko baimenak edo
arauak automatikoki aktibatzen. Egungo proposamen zehatza
[main.json](.github/rulesets/main.json) da. Administratzaileak bakarrik aplikatu
behar du, berrikusi ondoren; ez dago aktibatuta fitxategia Git-en gordetzeagatik.

## Ebidentziak

Gorde issuearen, lan-adarraren, commit-en, beste taldekidearen berrikuspenaren
eta CI exekuzioaren estekak. GitHub Actions-eko `php-tests-*` artefaktuek
JUnit txostena dute. Kontsultatu [pipelinearen dokumentazioa](docs/PIPELINEA.md).

Issue #16ko egungo egoera, benetako commitak, adarrak eta geratzen diren
giza berrikuspenak: [GIT-TALDE-LANA](docs/GIT-TALDE-LANA.md).
