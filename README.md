# IPS-App-Dashboard

Startseite für die Kachel-Visualisierung von IP-Symcon im Stil von Home Assistant.

## Modul "Dashboard"

Eine HTML-Kachel (HTML-SDK) mit den Räumen als Raster: Icon (HA-Blau) und Name mittig, optional eine Temperatur oben links. Am Handy drei Spalten, auf breiten Kacheln so viele wie Platz ist (mindestens 180 px). Ein Tipp öffnet den Raum.

- **Räume:** Liste der Raumordner (Kategorien). Icon und Name werden aus dem Ordner gelesen, ein Ordner ohne Icon bekommt `house`.
- **Temperatur:** optionale Variable je Raum, wird formatiert angezeigt und live aktualisiert.

Farben und Schrift kommen aus dem Design der Visualisierung. Benötigt Symcon 8.2 (`openObject`).
