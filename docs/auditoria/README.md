# Webgunearen auditoria — urratsez urrats

Helburua: ahuleziak banan-banan identifikatzea, frogatzea, zuzentzea eta
berriz probatzea. Dokumentazio honetan egindako probak eta oraindik egiteko
daudenak bereizten dira.

| IDa | Gaia | Egiaztapena | Egoera |
|---|---|---|---|
| [AUD-001](AUD-001.md) | Saio-hasierako `action` atributuan HTML/JavaScript injekzioa | Ikuspegi isolatua, Chrome eta PHPUnit | Puntu hori zuzenduta; HTTP integrazio-proba egiteke |

Oraingoz lehen puntu hau bakarrik landu da. Horrek ez du esan nahi webguneko
XSS guztiak edo beste ahuleziak konponduta daudenik.

## Lehen urratsaren fitxategiak

- [Azalpena, aurreko kodea, zuzenketa eta probak](AUD-001.md).
- [Zuzenketaren aurreko benetako captura](ebidentziak/AUD-001/aurretik.png).
- [Zuzenketaren ondorengo benetako captura](ebidentziak/AUD-001/ondoren.png).
- [Aldaketen checklist-a](CHECKLIST.md).

Hurrengo urrats txikia: webgunea eta datu-basea lokalean martxan jarri eta
AUD-001 kasua HTTP bidez egiaztatu. Ondoren, bilaketaren irteera aztertu,
beste aldaketa batekin nahastu gabe.
