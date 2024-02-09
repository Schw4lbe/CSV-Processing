# FRONTEND

## COMPONENTS

---

### FileUpload.vue

> File Upload stellt Einstiegspunkt der Anwendung dar. File Upload wird gerendert solange kein tableName ergo kein Data Table in SQL angelegt wurde. Der Form Tag hat ein file Input Tag samt soft indicator als p-Tag, ob die Datei den Vorgaben engspricht. Der Input reagiert auf Veränderung und löst ein Event zur Validierung aus. Der Button schickt die CSV Datei via action und service API an das Backend zur zweiten Validierung und erstellt einen SQL Data Table. Solange eine Antwort austeht, wird eine Animation angezeigt. Bei Erfolg stoppt die Animation, vom Backend kommt der tableName zurück und wird im local Storage sowie State gespeichert. Eine Erfolgsmeldung wird angezeigt.

##### TEMPLATE

> Das Template mit v-if.

```html
<div v-if="!getTableName" class="form-wrapper">
  <!-- ... -->

    <!-- on Change Event in input file tag -->
      <input id="csv" ref="fileInput" type="file" @change="onFileChange" />
    <!-- soft Indikator für CSV Validierung -->
      <p v-if="isCsv === null" class="msg-csv-pending">
        CSV Datei auswählen...
      </p>
      <p v-if="isCsv === true" class="msg-csv-valid">
        CSV ausgewählt<i class="fa-solid fa-circle-check"></i>
      </p>
       <!-- Button feuert Event im Form ab -->
      <button type="submit">importieren</button>
    </form>
  </div>
</div>
```

##### SCRIPT

> onFileChange Event fängt die ausgewählte Datei ab und gibt Sie an die Validierung weiter.

```js
    onFileChange(e) {
      const file = e.target.files[0];
      // Erste Sicherheitsebene im Frontend
      if (file && this.isValidFile(file)) {
        this.selectedFile = file;
        this.isCsv = true;
      } else if (!this.isValidFile(file)) {
        // Falls Validierung fehlschlägt wird die Datei aus der Komponente gelöscht
        this.$refs.fileInput.value = "";
      }
    },

```

> Die Validierung prüft Die Größe, den Dateinamen und den Dateitypen.

```js
isValidFile(file) {
      // 5MB maximale Größe
      const maxSize = 5 * 1024 * 1024;
      // Regex für Sonderzeichen
      const invalidChars = /[\]/*?"<>|\\]/;
      // Regex für ".." Directory Traversal Attacks
      const parentDirectoryTraversal = /\.\./;
      // MIME Type basic check
      const validTypes = ["text/csv", "application/vnd.ms-excel"];

      // Nachfolgen Überprüfung mittels Condition und bei Fehlschlag Ausgabe von Fehlermeldung
      if (file.size > maxSize) {
        this.setErrorCode("FEE01");
        return false;
      }

      if (
        invalidChars.test(file.name) ||
        parentDirectoryTraversal.test(file.name)
      ) {
        this.setErrorCode("FEE02");
        return false;
      }

      if (!validTypes.includes(file.type)) {
        this.setErrorCode("FEE03");
        return false;
      }

      return true;
    },
```

> onSubmit Event checkt ob eine Datei vorhanden ist und schickt anschließend die Datei an das Backend. Im Nachfolgenden die einzelnen Schritte.

```js
async onSubmit() {
      // Prüfung Datei vorhanden, wenn nicht Fehlermeldung
      if (!this.selectedFile) {
        this.setErrorCode("FEE04");

      } else if (this.selectedFile) {
        // Start Ladeanimation
        this.triggerLoadingAnimation();
        // benutzung build in web API FormData to set key/value pairs
        const formData = new FormData();
        // hinzufügen der Datei zu formData Object für Backend
        formData.append("file", this.selectedFile);
        // Reset File Input in UI
        this.$refs.fileInput.value = "";
        // Reset selected File in Cache
        this.selectedFile = null;
        // Reset Bool für soft indicator
        this.isCsv = null;
        try {
          // Initiierung der Action Mit formData als payload
          const response = await this.uploadCsv(formData);
          if (response && response.success) {
            // Bei Erfolg Success msg und Ende Ladeanmiation
            this.setSuccessCode("FES01");
          } else {
            // Bei Misserfolg Fehlermeldung und Ende Ladeanimation
            this.setErrorCode(response.errorCode);
          }
        } catch (error) {
          console.error("Error in onSubmit method:", error);
          throw error;
        } finally {
          // Ende Lade Animation
          this.unsetLoadingAnimation();
        }
      }
    },
```

---

### DataChart.vue

> Die DataChart Komponente agiert teilweise als Navigationsleiste. Sie lässt sich auf- und zuklappen um Diagramme an zu zeigen. Select Inputs ermöglichen die Grafische Darstellung der Tabellen Spalten. Rechts am Rand befindet sich der Beenden Button zum Schließen der Anwendung. Es sind 2 Diagramme dargestellt. Beide können unabhängig voneinander Daten anzeigen, welche sich auf alle aktuell dargestellten Zeilen beziehen. Der Beenden Button öffnet einen Dialog. Bei Bestätigung werden Session Variablen resettet / gelöscht und der Benutzer gelangt zurück zum Datei Upload. Ebenso wird ein Request an das Backend zum Drop des SQL Tables geschickt. Durch Löschung des tableName gelangt der Nutzer wieder zum FileUpload Screen.

##### TEMPLATE

> Vorweg der Beenden Dialog samt Auswahl Buttons und Events für Session Exit.

```html
   <!-- Nur angezeigt wenn Beenden Button geklickt wurde und noch ein tableName existiert -->
  <div v-if="exitConfirmPending && getTableName" class="exit-confirm">
    <!-- ... -->
      <div class="btn-confirm-container">
         <!-- Buttons mit Events zur Bestätigung -->
        <v-btn @click="cancelExit" class="btn-confirm-exit">Abbrechen</v-btn
        ><v-btn @click="confirmExit" class="btn-confirm-exit">Beenden</v-btn>
      </div>
    </div>
  </div>
```

