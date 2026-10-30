# DEV-03 — GitHub Actions pipelinea

Workflowa: [ci.yml](../.github/workflows/ci.yml).

Biltegiaren erroan egon behar du `.github/workflows/ci.yml` fitxategiak.
`push`, `pull_request` eta eskuzko exekuzioetan PHP 8.3 eta 8.5 erabiltzen ditu.
Ez du webgunea automatikoki ekoizpenean hedatzen; une honetako lana CI da.

| Kontrola | Komandoa | Helburua |
|---|---|---|
| Instalazioa | `composer install` | `composer.lock`-eko bertsio berak erabiltzea |
| Konfigurazioa | `composer validate --strict` | Manifestua eta lock fitxategia koherenteak direla ziurtatzea |
| Sintaxia | `composer lint` | Webguneko, tresnetako eta probetako PHP sintaxia aztertzea |
| Analisi estatikoa | `composer analyse` | PHPStan-en bidez programazio-akats berriak detektatzea |
| Unitate-probak | `composer test` | Saio-hasierako eta kontuko ikuspegien portaera egiaztatzea |

Kontrol batek huts egiten badu, job osoak huts egiten du. Ez dago
`continue-on-error` aukerarik. Proben XML txostena artefaktu gisa gordetzen da,
probak huts egiten badute ere. `contents: read` baimena erabiltzen da.

## Uneko egoera — 2026-10-29

`composer check` gaindituta dago: PHP sintaxia, PHPStan 0 maila eta
**11 proba / 30 baieztapen**. Baselineko `ignoreErrors` zerrenda hutsik dago.
`SecurityHelpersTest.php`-k tokenen saio-lotura, sarrera mota okerrak,
iraungitze absolutua/idle, pasahitzen byte-muga eta HTML kodetzea probatzen ditu.
`AuthenticationViewsTest.php`-ko aurreko bost probak mantendu dira.

CI-k bi lan egiten ditu:

1. PHP 8.3 eta 8.5: Composer baliozkotu, sintaxia, PHPStan eta PHPUnit;
   `php-tests-*` artefaktuetan JUnit txostenak gorde.
2. `HTTP security and Docker`: web/Apache eta MySQL edukiontzi isolatuetan
   eraiki, **76 HTTP/MySQL egiaztapen** exekutatu eta `security-http` JSON
   artefaktua gorde. Amaieran proba-ingurunea gelditu.

Probek BD isolatua eskatzen dute (`arrue_test`); ez dira erabiltzailearen
`arrue-mysql` edukiontzian edo datu errealetan exekutatzen. Saio-probetako
zerbitzari gehigarriak iraungitze laburra eta Secure cookie flag-a ditu;
barne-sarean bakarrik dago eta ez du host-porturik.

Aurreko diseinu-commit-aren (`6622351`) CI urrunekoa egiaztatu da:
[40843454090 — success](https://github.com/91Adrian91/ERRONKA2_TALDE5/actions/runs/40843454090).
Horrek ez du ondorengo segurtasun-aldaketen CI frogatzen; horien exekuzioa
commit berriarekin lotu behar da. Ez dago ekoizpenerako deploy automatikoa.

Egungo inbentarioa, mugak eta ebidentziak: [WEB-SEGURTASUNA](WEB-SEGURTASUNA.md).

## Erabilera lokala

Web osoa eta MySQL Compose bidez abiatu daitezke. Arrankea, instalazio
zaharren migrazioa eta proba isolatuen komandoak: [README](../README.md).

```sh
composer install
composer check
composer test -- --log-junit build/junit.xml
```

Windows-en Composer-ek bere konfigurazio-karpetan idazteko baimenik ez badu:

```powershell
$env:COMPOSER_HOME = Join-Path (Get-Location) '.composer'
$env:COMPOSER_CACHE_DIR = Join-Path $env:COMPOSER_HOME 'cache'
composer install
composer check
```

## Entregatzeko ebidentziak

- PRaren eta beste taldekidearen berrikuspenaren estekak.
- GitHub Actions-en exekuzio berdea eta bi PHP bertsioen emaitzak.
- Akats kontrolatu baten ondorioz huts egin duen exekuzioa eta zuzenketaren ondorengo emaitza.
- JUnit txostena eta baselineko akatsaren azalpena.
- Aldatutako/sortutako fitxategien zerrenda checklist-ean: `.github/`,
  `.gitignore`, `.gitattributes`, `composer.json`, `composer.lock`,
  `phpstan.neon`, `phpstan-baseline.neon`, `phpunit.xml`, `tools/`, `tests/`,
  `CONTRIBUTING.md` eta `docs/`.

## Tokiko egiaztapena — 2026-10-26

Atal hau lehen exekuzioaren erregistro historikoa da; ondorengo kodea eta
proba kopurua goiko uneko egoeran daude.

PHP 8.5.1, PHPStan 2.3.2 eta PHPUnit 11.5.57 erabiliz:

- `composer validate --strict`: zuzena.
- `composer check`: sintaxia eta analisi estatikoa zuzenak; 4 proba eta 12 baieztapen gaindituta.
- Aldi baterako `strlen()` dei oker batekin: PHPStan-ek akats berria detektatu eta 1 itzuli du.
- Saio-hasierako mezua aldi baterako aldatuta: PHPUnit-ek erregresioa detektatu eta huts egin du.

Bi aldaketa esperimentalak kendu dira. Emaitza hauek tokikoak dira;
GitHub-eko exekuzioak bereiz egiaztatu behar dira. Webgunearen kodea ez da aldatu.

## Erreferentziak

- [PHPStan: instalazioa eta analisia](https://phpstan.org/user-guide/getting-started)
- [PHPStan: baselinea](https://phpstan.org/user-guide/baseline)
- [GitHub Actions: workflow sintaxia eta baimenak](https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax)
