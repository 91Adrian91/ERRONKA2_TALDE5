# DEV-02 / #16 — Git eta adarren bidezko talde-lana

Egiaztapen-data: 2026-10-30. Issuea: [#16](https://github.com/91Adrian91/ERRONKA2_TALDE5/issues/16).
Helburua: webaren kodea lokalean eta GitHuben, adarrak eta taldeko
berrikuspen-prozedura modu egiaztagarrian erabiltzea.

## Adarrak eta une honetako egitura

| Adarra | Egiaztatutako erabilera |
|---|---|
| `main` | Lehen integrazioaren helmuga; egiaztapenean `b627130` hasierako commit-a du |
| `web/desarrollo` | Webaren integrazio-adarra; kode osoa, segurtasuna, Docker, probak eta dokumentazioa |
| `dev/git-github-actions` | CI prestatzeko adarra; `8ff97b4` commit-a |
| `web/WEB-11-login-xss` | AUD-001eko lan-adar lokala; bere commit-a `web/desarrollo` historian dago |
| `web/sonarqube-avisos` | SonarSource zuzenketen adar lokala; commitak `web/desarrollo` historian daude |
| `web/arrue-edukia-diseinua` | Eduki/diseinuaren adar lokala; `6622351` dago integrazio-historian |

Tokiko adar bat egoteak ez du bere adar-izena GitHuben argitaratuta dagoela
frogatzen. Argitaratutako adar nagusia `web/desarrollo` da; aurreko lana
bere commit-historian dago.

Snapshot egiaztagarria: [git-state.json](ebidentziak/DEV-02/git-state.json).

## Benetako ekarpenen trazabilitatea

`309ea70` oinarriko historian, `main`-ekiko zortzi commitak `AnderAzpiazu`
egilearekin agertzen dira. Ez da beste kide baten ekarpenik edo onarpenik
asmatu. Egile-erregistroa ez da berez kide guztien parte-hartzearen froga.

| Commit-a | Ekarpena |
|---|---|
| [14b3d9d](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/14b3d9d) | Irakasleen weba gehitzea |
| [8ff97b4](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/8ff97b4) | GitHub Actions, PHPStan eta unitate-probak |
| [4f79186](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/4f79186) | AUD-001: login action finkoa eta erregresioa |
| [7b05a2a](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/7b05a2a) | SonarQube zuzenketak eta dokumentazioa |
| [4180982](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/4180982) | SonarSource analisiaren zuzenketak eta ebidentziak |
| [6a7f71a](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/6a7f71a) | Txostenaren antolaketa |
| [6622351](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/6622351) | Arrueren edukiak, irudiak eta diseinua |
| [309ea70](https://github.com/91Adrian91/ERRONKA2_TALDE5/commit/309ea70) | Saio/CSRF babesa, Docker eta HTTP suitea |

Historiala egiaztatzeko, repoaren barruan:

```sh
git fetch origin
git log --oneline --decorate --graph --all
git log --format="%h %an %s" origin/main..origin/web/desarrollo
git diff --stat origin/main...origin/web/desarrollo
```

## Taldeko lan-fluxua

1. Kide bakoitzak bere GitHub kontua eta benetako Git identitatea erabili.
2. `web/desarrollo` eguneratu `pull --ff-only` bidez eta issueari lotutako adarra sortu.
3. Aldaketa egin; dagozkion probak eta dokumentazioa egiaztatu.
4. Fitxategi zehatzak stage-ra gehitu, diff-a berrikusi eta commit ulergarria sortu.
5. Lan-adarra GitHubera igo eta PRan issuea, helburua eta ebidentziak lotu.
6. Beste kide batek bere kontutik review-a egin; zuzenketak badaude lan-adarrean osatu.
7. CI berde eta onarpen errealaren ondoren bateratu; issuea irizpide guztiak betetzean itxi.

Komando zehatzak: [CONTRIBUTING](../CONTRIBUTING.md). Bateratu aurretik
`git diff --check` eta CI kontrolak egiaztatu. Ez igo `.env`, `vendor/`,
tokiko proben `build/` edo kredentzialik.

## CI eta ebidentziak

- [309ea70-ko CI: 40859846009](https://github.com/91Adrian91/ERRONKA2_TALDE5/actions/runs/40859846009), **success**.
- `PHP checks (8.3)`, `PHP checks (8.5)` eta `HTTP security and Docker` gaindituta.
- `php-tests-8.3`, `php-tests-8.5` eta `security-http` artefaktuak daude exekuzioan.
- [11 unitate-proba / 30 baieztapen eta 76 HTTP egiaztapen](auditoria/ebidentziak/WEB-SEC-001/validation.json).
- [Webaren inbentarioa eta erreprodukzioa](WEB-SEGURTASUNA.md).

Ebidentzia horiek `309ea70` bertsioari dagozkio. PRaren azken commitak
dagokion CI propioa izan behar du; ez aurkeztu exekuzio zaharra commit berri
baten emaitza balitz bezala.

## Main babesa — prestatuta, administratzaileak aktibatzeke

2026-10-30eko GitHub kontsultan `main.protected=false` eta ruleset zerrenda
hutsik zeuden. Konektatutako kontuak WRITE baimena du, ez ADMIN.

[.github/rulesets/main.json](../.github/rulesets/main.json) proposamenak:

- PR bidezko integrazioa eta beste pertsona baten onarpen bat eskatzen ditu.
- Azken push-aren ondoren onarpena eta elkarrizketa ebatzita egotea eskatzen du.
- CI-ko hiru kontrolak eskatzen ditu, helmuga-adarrarekiko eguneratuta.
- Force push eta `main` ezabatzea blokeatzen ditu.

Fitxategia ez da automatikoki aplikatzen. Repoaren jabeak edo adminak,
proposamena berrikusi ondoren, GitHub Rulesets bidez konfiguratu edo bere
terminal autentifikatuan eskaera hau egin dezake:

```sh
gh api --method POST repos/91Adrian91/ERRONKA2_TALDE5/rulesets --input .github/rulesets/main.json
```

[GitHub dokumentazio ofiziala: repository rulesets](https://docs.github.com/en/rest/repos/rules#create-a-repository-ruleset).

## #16 ixteko egiaztapenak

- [x] Kodea lokalean eta GitHubeko lan-adarrean dago.
- [x] Adarren eta commit-en erabilera dokumentatu da.
- [x] Klonazio, aldaketa, push eta PR prozedura zehaztuta dago.
- [x] Benetako CI eta proben ebidentziak lotuta daude.
- [ ] Integrazioaren PRa beste kide batek berrikusi eta onartu du.
- [ ] Onartutako lana `main`-en bateratu da, gatazkak eta CI egiaztatuta.
- [ ] Main babesa adminak aktibatu du, taldeak ezarri duen lan-araua betearazteko.

Erabiltzaileak berrikuspen-eskaera geroago egitea hautatu du. PRa zirriborro
gisa prestatzen da; ez da inor berrikusle gisa esleitzen eta issuea ez da
automatikoki ixten. PRaren esteka eta egoera zehatza #16 issuean jasoko dira.