> Im Chart Wrapper befindet sich der toggle Container zum ein- und ausblenden der Diagramme sowie der Beenden Button.

```html
<div v-if="getTableName" class="chart-wrapper">
  <div class="chart-control">
    <!-- Steuert Sichtbarkeit der Diagramme und animiert Pfeil und Text -->
    <div @click="toggleCharts" class="chart-header chart-toggle">
      <span
        :class="{
            'arrow-right': !chartsVisible,
            'arrow-down': chartsVisible,
          }"
      ></span
      ><span class="cart-description">Grafische Auswertung</span>
    </div>
    <!-- Beenden Button zum öffnen des Dialogs -->
    <button @click="handleExit" class="btn-exit">beenden</button>
  </div>
</div>
```

> Die beiden Dropdowns zur Auswahl der Diagrammdaten besitzen beide ein Event, welches bei Änderungen neue Daten importiert. Mittels V-Model sind diese mit einer property verknüpft. Die Optionen werden per v-for anhand der chartCategories erzeugt (hierzu nachfolgend mehr).

```html
// select-container wird nur dargestellt, wenn Categorien gesetzt sind
<div v-if="chartCategories.length > 0" class="select-container">
  <div class="select">
    <label for="select1">Kuchen Diagramm</label>
    <select
      @change="updateSelect"
      v-model="selectedChartCat1"
      name="select1"
      id="select1"
    >
      <!-- chartCategorie bekommt daten von dataTable fetch und wird vorab gefiltert -->
      <option
        v-for="(item, index) in chartCategories"
        :key="index"
        :value="item"
      >
        {{ item }}
      </option>
    </select>
  </div>

  <div class="select">
    <label for="select2">Balken Diagramm</label>
    <select>
      <!-- Gleicher Aufbau hier -->
      <!-- <option> Gleicher Aufbau hier</option> -->
    </select>
  </div>
</div>
```

> Die beiden Diagramme werden mittels ChartKick Librarie eingebunden. Der Suffix Wert ermöglich die Darstellung in %.

```html
<pie-chart :data="pieChartData" suffix="%" />
<column-chart :data="columnChartData" suffix="%"></column-chart>
```

##### SCRIPT

> Data Values zur Komponentensteuerung.

```js
 data() {
    return {
      // toggle Indikator
      chartsVisible: false,

      // Caching von Diagrammdaten
      chartCategories: [],
      pieChartData: [],
      columnChartData: [],

      // Standardwerte für Diagramme
      selectedChartCat1: "Geschlecht",
      selectedChartCat2: "Grammatur",

      // Bool für Beenden Dialog
      exitConfirmPending: false,
    };
  },
```

> Ein Watcher achtet auf neue Daten im chartData State. Daten werden danach zur Aufbereitung weitergereicht. Beide Diagramme bekommen einen Default Wert zugewiesen.

```js
watch: {
    getChartData(newVal, oldVal) {
      // Guard zur vermeidung von Fehlern bei Session Exit ohne local Storage Daten
      if (newVal === null || newVal === undefined) {
        return;
      }
      // Wenn ein neuer Wert eingeht, Weitergabe zur Aufbereitung und Defaults setzen
      if (newVal !== oldVal) {
        this.setChartCategories(newVal);
        this.setChartData(newVal, this.selectedChartCat1, "select1");
        this.setChartData(newVal, this.selectedChartCat2, "select2");
      }
    },
  },
```

> setChartCategories lädt aufbereitete Kategorien in den Cache.

```js
    setChartCategories(data) {
      const categories = [];
      // Guard wenn keine Daten ankommen oder die Suche kein Ergebnis ergibt
      if (data.length === 0) {
        this.chartCategories = categories;
        return;
      }
      // Filtert gewisse Kategorien heraus
      Object.keys(data[0]).forEach((key) => {
        if (
          key === "id" ||
          key === "Hauptartikelnr" ||
          key === "Artikelname" ||
          key === "Beschreibung"
        ) {
          return;
        }
        categories.push(key);
      });
      // setzt Cache Wert für Rendering
      this.chartCategories = categories;
    },
```

> setChartData bereitet die empfangenen Daten aus dem State zur Darstellung in den Diagrammen auf.

- **data** = Daten aller Kategorien
- **cat** = Kategorie welche aufbereitet werden soll
- **id** = id des Diagramms welches die Daten empfangen soll

```js
    setChartData(data, cat, id) {
      // Ermittlung Anzahl an Datensätzen
      const amount = data.length;
      // Zusammenfassung aller Datensätze in Key: Value Paare nach Kategorie mit summierter Anzahl
      const itemsObj = data.reduce((acc, el) => {
        acc[el[cat]] = (acc[el[cat]] || 0) + 1;
        return acc;
      }, {});

      // In Object mit Key empty String durch "k.A." für keine Angabe ersetzen
      if (Object.hasOwnProperty.call(itemsObj, "")) {
        itemsObj["k.A."] = itemsObj[""];
        delete itemsObj[""];
      }

      // Umwandlung von Stück zu Prozent (Eintrag Anzahl / Datensatz Anzahl) * 100 gerundet auf .00
      Object.keys(itemsObj).forEach((key) => {
        itemsObj[key] = ((itemsObj[key] / amount) * 100).toFixed(2);
      });

      // Zuweisung der Daten anhand ID
      if (id === "select1") {
        this.pieChartData = Object.entries(itemsObj);
        this.selectedChartCat1;
      } else if (id === "select2") {
        this.columnChartData = Object.entries(itemsObj);
      }
    },
```

> Bei Auswahl anderer Kategorie in Select Input reagiert das updateSelect Event.

```js
    updateSelect(e) {
      // Abfangen der ID via Event Target
      if (e.target.id === "select1") {
        this.setChartData(
          this.getChartData,
          this.selectedChartCat1,
          e.target.id
        );
      } else if (e.target.id === "select2") {
        // das gleiche für das 2. Diagramm
      }
    },
```

