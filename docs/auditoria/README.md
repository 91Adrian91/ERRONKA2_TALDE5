# Webgunearen auditoria — urratsez urrats

Helburua: ahuleziak banan-banan identifikatzea, frogatzea, zuzentzea eta
berriz probatzea. Dokumentazio honetan egindako probak eta oraindik egiteko
daudenak bereizten dira.

## Uneko egoera — 2026-10-29

Egungo kontrolen, aurkikuntzen eta erreprodukzioaren dokumentu nagusia:
[WEB-SEC-001 / WEB-SEGURTASUNA](../WEB-SEGURTASUNA.md).

- PHP sintaxia eta PHPStan gaindituta; **11 unitate-proba / 30 baieztapen**.
- **76 HTTP/MySQL egiaztapen gaindituta** ingurune isolatuan: XSS, SQLi,
  pasahitzak, CSRF, baimenak, irudi-igoerak eta saioen bizi-zikloa.
- Weba eta MySQL edukiontzietan martxan. CI-k probak automatikoki errepikatzen ditu.
- [HTTP JSONa](ebidentziak/WEB-SEC-001/http-security.json) eta
  [baliozkotzea + iturrien hashak](ebidentziak/WEB-SEC-001/validation.json).

## Aurreko ebidentziak

| IDa | Gaia | Esparrua / egoera |
|---|---|---|
| [AUD-001](AUD-001.md) | Login action / PHP_SELF | Aurreko proba isolatua mantenduta. Egungo Apache-n PATH_INFO erasoko eskaera HTTP 404 bidez baztertzen da. |
| SONAR-001 | HTML/CSS eta sarbideak | [29 egiaztapen historiko](ebidentziak/SONAR-001/egiaztapenak.json); ez da SonarQube berranalisia |
| SONAR-002 | PHP/HTML/CSS eta SQL/pasahitz zuzenketak | [2026-10-27 laburpena](ebidentziak/SONAR-002/laburpena.json): 147 → 0, 89 HTTP egiaztapen |
| SONAR-004 | index.php berreskuratzea | [2026-10-28 laburpena](ebidentziak/SONAR-004/laburpena.json): 45 → 0; karpeta oraindik Git-en jarraipenik gabe |

Ez da SonarQube analisi berririk egin egungo aldaketetarako. Proba hauek
estaldura zehatza dute; ez dira segurtasun-ziurtagiri edo pentest independente osoa.
Ez aldatu aurreko ebidentziak egungo emaitzak balira bezala.
