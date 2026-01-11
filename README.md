# Temperatur Dashboard

Ein Web-Dashboard zur Visualisierung von Temperaturdaten von ESP-Sensoren.

## Features

- **Echtzeit-Temperaturanzeige** für drei Sensoren:
  - 🔴 Außentemperatur
  - 🟡 Innentemperatur  
  - 🟢 Heizungstemperatur

- **Interaktive Bedienung**:
  - Zoom und Pan im Diagramm
  - Zeitraum-Auswahl (1h bis 7 Tage)
  - Datum/Zeit-Filter
  - Auto-Refresh alle 5 Sekunden

- **Visuelle Highlights**:
  - 0°C-Linie als Referenz
  - Ausreißer-Markierung bei Temperaturen < 0°C
  - Statistiken für sichtbaren Bereich
  - Custom Tooltip mit Datum und farbigen Werten

## Technologie

- **Frontend**: HTML5, CSS3, JavaScript
- **Chart Library**: Chart.js 4.4.1 mit Zoom-Plugin
- **Backend**: PHP API (`api/query.php`)
- **Device**: ESP13 Temperatursensor

## Installation

### Konfiguration
1. `.env.example` zu `.env` kopieren
2. Datenbank-Verbindung in `.env` anpassen:
   ```
   DB_HOST=localhost
   DB_NAME=temperatur
   DB_USER=root
   DB_PASS=yourpassword
   API_KEY=yoursecretkey
   ```

### Setup
1. Dateien auf Webserver kopieren
2. PHP-Backend für Datenabfrage einrichten
3. `index.html` im Browser öffnen

### Docker
- Nutzt echte Umgebungsvariablen (keine .env Datei)
- `.env` nur für lokale Entwicklung
- Container-ready Konfiguration

## Bedienung

- **Mausrad**: Zoom im Diagramm
- **Ziehen**: Pan/Verschieben der Ansicht
- **Hover**: Tooltip mit Temperaturwerten
- **Reset Zoom**: Zurück zur Vollansicht
- **Range**: Zeitbereich eingrenzen