> Weitere Methoden.

```js
    // blendet Diagramme ein und aus
    toggleCharts() {
      this.chartsVisible = !this.chartsVisible;
    },
    // Zeigt Beenden Dialog an
    handleExit() {
      this.exitConfirmPending = true;
    },
    // Bricht Beenden Prozess ab
    cancelExit() {
      this.exitConfirmPending = false;
    },
```

> confirmExit Beendet den Bearbeitungsprozess, Setzt Session Data zurück / löscht state values. Zudem wird via action und service API ein Request ans Backend zur Löschung des Data Tables in SQL angestoßen.

```js
    async confirmExit() {
      const tableName = this.getTableName;
      try {
        const response = await this.dropTable(tableName);
        if (response && response.success) {
          // Session Data Reset
          this.unsetSessionData();
          // Erfolgsnachricht nach Beenden
          this.setSuccessCode("FES99");
        }
      } catch (error) {
        console.error("Error in confirmExit method:", error);
        throw error;
      } finally {
        // Reset der Cache variablen
        this.resetChartData();
      }
    },
```

> resetChartData setzt bei Verlassen der Anwendung alle cached Values zurück.

```js
    resetChartData() {
      this.chartsVisible = false;
      this.chartCategories = [];
      this.pieChartData = [];
      this.columnChartData = [];
      this.selectedChartCat1 = "Geschlecht";
      this.selectedChartCat2 = "Grammatur";
      this.exitConfirmPending = false;
    },
```

---

### FileExport.vue

> Komponente zur Darstellung eines Buttons zum CSV Download und zur finalen Abfrage vor dem Download. On Button klick öffnet einen Dialog zur Bestätigung.

##### TEMPLATE

```html
<v-btn v-if="getTableName" id="btn-export" color="primary" @click="handleExport"
  >als csv exportieren...</v-btn
>
```

> Dialog wird mit v-if via prop gerendert. Klick auf "Speichern" triggert eine Anfrage an das Backend zur Erstellung einer CSV Datei.

```html
<div v-if="exportConfirmPending" class="export-confirm">
  <!-- Auswahl Buttons abbrechen oder speichern; Verhalten abhängig von Browsereinstellung -->
  <v-btn @click="cancelExport" class="btn-confirm-export"> Abbrechen</v-btn
  ><v-btn @click="confirmExport" class="btn-confirm-export"> Speichern </v-btn>

  <!-- ... -->
</div>
```

##### SCRIPT

> Methode handle Export verwendet tableName für den call der action "exportData". Bei Erfolg wird mittels msgSuccessCode mutation der msgSuccess state aktualisiert.

```js
    async handleExportData() {
      const tableName = this.getTableName;
      try {
        // fetch Data für CSV
        const response = await this.exportData(tableName);
        if (!response.success) {
          console.error("Export failed:", response.error);
        } else {
        // set Success msg
          this.setSuccessCode("FES05");
        }
      } catch (error) {
        console.error("Error in handleExportData method:", error);
      }
    },
```

---

### UiMsgModal.vue

> Komponente zu Darstellung von UI Info für den User. Darstellung von Fehlern, Warnungen, Erfolgsmeldungen und Animationen. Warnungen und Fehler setzen einen Klick auf OK voraus. Erfolg faded automatisch langsam aus. Template ist via conditional rendering mit v-if Bedingungen gesteuert. Fehlercodes werden mittels msgMap.js abgeglichen und ergeben somit den Meldetext.

##### TEMPLATE

> Der obere Abschnitt stellt den Container für die dynamische Darstellung von UI Meldungen dar.

```html
<!-- UI Message Container -->
<div v-if="isError || isWarning || isSuccess" class="msg-wrapper">
  <div v-if="isError || isWarning" class="msg-container">
    <p v-if="isError" class="errorMsg">
      {{ errorMsg }}<i class="fa-solid fa-circle-exclamation"></i>
    </p>
    <p v-if="isWarning" class="warningMsg">{{ warningMsg }}</p>
    <button @click="confirmMsg" class="btn-confirm-msg">OK</button>
  </div>
  <div v-if="isSuccess" class="msg-container-fade">
    <p class="successMsg">{{ successMsg }}</p>
  </div>
</div>
```

> Die Animation wird wie folgt dargestellt.

```html
<!-- Loading Animation Container -->
<div v-if="isLoading" class="animation-wrapper">
  <div class="spinnerContainer">
    <div class="ball1"></div>
    <div class="ball2"></div>
    <div class="ball3"></div>
    <div class="ball4"></div>
    <div class="ball5"></div>
    <div class="ball6"></div>
    <div class="ball7"></div>
    <div class="ball8"></div>
  </div>
  <h2 class="animation-header">Tabelle wird erstellt...</h2>
</div>
```

##### SCRIPT

```js
// Einbinung der msgMap.js Datei in component:
import messageMap from "../store/mapping/msgMap";

// Inhalt der msgMap.js Datei enthält alle Codes samt zugehörigem Text.
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
```

> 4 Watcher für VUEX State changes für die Darstellung von UI Updates / Benachrichtigungen.

```js
watch: {
    // Für die Darstellung von Fehlern
    getErrorCode(nCode, oCode) {
    // Guard bei beenden der Session da local Storage variable zerstört wird
    // Dieser Guard befindet sich auch in Warning und Success watcher
      if (nCode === null || nCode === undefined) {
        return;
      }
    },

    // Für die Darstellung von Warnungen
    getWarningCode(nCode, oCode) {
        // ...
    },

    // Für die Anzeige von Erfolgsmeldungen
    // call von timed function successFadeTimer()
    getSuccessCode(newCode, oldCode) {
        // ...
    }

    // für Ladeanimation nach CSV Upload
    // setzt sich automatisch wieder zurück, nachdem Ladevorgang abgeschlossen ist
    getLoadingState(newState, oldState) {
        // ...
    },
  },
```

> Data Properties dienen zum conditional Rendering & Caching von Texten.

