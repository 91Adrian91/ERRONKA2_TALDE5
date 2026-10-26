# Talde-lana Git eta GitHub bidez

Biltegia: https://github.com/91Adrian91/ERRONKA2_TALDE5

`main` adarrean berrikusitako lana mantendu. Zeregin bakoitzeko adar bat sortu,
adibidez `web/WEB-02-xss` edo `dev/DEV-03-pipeline`. Issuearen arduradunak
aldaketa egiten du eta beste taldekide batek pull requesta berrikusten du.

## Lehenengo aldia

```sh
git clone https://github.com/91Adrian91/ERRONKA2_TALDE5.git
cd ERRONKA2_TALDE5
composer install
composer check
```

PHP 8.3 edo berriagoa, Composer 2 eta PHPUnit-ek behar dituen `dom`, `mbstring`,
`xml` eta `xmlwriter` luzapenak behar dira. Webgunearen benetako exekuzioak
MySQL eta `mysqli` ere behar ditu; unitate-probek ez dute datu-basea erabiltzen.

## Zeregin bakoitza

```sh
git switch main
git pull --ff-only origin main
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

GitHub-en ireki pull request bat `main` adarrera. Lotu issuea `Closes #ZENBAKIA`
erabiliz, azaldu aldaketa eta gehitu proben ebidentziak. Berrikusleak kodea,
probak eta CI emaitza aztertzen ditu; ondoren, taldekide batek bateratzen du.
Ez egin force push `main` adarrean.

## Administratzailearen konfigurazioa

Settings > Rules > Rulesets atalean babestu `main`: pull requesta eskatu,
gutxienez beste pertsona baten onarpena, elkarrizketak ebatzita izatea eta
`PHP checks (8.3)` / `PHP checks (8.5)` kontrolak gainditzea. Blokeatu force
push eta adarraren ezabaketa. Kontrolak lehenengo CI exekuzioaren ondoren
hautatu ahal izango dira. Fitxategi honek ez ditu GitHub-eko baimenak edo
arauak automatikoki aktibatzen.

## Ebidentziak

Gorde issuearen, lan-adarraren, commit-en, beste taldekidearen berrikuspenaren
eta CI exekuzioaren estekak. GitHub Actions-eko `php-tests-*` artefaktuek
JUnit txostena dute. Kontsultatu [pipelinearen dokumentazioa](docs/PIPELINEA.md).
