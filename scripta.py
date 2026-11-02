#!/usr/bin/env python
# coding: utf-8

# # 1. ATALA: Datu-Basera Konexioa eta Erabiltzaileen Bilaketa
# Gelaxka honek 'bdweb' datu-basera konektatzen da eta 'users' taulan profileko azrgazkirik ('irudia' eremua NULL edo hutsik) ez duten erabiltzaileen izena ('izena') eta posta elektronikoa ('username') lortzen ditu.

# In[1]:


import mysql.connector

# MariaDB / MySQL datu-basearen konfigurazioa
DB_KONFIGURAZIOA = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'bdweb'
}

def irudirik_gabeko_erabiltzaileak_lortu():
    """bdweb datu-basera konektatzen da eta profileko argazkia falta zaien erabiltzaileak lortzen ditu."""
    try:
        konexioa = mysql.connector.connect(**DB_KONFIGURAZIOA)
        kurtsorea = konexioa.cursor(dictionary=True)

        # 'irudia' NULL edo katea hutsik duten erregistroak irazten ditu
        kontsulta = "SELECT izena, username FROM users WHERE irudia IS NULL OR irudia = ''"
        kurtsorea.execute(kontsulta)

        erabiltzaileak = kurtsorea.fetchall()
        kurtsorea.close()
        konexioa.close()

        return erabiltzaileak
    except mysql.connector.Error as err:
        print(f"[ERROREA] Errorea datu-basera konektatzean edo kontsulta egitean: {err}")
        return []

# Erabiltzaileak lortu eta bistaratu
irudirik_gabekoak = irudirik_gabeko_erabiltzaileak_lortu()
print(f"[INFORMAZIOA] Irudirik gabeko {len(irudirik_gabekoak)} erabiltzaile aurkitu dira.")


# # 2. ATALA: Posta Elektronikoak Bidaltzea (SMTP)
# Gelaxka honek aurreko atalean lortutako erabiltzaile bakoitzari abisu-mezu bat bidaltzen dio local-ean dagoen `smtp4dev` zerbitzariaren bidez (SMTP protokoloa erabiliz).

# In[2]:


import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# Probako SMTP zerbitzariaren konfigurazioa (smtp4dev)
SMTP_ZERBITZARIA = 'localhost'
SMTP_PORTUA = 2525           # Docker-en bideratutako portua (-p 2525:25)
BIDALTZAILE_EMAILA = 'admin@arrue.com'

def jakinarazpen_postak_bidali(erabiltzaileak):
    """Pasatako erabiltzaile zerrendako bakoitzari abisu-posta elektroniko bat bidaltzen dio."""
    if not erabiltzaileak:
        print("[INFORMAZIOA] Ez dago jakinarazpenik bidaltzeko pendente.")
        return

    print(f"[INFORMAZIOA] Postak bidaltzen hasten...")

    try:
        # SMTP zerbitzarira konektatu
        zerbitzaria = smtplib.SMTP(SMTP_ZERBITZARIA, SMTP_PORTUA)

        for erabiltzailea in erabiltzaileak:
            jasotzailea = erabiltzailea['username']
            izena = erabiltzailea['izena']

            # MIME mezua eraiki
            mezua = MIMEMultipart()
            mezua['From'] = BIDALTZAILE_EMAILA
            mezua['To'] = jasotzailea
            mezua['Subject'] = "GARRANTZITSUA: Profileko argazkia kargatzea falta da"

            gorputza = f"""Kaixo {izena},

            Gure sisteman egiaztatu dugu ez duzula profileko argazkirik kargatuta. 
            Segurtasun eta trazabilitate arrazoiengatik, irudia jartzea derrigorrezkoa da.

            Mesedez, sartu sisteman ahalik eta azkarren eta igo zure argazkia.

            Eskerrik asko,
            Arreta Zerbitzua
            """
            mezua.attach(MIMEText(gorputza, 'plain', 'utf-8'))

            # Posta bidali
            zerbitzaria.sendmail(BIDALTZAILE_EMAILA, jasotzailea, mezua.as_string())
            print(f"[ONDO] Jakinarazpena ondo bidali da hona: {jasotzailea}")

        zerbitzaria.quit()
        print("[INFORMAZIOA] Jakinarazpen prozesua zuzen amaitu da.")

    except Exception as e:
        print(f"[ERROREA] Errorea gertatu da SMTP protokoloan: {e}")

# Postak bidali
jakinarazpen_postak_bidali(irudirik_gabekoak)