```js
data() {
    return {
      // Props für caching von Nachrichten
      errorMsg: null,
      warningMsg: null,
      successMsg: null,

      // Bools für conditional rendering
      isError: false,
      isWarning: false,
      isSuccess: false,

      isLoading: false,
    };
  },
```

> UI controll Methoden:

```js
    // bei klick auf OK in Warnung und Fehler, wird Fenster ausgeblendet
    confirmMsg() {
      this.isError = false;
      this.isWarning = false;
      this.errorMsg = null;
      this.warningMsg = null;
      this.unsetErrorCode();
      this.unsetWarningCode();
    },

    // Timed method zur Anzeigen von Erfolgsmeldungen für 1,5 Sek.
    successFadeTimer() {
      setTimeout(() => {
        this.isSuccess = false;
        this.successMsg = null;
        this.unsetSuccessCode();
      }, "1500");
    },
```

---

### ServerDataTable.vue

> Zur Darstellung wurde Vuetify als Library eingebunden. v-data-table-server ist für async Abfragen konzipiert. Die Tabelle arbeitet mit Paginierung. Neue Datensätze können angelegt werden. Spalte "Actions" Import ermöglicht Bearbeiten und Löschen. Eine Suche nach Kategorien wurde eingerichtet. Eine Standard Paginierung in Schritten wurde definiert.

##### TEMPLATE

> Die Tabelle wird mit den folgenden Attributen konfiguriert.

- **v-model:items-per-page="itemsPerPage"** = Paginierungswert
- **:items-per-page-options="[5, 10, 20, 50, 100, 200]"** = Options API Paginierungsinterval
- **:headers="visibleHeaders"** = Spaltennamen
- **:items-length="totalItems"** = Paginierungsparameter für Gesamtanzahl an Seiten
- **:items="serverItems"** = Daten zur Darstellung
- **:loading="loading"** = Aktiviert Ladeanimation
- **no-data-text="Text"** = Text für Tabelle wenn keine Daten empfangen wurden
- **@update:options="handleUpdate"** = default Event mit Params page, totalItems, itemsPerPage (Paginierung)

```html
<v-data-table-server
  v-if="getTableName"
  v-model:items-per-page="itemsPerPage"
  :items-per-page-options="[5, 10, 20, 50, 100, 200]"
  :headers="visibleHeaders"
  :items-length="totalItems"
  :items="serverItems"
  :loading="loading"
  no-data-text="Die Suche ergab keine Übereinstimmungen"
  @update:options="handleUpdate"
></v-data-table-server>
```

> FileExport Komponente ist zu Beginn der Toolbar eingebunden.

```html
<template v-slot:top>
  <v-toolbar flat style="height: 100px; padding: 1rem; background: #333">
    <FileExport />
  </v-toolbar>

  <!-- ... -->
</template>
```

> Die Suchleiste innerhalb des v-slot's innerhalb der v-toolbar ist wie folgt gegliedert und lässt nur eine Suche nach Auswahl der Kategorie zu. Das Suchfeld wird mit **validateSearchInput** auf Sonderzeichen überprüft. **onSubmitSearch** registriert den Enter Key und schickt die Anfrage ab. Ein Button zum Zurücksetzen der Suche befindet sich neben dem Suchfeld.

```html
<!-- Kategorie Dropdown -->
<v-select
  v-model="searchCategory"
  :items="searchCategories"
  label="Suchkategorie"
  dense
  hide-details
  outlined
  small
  color="primary"
  style="margin-right: 10px"
></v-select>

<!-- Eingabefeld für Volltextsuche -->
<v-text-field
  v-model="searchQuery"
  append-icon="mdi-magnify"
  label="Suchen"
  single-line
  hide-details
  color="primary"
  :disabled="!searchCategory"
  @keyup="validateSearchInput"
  @keyup.enter="onSubmitSearch"
></v-text-field>

<!-- Button zum reset des Suchergebnisses -->
<v-btn @click="resetSearch" color="red-lighten-2" :disabled="!searchQuery"
  >Suche zurücksetzen</v-btn
>
```

> Button zur Anlage gibt props als Trigger für parent component Anzeige des Dialogs weiter. Dadurch wird ein Dialog mit allen Spaltennamen erzeugt.

```html
<template v-slot:activator="{ props }">
  <v-btn color="primary" dark v-bind="props"> Neuer Artikel </v-btn>
</template>
```

> formTitle wird durch den aktuellen Index bestimmt. Je nach Wert wird ein Dialog zur Neuanlage oder zur Bearbeitung geöffnet. Beispiel: Bei Index -1 ergo kein Index wird eine Neuanlage getriggert, ergo auch ein passender Titel gewählt. Die Inputs werden mittels v-for anhand der empfangenen Daten generiert. Gewisse Felder werden conditional als textarea ausgegeben. Die ID wird in der Bearbeitung deaktivert, da diese im Backend erzeugt wird und als unique identifier gilt. Die Eingabe wird mittels validateEditInput auf Sonderzeichen geprüft.

```html
<!-- Titel abhängig von Index -->
<v-card-title>
  <span color="green-lighten-1" class="text-h6">{{ formTitle }}</span>
</v-card-title>

<v-card-text>
  <v-container>
    <v-row>
      <!-- For Loop anhand Serverdaten -->
      <v-col
        v-for="(value, key) in editedItem"
        :key="key"
        cols="12"
        sm="6"
        md="4"
      >
        <!-- textarea für Lange texte -->
        <template v-if="key === 'Beschreibung' || key === 'Materialangaben'">
          <v-textarea
            v-model="editedItem[key]"
            :label="key"
            :disabled="key === 'id'"
            auto-grow
            full-width
            maxlength="255"
            @keyup="validateEditInput(value, key)"
          ></v-textarea>
        </template>

        <!-- für alle anderen normaler Text input -->
        <template v-else>
          <v-text-field
            v-model="editedItem[key]"
            :label="key"
            :disabled="key === 'id'"
            full-width
            maxlength="50"
            @keyup="validateEditInput(value, key)"
          ></v-text-field>
        </template>
      </v-col>
    </v-row>
  </v-container>
</v-card-text>

<!-- Buttons zur Steuerung der Anlage und Bearbeitung -->
<v-card-actions>
  <v-spacer></v-spacer>
  <v-btn color="blue-lighten-1" variant="text" @click="close">
    Abbrechen
  </v-btn>
  <v-btn color="blue-lighten-1" variant="text" @click="save"> Speichern </v-btn>
</v-card-actions>
```

