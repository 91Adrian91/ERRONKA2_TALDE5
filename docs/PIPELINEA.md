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

## Hasierako kodearen egoera

Webgunearen PHP kodea jatorrizko moduan mantendu da. PHPStan 0 mailarekin
hasten da, eta hasierako akats bakarra `phpstan-baseline.neon` fitxategian
jasota dago: `includes/register.php` fitxategiko `mysqli_error()` deiak ez du
beharrezko konexio-argumentua jasotzen. Baselineak akats hori ezaguntzat
hartzen du; akats berriek pipelinea geldiarazten dute. Akatsa konpontzean,
kendu haren baseline sarrera ere, bestela CI-k sarrera zaharkitua salatuko du.
Ez birsortu baselinea akats berriak isilarazteko. Helburua baselinea hustea
eta pixkanaka analisi-maila igotzea da.

Lau unitate-probek honakoak egiaztatzen dituzte:

1. Gonbidatuak saio-hasierako formularioa ikusten du, POST metodoarekin eta pasahitz-eremuarekin.
2. Autentifikatutako erabiltzaileak ez du saio-hasierako formularioa berriz ikusten.
3. `admin=0` duen saioak saio-hasierako formularioa ikusten du.
4. Erabiltzaile arruntaren kontuak pasahitza aldatzeko formularioa erakusten du, erabiltzaileak kudeatzeko kontrolik gabe.

Probek PHP ikuspegiak isolatuta exekutatzen dituzte, datu-baserik gabe.
Ez dute frogatzen SQLi, XSS, autentifikazioa edo baimenak seguruak direnik.
Ahulezien zuzenketekin batera erregresio- eta integrazio-probak gehitu behar dira.
PHPStan 0 maila ere ez da segurtasun-auditoria baten ordezkoa.

## Erabilera lokala

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
