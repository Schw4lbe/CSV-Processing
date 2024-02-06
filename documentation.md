# frontend

### FileExport.vue

> Komponente zur Darstellung eines Buttons zum CSV Download und zur finalen Abfrage vor dem Download. On Button klick öffnet einen Dialog zur Bestätigung.

```html
<v-btn v-if="getTableName" id="btn-export" color="primary" @click="handleExport"
  >als csv exportieren...</v-btn
>
```

> Dialog wird mit v-if via prop gerendert. Klick auf "Speichern" triggert eine Anfrage an das Backend zur Erstellung einer CSV Datei.

```html
<div v-if="exportConfirmPending" class="export-confirm">
  // Auswahl Buttons abbrechen oder speichern; Verhalten abhängig von
  Browsereinstellung
  <v-btn @click="cancelExport" class="btn-confirm-export"> Abbrechen</v-btn
  ><v-btn @click="confirmExport" class="btn-confirm-export"> Speichern </v-btn>

  //...
</div>
```

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

### UiMsgModal.vue

> Komponente zu Darstellung von UI Info für den User. Darstellung von Fehlern, Warnungen, Erfolgsmeldungen und Animationen. Warnungen und Fehler setzen einen Klick auf OK voraus um zu verschwinden. Erfolg faded automatisch langsam aus. Template ist via conditional rendering mit v-if Bedingungen gesteuert. Fehlercodes werden mittels msgMap.js abgeglichen und ergeben somit den Meldetext.

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

  // Frontend Warnings:
  FEW01: "",

  // Frontend Success:
  FES01: "CSV erfolgreich importiert",
  FES02: "Artikel wurde gelöscht",
  FES03: "Artikel wurde bearbeitet",
  FES04: "Neuer Artikel wurde hinzugefügt",
  FES05: "CSV zum Download bereit",
  FES99: "Session wurde beendet",
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
        // code
    },

    // Für die Anzeige von Erfolgsmeldungen
    // call von timed function successFadeTimer()
    getSuccessCode(newCode, oldCode) {
        // code
    }

    // für Ladeanimation nach CSV Upload
    // setzt sich automatisch wieder zurück, nachdem Ladevorgang abgeschlossen ist
    getLoadingState(newState, oldState) {
        // code
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

> UI controll methods:

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

# backend