> Bei Löschung eines Eintrags wird ein Dialog zur Bestätigung aufgerufen.

```html
<v-dialog v-model="dialogDelete" max-width="700px">
  <v-card>
    <v-card-title class="text-h6 text-center"
      >Sind Sie sicher, dass Sie diesen Artikel löschen möchten?</v-card-title
    >
    <v-card-actions>
      <v-spacer></v-spacer>
      <!-- Bricht den Vorgang ab -->
      <v-btn color="blue-darken-1" variant="text" @click="closeDelete"
        >Cancel</v-btn
      >
      <!-- Bestätigt die Löschung und initiiert Query ans Backend zur Löschung in der Datenbank -->
      <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm"
        >OK</v-btn
      >
      <v-spacer></v-spacer>
    </v-card-actions>
  </v-card>
</v-dialog>
```

> in den nachfolgenden Zeilen wird der Text für manche Spalten limitiert, die Aktions Icons werden bestimmt und mit einem Event verknüpft. Zudem wird die Skeleton Loading Animation für die Zeilen eingebunden.

```html
<!-- Limitierung auf 50 Zeichen -->
<template v-slot:[`item.Beschreibung`]="{ item }">
  {{ truncateText(item.Beschreibung) }}
</template>
<template v-slot:[`item.Materialangaben`]="{ item }">
  {{ truncateText(item.Materialangaben) }}
</template>

<!-- Einbindung von Aktionen samt Icons und Events -->
<template v-slot:[`item.actions`]="{ item }">
  <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
  <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
</template>

<!-- Aktivierung von Skeleton Loading Animation -->
<template v-slot:loading>
  <v-skeleton-loader
    v-for="i in itemsPerPage === -1 ? totalItems : itemsPerPage"
    :key="`skeleton-row-${i}`"
    type="table-row"
    :headers="headers"
  />
</template>
```

##### SCRIPT

> Als Setup Parameter und zum Caching sind die folgenden Werte in data() definiert.

```js
  data: () => ({
    headers: [], // Überschriften
    serverItems: [], // Cache für fetched Data
    totalItems: 0, // Gesamtanzahl Alle Daten / Suche (Paginierung)
    currentPage: 1, // Aktuelle Seite g. Paginierung
    itemsPerPage: 10, // Datensätze pro Seite (Paginierung)
    currentSort: [{ key: "id", order: "asc" }], // Default Wert für Sortierung bei Query

    loading: false, // Bool für Animation
    dialog: false, // Bool für UI Abfragen (Anlage, Bearbeitung)
    dialogDelete: false, // Bool für UI Abfrage (Löschung)

    editedIndex: -1, // Indexabgleich zu Bestimmung FormTitle (Bearbeitung, Löschung oder Neuanlage)
    editedItem: {}, // Platzhalter für Datenverarbeitung
    defaultItem: {}, // wenn kein Datensatz im Platzhalter Fallback

    searchCategories: [], // Mapping der Suchkategorien
    searchCategory: "", // aktuell ausgewählte Kategorie
    searchQuery: "", // Suchbegriff
    isSearching: false, // Suchparameter gesetzt, unterbindet fetchAll
  }),
```

##### SCRIPT computed

> Info zu computed property.

```js
    // Filtert nur sichtbare Überschriften heraus
    visibleHeaders() {
      return this.headers.filter((header) => header.visible);
    },

    // Initiiert handleUpdate durch Anlage tableName
    watchTableNameSet() {
      return this.getTableName;
    },

    // bestimmt Überschrift für Dialog
    formTitle() {
      return this.editedIndex === -1 ? "NEUER ARTIKEL" : "ARTIKEL BEARBEITEN";
    },
```

##### SCRIPT watch

> Info zu Watchers.

```js
 watch: {
    // Wenn tableName gesetzt wurde, initiiert handleUpdate Datenabfrage ans Backend
    watchTableNameSet(newTableName, oldTableName) {
      if (newTableName !== oldTableName) {
        this.handleUpdate();
      }
    },

    // ui control dialogs
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },

    // Nach Beenden der Session reset aller Cache Daten auf Default
    getTableName(newVal) {
      if (newVal === null) {
        this.resetTableData();
      }
    },
  },
```

##### SCRIPT methods

> Um bereits im Frontend Fehleingaben des Users zu vermeiden, wird das Suchfeld onKeyUp mit validateSearchInput überprüft. Sollte ein Sonderzeichen außerhalb des Regex gefunden werden, erhält der benutzer eine Warnung und via v-model wird die letzte Eingabe wieder entfernt.

```js
    validateSearchInput() {
      const validChars = /[a-zA-Z0-9.,%&]/;
      const searchQuery = this.searchQuery;

      for (let i = 0; i < searchQuery.length; i++) {
        if (!validChars.test(searchQuery[i])) {
          this.setErrorCode("FEE08");
          this.searchQuery = searchQuery.substring(0, searchQuery.length - 1);
          return;
        }
      }
    },
```

> Die gleiche Überprüfung wird auf die Text Inputs der Neuanlage und Bearbeitung angewandt. Jedoch wird hier aus Gründen der usability auf Fehlermeldungen und Warnungen verzichtet. Die ungültige Eingabe wird lediglich rückgängig gemacht.

```js
    validateEditInput(value, key) {
      const validChars = /[a-zA-Z0-9.,%&]/;
      console.log(value, key);

      for (let i = 0; i < value.length; i++) {
        if (!validChars.test(value[i])) {
          this.editedItem[key] = value.substring(0, value.length - 1);
          return;
        }
      }
    },
```

