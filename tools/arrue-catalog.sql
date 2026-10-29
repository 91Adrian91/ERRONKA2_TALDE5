-- Dagoeneko instalatutako laborategiko BDweb datu-basean exekutatzeko.
-- Jatorrizko hiru adibideak soilik eguneratzen dira: ez dira beste produktuak ukitzen.
-- Prezioak eta stocka lehengoak dira; ez dira Arrueren benetako eskaintzak.
SET NAMES utf8mb4;
START TRANSACTION;
UPDATE produktuak
SET izena = 'Hortz sintetikoa',
    deskripzioa = 'Klinikaren 3D diseinutik abiatuta fabrikatutako hortz-pieza. Doitasunezko akabera duen katalogoko adibidea.',
    pic = 'hortz-protesia.jpg'
WHERE id = 1 AND izena = 'produktu 1' AND pic = 'generikoa.jpg';
UPDATE produktuak
SET izena = 'Hortz-eredua',
    deskripzioa = 'Hortz-arkuaren eredua, diseinuaren forma eta piezen kokapena aztertzeko. Katalogoko adibidea.',
    pic = 'hortz-eredua.jpg'
WHERE id = 2 AND izena = 'produktu 2' AND pic = 'generikoa.jpg';
UPDATE produktuak
SET izena = 'Hortz-moldeen multzoa',
    deskripzioa = 'Hortz-formak eta diseinuaren xehetasunak alderatzeko molde-multzoa. Katalogoko adibidea.',
    pic = 'hortz-moldeak.jpg'
WHERE id = 4 AND izena = 'produktu 3' AND pic = 'producto3.jpg';
COMMIT;
