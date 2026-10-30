# Arrue webgunearen edukia

Enpresaren testuen iturria Obsidianeko `CHAT_ARRUE_ERRONKA.md` oharra da:
Euskal Herriko enpresa, Europako banaketa, 55 langile, kliniken 3D diseinuak,
fabrikazio automatizatua eta bi kilometrora dagoen bigarren ekoizpen-gunea.
Trazabilitatea hobekuntza-helburu gisa aurkezten da, ez ezarritako funtzio gisa.

`erronka2_web/about.html` orriak argazkien egileak eta iturriak jasotzen ditu.
Hiru argazkiak tokiko `images/hortz-*.jpg` fitxategietan daude; ez dute kanpoko
zerbitzurik behar orria kargatzean. Irudi adierazgarriak dira, ez Arrueren argazkiak.
`arrue-hortza.svg` webgunerako sortutako hortz-ikur bat da.

## Katalogoa eguneratzea

Instalazio berriek `bdweb.sql` fitxategiko hortz-katalogoa jasotzen dute.
Prezioak eta stocka jatorrizko laborategiko balioak dira, ez benetako eskaintza.

Instalatutako datu-baseetarako, inportatu **`tools/arrue-catalog.sql` soilik**
BDweb datu-basean (phpMyAdmin edo MySQL bezeroarekin). Ez inportatu berriro
`bdweb.sql` osoa lehendik dagoen instalazio batean. Eguneratzeak jatorrizko
hiru produktuak bakarrik ordezkatzen ditu, IDa, izena eta irudia bat badatoz;
ez du konturik, preziorik, stockik edo bestelako produkturik aldatzen.

Pribatutasunaren atalak proiektuaren erabilera azaltzen du. Benetako zerbitzua
eskaini aurretik, arduradunaren eta tratamenduaren informazioa osatu behar da.