> Die Suche wird über zwei Funktionen gesteuert.

```js
    // wird durch Drücken der Enter Taste ausgelöst; return wenn kein Wert eingegeben wurde
    onSubmitSearch() {
      if (this.searchQuery.length === 0) {
        return;
      } else {
        // setzt Bool auf true damit handleUpdate die Suchergebnisse statt Default an das Backend sendet
        this.isSearching = true;
        this.handleUpdate();
      }
    },

    // setzt alle Suchparameter zurück
    resetSearch() {
      this.searchCategory = "";
      this.searchQuery = "";
      this.isSearching = false;
      this.handleUpdate();
    },
```

> handleUpdate is die Haupt Methode welche auf Änderungen der Tabelle reagiert. Sie unterscheidet ob normal gefetched oder gesucht wird. Zudem werden Default Parameter und ein Basic payload für das Backend definiert.

```js
    handleUpdate(options) {
      // Guard bei Beenden der Anwendung um Fehler zu verhindert
      if (!this.getTableName) {
        return;
      }
      // Weißt Default Werte zu, falls handleUpdate außerhalb von @update one Parameter aufgerufen wird
      if (!options) {
        options = {
          page: this.currentPage,
          itemsPerPage: this.itemsPerPage,
          sortBy: this.currentSort.length
            ? this.currentSort
            : [{ key: "id", order: "asc" }],
        };
      }

      // Cached den aktuellen Paginierungsstand damit diese nicht zurückgestzt wird
      this.currentPage = options.page;
      this.itemsPerPage = options.itemsPerPage;
      this.currentSort = options.sortBy;
      this.loading = true;

      // definiert einen Payload, der für beide loadItem Methoden passend ist
      const payload = {
        tableName: this.getTableName,
        page: this.currentPage,
        itemsPerPage: this.itemsPerPage,
        sortBy: options.sortBy.length
          ? options.sortBy[0]
          : { key: "id", order: "asc" },
      };

      // Bool is Searching entscheidet welche Daten vom Backend angefragt werden
      if (!this.isSearching) {
        this.loadItemsDefault(payload);
      } else {
        this.loadItemsSearch(payload);
      }
    },
```

> loadItemsDefault ist straight forward und initiiert über eine action und eine Service API eine Anfrage an das Backend. Als Antwort wird ein Array aus Objecten mit Daten empfangen.

```js
    async loadItemsDefault(payload) {
      try {
        const response = await this.fetchFormData(payload);
        if (response && response.success) {
          // gibt die empfangenen Daten zur Aufbereitung an setTableParams weiter
          this.setTableParams(response);
        }
      } catch (error) {
        console.error("error:", error);
      } finally {
        // setzt den loading Bool zurück welcher über die options API von Vuetify die Animation triggert
        this.loading = false;
        // scrollt die Seite zurück an den Anfang
        window.scrollTo({top: 0, behavior: "smooth"});
      }
    },
```

> loadItemsSearch ergänzt den Payload mit Zusatzdaten. Auch hier wird eine Abfrage an das Backend geschickt. Errorhandling ist integriert. Ergibt die Suche keine Übereinstimmung wird eine Warnung ausgegeben.

```js
    async loadItemsSearch(payload) {
      // fügt Suchkategorie / -begriff hinzu
      payload.searchCategory = this.searchCategory;
      payload.searchQuery = this.searchQuery;
      try {
        const response = await this.fetchSearchData(payload);
        if (!response.success) {
          this.setErrorCode("FEE07");
        } else if (response.total == 0) {
          this.setWarningCode("FEW01");
        }
        this.setTableParams(response);
      } catch (error) {
        console.error("error:", error);
      } finally {
        this.loading = false;
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    },
```

> setTableParams leitet die Erstellung der Überschriften und Suchkriterien ein und überprüft ob diese bereits existieren.

```js
    setTableParams(response) {
      // cached die Empfangenen Daten und die Gesamtanzahl; Gesamtanzahl für Paginierung
      this.serverItems = response.tableData;
      this.totalItems = response.total;

      // Setzt die Überschriften der Tabelle falls noch nicht
      if (this.headers.length === 0) {
        this.setTableHeaders(response.tableData[0]);
      }

      // Setzt die Suchkategorien der Tabelle falls noch nicht geschehen
      if (this.searchCategories.length === 0) {
        this.setSearchCategories(response.tableData[0]);
      }
    },
```

> setTableHeader stellt die Überschriften wie folgt zur Verfügung und bereitet die Daten ein wenig auf.

```js
    setTableHeaders(obj) {
      // Guard falls letztes Item auf einer Seite gelöscht wird
      if (obj === undefined) {
        return;
      }

      const keys = Object.keys(obj);
      keys.forEach((key) => {
        const newObj = {};
        // Passt Überschriften für bessere Darstellung an
        if (key === "Hauptartikelnr") {
          newObj.title = "Art#";
        } else if (key === "Geschlecht") {
          newObj.title = "Gender";
        } else {
          newObj.title = key;
        }

        // definiert den key und deaktiviert die Sortierfunktion
        newObj.key = key;
        newObj.sortable = false;

        // Stellt manche Überschriften auf unsichtbar; ID ist nur unique identifier für Backend Mapping; Bildname unwichtig
        if (key === "id" || key === "Bildname") {
          newObj.visible = false;
        } else {
          newObj.visible = true;
        }
        this.headers.push(newObj);
      });

      // fügt eine Spalte für Aktionen mit hinzu
      this.headers.push({
        title: "Aktionen",
        key: "actions",
        sortable: false,
        visible: true,
      });

      // Guard setzt initiale default Values für Bearbeitungs Dialog
      if (Object.keys(this.editedItem).length === 0) {
        this.setEditItemDefault(keys);
      }
    },
```

> setEditItemDefault stellt danach die Felder für die Bearbeitung zur Verfügung und exkludiert ID.

