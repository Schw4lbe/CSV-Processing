# Aufgabenergebnis - CSV Processing

## Eingesetzte Technologien / Frameworks

- Vue.js 3.2.13 samt VUEX als Frontend Framework
- SCSS für das Styling mit VSCode Extension LiveSass Compiler
- PHP 8 ohne Framework im Backend
- XAMPP Apache (Backend Webserver) und MySQL (Datenbanklösung)
- Gulp als Task Automatisierung für CSS Kompatibilität & Kompression
- Docker als Containerlösung zur Bereitstellung einer Demo

##### Why Vue.js?

- sehr zugänglich, top Schulungsmaterial und Dokumentation
- Reactive Data Bindings, sehr dynamisch und schnell
- Enge Zusammenarbeit mit der Community
- Erste Wahl bei privaten Projekten - macht einfach Spaß damit zu entwickeln!
  Erfahrung & Umgang Vue.js (7/10)

##### Why SCSS?

- Nesting, @use @forward Struktur, Zusatzfunktionen
- saubere Gliederung durch Partials, Wartbarkeit
  Erfahrung & Umgang SCSS (8/10)

##### Why PHP?

- Syntax sehr ähnlich zu C#
- Object Oriented Programming sehr gut umsetzbar
- aktuelle Marktpräsenz ca. 78%
- _Anmerkung_: Bewusster Verzicht auf ein Backend Framework und Node.js um erlernte PHP Inhalt zu vertiefen. Beginn selbständiger Weiterbildung in prozeduraler und Objekt orientierter PHP Programmierung vor 4 Monaten.
  Erfahrung & Umgang PHP (4/10)

##### Why XAMPP?

- Lightweight, funktioniert out of the Box
- erfüllt genau meine aktuellen Bedürfnisse
  Erfahrung & Umgang XAMPP (3/10)

##### Why Gulp?

- Tasks schnell und einfach angelegt
- nahtlose Integration in Build Prozess
- top Doku und sehr umfangreiche Erweiterungen
  Erfahrung & Umgang Gulp (2/10)

##### Why Docker?

- Betriebssystemunabhängig
- Sehr zugänglich, top Doku
- Umfangreicher Support verschiedener Technologien
- Automatisierung und Portabilität
- _Anmerkung_: Meine ersten Erfahrungswerte mit Docker, bislang begeistert und gewillt mehr zu lernen!
  Erfahrung & Umgang Docker (1/10)

---

## Eingesetzte 3rd Party Libraries

Ich setze in meinem Projekt die folgenden 3rd Party Libraries ein:

| Name                                              | Begründung                                            |
| ------------------------------------------------- | ----------------------------------------------------- |
| [Vuetify](https://vuetifyjs.com/en/)              | Zur Formulardarstellung, Paginierung und Bearbeitung. |
| [ChartKick + chart.js](https://chartkick.com/vue) | Darstellung von Diagrammen                            |

##### Why Vuetify?

- nahtlose Anbindung an Vue.js
- Doku umfangreich und mit Code Playground sehr intuitiv
- Umfangreiche Komponenten Bibliothek, Aufbau modular und flexibel
- clean Material Design
- _Anmerkung_: Bewusster Verzicht auf Bootstrap. Mit Boostrap konnte ich bereits viel Erfahrung sammeln. Ich wollte etwas Neues ausprobieren.
  Erfahrung & Umgang Vuetify (2/10)

##### Why Chartkick?

- schnell eingerichtet und rdy to go
- simple Darstellung ausreichend Funktionen für diesen Usecase
- _Anmerkung_: In Zukunft möchte ich mir noch andere Alternativen ansehen. Lösung in Summe gefühlt zu statisch.
  Erfahrung & Umgang Chartkick (2/10)

---

## Installation / Ausführen des Projektes

##### Installationsanweisung für Windows & MacOS

- Download Docker Desktop _[docker-desktop](https://www.docker.com/products/docker-desktop/) v4.27.1 or later_
- Installation durchführen und zum Abschluss der Einrichtung das Gerät neu starten.
- Zur Ausführung der Demo ist keine Anmeldung bei Docker notwendig.

##### Installationsanweisung für Linux

- Download und Installation von _Docker Engine & Docker Compose_ via Commandline
- Commandline öffnen und folgende Befehle ausführen

```console
// docker engine:
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io

// docker compose:
sudo curl -L "https://github.com/docker/compose/releases/download/2.24.5/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

// version check:
docker --version
docker-compose --version
```

##### Ausführung der Demo für alle Plattformen

- Download Projekt Tag _[CSV-Processing](https://github.com/TapeMate/CSV-Processing/tags) v1.2.1_
- Ordner in Wunschverzeichnis entpacken
- Docker Dienste starten
- Commandline öffnen und in das Root Verzeichnis des entpackten Projektes navigieren
- Folgenden Befehl ausführen:

```console
docker-compose up --build -d
```

- nach Abschluss ein paar Sekunden warten und Wunschbrowser öffnen
- **Frontend**: http://localhost:5000/
- **phpmyadmin**: http://localhost:8000/

Im Projektverzeichnis befindet sich die **Artikel.csv** Datei aus der Aufgabenstellung.
Diese kann für den Testimport verwendet werden.

---
