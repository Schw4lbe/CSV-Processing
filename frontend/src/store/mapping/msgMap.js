export default {
  // Frontend Errors:
  FEE01: "Die maximale Dateigröße beträgt 5MB",
  FEE02: "Dateiname enthält ungültige Zeichen",
  FEE03: "Ungültiges Dateiformat",
  FEE04: "Bitte CSV-Datei auswählen",
  FEE05: "CSV Upload fehlgeschlagen",
  FEE06: "CSV Export fehlgeschlagen",
  FEE07: "Fehler in Suchanfrage",
  FEE08: "Sonderzeichen in Suche",

  // Backend Errors:
  get BEE01() {
    return this.FEE01;
  },
  get BEE02() {
    return this.FEE02;
  },
  get BEE03() {
    return this.FEE03;
  },
  get BEE04() {
    return this.FEE04;
  },
  get BEE05() {
    return this.FEE05;
  },
  BEE06: "Tabelle angelegt, File import fehlgeschlagen",

  // Frontend Warnings:
  FEW01: "Suche ergab keine Übereinstimmungen",

  // Frontend Success:
  FES01: "CSV erfolgreich importiert",
  FES02: "Artikel wurde gelöscht",
  FES03: "Artikel wurde bearbeitet",
  FES04: "Neuer Artikel wurde hinzugefügt",
  FES05: "CSV zum Download bereit",
  FES99: "Sitzung wurde beendet",
};