```js
    setEditItemDefault(keys) {
      keys.forEach((key) => {
        // id wird raus gefilter -> auto increment in backend
        if (key === "id") {
          return;
        }
        this.editedItem[key] = "";
        this.defaultItem[key] = "";
      });
    },
```

> setSearchCategories stellt die Suchkategorien in angepasster Form zur Verfügung.

```js
    setSearchCategories(obj) {
      // Guard falls letztes Item auf einer Seite gelöscht wird
      if (obj === undefined) {
        return;
      }

      const keys = Object.keys(obj);
      keys.forEach((key) => {
        // id & Bildname werden herausgefiltert
        if (key === "id" || key === "Bildname") {
          return;
        }
        this.searchCategories.push(key);
      });
    },
```

> die folgenden Methoden sind alle samt UI management Methoden.

```js
    // Kürzt die Länge des Anzeigetextes in der Tabelle
    truncateText(text) {
      return text && text.length > 50 ? text.substr(0, 50) + "..." : text;
    },
    // aktuallisiert den Index zur Entscheidung ob Bearbeitung oder Neuanlage und öffnet Dialog
    editItem(item) {
      this.editedIndex = this.serverItems.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    // das Gleiche für die Löschung bezüglich Index und öffnet Dialog
    deleteItem(item) {
      this.editedIndex = this.serverItems.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    // schließt den Dialog und setzt cache Werte zurück
    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    // das Gleiche für die Löschung
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
```

> deleteItemCofirm wird ausgelöst, wenn ein Item zur Löschung mit Button klick im Dialog bestätigt wird. Die Methode sendet eine Anfrage via action und service API an das Backend zur Löschung des Datensatzes.

```js
    async deleteItemConfirm() {
      // greift die Item ID als Löschparameter ab
      const itemId = this.serverItems[this.editedIndex].id;
      try {
        const response = await this.removeItem(itemId);
        if (response && response.success) {
          // aktuallisiert den Datenbestand im Frontend und zeigt Erfolgsmeldung an
          this.handleUpdate();
          this.setSuccessCode("FES02");
        }
      } catch (error) {
        console.error("Error in remove item method.");
        throw error;
      }
      // schließt den Dialog zur Löschung
      this.closeDelete();
    },
```

> Die save Methode speichert Änderungen an bestehenden Items und Neuanlagen. Unterschieden wird anhand des Item Index.

```js
    async save() {
      // Wenn Index größer -1 dann Item in Data Table ergo Anpassung
      if (this.editedIndex > -1) {
        // Definiert Payload für Backend
        const item = Object.assign(
          this.serverItems[this.editedIndex],
          this.editedItem
        );
        try {
          // schickt Anfrage via action und service API an Backend zur Anpassung
          const response = await this.updateItem(item);
          if (response && response.success) {
          // aktuallisiert den Datenbestand im Frontend und zeigt Erfolgsmeldung an
            this.handleUpdate();
            this.setSuccessCode("FES03");
          }
        } catch (error) {
          console.error("Error in save updated item method.", error);
          throw error;
        }
      // Wenn Index nicht > -1 ergo -1 dann Item nicht in Data Table ergo Neuanlage
      } else {
        try {
          // Item aus cache aus Dialog wird übergeben; Anfrage via action und service API an Backend zur Neuanlage
          const response = await this.addNewItem(this.editedItem);
          if (response && response.success) {
            // aktuallisiert den Datenbestand im Frontend und zeigt Erfolgsmeldung an
            // Randnotiz: Neu angelegtes Item wird am Ende der Liste eingefügt da ID = auto increment
            this.handleUpdate();
            this.setSuccessCode("FES04");
          }
        } catch (error) {
          console.error("Error in save new item method.", error);
          throw error;
        }
      }
      this.close();
    },
```

> Bei Beenden der Anwendung wird durch resetTableData der Ausgangszustand wiederher gestellt.

```js
    resetTableData() {
      this.headers = [];
      this.serverItems = [];
      this.totalItems = 0;
      this.currentPage = 1;
      this.itemsPerPage = 10;
      this.currentSort = [{ key: "id", order: "asc" }];

      this.loading = false;
      this.dialog = false;
      this.dialogDelete = false;

      this.editedIndex = -1;
      this.editedItem = {};
      this.defaultItem = {};

      this.searchCategories = [];
      this.searchCategory = "";
      this.searchQuery = "";
      this.isSearching = false;
    },
```

---

## SCSS

> Die SCSS Struktur ist übersichtlich und schnell erklärt. Die **main.scss** Datei dient als Output file für CSS Compiling und bindet alle Partials mit **@use** ein. Ordner **abstracts** enthält die Partials **\_animations.scss** und **\_colors.scss**. Selbige enthalten Animationen und Farbvariablen. Order **partials** enthält die component Sytlings. Bei Bedarf sind abstracts via **@use** eingebunden. Es findet kein Namespacing statt ergo sind alle @use commands mit **as "\*"** ausgewiesen.

---

## SERVICES

> Das services Verzeichnis enthält APIs zur Kommunikation mit dem Backend.

- **crudService.js** = CRUD API für Item Management (Anlage, Anpassung, Löschung)
- **dropTableService.js** = Nach Beenden der Anwendung Löschung des SQL data table
- **exportService.js** = Empfängt Exportdaten zur CSV Erzeugung samt Download
- **fetchService.js** = Datenaustausch nach Paginierung und für Suchanfragen
- **uploadService.js** = Erzeugung von SQL Data Table samt CSV Upload

---

##### crudService.js

> Wie der Name vermuten lässt, kümmert sich diese API um die Neuanlage, Anpassung und Löschung von Items. Im Nachfolgenden die Unterschiede.

```js
// URL der Backend API
const baseURL = "http://localhost/external/api/crud.api.php";
// Weitergabe des Payload
// service zur Anpassung von Items
export const updateItem = async (payload) => {
  try {
    // URL wird in anderen services abweichen
    const response = await fetch(`${baseURL}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
    if (!response.ok) {
      // entsprechende Fehlermeldung pro service
      throw new Error("Network error while updating item!");
    }
    return await response.json();
  } catch (error) {
    // entsprechende Fehlermeldung pro service
    console.error("Error in updateItem service:", error);
    throw error;
  }
};

// service zur Neuanlage von Items
export const addNewItem = async (payload) => {
  try {
    // URL extension "/add"
    const response = await fetch(`${baseURL}/add`, {
      method: "POST",
      // ...
    });
    // ...
  } catch (error) {
    //...
  }
};

// service zur Löschung von Items
export const removeItem = async (payload) => {
  try {
    // URL extension "/delete"
    const response = await fetch(`${baseURL}/delete`, {
      // methode POST statt DELETE da einfacher im Backend zu handeln
      method: "POST",
      // ...
    });
    // ...
  } catch (error) {
    //...
  }
};
```

---

##### dropTableService.js

> API zur Löschung des angelegten Data Tables in SQL.

```js
// URL der Backend API
const baseURL = "http://localhost/external/api/drop.api.php";
// tableName als Payload ausreichend
export const dropTable = async (tableName) => {
  try {
    const response = await fetch(`${baseURL}`, {
      // ebenfalls POST statt DELETE für leichteres handling
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(tableName),
    });
    if (!response.ok) {
      throw new Error("Network error while dropping table!");
    }
    return await response.json();
  } catch (error) {
    console.error("Error in dropTable service:", error);
    throw error;
  }
};
```

---

##### exportService.js

> Export Serivce sendet eine Anfrage an das Backend um anhand des tableName eine CSV Datei zu generieren. Das Ergebnis wird in ein Blob Object umgewandelt und eine URL zum download wird generiert. Der Dateiname wird definiert und nach Abschluss wird der erzeugt a-Tag wieder gelöscht.

```js
// URL der Backend API
const baseURL = "http://localhost/external/api/export.api.php";
// tableName als Payload ausreichend
export const csvExport = async (tableName) => {
  try {
    const response = await fetch(
      // tableName wird in URL übertragen und zuvor in einen String umgewandelt
      `${baseURL}?tableName=${encodeURIComponent(tableName)}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );
    if (!response.ok) {
      throw new Error("Network error while exporting CSV!");
    }
    // Umwandlung Antwort in ein Blob Object
    const blob = await response.blob();
    // Download Link wird generiert
    const downloadUrl = window.URL.createObjectURL(blob);
    // ein a-Tag wird erzeugt
    const a = document.createElement("a");
    // href attribute erhält den Download Link
    a.href = downloadUrl;
    // Standard Dateiname wird angegeben
    a.download = "export.csv";
    // a-Tag wird in den DOM gepackt
    document.body.appendChild(a);
    // erzeugter a-Tag wird angeklickt um Download zu starten
    a.click();
    // danach wird der a-Tag wieder entfernt
    a.remove();
  } catch (error) {
    console.error("Error in csvExport service:", error);
    throw error;
  }
};
```

---

##### fetchService.js

> Die Fetch API fragt Daten im Backend ab. Es gibt einen Service für den Default und einen weiteren für Suchanfragen. In jedem Payload sind Paginierungsinformationen enthalten. Bei Suchanfragen zusätzlich die Suchkategorie und der Suchbegriff. Als Antwort kommt ein Array aus Objekten mit den Datensätzen zurück. Wird ein Fehler aus dem Backend kommuniziert, wird success auf false gesetzt und in der Component ein Error Code gesetzt.

###### Payload für Paginierung:

- **"tableName"** = Name des SQL Data Tables
- **"page"** = Aktuelle Seite lt. Tabelle in Frontend
- **"itemsPerPage"** = Anzahl an Elementen pro Seite lt. Tabelle in Frontend
- **"sortBy"** = Object mit Sortierungsparametern

```js
// URL der Backend API
const baseURL = "http://localhost/external/api/fetch.api.php";

export const fetchData = async (payload) => {
  try {
    const response = await fetch(
      // URL Erweiterung /fetch
      // encoded Strings in URL für Backend
      `${baseURL}/fetch?tableName=${encodeURIComponent(
        payload.tableName
      )}&page=${encodeURIComponent(
        payload.page
      )}&itemsPerPage=${encodeURIComponent(
        payload.itemsPerPage
      )}&sortBy=${encodeURIComponent(JSON.stringify(payload.sortBy))}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    const responseData = await response.json();
    if (!response.ok) {
      throw new Error(
        responseData.message || "Network error while fetching table data!"
      );
    }

    if (!responseData.success) {
      return { success: false };
    }
    return responseData;
  } catch (error) {
    console.error("Error in fetchData service:", error);
    throw error;
  }
};

export const fetchSearch = async (payload) => {
  try {
    const response = await fetch(
      // URL Erweiterung /search
      `${baseURL}/search?tableName=${encodeURIComponent(
        payload.tableName
        // ...
        // Zusätzliche Parameter für Suchanfragen
      )}&searchCategory=${encodeURIComponent(
        payload.searchCategory
      )}&searchQuery=${encodeURIComponent(payload.searchQuery)}`,
      {
        // ...
      }
    );

    // ...
  } catch (error) {
    // ...
  }
};
```

---

##### uploadService.js

> Die Upload API ist straight forward. FormData wird an das Backend gesendet, dort validiert und eine SQL Data Table wird erzeugt. Als Antwort erhalten wir den tableName.

```js
// URL der Backend API
const baseURL = "http://localhost/external/api/upload.api.php";
```

---

## VUEX STORE

> Im Vuex store sind alle Prozesse straight forward, daher im nachfolgenden eine Erläuterung über den modularen Aufbau **index.js**.

```js
import { createStore } from "vuex";

// import der Module
import state from "@/store/modules/state";
import mutations from "@/store/modules/mutations";
import getters from "@/store/modules/getters";
import actions from "@/store/modules/actions";

const store = createStore({
  state,
  mutations,
  getters,
  actions,
});

export default store;
```
